<!-- christmas animation -->
<div class="snow-container"></div>
<!-- new year animation -->
<div class="fireworksContainer"></div>
<?php
    // Extract relative path so active links match correctly in subfolders
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $current_path = trim(urldecode($requestUri), '/');

    // Strip subfolder prefix if present (e.g. "kenyatv/trending" -> "trending")
    $baseFolder = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($baseFolder && strpos($current_path, $baseFolder) === 0) {
        $current_path = trim(substr($current_path, strlen($baseFolder)), '/');
    }

    function isActive($route, $current_path, $exact = false) {
        $route = trim($route, '/');
        if ($exact) {
            return ($current_path === $route) ? ' active' : '';
        }
        return (strpos($current_path, $route) === 0 && ($route !== '' || $current_path === '')) ? ' active' : '';
    } 
?>
<aside id="ke-sidebar">
    <div class="sidebar-logo">
        <a href="<?= BASE_URL; ?>/"><img src="<?= BASE_IMAGES_URL.'logo.png'; ?>" alt="<?= SITE_NAME ?>"><?= SITE_NAME ?></a>
    </div>
    <nav>
        <ul>
            <li><a href="<?= BASE_URL; ?>/" class="<?= isActive('', $current_path, true); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?= BASE_URL; ?>/trending/" class="<?= isActive('trending', $current_path); ?>"><i class="fa fa-fire"></i> Trending</a></li>
            <li><a href="<?= BASE_URL; ?>/tv/" class="<?= isActive('tv', $current_path); ?>"><i class="fa fa-tv"></i> Television</a></li>
            <li><a href="<?= BASE_URL; ?>/radio/" class="<?= isActive('radio', $current_path); ?>"><i class="fa fa-radio"></i> Radio</a></li>
            <li><a href="<?= BASE_URL; ?>/news/" class="<?= isActive('news', $current_path); ?>"><i class="fa fa-newspaper"></i> News</a></li>
            <li><a href="<?= BASE_URL; ?>/soaps/" class="<?= isActive('soaps', $current_path); ?>"><i class="fa fa-soap"></i> Soaps</a></li>
            <li><a href="<?= BASE_URL; ?>/setbooks/" class="<?= isActive('setbooks', $current_path); ?>"><i class="fa fa-graduation-cap"></i> Setbooks</a></li>
            <li><a href="<?= BASE_URL; ?>/app/" class="<?= isActive('app', $current_path); ?>"><i class="fab fa-google-play"></i> Our App</a></li>
            <hr>
            <li><a href="<?= BASE_URL; ?>/search" class="<?= isActive('search', $current_path); ?>"><i class="fa fa-search"></i> Search</a></li>
            <li><a href="<?= BASE_URL; ?>/recently-watched/" class="<?= isActive('recently-watched', $current_path); ?>"><i class="fa fa-clapperboard"></i> Recently Watched</a></li>
            <li><a href="<?= BASE_URL; ?>/recently-listened/" class="<?= isActive('recently-listened', $current_path); ?>"><i class="fa fa-music"></i> Recently Listened</a></li>
            <li><a href="<?= BASE_URL; ?>/recently-added/" class="<?= isActive('recently-added', $current_path); ?>"><i class="fa fa-circle-plus"></i> Recently Added</a></li>
            <li><a href="<?= BASE_URL; ?>/favorites/" class="<?= isActive('favorites', $current_path); ?>"><i class="fa fa-heart"></i> Favorites</a></li>
            <hr>
            <div class="ke-social-links">
                <li><a href="https://www.facebook.com/allkenyalivetv" target="_blank" rel="noopener noreferrer" title="Our Facebook Page"><i class="fab fa-facebook"></i></a></li>
                <li><a href="https://www.twitter.com/allkenyalivetv" target="_blank" rel="noopener noreferrer" title="Our X (Twitter) Page"><i class="fab fa-x-twitter"></i></a></li>
                <li><a href="https://www.instagram.com/allkenyalivetv" target="_blank" rel="noopener noreferrer" title="Our Instagram Page"><i class="fab fa-instagram"></i></a></li>
                <li><a href="mailto:info@kenyalivetv.co.ke" target="_blank" rel="noopener noreferrer" title="Email Us"><i class="fa fa-envelope"></i></a></li>
            </div>
        </ul>
    </nav>
</aside>