<?php
    require_once __DIR__ . '/../config/constants.php';

    $all_channels = fetchCountRowsInAllTables('channels');
    $all_radios = fetchCountRowsInAllTables('radios');
    $all_soaps = fetchCountRowsInAllTables('soaps');
    $all_setbooks = fetchCountRowsInAllTables('setbooks');
    $all_news = fetchCountRowsInAllTables('news');
    $live_users = fetchCountRowsInAllTables('online_viewers'); 

    // fetch radios data lists
    $radios = fetchRadioList() ?? [];
    
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

    // seo meta tags
    $total_radio_count = count($allRadios);
    $meta_title = "Listen to Kenya Radio Stations Live | Kenya Live TV";
    $meta_description = "Stream live radio stations from Kenya anywhere in the world. Enjoy music, news, and cultural shows online in real-time on Kenya Live TV.";
    // $meta_title = "Kenya Radio Stations". " | " . $total_tv_count . " Radio Stations in Kenya";
    // $meta_description = "Kenya Live TV is a digital platform that offers live streaming of various radio stations from Kenya. It provides a convenient way for people around the world to tune in and listen to their favorite Kenyan radio stations in real-time. The platform aims to connect Kenyan diaspora, local residents, and global listeners, providing a sense of cultural connection and keeping them updated with the latest happenings in Kenya through the power of radio.";

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
                    <div class="ke-area-holder"><i class="fa-solid fa-radio"></i><?= $total_radio_count .' Radio Stations'; ?></div>
                    <div class="ke-channel-lists">
                        <?php 
                            if($allRadios){
                            foreach($allRadios as $key => $row){
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