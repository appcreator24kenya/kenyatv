<?php
    require_once __DIR__ . '/../../config/constants.php';

    // enable radio redirecting to new website temporarily
    if (REDIRECT_RADIOS) {
        // Define the base URL for the new site
        $newBaseUrl = 'https://kenyaliveradio.co.ke/';
        
        // Get the requested URI
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Define paths to exclude from redirection
        $excludedPaths = [
            '/radio/category/',
            '/radio/uploads/'
        ];
        
        // Check if the request URI starts with /radio/ and is not an excluded path
        $shouldRedirect = preg_match('#^/radio/(.*)#', $requestUri, $matches) &&
                        !array_filter($excludedPaths, fn($path) => strpos($requestUri, $path) === 0);
        
        if ($shouldRedirect) {
            // Extract the slug from the URI
            $slug = $matches[1];
        
            // Construct the new URL
            $newUrl = $newBaseUrl . $slug;
        
            // Perform the redirection
            header("Location: " . $newUrl, true, 302);
            exit();
        }
    }

    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 


    $radios = fetchRadioList() ?? [];
    $top_radios = fetchMostListenedRadios() ?? [];
    $most_rated = fetchRatedRadios() ?? [];
    $radioShows = fetchMostViewedShowsByType('radio') ?? [];
    $allRadioCategory = fetchRadioCategories() ?? [];

    $radioId = "";
    $radioDetails = [];
    
    // Find the radio details by slug
    $slug = $_GET['slug'];
    foreach ($radios as $radio) {
        if ($radio['radio_slug'] === $slug) {
            $radioId = $radio['id'];
            $radioDetails = $radio; 
            $radio_name = $radio['radio_name']. ' Live';
    
            break;
        }
    }
    
    // Check if $radioId is still empty after processing
    if (empty($radioId)) {
        // Redirect to 404 page or handle error as needed
        header("Location: /404");
        exit;
    }

    // Add 1 in Radio Views Values
    $cookie_name = "page_view_time_radio_" . $radioId;
    $cookie_expiration_time = time() + 64800; // 18 hours
    
    if (in_array($userLoginType, ['email', 'math'])) {
        if (!isset($_COOKIE[$cookie_name])) {
            // Set a new cookie with a unique name for this radio.
            setcookie($cookie_name, time(), [
                'expires' => $cookie_expiration_time,
                'path' => '/', // This makes the cookie available across the entire domain.
                'secure' => true, // Recommended for HTTPS sites.
                'httponly' => true, // Prevents access via JavaScript.
                'samesite' => 'Lax'
            ]);
            
            // Increment the view count for this specific radio in your database.
            storeRadioViewsCount($radioId); 
            
        }
    }

    // Fetch all radio schedules
    $GetRadioSchedule = fetchAllRadioSchedule();
    $GetRadioSchedule = $GetRadioSchedule[$radioId]['schedule'] ?? [];
    $GetRadioSchedule = is_array($GetRadioSchedule) ? $GetRadioSchedule : [];

    // Get the current day of the week
    $currentDay = date("l"); // e.g., Monday, Tuesday, etc.
    
    // Filter schedules for the current day
    $todaySchedule = array_filter($GetRadioSchedule, function ($schedule) use ($currentDay) {
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

                    // error_log("Views updated for show: {$schedule['show_name']} | Show ID: {$schedule['show_id']} | Start Time: " . date("h:i A", strtotime(convertFromUTC($schedule['start_time']))) . " | End Time: " . date("h:i A", strtotime(convertFromUTC($schedule['end_time']))) . " | Radio Name: {$radioDetails['radio_name']}");
                }
            }
            $activeShowFound = true;
        }

        $schedule['isActive'] = $isActive;
        $schedule['progressPercentage'] = round($progressPercentage);
        $processedSchedule[] = $schedule;
    }
    

    // Function to update the recently listened radios array in the cookie
    function updateRecentlyListened($radioId) {
        // Get the current recently listened array from the cookie
        $recentlyListened = isset($_COOKIE['recently_listened']) ? json_decode($_COOKIE['recently_listened'], true) : [];
    
        // Remove the radioId if it already exists in the array
        if (($key = array_search($radioId, $recentlyListened)) !== false) {
            unset($recentlyListened[$key]);
        }
    
        // Add the radioId to the beginning of the array
        array_unshift($recentlyListened, $radioId);
    
        // Update the cookie with the new array
        setcookie('recently_listened', json_encode($recentlyListened), time() + (86400 * 30), "/"); // 30 days expiry
    }
    
    // Update the recently listened array whenever this radio is played
    updateRecentlyListened($radioId);


    
    
    // Filter out the current radio from the list of radios
    $otherRadios = array_filter($radios, function ($radio) use ($radioId) {
        return $radio['id'] !== $radioId;
    });
    if (is_null($otherRadios)) {
        $otherRadios = [];
    }
    
    // Log if no other radios are found
    if (empty($otherRadios)) {
        error_log("Notice: No other Radios found to display for Radio ID $radioId");
    }
    
    // Check if the form is submitted
    if (isset($_POST['rating']) && isset($_POST['user_name']) && isset($_POST['user_review'])) {
        // Form is submitted, process the rating
        $radioId = $radioDetails['id'];
        $rating = $_POST['rating'];
        $userName = $_POST['user_name'];
        $userReview = $_POST['user_review'];
        $user_ip = getUserIP();
    
        if ($radioId && $rating && $userName && $userReview && $user_ip) {
            storeRadioReviewsData($radioId, $rating, $userName,$userReview, $user_ip);
    
            // Set a cookie to prevent the user from rating again
            $cookieName = 'radio_rated_' . $radioId;
            setcookie($cookieName, true, time() + 10 * 365 * 24 * 60 * 60, '/');
    
            // Redirect to the same page to avoid form resubmission on refresh
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    // fetch favorite radio stations
    $favoritesCookie = isset($_COOKIE['favorite_radios']) ? $_COOKIE['favorite_radios'] : null;
    $favoriteRadioIds = $favoritesCookie ? json_decode($favoritesCookie, true) : [];
    $favoriteRadios = [];

    // Filter radios to only include favorites
    if (!empty($favoriteRadioIds)) {
        foreach ($radios as $radio) {
            if (in_array($radio['id'], $favoriteRadioIds)) {
                $favoriteRadios[] = $radio;
            }
        }
    }

    // filter similar radios
    $categoryId = $radioDetails['category_id'] ?? null;

    $filteredSimilarRadios = [];
    if ($categoryId) {
        $filteredSimilarRadios = array_filter($radios, function($radio) use ($categoryId, $radioDetails) {
            // Match same category, exclude the current radio itself
            return $radio['category_id'] == $categoryId && $radio['id'] != $radioDetails['id'];
        });
    }


    // Prepare recently listened radios from cookies
    $recentlyListenedRadios = [];
    if (isset($_COOKIE['recently_listened'])) {
        $recentlyListened = json_decode($_COOKIE['recently_listened'], true);
        if (!is_null($recentlyListened)) {
            foreach ($recentlyListened as $radioId) {
                foreach ($radios as $radio) {
                    if ($radio['id'] == $radioId) {
                        $recentlyListenedRadios[] = $radio;
                        break;
                    }
                }
            }
            usort($recentlyListenedRadios, function($a, $b) use ($recentlyListened) {
                return array_search($a['id'], $recentlyListened) - array_search($b['id'], $recentlyListened);
            });
        }
    }
    
    $current_url = BASE_URL. '/radio/'. $radioDetails['radio_slug'];
    
    // echo phone numbers in 3 pairs
    function displayDivIfContains254($link) {
        $formattedNumbers = ''; // Initialize the variable outside the function
    
        // Check if the link contains the number 254
        if (strpos($link, '254') !== false) {
            // Extract the numbers following 254 in the link using regular expression
            preg_match('/254(\d{3})(\d{3})(\d{3})/', $link, $matches);
    
            // If matches are found, display the div
            if (!empty($matches)) {
                $formattedNumbers = implode(' ', array_slice($matches, 1));
                //echo '<div>' . '+254 ' . $formattedNumbers . '</div>';
            }
        }
    
        return $formattedNumbers; // Return the formatted numbers
    }
    // Call the function with the example link
    $formattedNumbers = displayDivIfContains254($radioDetails['whatsapp']);
    



    $freq = !empty($radioDetails['frequency']) ? ' (' . trim($radioDetails['frequency']) . ')' : '';
    $meta_title = 'Listen to ' . $radioDetails['radio_name'] . $freq . ' Live | Kenya Live TV';
    $meta_description = 'Stream ' . $radioDetails['radio_name'] . $freq . ' live online. Tune in for live news, talk shows, and top Kenyan music anytime on Kenya Live TV.';
    // $meta_title = $radioDetails['radio_name'] . $freq . ' Live - Stream Online on Kenya Live TV';
    // $meta_description = "Listen to " . $radioDetails['radio_name'] . $freq . " live online on Kenya Live TV. Catch live news, top music, and broadcast shows anytime, anywhere.";
    // $meta_title = $radioDetails['radio_name']. ' Live - Stream Online on Kenya Live TV';
    // $meta_description = "Listen to " .$radioDetails['radio_name']. " live streaming online on Kenya Live TV. Enjoy nonstop music, news, and entertainment from Kenya's top radio station, " .$radioDetails['radio_name']. " . Stay tuned anytime, anywhere.";

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
    <meta name="keywords" content="<?= $radioDetails['radio_name']. ' Live'; ?> , <?= $radioDetails['keywords']; ?>">
    <!-- facebook og tags  -->
    <meta property="og:title" content="<?= $meta_title; ?>" />
    <meta property="og:url" content="<?= BASE_URL. '/radio/'. $radioDetails['radio_slug']; ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_GB" />
    <meta property="og:description" content="<?= $meta_description; ?>" />
    <meta property="og:image" content="<?= $radioDetails['radio_poster']; ?>" />
    <!-- twiiter cards  -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="" />
    <meta name="twitter:creator" content="@allkenyalivetv" />
    <meta name="twitter:title" content="<?= $meta_title; ?>" />
    <meta name="twitter:description" content="<?= $meta_description; ?>" />
    <meta name="twitter:image" content="<?= $radioDetails['radio_poster']; ?>" />
    <!--<link rel="preload stylesheet" href="<?= BASE_CSS_URL .'new-single-radio-details.css' ?>" as="style" type="text/css">-->
    <link rel="preload" href="<?= BASE_CSS_URL . 'new-single-radio-details.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL . 'new-single-radio-details.css'; ?>"></noscript>
    <link rel="preload" fetchpriority="high" as="image" href="<?= $radioDetails['radio_icon']; ?>?w=126"
      imagesrcset="<?= $radioDetails['radio_icon']; ?>?w=64 64w,
                   <?= $radioDetails['radio_icon']; ?>?w=96 96w,
                   <?= $radioDetails['radio_icon']; ?>?w=126 126w,
                   <?= $radioDetails['radio_icon']; ?>?w=256 256w"
      imagesizes="(max-width: 400px) 96px,
                  (max-width: 768px) 126px,
                  126px">
    <!--video player -->
    <?php if ($radioDetails['show_video'] == 1) { ?>
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/video.js@8.23.6/dist/video-js.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/video.js@8.23.6/dist/video-js.min.css"></noscript>
        <script src="https://cdn.jsdelivr.net/npm/video.js@8.23.6/dist/video.min.js" defer></script>
    <?php } ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "RadioStation",
            "name": "<?= $radioDetails['radio_name']. ' Live'; ?>",
            "url": "<?= BASE_URL. '/radio/'. $radioDetails['radio_slug']; ?>",
            "image": [
                "<?= $radioDetails['radio_icon']; ?>",
                "<?= $radioDetails['radio_poster']; ?>"
            ],
            "datePublished": "<?= date('Y-m-d\TH:i:sP', strtotime($radioDetails['created'])); ?>",
            "dateModified": "<?= date('Y-m-d\TH:i:sP', strtotime($radioDetails['last_modified'])); ?>",
            "description": "<?= $meta_description; ?>",
            "slogan": "<?= $radioDetails['slogan']; ?>",
            "telephone": "<?= $radioDetails['phone']; ?>",
            "email": "<?= $radioDetails['email']; ?>",
            "address": "<?= $radioDetails['address']; ?>",
            "sameAs": [
                "<?= $radioDetails['facebook']; ?>",
                "<?= $radioDetails['twitter']; ?>", 
                "<?= $radioDetails['instagram']; ?>",
                "<?= $radioDetails['youtube']; ?>"
            ],
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "<?= $radioDetails['average_rating']; ?>",
                "bestRating": "5",
                "ratingCount": "<?= $radioDetails['reviews']; ?>"
            }
        }
    </script>
    <link rel="canonical" href="<?= BASE_URL. '/radio/'. $radioDetails['radio_slug']; ?>" />
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
                        <li><a href="<?= BASE_URL; ?>/radio/">Radio</a></li>>
                        <li><?= $radioDetails['radio_name']. ' Live'; ?></li>
                    </ul>
                </nav>
            </div>
        </section>
        <section class="ke-two-column-layout">
            <div class="ke-first-column">
                <div class="ke-channel-small-info">
                    <div class="channel-icon">
                        <img src="<?= $radioDetails['radio_icon']; ?>" alt="<?= $radioDetails['radio_name']. ' Live'; ?>">
                    </div>
                    <div class="channel-metadata">
                        <div class="channel-row">
                            <div class="first-row">
                                <h1><?= $radioDetails['radio_name']; ?></h1>
                                <div class="channel-stats"><?= ($radioDetails['frequency']) ? 'Frequency : <strong>' . $radioDetails['frequency']. '</strong>FM' : 'Online Radio'; ?></div>
                                <div class="channel-stats"><?= "Listeners : ". number_format($radioDetails['viewed']); ?> views</div>
                                <div class="channel-stats">Last Updated :<time datetime="<?= date('Y-m-d\TH:i:sP', strtotime($radioDetails['last_modified'])); ?>"><?= timeAgo($radioDetails['last_modified']); ?></time></div>
                                <div class="single-radio-status live">Live</div>
                            </div>
                            <div class="second-row">
                                <div class="ke-audio-player">
                                    <div class="icon-buttons" title="Play / Pause"><i class="far fa-play-circle" id="play-pause"></i></div>
                                    <div class="icon-buttons" title="Mute / Unmute" id="mute-unmute"><i class="fa fa-volume-up"></i></div>
                                    <div class="icon-buttons" title="Reload Stream" id="reload-stream"><i class="fa fa-refresh"></i></div>
                                </div>
                                <audio id="radio-player" controls loop autoplay preload="none" hidden></audio>
                            </div>
                        </div>
                        <div class="channel-social-media-share-button">
                            <div class="icon-buttons favorite-button"><a href="#" title="Add / Remove From Favorites" data-radio-id="<?= htmlspecialchars($radioId) ?>"><i class="far fa-heart"></i></a></div>
                            <?php if (!empty($radioDetails['facebook'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['facebook']; ?>" title="Facebook"><i class="fab fa-facebook"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['whatsapp'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['whatsapp']; ?>" title="Whatsapp"><i class="fab fa-whatsapp"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['twitter'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['twitter']; ?>" title="X ( Twitter )"><i class="fab fa-x-twitter"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['instagram'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['instagram']; ?>" title="Instagram"><i class="fab fa-instagram"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['youtube'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['youtube']; ?>" title="YouTube"><i class="fab fa-youtube"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['tiktok'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['tiktok']; ?>" title="TikTok"><i class="fab fa-tiktok"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['email'])) { ?>
                            <div class="icon-buttons"><a href="<?= 'mailto:'.$radioDetails['email']; ?>" title="Email"><i class="fa fa-envelope"></i></a></div>
                            <?php } ?>
                            <?php if (!empty($radioDetails['website'])) { ?>
                            <div class="icon-buttons"><a href="<?= $radioDetails['website']; ?>" title="Website"><i class="fa fa-globe"></i></a></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="ke-channel-description-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-circle-question"></i><?= 'About '.$radioDetails['radio_name']; ?></div>
                    <div class="ke-channel-description">
                        <div class="channel-stats"><?= 'Slogan : '.$radioDetails['slogan']; ?></div><br>
                        <p><?= nl2br(htmlspecialchars($radioDetails['radio_description'])); ?></p>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
                <?php if ($radioDetails['show_video'] == 1) { ?>
                <div class="ke-video-player-container">
                    <div class="ke-video-container">
                        <?= $radioDetails['video_code']; ?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($filteredSimilarRadios) { ?>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-angle-right"></i>Similar Radio Stations</div>
                    <div class="ke-channel-lists">
                        <?php $limitedRadios = array_slice($filteredSimilarRadios, 0, 8);
                        foreach ($limitedRadios as $radio): ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= BASE_URL . "/radio/" . $radio['radio_slug']; ?>">
                                    <img src="<?= $radio['radio_icon']; ?>" loading="lazy"
                            decoding="async" alt="<?= $radio['radio_name']; ?>">
                                </a>
                                <div class="channel-status <?= ($radio['status'] == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $radio['status']; ?></div>
                            </div>
                            <div class="channel-name"><?= $radio['radio_name']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php } ?>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-radio"></i>Other Radio Stations</div>
                    <div class="ke-channel-lists">
                        <?php 
                            if($otherRadios){
                            $otherRadios = array_slice($otherRadios, 0, 8);
                            foreach($otherRadios as $except){
                            $radio_name = $except['radio_name']. " Live";
                            $radio_slug = $except['radio_slug'];
                            $radio_icon = $except['radio_icon'];
                            $viewed = $except['viewed'];
                            $status = $except['status'];
                            $showviews = $except['showviews'];
                            $frequency = $except['frequency'];
                        ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= BASE_URL . "/radio/" . $radio_slug; ?>">
                                    <img src="<?= $radio_icon; ?>" loading="lazy"
                            decoding="async" alt="<?= $radio_name; ?>">
                                </a>
                                <div class="channel-status <?= ($status == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $radio['status']; ?></div>
                            </div>
                            <div class="channel-name"><?= $radio_name; ?></div>
                        </div>
                        <?php } } ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-ticket"></i>Popular Shows</div>
                    <div class="ke-channel-lists">
                        <?php
                        // Check if the response contains valid data
                        $radioShows = array_slice($radioShows, 0, 8);
                        if (!empty($radioShows) && is_array($radioShows)) {
                            foreach ($radioShows as $show) {
                                // Extract data for better readability
                                // $showId = htmlspecialchars($show['show_id']);
                                $showName = htmlspecialchars($show['show_name']);
                                $showViews = htmlspecialchars($show['show_views']);
                                $showIcon = !empty($show['show_icon']) ? htmlspecialchars($show['show_icon']) : '';
                                $showPoster = !empty($show['show_poster']) ? htmlspecialchars($show['show_poster']) : '';
                                $radioSlug = htmlspecialchars($show['radio_slug']);
                                $radioIcon = htmlspecialchars($show['radio_icon']);
                                $backgroundImage = $showIcon ? $showIcon : ($showPoster ? $showPoster : $radioIcon);
                                ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= $radioSlug; ?>" style="background-image: url('<?= $backgroundImage; ?>');" title="<?= $showName.' ( '.$showViews.' weekly views )' ?>">
                                    <img src="<?= $radioIcon; ?>" alt="<?= $showName; ?>" loading="lazy">
                                </a>
                            </div>
                            <div class="channel-name"><a href="<?= $radioSlug; ?>"><?= $showName; ?></a></div>
                        </div>
                        <?php } }?>
                    </div>
                </div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-arrow-trend-up"></i>Popular Radio Stations</div>
                    <div class="ke-channel-lists">
                        <?php 
                            $limitedTopRadios = array_slice($top_radios, 0, 8);
                            foreach ($limitedTopRadios as $key => $row):
                                $id = $row['id'];
                                $radio_name = trim($row['radio_name']);
                                $radio_slug = $row['radio_slug'];
                                $status = $row['status'];
                                $frequency = $row['frequency'];
                                $radio_icon = $row['radio_icon'];
                                $viewed = $row['viewed'];
                                $average_rating = $row['average_rating'];
                        ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= BASE_URL . "/radio/" . $radio_slug; ?>">
                                    <img src="<?= $radio_icon; ?>" loading="lazy" alt="<?= $radio_name; ?>">
                                </a>
                                <div class="channel-status <?= ($status == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $status; ?></div>
                            </div>
                            <div class="channel-name"><?= $radio_name; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
            </div>
            <div class="ke-second-column">
                <div class="ads-container"><?= getAdsenseAd('square', 'auto'); ?></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-clock-rotate-left"></i> CONTINUE LISTENING</div>
                    <div class="item-card-container">
                    <?php if (empty($recentlyListenedRadios)): ?>
                        <p>No recently listened radios found.</p>
                    <?php else: ?>
                    <?php foreach ($recentlyListenedRadios as $row) : ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL ."/radio/" . $row['radio_slug']; ?>">
                                <img src="<?= $row['radio_icon']; ?>" loading="lazy" alt="<?= $row['radio_name']; ?>">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL ."/radio/" . $row['radio_slug']; ?>"><?= $row['radio_name']; ?></a></strong>
                                <div class="row-items">
                                    <span>
                                        <?php if (!empty($row['frequency'])) {
                                        echo '<i class="fa fa-tower-cell"></i> ' . $row['frequency'] . ' FM';
                                        } else {
                                        echo 'Online Radio';
                                        }
                                        ?>
                                    </span>
                                    <span><?= '<i class="fa fa-headphones"></i> '.formatNumber($row['viewed']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('square', 'auto'); ?></div>
                <?php if ($favoriteRadios) { ?>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-heart"></i> FAVORITES</div>
                    <div class="item-card-container">
                        <?php foreach ($favoriteRadios as $radio) { 
                            $radio_name = $radio['radio_name'] . " Live";
                            $radio_slug = $radio['radio_slug'];
                            $viewed = $radio['viewed'];
                            $showviews = $radio['showviews'];
                            $frequency = !empty($radio['frequency']) ? 'Frequency: '.$radio['frequency'] . ' FM' : 'Online Radio';
                        ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL . '/radio/' . trim($radio_slug); ?>">
                                <img src="<?= $radio['radio_icon']; ?>" alt="<?= htmlspecialchars($radio_name); ?>" loading="lazy">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL . '/radio/' . trim($radio_slug); ?>"><?= htmlspecialchars($radio_name); ?></a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> <?= number_format($viewed) . ' Listeners'; ?> </span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../../inc/footer.php'; ?>
    </main>
    <script>
        const audio = document.getElementById('radio-player');
        const playPauseButton = document.getElementById('play-pause');
        const muteUnmuteButton = document.getElementById('mute-unmute');
        const reloadStreamButton = document.getElementById('reload-stream');
        const radioStatus = document.querySelector('.single-radio-status');
        // const _0xc1ba43=_0x2a3b;function _0x2a3b(_0x4b0b5b,_0x5599bb){const _0x12f652=_0x12f6();return _0x2a3b=function(_0x2a3be0,_0x459127){_0x2a3be0=_0x2a3be0-0x1ee;let _0x28fb61=_0x12f652[_0x2a3be0];return _0x28fb61;},_0x2a3b(_0x4b0b5b,_0x5599bb);}(function(_0x394453,_0x557cc6){const _0x364cfa=_0x2a3b,_0x558ea3=_0x394453();while(!![]){try{const _0xce702f=-parseInt(_0x364cfa(0x1fb))/0x1+-parseInt(_0x364cfa(0x1f6))/0x2*(-parseInt(_0x364cfa(0x1f5))/0x3)+-parseInt(_0x364cfa(0x1f4))/0x4*(parseInt(_0x364cfa(0x1f2))/0x5)+parseInt(_0x364cfa(0x1f0))/0x6*(-parseInt(_0x364cfa(0x1f9))/0x7)+parseInt(_0x364cfa(0x1ee))/0x8*(parseInt(_0x364cfa(0x1f8))/0x9)+-parseInt(_0x364cfa(0x1fd))/0xa*(-parseInt(_0x364cfa(0x1f7))/0xb)+parseInt(_0x364cfa(0x1ef))/0xc*(parseInt(_0x364cfa(0x1fa))/0xd);if(_0xce702f===_0x557cc6)break;else _0x558ea3['push'](_0x558ea3['shift']());}catch(_0x3a7565){_0x558ea3['push'](_0x558ea3['shift']());}}}(_0x12f6,0x5abee));const sources=[_0xc1ba43(0x1fc),_0xc1ba43(0x1f3),_0xc1ba43(0x1f1)];function _0x12f6(){const _0x61bb3b=['7737886zAnJgP','225280LRtaPH','<?= $radioDetails['primary_stream_url']; ?>','19410gstAFd','144BgIqHf','12MVpXbL','113700CuSIXU','<?= $radioDetails['alternate_stream_url']; ?>','578145IJABbG','<?= $radioDetails['secondary_stream_url']; ?>','20iqSVUi','3LNcdzN','1201436AFMBzx','1331BBjdAB','232209eTYCJT','266TohhRA'];_0x12f6=function(){return _0x61bb3b;};return _0x12f6();}
        const sources = ['<?= $radioDetails['primary_stream_url']; ?>', '<?= $radioDetails['secondary_stream_url']; ?>', '<?= $radioDetails['alternate_stream_url']; ?>'];

        let currentSourceIndex = 0;
        let isLive = false;

        // Function to update the audio type based on the file extension of the URL
        function updateAudioSourceType(url) {
            const sourceElement = document.createElement('source');

            sourceElement.src = url;
            if (url.includes('.mp3')) {
                sourceElement.setAttribute('type', 'audio/mp3');
            } else {
                sourceElement.setAttribute('type', 'audio/mpeg');
            }

            audio.innerHTML = ''; 

            audio.appendChild(sourceElement);
        }

        // Check if the stream exists
        if (sources[currentSourceIndex]) {
            isLive = true;
            radioStatus.textContent = "Loading...";
            radioStatus.classList.add('loading');

            updateAudioSourceType(sources[currentSourceIndex]);

            // Wait for the stream to be able to play
            audio.addEventListener('canplay', () => {
                audio.play().then(() => {
                    radioStatus.textContent = "Live";
                    radioStatus.classList.remove('loading');
                    radioStatus.classList.add('live');
                }).catch(() => {
                    // radioStatus.textContent = "Error playing stream";
                    radioStatus.textContent = "Click play button";
                    radioStatus.setAttribute('title', 'Click reload button to refresh stream');
                    radioStatus.classList.add('error');
                    radioStatus.classList.remove('loading');
                });
            });

            // Handle errors if the stream fails to load
            audio.addEventListener('error', () => {
                radioStatus.textContent = "Offline";
                radioStatus.setAttribute('title', 'This Radio might be offline at the moment');
                radioStatus.classList.add('error');
                radioStatus.classList.remove('loading');
            });

            audio.load(); 
        } else {
            radioStatus.textContent = "No stream available";
            radioStatus.classList.add('error');
        }

        // Function to update the play/pause icon
        function updatePlayPauseIcon() {
            if (audio.paused) {
                playPauseButton.classList.replace('fa-circle-pause', 'fa-circle-play');
            } else {
                playPauseButton.classList.replace('fa-circle-play', 'fa-circle-pause');
            }
        }

        // Function to play audio
        function playAudio() {
            audio.play().then(() => {
                isLive = true;
                radioStatus.textContent = "Live";
                radioStatus.classList.add('live');
                radioStatus.classList.remove('paused', 'error', 'mute');
            }).catch(() => {
                radioStatus.textContent = "Offline";
                radioStatus.classList.add('error');
                radioStatus.classList.remove('live', 'paused');
            });
            updatePlayPauseIcon();
        }

        // Function to pause audio
        function pauseAudio() {
            audio.pause();
            isLive = false;
            radioStatus.textContent = "Paused";
            radioStatus.classList.add('paused');
            radioStatus.classList.remove('live', 'error', 'mute');
            updatePlayPauseIcon();
        }

        // Function to reload the stream
        function reloadStream() {
            reloadStreamButton.classList.add('fa-spin');
            currentSourceIndex = (currentSourceIndex + 1) % sources.length;
            updateAudioSourceType(sources[currentSourceIndex]);  
            audio.load(); 

            radioStatus.textContent = "Loading...";
            radioStatus.classList.add('loading');
            radioStatus.classList.remove('live', 'paused', 'error', 'mute');

            audio.play()
                .then(() => {
                    reloadStreamButton.classList.remove('fa-spin');
                    radioStatus.textContent = "Live";
                    radioStatus.classList.add('live');
                    radioStatus.classList.remove('loading', 'paused', 'error', 'mute');
                })
                .catch(() => {
                    reloadStreamButton.classList.remove('fa-spin');
                    radioStatus.textContent = "Offline";
                    radioStatus.classList.add('error');
                    radioStatus.classList.remove('loading', 'live', 'paused', 'mute');
                });
        }

        playPauseButton.addEventListener('click', () => {
            if (audio.paused) {
                playAudio();
            } else {
                pauseAudio();
            }
        });

        muteUnmuteButton.addEventListener('click', () => {
            audio.muted = !audio.muted;
            if (audio.muted) {
                muteUnmuteButton.classList.replace('fa-volume-up', 'fa-volume-mute');
                radioStatus.textContent = "Audio Muted";
                radioStatus.classList.add('mute');
                radioStatus.classList.remove('live', 'paused', 'error');
            } else {
                muteUnmuteButton.classList.replace('fa-volume-mute', 'fa-volume-up');
                radioStatus.textContent = "Live";
                radioStatus.classList.add('live');
                radioStatus.classList.remove('mute', 'paused', 'error');
            }
        });

        reloadStreamButton.addEventListener('click', reloadStream);

        audio.addEventListener('playing', updatePlayPauseIcon);
        audio.addEventListener('pause', updatePlayPauseIcon);

        audio.addEventListener('error', () => {
            isLive = false;
            radioStatus.textContent = "Offline";
            radioStatus.classList.add('error');
            radioStatus.classList.remove('live', 'paused', 'loading', 'mute');
        });

        updatePlayPauseIcon();
    </script>
    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};path=/;expires=${date.toUTCString()}`;
        }
        document.querySelector('.favorite-button').addEventListener('click', function () {
            const radioId = this.getAttribute('data-radio-id');
            let favoriteRadios = getCookie('favorite_radios');

            favoriteRadios = favoriteRadios ? JSON.parse(favoriteRadios) : [];

            if (favoriteRadios.includes(radioId)) {

                favoriteRadios = favoriteRadios.filter(id => id !== radioId);
                this.querySelector('i').classList.remove('fa-solid');
                this.querySelector('i').classList.add('fa-regular');
            } else {

                favoriteRadios.push(radioId);
                this.querySelector('i').classList.remove('fa-regular');
                this.querySelector('i').classList.add('fa-solid');
            }

            setCookie('favorite_radios', JSON.stringify(favoriteRadios), 30);
        });

        const radioId = document.querySelector('.favorite-button').getAttribute('data-radio-id');
        const favoriteRadios = getCookie('favorite_radios') ? JSON.parse(getCookie('favorite_radios')) : [];
        if (radioId && favoriteRadios) {
            if (favoriteRadios.includes(radioId)) {
                document.querySelector('.favorite-button i').classList.remove('fa-regular');
                document.querySelector('.favorite-button i').classList.add('fa-solid');
            }
        }
    </script>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>