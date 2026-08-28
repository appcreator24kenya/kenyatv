<?php
    require_once __DIR__ . '/../config/constants.php';

    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 

    // Fetch all channels
    $channels = fetchTVList() ?? [];
    
    // Prepare the $allChannels array
    $allChannels = [];
    if ($channels) {
        foreach ($channels as $index => $row) {
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
        }
    }

    $total_tv_count = count($allChannels);
    $meta_title = $total_tv_count . " Kenya TV Channels | " . SITE_NAME;
    $meta_description = "Watch all Kenya TV channels live online on Kenyalivetv.co.ke. Live stream news , tv shows , sports & movies";

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
    <link rel="preload" href="<?= BASE_CSS_URL .'tv-home.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL .'tv-home.css'; ?>"></noscript>
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
        <section class="ke-two-column-layout one-column">
            <div class="ke-first-column">
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-tv"></i><?= $total_tv_count .' TV Channels'; ?></div>
                    <div class="ke-channel-lists">
                        <?php 
                        if($allChannels){
                        foreach($allChannels as $key => $row){
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
                        <?php } } ?>
                    </div>
                </div>
                <div class="ads-container"><?= getAdsenseAd('horizontal', 'auto'); ?></div>
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