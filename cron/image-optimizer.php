<?php
    include __DIR__ . '/../config/constants.php';

    // Directories with images
    $folders = [
        'channels'       => BASE_ROOT_PATH .'uploads/tv/',
        'shows'          => BASE_ROOT_PATH .'uploads/shows/',
        'radios'         => BASE_ROOT_PATH .'uploads/radio/',
        'soaps'          => BASE_ROOT_PATH .'uploads/soaps/',
        'setbooks'       => BASE_ROOT_PATH .'uploads/setbooks/',
        'news'           => BASE_ROOT_PATH .'uploads/news/',
        'videos'         => BASE_ROOT_PATH .'uploads/videos/',
        'radio_category' => BASE_ROOT_PATH .'uploads/radio/category/',
        'authors'        => BASE_ROOT_PATH .'uploads/authors/'
    ];

    $quality = 85;

    foreach ($folders as $key => $dir) {
        if (!is_dir($dir)) continue;

        $files = glob($dir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

        foreach ($files as $file) {
            $relPath = str_replace(BASE_ROOT_PATH, '', $file);
            $name    = strtolower(pathinfo($file, PATHINFO_FILENAME));

            // Detect type (icon vs thumbnails/poster)
            if (strpos($name, 'icon') !== false) {
                $responsiveWidths = [128, 256, 512];
            } elseif (
                strpos($name, 'poster')     !== false ||
                strpos($name, 'thumnail')   !== false || 
                strpos($name, 'thumbnail')  !== false ||
                strpos($name, 'thumbnails') !== false
            ) {
                $responsiveWidths = [300, 600, 1200];
            } else {
                continue; // skip if not recognized
            }

            // Make path relative to /uploads/
            $relPath = ltrim(str_replace('uploads/', '', $relPath), '/');

            // Generate each responsive width
            foreach ($responsiveWidths as $w) {
                $url = BASE_URL . '/uploads/index.php?path=' 
                    . urlencode($relPath) 
                    . '&w=' . $w 
                    . '&q=' . $quality;

                // Optional: skip if file already exists in /storage
                $storagePath = BASE_ROOT_PATH . 'storage/' . dirname($relPath) . '/' 
                            . pathinfo($file, PATHINFO_FILENAME) . "_{$w}.webp";

                if (file_exists($storagePath)) {
                    echo "Already exists: $storagePath\n";
                    continue;
                }

                // Trigger optimizer
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);

                echo "Generated: $url\n";
            }
        }
    }
?>