<?php
    require_once __DIR__ . '/../config/constants.php';
    require_once __DIR__ . '/../plugins/simplepie-master/autoloader.php';

    // 1. Fetch news sources from database
    $newsSources = fetchNewsList() ?? [];

    // 2. Cache Setup
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    // 3. Process each feed in its own SimplePie instance
    $feedObjects = [];

    foreach ($newsSources as $source) {
        if (isset($source['status']) && $source['status'] === 'Publish' && !empty($source['feed_url'])) {
            
            $sp = new SimplePie();
            $sp->set_feed_url($source['feed_url']);
            
            // Cache & performance settings
            $sp->enable_cache(true);
            $sp->set_cache_location($cacheDir);
            $sp->set_cache_duration(1800); // 30 minutes
            $sp->set_timeout(3); // 3s network cutoff
            $sp->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);
            
            $sp->init();
            $sp->handle_content_type();

            if (!$sp->error()) {
                // Store feed instance keyed by feed URL
                $feedObjects[$source['feed_url']] = [
                    'meta' => $source,
                    'feed' => $sp
                ];
            }
        }
    }

    // seo meta tags
    $total_news_count = count($newsSources);
    $meta_title = "Kenya Live News | Kenya News Today | Kenya Breaking News";
    $meta_description = "Kenya Live News  Stay updated with all the latest news available Kenya. Explore breaking news, politics, business, sports, entertainment, and more on Kenya Live TV. Your reliable source for timely and accurate information.";

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
    <!-- <link rel="preload" href="<?= BASE_CSS_URL .'tv-home.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= BASE_CSS_URL .'tv-home.css'; ?>"></noscript> -->
    <link rel="canonical" href="<?= BASE_URL .'/news/'; ?>">
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
                <div class="ke-more-news-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-newspaper"></i><?= $total_news_count .' News Sources'; ?></div>
                    <div class="ke-news-lists">
                        <?php 
                            foreach ($feedObjects as $url => $data): 
                                $info = $data['meta'];
                                $sp   = $data['feed'];

                                // Get the latest single item for this feed instance
                                $items = $sp->get_items(0, 1);
                                
                                if (!empty($items)):
                                    $latestNews = $items[0];
                            ?>
                                <div class="ke-news-card">
                                    <div class="card-flex">
                                        <div class="card-image">
                                            <a href="<?= BASE_URL . '/news/' . htmlspecialchars($info['source_slug']); ?>" target="_blank" rel="noopener noreferrer">
                                                <img src="<?= htmlspecialchars($info['source_icon_url']); ?>" alt="<?= htmlspecialchars($info['source_name']); ?>">
                                            </a>
                                        </div>
                                        <div class="card-column">
                                            <div class="card-origin-name"><?= htmlspecialchars($info['source_name']); ?></div>
                                            <div class="card-datetime"><?= htmlspecialchars($latestNews->get_date('M d, Y \a\t h:i A')); ?></div>
                                        </div>
                                    </div>
                                    <div class="card-title">
                                        <a href="<?= htmlspecialchars($latestNews->get_permalink()); ?>" target="_blank" rel="noopener noreferrer">
                                            <?= htmlspecialchars($latestNews->get_title()); ?>
                                        </a>
                                    </div>
                                    <div class="card-description">
                                        <?= htmlspecialchars(mb_strimwidth(strip_tags($latestNews->get_description()), 0, 160, '...')); ?>
                                    </div>
                                </div>
                            <?php 
                                endif; 
                            endforeach; 
                        ?>
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