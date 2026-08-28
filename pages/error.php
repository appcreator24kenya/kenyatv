<?php 
    require_once __DIR__ . '/../config/constants.php';
    
    // Get the error status code from the URL query parameter (default to 404 if not set)
    $status_code = isset($_GET['code']) ? intval($_GET['code']) : 404;
    
    // Define custom titles and messages for each error code
    $errors = [
        400 => [
            'title'   => '400 - Bad Request',
            'heading' => 'Bad Request',
            'message' => 'The server could not understand the request due to invalid syntax.'
        ],
        401 => [
            'title'   => '401 - Unauthorized',
            'heading' => 'Authentication Required',
            'message' => 'You must log in or be authenticated to view this page.'
        ],
        403 => [
            'title'   => '403 - Forbidden',
            'heading' => 'Access Denied',
            'message' => 'You do not have permission to access this resource or page.'
        ],
        404 => [
            'title'   => '404 - Page Not Found',
            'heading' => 'Oops! Page Not Found',
            'message' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.'
        ],
        500 => [
            'title'   => '500 - Internal Server Error',
            'heading' => 'Internal Server Error',
            'message' => 'Something went wrong on our servers. Please try refreshing the page later.'
        ],
        503 => [
            'title'   => '503 - Service Unavailable',
            'heading' => 'Service Temporarily Unavailable',
            'message' => 'The server is currently unable to handle the request due to a temporary overload or maintenance.'
        ]
    ];
    
    // Fallback for unlisted error codes
    if (array_key_exists($status_code, $errors)) {
        $error_info = $errors[$status_code];
    } else {
        $error_info = [
            'title'   => "$status_code - Error",
            'heading' => "Error $status_code",
            'message' => 'An unexpected error occurred. Please try again later.'
        ];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL . '/favicon.ico' ?>">
    <link rel="icon" sizes="16x16" type="image/x-icon" href="<?= BASE_ICONS_URL . 'favicon_16x16.ico' ?>">
    <link rel="icon" sizes="32x32" type="image/x-icon" href="<?= BASE_ICONS_URL . 'favicon_32x32.ico' ?>">
    
    <!-- CSS (you can use maintenance.css or create error.css) -->
    <link rel="preload" href="<?= BASE_CSS_URL . 'maintenance.css'; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= BASE_CSS_URL . 'maintenance.css'; ?>">
    </noscript>
    
    <title><?= htmlspecialchars($error_info['title']); ?></title>
</head>
<body>
    <div class="container">
        <img src="<?= BASE_IMAGES_URL . 'tv-logo.png' ?>" alt="Kenya Live TV Logo" class="logo">
        
        <h2><?= htmlspecialchars($error_info['heading']); ?></h2>
        <p><?= htmlspecialchars($error_info['message']); ?></p>
        
        <div class="contact-info">
            <p>
                <a href="<?= BASE_URL; ?>">Return to Homepage</a> 
                | Need help? Contact us at 
                <a href="mailto:info@kenyalivetv.co.ke" target="_blank">info@kenyalivetv.co.ke</a>
            </p>
        </div>
    </div>
    <!--Start of Tawk.to Script-->
    <?php require_once  __DIR__ . '/../inc/chat.php'; ?>
    <!--End of Tawk.to Script-->
</body>
</html>