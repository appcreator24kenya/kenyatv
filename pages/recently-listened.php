<?php
    require_once __DIR__ . '/../config/constants.php';

    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 


    $channels = fetchTVList() ?? [];
    $top_channels =  fetchMostWatchedTV() ?? [];
    $most_rated_tv = fetchRatedTV() ?? [];

    $radios = fetchRadioList() ?? [];
    $top_radios = fetchMostListenedRadios() ?? [];
    $most_rated = fetchRatedRadios() ?? [];
    $allRadioCategory = fetchRadioCategories() ?? [];

    // recently listened radios
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

    $meta_title = "Your Recently Listened | Kenya Live TV";
    $meta_description = "Your recently listened Kenyan tv stations. Watch the best Kenyan music, news, and shows.";

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
    <link rel="canonical" href="<?= BASE_URL .'/recently-watched/'; ?>">
    <title><?= $meta_title; ?></title>
</head>
<body>
    <!--wordpress sidebar  -->
    <?php require_once __DIR__ . '/../inc/sidebar.php'; ?>
    <main>
        <!--wordpress header  -->
        <?php  require_once __DIR__ . '/../inc/header.php'; ?>
        <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
        <section class="ke-two-column-layout one-column">
            <div class="ke-first-column">
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-clock-rotate-left"></i>Continue Listening</div>
                    <?php if (empty($recentlyListenedRadios)): ?>
                        <p>No recently listened radio found.</p>
                        <?php else: ?>
                        <div class="ke-channel-lists">
                            <?php foreach ($recentlyListenedRadios as $radio) : ?>
                            <div class="ke-channel-card">
                                <div class="channel-image">
                                    <a href="<?= BASE_URL . "/radio/" . $radio['radio_slug']; ?>">
                                        <img src="<?= $radio['radio_icon']; ?>" loading="lazy" alt="<?= $radio['radio_name']; ?>">
                                    </a>
                                    <div class="channel-status <?= ($radio['status'] == 'Live Now') ? ' live-now' : ' off-air'; ?>"><?= $radio['status']; ?></div>
                                </div>
                                <div class="channel-name"><?= $radio['radio_name']; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../inc/footer.php'; ?>
    </main>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>