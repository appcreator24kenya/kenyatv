<?php
    require_once __DIR__ . '/config/constants.php';
    require_once __DIR__ . '/inc/functions.php';
    require_once __DIR__ . '/inc/fetch_data.php';

    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim(urldecode($requestUri), '/');

    // Strip base directory if hosted in a subfolder (e.g., /kenyatv/)
    $baseFolder = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($baseFolder && strpos($path, $baseFolder) === 0) {
        $path = trim(substr($path, strlen($baseFolder)), '/');
    }

    $staticPages = [
        'tv'             => 'tv.php',
        'radio'          => 'radio.php',
        'soaps'          => 'soaps.php',
        'news'           => 'news.php',
        'about'          => 'about.php',
        'privacy'        => 'privacy.php',
        'contact'        => 'contact.php',
        'disclaimer'     => 'disclaimer.php',
        'terms'          => 'terms.php',
        'trending'       => 'trending.php',
        'search'         => 'search.php',
        'app'            => 'app.php',
        'recently-watched'  => 'recently-watched.php',
        'recently-listened' => 'recently-listened.php',
        'recently-added'    => 'recently-added.php',
        'favorites'      => 'favorites.php',
    ];

    switch (true) {
        // Home Page
        case $path === '':
        case $path === 'home':
            require __DIR__ . '/pages/home.php';
            break;

        // Static Pages (Lookup array)
        case isset($staticPages[$path]):
            require __DIR__ . '/pages/' . $staticPages[$path];
            break;

        // TV Routes
        case preg_match('#^tv/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/tv/tv_details.php';
            break;

        case preg_match('#^tv/([^/]+)/schedules$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/tv/schedule.php';
            break;

        case preg_match('#^tv-show/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/tv/shows.php';
            break;

        // Radio Routes
        case preg_match('#^radio/([^/]+)/schedules$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/radio/schedule.php';
            break;

        case preg_match('#^radio/category/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/radio/category.php';
            break;

        case preg_match('#^radio-show/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/templates/radio/shows.php';
            break;

        case preg_match('#^radio/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/radio_details.php';
            break;

        // Dynamic Media & Resource Routes
        case preg_match('#^setbooks/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/setbooks_details.php';
            break;

        case preg_match('#^soaps/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/soaps_details.php';
            break;

        case preg_match('#^news/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/news_details.php';
            break;

        case preg_match('#^videos/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/videos_details.php';
            break;

        case preg_match('#^authors/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/authors.php';
            break;

        // Embed Routes
        case preg_match('#^embed/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/frontend/embed/embed-tv.php';
            break;

        case preg_match('#^widget/([^/]+)$#', $path, $matches):
            $_GET['slug'] = $matches[1];
            require __DIR__ . '/frontend/embed/embed-radio.php';
            break;

        // Fallback 404
        default:
            require __DIR__ . '/pages/home.php';
            break;
    }
    exit;

?>