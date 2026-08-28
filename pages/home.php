<?php
    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 

    // Fetch all TV and radio schedules
    $tvSchedules = fetchAllTVSchedule() ?? [];
    $radioSchedules = fetchAllRadioSchedule() ?? [];


    $channels = fetchTVList() ?? [];
    $top_channels =  fetchMostWatchedTV() ?? [];
    $most_rated_tv = fetchRatedTV() ?? [];

    $allChannels = array();
    if($channels){
        foreach ($channels as $index => $row){
            $allChannels[$index]['id'] = $row['id'];
            $allChannels[$index]['channel_name'] = $row['channel_name'];
            $allChannels[$index]['channel_slug'] = $row['channel_slug'];
            $allChannels[$index]['channel_icon'] = $row['channel_icon'];
            $allChannels[$index]['tv_embed_code'] = $row['tv_embed_code'];
            $allChannels[$index]['tv_description'] = $row['tv_description'];
            $allChannels[$index]['status'] = $row['status'];
            $allChannels[$index]['channel_poster'] = $row['channel_poster'];
            $allChannels[$index]['viewed'] = $row['viewed'];
            $allChannels[$index]['created'] = $row['created'];
            
            $index++;
        }
    }

    //echo '<pre>';print_r($allChannels);exit;
    
    $radios = fetchRadioList() ?? [];
    $top_radios = fetchMostListenedRadios() ?? [];
    $most_rated = fetchRatedRadios() ?? [];
    $allRadioCategory = fetchRadioCategories() ?? [];
    
    $allRadios = array();
    if($radios){
        foreach ($radios as $index => $row){
            $allRadios[$index]['id'] = $row['id'];
            $allRadios[$index]['radio_name'] = $row['radio_name'];
            $allRadios[$index]['radio_slug'] = $row['radio_slug'];
            $allRadios[$index]['status'] = $row['status'];
            $allRadios[$index]['showviews'] = $row['showviews'];
            $allRadios[$index]['average_rating'] = $row['average_rating'];
            $allRadios[$index]['radio_description'] = $row['radio_description'];
            $allRadios[$index]['frequency'] = $row['frequency'];
            $allRadios[$index]['radio_poster'] = $row['radio_poster'];
            $allRadios[$index]['radio_icon'] = $row['radio_icon'];
            $allRadios[$index]['viewed'] = $row['viewed'];
            
            $index++;
        }
    }
    
    
    // get recently watched tv
    $recentlyWatchedTV = [];
    if (isset($_COOKIE['recently_listened_tv'])) {
        $recentlyWatchedTV = json_decode($_COOKIE['recently_listened_tv'], true);
        if (is_null($recentlyWatchedTV)) {
            $recentlyWatchedTV = [];
        }
    }

    // get recently listened radios
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

    // seo meta tags
    // $meta_title = "Kenya Live TV | Kenya TV Channels | Kenya TV Stations";
    // $meta_description = "Explore Kenya Live TV! Dive into a world of entertainment, news and knowledge with 150 Kenya TV channels, 217 Kenya radio stations, 40 news sources and 150 Soap updates. Your one-stop destination for diverse content!";
    // $meta_title = "Watch Live TV Channels from Kenya | Kenya Live TV";
    // $meta_description = "Stream Kenya's best live TV channels online with Kenya Live TV. Watch your favorite Kenyan stations live, anytime, anywhere.";
    // $meta_title = "Explore Live TV Channels in Kenya | Kenya Live TV";
    // $meta_description = "Discover a variety of live TV channels from Kenya. Watch top stations streaming live online with Kenya Live TV.";
    // $meta_title = "Watch Live TV Channels in Kenya | Kenya Live TV | TV Schedules, Shows & Radio";
    $meta_title = "Watch Live TV Channels & Radios in Kenya | Kenya Live TV";
    $meta_description = "Stream live TV channels in Kenya on Kenya Live TV. Explore TV schedules, watch popular shows, and listen to live radio stations, all in one place.";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once __DIR__ . '/../inc/head-scripts.php'; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $meta_title; ?>">
    <meta name="twitter:site" content="@allkenyalivetv">
    <meta name="twitter:description" content="<?= $meta_description; ?>">
    <meta name="twitter:image" content="https://kenyalivetv.co.ke/screenshot.jpg">
    <meta name="twitter:image:alt" content="<?= $meta_title; ?>">
    <meta property="og:url" content="<?= BASE_URL .'/'; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= $meta_title; ?>" />
    <meta property="og:description" content="<?= $meta_description; ?>" />
    <meta property="og:image" content="https://kenyalivetv.co.ke/screenshot.jpg" />
    <meta name="description" content="<?= $meta_description; ?>">
    <meta name="revisit-after" content="1 days">
    <meta name="keywords" content=" Kenya live tv , kbc world cup live , Kenya TV Channels , Kenya TV stations, thapki ramogi tv , all kenya tv , all kenya tv stations app , kenya tv stations , watch kewnya tv channels , kenya tv , online kenya tv">
    <meta name="author" content="Abraham Omondi">
    <link rel="preload" href="<?= BASE_CSS_URL .'home.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL .'home.css'; ?>"></noscript>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Website",
            "url": "<?= BASE_URL .'/'; ?>",
            "name": "Kenya Live TV",
            "headline": "<?= $meta_title; ?>",
            "image": [
                "<?= BASE_IMAGES_URL . 'tv-logo.png' ?>",
                "https://kenyalivetv.co.ke/screenshot.jpg"
            ],
            "description": "<?= $meta_description; ?>",
            "sameAs": [
                "https://www.facebook.com/allkenyalivetv",
                "https://www.twitter.com/allkenyalivetv",
                "https://www.instagram.com/allkenyalivetv"
            ],
            "publisher": {
                "@type": "Organization",
                "name": "Kenya Live TV",
                "logo": {
                "@type": "ImageObject",
                "url": "<?= BASE_IMAGES_URL . 'tv-logo.png' ?>"
                }
            }
        }
    </script>
    <link rel="canonical" href="<?= BASE_URL .'/'; ?>">
    <title><?= $meta_title; ?></title>
</head>
<body>
    <!--wordpress sidebar  -->
    <?php require_once __DIR__ . '/../inc/sidebar.php'; ?>
    <main>
        <!--wordpress header  -->
        <?php  require_once __DIR__ . '/../inc/header.php'; ?>
        <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
        <section class="ke-two-column-layout">
            <div class="ke-first-column">
                <?php if (!empty($recentlyWatchedTV) && !empty($allChannels)) { ?>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-clock-rotate-left"></i>Continue Watching</div>
                    <div class="ke-channel-lists">
                        <?php
                            // Limit recently watched TV IDs to 3
                            $limitedRecentlyWatchedTV = array_slice($recentlyWatchedTV, 0, 5);

                            // Loop through the recently watched TV IDs
                            foreach ($limitedRecentlyWatchedTV as $watchedId) {
                            // Find the matching channel in $allChannels
                            foreach ($allChannels as $channel) {
                                if ($channel['id'] == $watchedId) {
                        ?>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <a href="<?= BASE_URL . "/tv/" . $channel['channel_slug']; ?>">
                                    <img src="<?= $channel['channel_icon']; ?>" loading="lazy" alt="<?= $channel['channel_name']; ?>">
                                </a>
                                <div class="channel-status <?= ($channel['status'] == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $channel['status']; ?></div>
                            </div>
                            <div class="channel-name"><?= $channel['channel_name']; ?></div>
                        </div>
                        <?php break; } } } ?>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($recentlyListened)) { ?>
                    <div class="ke-more-channels-container">
                        <div class="ke-area-holder"><i class="fa fa-clock-rotate-left"></i>Continue Listening</div>
                        <div class="ke-channel-lists">
                            <?php
                                // Limit the number of recently listened radios to 3
                                $recentlyListenedLimited = array_slice($recentlyListened, 0, 5);

                                // Loop through the recently watched radio IDs
                                foreach ($recentlyListenedLimited as $watchedId) {
                                    // Find the matching radio in $allRadios
                                    foreach ($allRadios as $radio) {
                                        if ($radio['id'] == $watchedId) {
                            ?>
                            <div class="ke-channel-card">
                                <div class="channel-image">
                                    <a href="<?= BASE_URL . "/radio/" . $radio['radio_slug']; ?>">
                                        <img src="<?= $radio['radio_icon']; ?>" loading="lazy" alt="<?= $radio['radio_name']; ?>">
                                    </a>
                                    <div class="channel-status <?= ($radio['status'] == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $radio['status']; ?></div>
                                </div>
                                <div class="channel-name"><?= $radio['radio_name']; ?></div>
                            </div>
                            <?php break; } } } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($recentlyWatchedTV) || !empty($recentlyListened)) { ?>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
                <?php } ?>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-arrow-trend-up"></i>Popular TV Channels</div>
                    <div class="ke-channel-lists">
                        <?php 
                            $limitedChannels = array_slice($top_channels, 0, 7);
                            foreach ($limitedChannels as $key => $row): // Add $key to get the index of the array
                            $id = $row['id'];
                            $channel_name= trim($row['channel_name']);
                            $channel_icon= $row['channel_icon'];
                            $channel_slug = $row['channel_slug'];
                            $channel_poster = $row['channel_poster'];
                            $status = $row['status'];
                            $viewed = $row['viewed'];

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
                        <?php endforeach; ?>
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
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-list"></i>Radio Categories</div>
                    <div class="ke-radio-categories-container">
                        <div class="ke-radio-categories-lists">
                            <?php if($allRadioCategory) {
                                foreach($allRadioCategory as $row){
                            ?>
                            <div class="radio-category-card">
                                <a href="<?= BASE_URL .'/radio/category/'.$row['category_slug']; ?>">
                                    <i class="fa fa-location"></i>
                                    <div class="radio-category-metadata">
                                        <div class="radio-category-name"><?= $row['category_name']; ?></div>
                                        <div class="radio-category-stats"><?= $row['total_radios']. ' Stations'; ?></div>
                                    </div>
                                </a>
                            </div>
                            <?php } }?>
                        </div>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
            </div>
            <div class="ke-second-column">
                <div class="ads-container"><?= getAdsenseAd('square', 'auto'); ?></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="far fa-star"></i> Best Rated TV</div>
                    <div class="item-card-container">
                        <?php
                            $limitedRatedTV = array_slice($most_rated_tv, 0, 6);
                            foreach ($limitedRatedTV as $key => $row):
                                $id = $row['id'];
                                $channel_name = str_replace("Live", "", trim($row['channel_name']));
                                $status = $row['status'];
                                $channel_icon = $row['channel_icon'];
                                $viewed = $row['viewed'];
                                $average_rating = $row['average_rating'];
                        ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL .'/tv/' . $row['channel_slug']; ?>">
                                <img src="<?= $channel_icon; ?>" alt="<?= $channel_name; ?>">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL .'/tv/' . $row['channel_slug']; ?>"><?= $channel_name; ?></a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> <?= formatNumber($viewed) ?></span>
                                    <span><i class="far fa-star"></i> <?= $average_rating; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('square', 'auto'); ?></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="far fa-star"></i> Best Rated Radios</div>
                    <div class="item-card-container">
                        <?php
                            $limitedRatedRadio = array_slice($most_rated, 0, 6);
                            foreach ($limitedRatedRadio as $key => $row):
                                $id = $row['id'];
                                $radio_name = str_replace("Live", "", trim($row['radio_name']));
                                $status = $row['status'];
                                $frequency = ($row['frequency']) ? $row['frequency'] .' FM' : 'Online';
                                $radio_icon = $row['radio_icon'];
                                $viewed = $row['viewed'];
                                $reviews = $row['reviews'];
                                $average_rating = $row['average_rating'];
                        ?>
                        <div class="ke-item-card">
                            <a href="<?= BASE_URL .'/radio/' . $row['radio_slug']; ?>">
                                <img src="<?= $radio_icon; ?>" alt="<?= $radio_name; ?>">
                            </a>
                            <div class="card-metadata">
                                <strong><a href="<?= BASE_URL .'/radio/' . $row['radio_slug']; ?>"><?= $radio_name; ?></a></strong>
                                <div class="row-items">
                                    <span><i class="fa fa-tower-cell"></i> <?= $frequency; ?></span>
                                    <span><i class="far fa-headphones"></i> <?= formatNumber($viewed) ?></span>
                                    <span><i class="far fa-star"></i> <?= $average_rating; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../inc/footer.php'; ?>
    </main>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>