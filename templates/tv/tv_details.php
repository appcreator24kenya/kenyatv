<?php
    require_once __DIR__ . '/../../config/constants.php';

    $channelName = "Citizen TV";
    $embedUrl    = "https://kenyalivetv.co.ke/embed/citizen-tv-live?theme=dark";

    $status       = "live";       // Options: 'live', 'off_air'
    $availability = "both";       // Options: 'both', 'website_only', 'app_only', 'tv_only', 'restricted'

    // Determine embed visibility: ONLY show player when status is LIVE and availability is BOTH or WEBSITE ONLY
    $canEmbedVideo = ($status === 'live' && ($availability === 'both' || $availability === 'website_only'));

    // Custom message matrix based on status & availability
    $message = null;
    $messageClass = "";

    if (!$canEmbedVideo) {
        if ($status === 'off_air') {
            $message = "{$channelName} IS OFF AIR OR NO LONGER LIVESTREAMING ONLINE.";
            $messageClass = "avail-offair";
        } else {
            // Status is LIVE, but playback is restricted by availability setting
            switch ($availability) {
                case 'app_only':
                    $message = "{$channelName} is available on Mobile App only. Download our app to watch.";
                    $messageClass = "avail-app";
                    break;
                    
                case 'tv_only':
                    $message = "{$channelName} is available on DTV (Digital TV) e.g., Signet, Pang & ADN.";
                    $messageClass = "avail-tv";
                    break;
                    
                case 'restricted':
                    $message = "{$channelName} stream is restricted in your region due to broadcasting rights.";
                    $messageClass = "avail-restricted";
                    break;
            }
        }
    }
    

    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 

    if (!isset($_GET['slug'])) {
        die('Channel URL not correct');
    }
    
    $slug = $_GET['slug'];
    $channels = fetchTVList() ?? [];
    $TopTVShows = fetchMostViewedShowsByType('tv') ?? [];
    
    if (is_null($channels)) {
        $channels = []; 
    }
    

    
    $channelId = "";
    $channelDetails = [];

    // Find the channel details by slug
    foreach ($channels as $channel) {
        if (!is_array($channel)) {
            continue; // Skip non-array elements
        }
        if (isset($channel['channel_slug']) && $channel['channel_slug'] === $slug) {
            $channelId = $channel['id'];
            $channelDetails = $channel;
            
            break;
        }
    }
    
    // Check if $channelId is still empty after processing
    if (empty($channelId)) {
        // Redirect to 404 page or handle error as needed
        header("Location: /pages/error.php");
        exit;
    }

    // fetch current tv reviews data
    $GetTVReviewsData = fetchAllTVReviewsData();
    if (isset($GetTVReviewsData[$channelId])) {
        $GetTVReviewsData = $GetTVReviewsData[$channelId];
    } else {
        $GetTVReviewsData = [];
    }
    // Filter reviews with non-empty comments
    $validReviews = [];
    if (!empty($GetTVReviewsData['reviews'])) {
        foreach ($GetTVReviewsData['reviews'] as $review) {
            if (!empty($review['comment']) && is_string($review['comment']) && trim($review['comment']) !== '') {
                $validReviews[] = $review;
            }
        }
    }
    // Recalculate stats based only on reviews with comments
    $totalReviews = count($validReviews);
    $totalRating = 0;
    foreach ($validReviews as $review) {
        $totalRating += (int)$review['rating'];
    }
    $averageRating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;
    // Sort reviews from latest to oldest
    usort($validReviews, function ($a, $b) {
        return strtotime($b['created']) - strtotime($a['created']);
    });

    // Function to get initials
    function getInitials($name) {
        $parts = explode(" ", trim($name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($parts[0], 0, 1));
    }
    

    //Add 1 in Channel Views Values
    $cookie_name = "page_view_time_tv_" . $channelId;
    $cookie_expiration_time = time() + 64800; // 18 hours

    // This condition ensures the code only runs for specific user types.
    if (in_array($userLoginType, ['email', 'math'])) {
        if (!isset($_COOKIE[$cookie_name])) {
            // Set a new cookie with a unique name for this channel.
            setcookie($cookie_name, time(), [
                'expires' => $cookie_expiration_time,
                'path' => '/', // This makes the cookie available across the entire domain.
                'secure' => true, // Recommended for HTTPS sites.
                'httponly' => true, // Prevents access via JavaScript.
                'samesite' => 'Lax'
            ]);
            // Increment the view count for this specific channel in your database.
            storeTVViewsCount($channelId);
        }
    }

    // Fetch all TV schedules
    $GetChannelSchedule = fetchAllTVSchedule();
    $GetChannelSchedule = $GetChannelSchedule[$channelId]['schedule'] ?? [];
    $GetChannelSchedule = is_array($GetChannelSchedule) ? $GetChannelSchedule : [];

    // Get the current day of the week
    $currentDay = date("l"); // e.g., Monday, Tuesday, etc.
    
    // Filter schedules for the current day
    $todaySchedule = array_filter($GetChannelSchedule, function ($schedule) use ($currentDay) {
        if (isset($schedule['schedule_days'])) {
            // Normalize schedule_days to an array
            $daysArray = is_array($schedule['schedule_days']) 
                ? $schedule['schedule_days'] 
                : array_map('trim', explode(',', $schedule['schedule_days']));
            
            // Check if current day exists in the days array
            return in_array($currentDay, $daysArray);
        }
        return false;
    });                    
    
    // PHP logic to process the schedule and set the cookie (placed at the very top of the file)
    $processedSchedule = [];
    $activeShowFound = false; // Flag to ensure we only process the cookie once

    // Get the user's timezone once to prevent multiple function calls in the loop
    $userTimezone = getUserTimezone();

    // Sort the schedule based on the user's local time.
    if (!empty($todaySchedule)) {
        usort($todaySchedule, function ($a, $b) use ($userTimezone) {
            $aTime = convertFromUTC($a['start_time'], $userTimezone);
            $bTime = convertFromUTC($b['start_time'], $userTimezone);
            return strtotime($aTime) - strtotime($bTime);
        });
    }

    // Create a single "current time" object in the user's timezone
    $dtCurrentTime = new DateTime('now', new DateTimeZone($userTimezone));

    foreach ($todaySchedule as $schedule) {
        // Convert show times to the user's timezone for comparison
        $startTime = convertFromUTC($schedule['start_time'], $userTimezone);
        $endTime = convertFromUTC($schedule['end_time'], $userTimezone);

        // Create DateTime objects using the user's local time strings and timezone
        $dtStartTime = DateTime::createFromFormat('H:i:s', $startTime, new DateTimeZone($userTimezone));
        $dtEndTime = DateTime::createFromFormat('H:i:s', $endTime, new DateTimeZone($userTimezone));

        $isActive = '';
        $progressPercentage = 0;

        // Case 1: Show spans midnight
        if ($dtEndTime < $dtStartTime) {
            $dtEndTime->modify('+1 day');
            if (($dtCurrentTime >= $dtStartTime) || ($dtCurrentTime < $dtEndTime)) {
                $isActive = 'active';
            }
        } else { // Case 2: Show does not span midnight
            if (($dtCurrentTime >= $dtStartTime) && ($dtCurrentTime < $dtEndTime)) {
                $isActive = 'active';
            }
        }

        if ($isActive) {
            $totalDurationSeconds = ($dtEndTime->getTimestamp() - $dtStartTime->getTimestamp());
            $elapsedSeconds = ($dtCurrentTime->getTimestamp() - $dtStartTime->getTimestamp());
            $progressPercentage = ($totalDurationSeconds > 0) ? ($elapsedSeconds / $totalDurationSeconds) * 100 : 0;
        }

        if ($isActive === 'active' && !$activeShowFound) {
            $showId = $schedule['show_id'];
            $cookie_name = "page_view_time_show_" . $showId;
            $show_end_timestamp = strtotime($endTime);
            $current_timestamp = time();

            if ($show_end_timestamp > $current_timestamp && in_array($userLoginType, ['email', 'math'])) {
                if (!isset($_COOKIE[$cookie_name])) {
                    setcookie($cookie_name, $current_timestamp, [
                        'expires' => $show_end_timestamp,
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);

                    updateActiveTVShowViews($showId);
                    // error_log("Views updated for show: {$schedule['show_name']} | Show ID: {$schedule['show_id']} | Start Time: " . date("h:i A", strtotime($startTime)) . " | End Time: " . date("h:i A", strtotime($endTime)) . " | Channel Name: {$channelDetails['channel_name']}");
                }
            }
            $activeShowFound = true;
        }

        $schedule['isActive'] = $isActive;
        $schedule['progressPercentage'] = round($progressPercentage);
        $processedSchedule[] = $schedule;
    }

    // Function to update the recently watched tv array in the cookie
    function updateRecentlyWatched($channelId) {
        // Get the current recently listened array from the cookie
        $recentlyWatched = isset($_COOKIE['recently_listened_tv']) ? json_decode($_COOKIE['recently_listened_tv'], true) : [];
    
        // Remove the channelId if it already exists in the array
        if (($key = array_search($channelId, $recentlyWatched)) !== false) {
            unset($recentlyWatched[$key]);
        }
    
        // Add the channelId to the beginning of the array
        array_unshift($recentlyWatched, $channelId);
    
        // Update the cookie with the new array
        setcookie('recently_listened_tv', json_encode($recentlyWatched), time() + (86400 * 30), "/"); // 30 days expiry
    }
    
    // Update the recently Watched array whenever this radio is played
    updateRecentlyWatched($channelId);
    
    // Get recently listened channels from cookies
    $recentlyWatched = isset($_COOKIE['recently_listened_tv']) ? json_decode($_COOKIE['recently_listened_tv'], true) : [];
    $recentlyWatchedTV = getRecentlyWatchedTV($channels);
    
    // Sort recently listened channels based on their position in the reversed recently_listened_tv array
    if (!empty($recentlyWatched)) {
        usort($recentlyWatchedTV, function($a, $b) use ($recentlyWatched) {
            return array_search($a['id'], $recentlyWatched) - array_search($b['id'], $recentlyWatched);
        });
    } else {
        $recentlyWatchedTV = [];
    }


    // Filter out the current channel from the list of channels
    $otherChannels = [];
    foreach ($channels as $channel) {
        if ($channel['id'] !== $channelId) {
            $otherChannels[] = $channel;
        }
    }

    
    // Log if no other channels are found
    if (empty($otherChannels)) {
        error_log("Notice: No other channels found to display for channel ID $channelId");
    }
    
    $current_url = BASE_URL.'/tv/'.$channelDetails['channel_slug'];
    $meta_title = 'Watch '.$channelDetails['channel_name']. ' Live Online | Kenya Live TV';
    $meta_description = 'Watch '.$channelDetails['channel_name']. ' live online on Kenya Live TV. Stream your favorite Kenyan channels anytime, anywhere with high-quality streaming.';
?>
<!DOCTYPE html>
<html lang="en" oncontextmenu="return false" oncut="return false" oncopy="return false" onpaste="return false">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once __DIR__ . '/../../inc/head-scripts.php'; ?>
    <!-- html seo tags  -->
    <title><?= $meta_title; ?></title>
    <meta name="description" content="<?= $meta_description; ?>">
    <link rel="canonical" href="<?= BASE_URL.'/tv/'.$channelDetails['channel_slug']; ?>" />
    <meta name="revisit-after" content="1 days">
    <meta name="keywords" content="<?= $channelDetails['channel_name']; ?> , <?= $channelDetails['keywords']; ?>">
    <meta name="author" content="Abraham Omondi">
    <!-- facebook og tags  -->
    <meta property="og:title" content="<?= $meta_title; ?>" />
    <meta property="og:url" content="<?= BASE_URL.'/tv/'.$channelDetails['channel_slug']; ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_GB" />
    <meta property="og:description" content="<?= $meta_description; ?>" />
    <meta property="og:image" content="<?= $channelDetails['channel_poster']. "?w=1200&h=630&format=jpg"; ?>" />
    <!-- twiiter cards  -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="" />
    <meta name="twitter:creator" content="@allkenyalivetv" />
    <meta name="twitter:title" content="<?= $meta_title; ?>" />
    <meta name="twitter:description" content="<?= $meta_description; ?>" />
    <meta name="twitter:image" content="<?= $channelDetails['channel_poster']; ?>" />
    <!-- schema  -->
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "<?= $meta_title; ?>",
        "url": "<?= BASE_URL.'/tv/'.$channelDetails['channel_slug']; ?>",
        "datePublished": "<?= date('Y-m-d\TH:i:sP', strtotime($channelDetails['created'])); ?>",
        "dateModified": "<?= date('Y-m-d\TH:i:sP', strtotime($channelDetails['last_modified'])); ?>",
        "image": [
            "<?= $channelDetails['channel_icon']; ?>",
            "<?= $channelDetails['channel_poster']; ?>"
        ],
        "keywords": "<?= $channelDetails['keywords']; ?>",
        "description": "<?= $meta_description; ?>"
        }
    </script>
    <!--<link rel="preload stylesheet" href="<?= BASE_CSS_URL .'new-single-tv.css'; ?>" as="style" type="text/css">-->
    <link rel="preload" href="<?= BASE_CSS_URL .'new-single-tv.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL .'new-single-tv.css'; ?>"></noscript>
    <?php if ($channelDetails['status'] == "Live Now" && $channelDetails['availability'] == "Both") { ?>
        <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.16.1/video-js.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.16.1/video-js.min.css"></noscript>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/8.16.1/video.min.js" defer></script>
    <?php } ?>
    <link rel="preload" href="<?= BASE_CSS_URL .'tv-details.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL .'tv-details.css'; ?>"></noscript>
</head>
<body>
    <!--wordpress sidebar  -->
    <?php require_once __DIR__ . '/../../inc/sidebar.php'; ?>
    <main>
        <!--wordpress header  -->
        <?php  require_once __DIR__ . '/../../inc/header.php'; ?>
        <section>
            <div class="ke-breadcramp-lists">
                <nav>
                    <ul>
                        <li><a href="<?= BASE_URL; ?>/">Home</a></li>>
                        <li><a href="<?= BASE_URL; ?>/tv/">TV</a></li>>
                        <li><?= $channelDetails['channel_name']. ' Live'; ?></li>
                    </ul>
                </nav>
            </div>
        </section>
        <section class="ke-two-column-layout">
            <div class="ke-first-column">
                <div class="ke-video-player-container">
                    <div class="ke-video-container">
                        <iframe loading="lazy" src="<?= 'https://kenyalivetv.co.ke/embed/'.$channelDetails['channel_slug']. '?theme=dark'; ?>" 
                            allow="autoplay" title="Kenya Live TV Video Player" scrolling="no" frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                    <div class="ke-video-availability-container">
                        <div class="video-availability-message">
                            Citizen TV is off air or no longer livestreaming online due to scheduled maintenance.
                        </div>
                    </div>
                </div>
                <div class="ke-channel-small-info">
                    <div class="channel-icon">
                        <img src="<?= $channelDetails['channel_icon']; ?>" alt="<?= 'Watch '.$channelDetails['channel_name']. ' Live'; ?>">
                    </div>
                    <div class="channel-metadata">
                        <h1><?= 'Watch '.$channelDetails['channel_name']. ' Live'; ?></h1>
                        <div class="channel-stats">Kenya &middot; <?= number_format($channelDetails['viewed']) . ' views'; ?> &middot; 4 streams</div>
                        <div class="channel-stats">Last Updated : <time datetime="<?= date('Y-m-d\TH:i:sP', strtotime($channelDetails['last_modified'])); ?>"><?= timeAgo($channelDetails['last_modified']); ?></time></div>
                        <div class="channel-social-media-share-button">
                            <div class="icon-buttons"><a href="" title="Add To Favorites"><i class="far fa-heart active"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Go To Reviews"><i class="fa-regular fa-comment-dots"></i></a></div>
                            <div class="icon-buttons" id="embed-icon-button" title="Embed On Your Website"><i class="fa fa-code"></i></div>
                            <?php if (!empty($channelDetails['facebook'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['facebook']; ?>" title="Facebook"><i class="fab fa-facebook"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($channelDetails['twitter'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['twitter']; ?>" title="X ( Twitter )"><i class="fab fa-x-twitter"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($channelDetails['instagram'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['instagram']; ?>" title="Instagram"><i class="fab fa-instagram"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($channelDetails['youtube'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['youtube']; ?>" title="YouTube"><i class="fab fa-youtube"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($channelDetails['tiktok'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['tiktok']; ?>" title="TikTok"><i class="fab fa-tiktok"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($channelDetails['website'])) { ?>
                            <div class="icon-buttons"><a href="<?= $channelDetails['website']; ?>" title="Website"><i class="fa fa-globe"></i></a></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="ke-channel-description-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-circle-question"></i><?= 'About '.$channelDetails['channel_name']; ?></div>
                    <div class="ke-channel-description">
                        <?= nl2br($channelDetails['tv_description']); ?>
                    </div>
                </div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-tv"></i>Other TV Channels</div>
                    <div class="ke-channel-lists">
                        <?php 
                            if (!empty($otherChannels)) {
                                $otherChannels = array_slice($otherChannels, 0, 15);
                                foreach ($otherChannels as $except) {
                                    $channel_name = trim($except['channel_name']);
                                    $channel_icon = $except['channel_icon'];
                                    $channel_slug = $except['channel_slug'];
                                    $status = $except['status'];
                            ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= BASE_URL . "/tv/" . $channel_slug; ?>">
                                    <img src="<?= $channel_icon; ?>" loading="lazy" alt="<?= $channel_name; ?>">
                                </a>
                                <div class="channel-status <?= ($status == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $status; ?></div>
                            </div>
                            <div class="channel-name"><?= $channel_name; ?></div>
                        </div>
                        <?php } } ?>
                    </div>
                </div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-ticket"></i>Popular TV Shows</div>
                    <div class="ke-channel-lists">
                        <?php
                            // Check if the response contains valid data
                            $TopTVShows = array_slice($TopTVShows, 0, 8);
                            if (!empty($TopTVShows) && is_array($TopTVShows)) {
                                foreach ($TopTVShows as $show) {
                                // Extract data for better readability
                                // $showId = htmlspecialchars($show['show_id']);
                                $showName = htmlspecialchars($show['show_name']);
                                $showViews = htmlspecialchars($show['show_views']);
                                $showIcon = !empty($show['show_icon']) ? htmlspecialchars($show['show_icon']) : '';
                                $showPoster = !empty($show['show_poster']) ? htmlspecialchars($show['show_poster']) : '';
                                $channelSlug = htmlspecialchars($show['channel_slug']);
                                $channelIcon = htmlspecialchars($show['channel_icon']);
                                $backgroundImage = $showIcon ? $showIcon : ($showPoster ? $showPoster : $channelIcon);
                            ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= $channelSlug; ?>" style="background-image: url('<?= $backgroundImage; ?>');" title="<?= $showName.' ( '.$showViews.' weekly views )' ?>">
                                    <img src="<?= $channelIcon; ?>" alt="<?= $showName; ?>" loading="lazy">
                                </a>
                            </div>
                            <div class="channel-name"><a href="<?= $channelSlug; ?>"><?= $showName; ?></a></div>
                        </div>
                        <?php } } ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
            </div>
            <div class="ke-second-column">
                <div class="ads-container"></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-clock-rotate-left"></i> CONTINUE WATCHING</div>
                    <?php if (empty($recentlyWatchedTV)): ?>
                    <p>No recently watched tv found.</p>
                    <?php else: ?>
                    <div class="item-card-container">
                        <?php $recentlyWatchedTV = array_slice($recentlyWatchedTV, 0, 6);
                        foreach ($recentlyWatchedTV as $channel) : ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL . "/tv/" . $channel['channel_slug']; ?>">
                                <img src="<?= $channel['channel_icon']; ?>" loading="lazy" alt="<?= $channel['channel_name']; ?>">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL . "/tv/" . $channel['channel_slug']; ?></a>"><?= $channel['channel_name']; ?></a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> <?= formatNumber($channel['viewed']) ?></span>
                                    <span><i class="far fa-star"></i> <?= $channel['average_rating']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="ads-container"></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-heart"></i> FAVORITES</div>
                    <?php if (empty($recentlyWatchedTV)): ?>
                    <p>No favorite tv found.</p>
                    <?php else: ?>
                    <div class="item-card-container">
                        <?php foreach ($recentlyWatchedTV as $channel) : ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL . "/tv/" . $channel['channel_slug']; ?>">
                                <img src="<?= $channel['channel_icon']; ?>" loading="lazy" alt="<?= $channel['channel_name']; ?>">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL . "/tv/" . $channel['channel_slug']; ?></a>"><?= $channel['channel_name']; ?></a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> <?= formatNumber($channel['viewed']) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../../inc/footer.php'; ?>
    </main>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
    <!-- embed video player  -->
    <div id="embedVideoContainer">
        <div id="EmbedSupportMessage">
            <h2><?= 'Embed ' . str_replace("Live", "", $channelDetails['channel_name']) . ' On Your Website or Apps' ?></h2>
            <p>Copy the following code and paste it into your website or application:</p>
            <div class="embed-code-container">
                <code>
                    &lt;style&gt;
                        .ke-video-container{width:100%;position:relative;overflow:hidden;padding-top:56.25%;}
                        @media screen and (max-width:768px){.ke-video-container{padding-top:90%;}}
                        .ke-video-container iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:0;}
                    &lt;/style&gt;
                    &lt;div class="ke-video-container"&gt;
                        &lt;iframe src="<?= BASE_URL.'/embed/' . $channelDetails['channel_slug'].'?theme=dark'; ?>" 
                            allow="autoplay" title="Kenya Live TV Video Player" scrolling="no" frameborder="0" allowfullscreen&gt;
                        &lt;/iframe&gt;
                    &lt;/div&gt;
                </code>
                <button class="copy-btn" id="copyCode"><i class="fa fa-copy"></i></button>
            </div>
            <button id="hideEmbedContainer">Dismiss</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const embedVideoContainer = document.getElementById('embedVideoContainer');
            const embedIcon = document.getElementById('embed-icon-button');
            const hideEmbedButton = document.getElementById('hideEmbedContainer');
            const copyCodeButton = document.getElementById('copyCode');
            const embedCodeBlock = embedVideoContainer.querySelector('code');

            if (embedIcon) {
                embedIcon.addEventListener('click', function(event) {
                    event.preventDefault(); 
                    if (embedVideoContainer) {
                        embedVideoContainer.style.display = 'block'; 
                    }
                });
            }

            if (hideEmbedButton) {
                hideEmbedButton.addEventListener('click', function() {
                    if (embedVideoContainer) {
                        embedVideoContainer.style.display = 'none';
                    }
                });
            }

            if (copyCodeButton && embedCodeBlock) {
                copyCodeButton.addEventListener('click', function() {
                    let codeToCopy = embedCodeBlock.textContent;

                    if (!codeToCopy.includes('loading="lazy"')) {
                        codeToCopy = codeToCopy.replace(/<iframe\s(?![^>]*loading=["']lazy["'])/i, '<iframe loading="lazy" ');
                    }
                    if (!codeToCopy.includes('allow="autoplay"')) {
                        codeToCopy = codeToCopy.replace(/<iframe\s(?![^>]*allow=["']autoplay["'])/i, '<iframe allow="autoplay" ');
                    }

                    const tempTextArea = document.createElement('textarea');
                    tempTextArea.value = codeToCopy;
                    document.body.appendChild(tempTextArea);

                    tempTextArea.select();
                    navigator.clipboard.writeText(tempTextArea.value);

                    document.body.removeChild(tempTextArea);

                    copyCodeButton.innerHTML = '<i class="fa fa-check"></i> Copied!';
                    setTimeout(() => {
                        copyCodeButton.innerHTML = '<i class="fa fa-copy"></i>';
                    }, 2000);
                });
            }

            if (embedVideoContainer) {
                embedVideoContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>