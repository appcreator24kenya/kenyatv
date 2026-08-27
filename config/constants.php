<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
    error_reporting(E_ALL);

    $env = "test";
    
    $use_cached_data = true;
    
    $userLoginType = isset($_COOKIE['user_login_type']) ? $_COOKIE['user_login_type'] : '';

    define('ENABLE_MONETAG_ADS', false);
    
    define('SHOW_TV_SCHEDULES', true); // Set to false to disable schedules
    
    define('SHOW_RADIO_PLAYLISTS', true); // or false to disable auto-reminders

    define('ENABLE_ONE_SIGNAL', false); // or false to disable one signal
    
    define('ENABLE_CACHING', true); // or false to disable caching

    // define('FORCE_ADD_SHOW_REMINDERS', true); // or false to disable auto-reminders
    define('FORCE_ADD_SHOW_REMINDERS', false); // or false to disable auto-reminders
    
    // Define whether redirection for radio stations should happen
    define('REDIRECT_RADIOS', false);  // Set to true to enable redirection, false to disable it

    define('SITE_NAME', 'Kenya Live TV');
    
    // define('NOTIFICATION_SOUND', BASE_URL.'/assets/audio/notification.mp3');

    // Define any API keys or other constants
    define('API_KEY', 'tv-websites-04b0-4bb3-a774-d5467ff09393');

    define('BASE_ROOT_PATH', str_replace('\\', '/', dirname(__DIR__)));

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $appPath = trim(str_replace($docRoot, '', BASE_ROOT_PATH), '/');
    $subfolder = !empty($appPath) ? '/' . $appPath : '';

    define('BASE_URL', rtrim($protocol . $host . $subfolder, '/'));

    if ($env == "live") {

        // Base CSS of the site
        define('BASE_CSS_URL', BASE_URL.'/assets/css/');

        // Base JS of the site
        define('BASE_JS_URL', BASE_URL.'/assets/js/');

        // Base ICONS of the site
        define('BASE_ICONS_URL', BASE_URL.'/assets/icons/');

        // Base IMAGES of the site
        define('BASE_IMAGES_URL', BASE_URL.'/assets/images/');

        // Base URL of tv channels image
        define('TV_IMAGES_BASE_URL', BASE_URL.'/uploads/tv/');

        // Base URL of Shows image
        define('SHOWS_IMAGES_BASE_URL', BASE_URL.'/uploads/shows/');

        // Base URL of RADIO IMAGES 
        define('RADIO_IMAGES_BASE_URL', BASE_URL.'/uploads/radio/');
        
        // Base URL of SETBOOKS IMAGES 
        define('SETBOOKS_IMAGES_BASE_URL', BASE_URL.'/uploads/setbooks/');

        // Base URL of SOAPS IMAGES 
        define('SOAPS_IMAGES_BASE_URL', BASE_URL.'/uploads/soaps/');

        // Base URL of AUTHORS IMAGES 
        define('AUTHORS_IMAGES_BASE_URL', BASE_URL.'/uploads/authors/');

        // Base URL of NEWS IMAGES 
        define('NEWS_IMAGES_BASE_URL', BASE_URL.'/uploads/news/');

        // Base URL of VIDEOS IMAGES 
        define('VIDEOS_IMAGES_BASE_URL', BASE_URL.'/uploads/videos/');

        // Base URL of RADIO CATEGORY IMAGES 
        define('RADIO_CATEGORY_IMAGES_BASE_URL', BASE_URL.'/uploads/radio/category/');

        // Other global settings can go here
        define('ADS_ENVIRONMENT', 'live'); // Change to 'testing' or 'live' in your development environment
        
    } else if ($env == "dev") {

        // Base CSS of the site
        define('BASE_CSS_URL', BASE_URL .'/assets/css/');

        // Base JS of the site
        define('BASE_JS_URL', BASE_URL .'/assets/js/');

        // Base ICONS of the site
        define('BASE_ICONS_URL', BASE_URL .'/assets/icons/');

        // Base IMAGES of the site
        define('BASE_IMAGES_URL', BASE_URL .'/assets/images/');

        // Base URL of tv channels image
        define('TV_IMAGES_BASE_URL', BASE_URL .'/uploads/tv/');

        // Base URL of Shows image
        define('SHOWS_IMAGES_BASE_URL', BASE_URL .'/uploads/shows/');

        // Base URL of RADIO IMAGES 
        define('RADIO_IMAGES_BASE_URL', BASE_URL .'/uploads/radio/');

        // Base URL of SETBOOKS IMAGES 
        define('SETBOOKS_IMAGES_BASE_URL', BASE_URL .'/uploads/setbooks/');

        // Base URL of SOAPS IMAGES 
        define('SOAPS_IMAGES_BASE_URL', BASE_URL .'/uploads/soaps/');

        // Base URL of AUTHORS IMAGES 
        define('AUTHORS_IMAGES_BASE_URL', BASE_URL .'/uploads/authors/');

        // Base URL of NEWS IMAGES 
        define('NEWS_IMAGES_BASE_URL', BASE_URL .'/uploads/news/');

        // Base URL of VIDEOS IMAGES 
        define('VIDEOS_IMAGES_BASE_URL', BASE_URL .'/uploads/videos/');

        // Base URL of RADIO CATEGORY IMAGES 
        define('RADIO_CATEGORY_IMAGES_BASE_URL', BASE_URL .'/uploads/radio/category/');

        // Other global settings can go here
        define('ADS_ENVIRONMENT', 'testing'); // Change to 'testing' or 'dev' in your development environment

    } else {
        
        // Base CSS of the site
        define('BASE_CSS_URL', BASE_URL .'/assets/css/');

        // Base JS of the site
        define('BASE_JS_URL', BASE_URL .'/assets/js/');

        // Base ICONS of the site
        define('BASE_ICONS_URL', BASE_URL .'/assets/icons/');

        // Base IMAGES of the site
        define('BASE_IMAGES_URL', BASE_URL .'/assets/images/');

        // Base URL of tv channels image
        define('TV_IMAGES_BASE_URL', BASE_URL .'/uploads/tv/');

        // Base URL of Shows image
        define('SHOWS_IMAGES_BASE_URL', BASE_URL .'/uploads/shows/');

        // Base URL of RADIO IMAGES 
        define('RADIO_IMAGES_BASE_URL', BASE_URL .'/uploads/radio/');

        // Base URL of SETBOOKS IMAGES 
        define('SETBOOKS_IMAGES_BASE_URL', BASE_URL .'/uploads/setbooks/');

        // Base URL of SOAPS IMAGES 
        define('SOAPS_IMAGES_BASE_URL', BASE_URL .'/uploads/soaps/');

        // Base URL of AUTHORS IMAGES 
        define('AUTHORS_IMAGES_BASE_URL', BASE_URL .'/uploads/authors/');

        // Base URL of NEWS IMAGES 
        define('NEWS_IMAGES_BASE_URL', BASE_URL .'/uploads/news/');

        // Base URL of VIDEOS IMAGES 
        define('VIDEOS_IMAGES_BASE_URL', BASE_URL .'/uploads/videos/');

        // Base URL of RADIO CATEGORY IMAGES 
        define('RADIO_CATEGORY_IMAGES_BASE_URL', BASE_URL .'/uploads/radio/category/');

        // Other global settings can go here
        define('ADS_ENVIRONMENT', 'testing'); // Change to 'testing' or 'live' in your development environment
    }
?>