<link rel='preconnect dns-prefetch' href='//www.googletagmanager.com' />
    <link rel='preconnect dns-prefetch' href='//fonts.googleapis.com' />
    <link rel="preconnect dns-prefetch" href="https://fonts.gstatic.com" crossorigin>
    <link rel='preconnect dns-prefetch' href='//googleads.g.doubleclick.net' />
    <link rel='preconnect dns-prefetch' href='//fonts.gstatic.com' />
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL .'/favicon.ico'?>">
    <link rel="icon" sizes="16x16" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_16x16.ico'?>">
    <link rel="icon" sizes="32x32" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_32x32.ico'?>">
    <link rel="icon" sizes="48x48" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_48x48.ico'?>">
    <link rel="icon" sizes="96x96" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_96x96.ico'?>">
    <link rel="icon" sizes="128x128" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_128x128.ico'?>">
    <link rel="icon" sizes="256x256" type="image/x-icon" href="<?= BASE_ICONS_URL .'favicon_256x256.ico'?>">
    <link rel="icon" sizes="48x48" type="image/png" href="<?= BASE_ICONS_URL .'favicon_48x48.png'?>">
    <link rel="icon" sizes="96x96" type="image/png" href="<?= BASE_ICONS_URL .'favicon_96x96.png'?>">
    <link rel="icon" sizes="128x128" type="image/png" href="<?= BASE_ICONS_URL .'favicon_128x128.png'?>">
    <link rel="icon" sizes="196x196" type="image/png" href="<?= BASE_ICONS_URL .'favicon_196x196.png'?>">
    <link rel="icon" sizes="256x256" type="image/png" href="<?= BASE_ICONS_URL .'favicon_256x256.png'?>">
    <link rel="preload" href="<?= BASE_URL .'/assets/webfonts/fa-solid-900.woff2' ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= BASE_URL .'/assets/webfonts/fa-regular-400.woff2' ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= BASE_URL .'/assets/webfonts/fa-brands-400.woff2' ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= BASE_URL .'/assets/fonts/Jost-Regular.woff2' ?>" as="font" type="font/woff2" crossorigin fetchpriority="high">
    <link rel="preload" href="<?= BASE_CSS_URL .'font.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= BASE_CSS_URL .'fontawesome.min.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'"> 
    <link rel="preload" href="<?= BASE_CSS_URL .'main.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= BASE_CSS_URL .'header.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= BASE_CSS_URL .'sidebar.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?= BASE_CSS_URL .'footer.css' ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'font.css' ?>">
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'fontawesome.min.css' ?>"> 
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'main.css' ?>">
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'header.css' ?>">
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'sidebar.css' ?>">
    <link rel="stylesheet" href="<?= BASE_CSS_URL .'footer.css' ?>">
    </noscript>
    <meta name="theme-color" content="#FFC50c" />
    <script defer src="<?= BASE_JS_URL .'main.js?min' ?>"></script>
    <?php if(!ENABLE_CACHING) { ?>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php } ?>
    <script>
        try {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            document.cookie = "timezone=" + timezone + ";path=/;max-age=31536000"; 
        } catch (error) {
            console.error("Error getting timezone:", error);
        }
    </script>
    <meta name="propeller" content="a29de596e1970815446bb394cdbbc347">
    <meta property="og:site_name" content="<?= SITE_NAME ?>" />
    <?php if ($env == "live") { ?>
        
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-XFC85EWQGV"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            
            gtag('config', 'G-XFC85EWQGV');
        </script>
        <!-- Microsoft Clarity -->
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "ewopkcsld8");
        </script>
        <!-- end of microsoft clarity  -->
    <?php } ?>
    <link rel="sitemap" type="application/xml" href="<?= BASE_URL .'/sitemap.xml' ?>">
    <?php
        // $stats = [
        //     'all_channels' => $all_channels ?? 149,
        //     'all_radios' => $all_radios ?? 221,
        //     'all_soaps' => $all_soaps ?? 155,
        //     'all_setbooks' => $all_setbooks ?? 15,
        //     'all_news' => $all_news ?? 41,
        //     // 'live_users' => $live_users ?? 10000,
        // ];
    ?>
    <?php if ($env == "live") { ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delay loading the ShareThis script by 1 minute (60,000 milliseconds)
            setTimeout(function() {
                const script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://platform-api.sharethis.com/js/sharethis.js#property=635f02b21832cb0012f851e5&product=inline-reaction-buttons';
                document.body.appendChild(script);
                // console.log('ShareThis script loaded after 1 minute.');
            }, 60000); // 60000 milliseconds = 1 minute
        });
    </script>
    <?php } ?>
    <script async src="https://cdn.jsdelivr.net/npm/just-detect-adblock@1.1.0/dist/bundle.umd.min.js"></script>
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
    <?php if (ENABLE_MONETAG_ADS) { ?>
        <script>(function(s){s.dataset.zone='10881808',s.src='https://n6wxm.com/vignette.min.js'})([document.documentElement, document.body].filter(Boolean).pop().appendChild(document.createElement('script')))</script>
        <!--<script>(function(s){s.dataset.zone='11068521',s.src='https://nap5k.com/tag.min.js'})([document.documentElement, document.body].filter(Boolean).pop().appendChild(document.createElement('script')))</script>-->
    <?php } ?>