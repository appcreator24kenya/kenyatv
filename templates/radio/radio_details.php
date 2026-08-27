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
    $current_url = '';
    $meta_title = 'Listen To TV One Live Online | Kenya Live TV';
    $meta_description = 'Listen To TV One live online on Kenya Live TV. Stream your favorite Kenyan channels anytime, anywhere with high-quality streaming.';
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
    <link rel="canonical" href="<?= BASE_URL.'/tv/'; ?>" />
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
                        <li><a href="/">Home</a></li>>
                        <li><a href="/radio/">Radio</a></li>>
                        <li>Radio Citizen</li>
                    </ul>
                </nav>
            </div>
        </section>
        <section class="ke-two-column-layout">
            <div class="ke-first-column">
                <div class="ke-channel-small-info">
                    <div class="channel-icon">
                        <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="Citizen TV">
                    </div>
                    <div class="channel-metadata">
                        <div class="channel-row">
                            <div class="first-row">
                                <h1>Radio Citizen</h1>
                                <div class="channel-stats">Frequency : <strong>103.0 FM</strong></div>
                                <div class="channel-stats">Listeners : 123,456 views</div>
                                <div class="channel-stats">Last Updated :<time datetime="">4 hours ago</time></div>
                                <div class="single-radio-status live">Live</div>
                            </div>
                            <div class="second-row">
                                <div class="ke-audio-player">
                                    <div class="icon-buttons" title="Play / Pause" id="play-pause"><i class="far fa-play-circle"></i></div>
                                    <div class="icon-buttons" title="Mute / Unmute" id="mute-unmute"><i class="fa fa-volume-up"></i></div>
                                    <div class="icon-buttons" title="Reload Stream" id="reload-stream"><i class="fa fa-refresh"></i></div>
                                </div>
                                <audio id="radio-player" controls loop autoplay preload="none" hidden></audio>
                            </div>
                        </div>
                        <div class="channel-social-media-share-button">
                            <div class="icon-buttons"><a href="" title="Add To Favorites"><i class="far fa-heart active"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Facebook"><i class="fab fa-facebook"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Whatsapp"><i class="fab fa-whatsapp"></i></a></div>
                            <div class="icon-buttons"><a href="" title="X ( Twitter )"><i class="fab fa-x-twitter"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Instagram"><i class="fab fa-instagram"></i></a></div>
                            <div class="icon-buttons"><a href="" title="YouTube"><i class="fab fa-youtube"></i></a></div>
                            <div class="icon-buttons"><a href="" title="TikTok"><i class="fab fa-tiktok"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Email"><i class="fa fa-envelope"></i></a></div>
                            <div class="icon-buttons"><a href="" title="Website"><i class="fa fa-globe"></i></a></div>
                        </div>
                    </div>
                </div>
                <div class="ke-channel-description-container">
                    <div class="ke-area-holder"><i class="fa-solid fa-circle-question"></i>About Radio Citizen</div>
                    <div class="ke-channel-description">
                        <div class="channel-stats">Slogan : Home Of Good Times and Great Hits</div><br>
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. In minima voluptas, corporis cupiditate tempora amet id enim consequatur.<br>
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. In minima voluptas, corporis cupiditate tempora amet id enim consequatur.<br>
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. In minima voluptas, corporis cupiditate tempora amet id enim consequatur.
                    </div>
                </div>
                <div class="ads-container"></div>
                <div class="ke-video-player-container">
                    <div class="ke-video-container">
                        <iframe loading="lazy" src="https://kenyalivetv.co.ke/embed/citizen-tv-live?theme=dark" 
                            allow="autoplay" title="Kenya Live TV Video Player" scrolling="no" frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                    <div class="ke-video-availability-container">
                        <div class="video-availability-message">
                            Citizen TV is off air or no longer livestreaming online due to scheduled maintenance.
                        </div>
                    </div>
                </div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-tv"></i>Other Radio Stations</div>
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
                                <div class="channel-status live-now">Live Now</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Off Air</div>
                            </div>
                            <div class="channel-name">Citizen TV</div>
                        </div>
                        <div class="ke-channel-card">
                            <div class="channel-image">
                                <img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt="">
                                <div class="channel-status off-air">Off  Air</div>
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
                <div class="ads-container"></div>
                <div class="ke-more-channels-container">
                    <div class="ke-area-holder"><i class="fa fa-ticket"></i>Popular Shows</div>
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
                <div class="ads-container"></div>
            </div>
            <div class="ke-second-column">
                <div class="ads-container"></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-clock-rotate-left"></i> CONTINUE LISTENING</div>
                    <div class="item-card-container">
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="fa fa-tower-cell"></i> 103.0 FM</span>
                                    <span><i class="far fa-headphones"></i> 20.1K</span>
                                    <span><i class="far fa-star"></i> 4.9</span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                    <span><i class="far fa-star"></i> 4.9</span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                    <span><i class="far fa-star"></i> 4.9</span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                    <span><i class="far fa-star"></i> 4.9</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ads-container"></div>
                <div class="ke-item-lists-container">
                    <div class="area-label"><i class="fa fa-heart"></i> FAVORITES</div>
                    <div class="item-card-container">
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                </div>
                            </div>
                        </div>
                        <div class="ke-item-card">
                            <a href=""><img src="https://kenyalivetv.co.ke/uploads/tv/1_icon_citizentv.webp" alt=""></a>
                            <div class="card-metadata">
                                <strong><a href="">Citizen TV</a></strong>
                                <div class="row-items">
                                    <span><i class="far fa-eye"></i> 1.4 M </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--wordpress footer  -->
        <?php require_once __DIR__ . '/../../inc/footer.php'; ?>
    </main>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>