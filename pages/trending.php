<?php
    require_once __DIR__ . '/../config/constants.php';

    // seo meta tags
    // $meta_title = "Kenya Live TV | Kenya TV Channels | Kenya TV Stations";
    // $meta_description = "Explore Kenya Live TV! Dive into a world of entertainment, news and knowledge with 150 Kenya TV channels, 217 Kenya radio stations, 40 news sources and 150 Soap updates. Your one-stop destination for diverse content!";
    // $meta_title = "Watch Live TV Channels from Kenya | Kenya Live TV";
    // $meta_description = "Stream Kenya's best live TV channels online with Kenya Live TV. Watch your favorite Kenyan stations live, anytime, anywhere.";
    // $meta_title = "Explore Live TV Channels in Kenya | Kenya Live TV";
    // $meta_description = "Discover a variety of live TV channels from Kenya. Watch top stations streaming live online with Kenya Live TV.";
    // $meta_title = "Watch Live TV Channels in Kenya | Kenya Live TV | TV Schedules, Shows & Radio";
    $meta_title = "Watch Popular Live TV Channels & Radios in Kenya | Kenya Live TV";
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
        <div class="ads-container"></div>
        <section class="ke-two-column-layout one-column">
            <div class="ke-first-column">
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-arrow-trend-up"></i>Popular TV Channels</div>
                    <div class="ke-channel-lists">
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status live-now">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status live-now">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                    </div>
                </div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-arrow-trend-up"></i>Popular Radio Stations</div>
                    <div class="ke-channel-lists">
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status live-now">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status live-now">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status">Live Now</div>
                            </div>
                            <div class="channel-name">Radio Citizen</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ads-container"></div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../inc/footer.php'; ?>
    </main>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>