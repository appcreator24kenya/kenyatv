<?php
    include __DIR__.'/../config/constants.php';
    include __DIR__.'/../config/db.php';

    // ================== CONFIG ================== //
    $folders = [
        'channels'      => BASE_ROOT_PATH .'uploads/tv/',
        'shows'          => BASE_ROOT_PATH .'uploads/shows/',
        'radios'         => BASE_ROOT_PATH .'uploads/radio/',
        'soaps'          => BASE_ROOT_PATH .'uploads/soaps/',
        'setbooks'       => BASE_ROOT_PATH .'uploads/setbooks/',
        'news'           => BASE_ROOT_PATH .'uploads/news/',
        'videos'         => BASE_ROOT_PATH .'uploads/videos/',
        'radio_category' => BASE_ROOT_PATH .'uploads/radio/category/',
        'authors'        => BASE_ROOT_PATH .'uploads/authors/'
    ];

    $tables = [
        'channels'       => ['channel_icon','channel_poster'],
        'shows'          => ['show_icon','show_poster'],
        'radios'         => ['radio_icon','radio_poster'],
        'radio_category' => ['category_poster'],
        'soaps'          => ['soap_icon','soap_poster'],
        'setbooks'       => ['set_icon','set_poster'],
        'news'           => ['source_icon','source_poster'],
        'authors'        => ['author_icon','author_poster'],
        'videos'         => ['video_poster']
    ];

    $validExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    // ================== MAIN PROCESS ================== //
    foreach ($folders as $table => $folder) {
        if (!file_exists($folder)) continue;

        $files = glob($folder . '*');
        foreach ($files as $file) {
            if (!is_file($file)) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $validExtensions)) continue;

            optimizeImage($file, $ext, $tables);
        }
    }

    echo "✅ Image optimization completed.\n";


    // ================== FUNCTIONS ================== //

    function containsAny(string $haystack, array $needles): bool {
        foreach ($needles as $needle) {
            if (stripos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    function optimizeImage($file, $ext, $tables) {
        global $pdo;

        $imageInfo = @getimagesize($file);
        if (!$imageInfo) {
            echo "❌ Could not get image info for $file. Skipping...\n";
            return;
        }

        [$width, $height] = $imageInfo;
        $mime = $imageInfo['mime'];

        // Handle mislabelled webp
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $mime === 'image/webp') {
            $oldName = basename($file);
            $file_without_ext = pathinfo($oldName, PATHINFO_FILENAME);
            $newName = $file_without_ext . '.webp';
            $newFile = dirname($file) . '/' . $newName;

            echo "Renaming $oldName to $newName as it is already a WebP file...\n";
            if (rename($file, $newFile)) {
                echo "✅ Renamed file successfully. Updating DB...\n";
                updateDbReference($oldName, $newName, $tables);
                return;
            } else {
                echo "❌ Failed to rename $oldName. Check file permissions.\n";
                return;
            }
        }

        $fileSizeKB = filesize($file) / 1024;
        $maxSizeKB = 60;

        try {
            switch ($mime) {
                case 'image/jpeg':
                    $img = imagecreatefromjpeg($file);
                    break;
                case 'image/png':
                    $img = imagecreatefrompng($file);
                    break;
                case 'image/webp':
                    $img = imagecreatefromwebp($file);
                    break;
                default:
                    echo "❌ Unsupported MIME type: $mime for $file. Skipping...\n";
                    return;
            }

            if (!$img) {
                echo "❌ Could not create image resource for $file. Skipping...\n";
                return;
            }

            $basename = basename($file);
            $isIcon   = stripos($basename, 'icon') !== false;

            $posterKeywords = ['poster', 'thumbnail', 'thumnail'];
            $isPoster = containsAny($basename, $posterKeywords);

            $webpQuality = 95;

            // Apply ratio-based resizing
            if ($isIcon) {
                $img = resizeToRatio($img, $width, $height, 1); // 1:1
                echo "Processing icon (forced 1:1): $basename\n";
            } elseif ($isPoster) {
                $img = resizeToRatio($img, $width, $height, 16/9); // 16:9
                echo "Processing poster (forced 16:9): $basename\n";
            } else {
                echo "Processing standard image: $basename\n";
            }

            if ($fileSizeKB > $maxSizeKB) {
                $webpQuality = 80;
                echo "File size ($fileSizeKB KB) over $maxSizeKB KB, reducing quality to $webpQuality...\n";
            }

            $newFile = preg_replace('/\.[^.]+$/', '.webp', $file);
            imagewebp($img, $newFile, $webpQuality);
            echo "✅ Image saved as WebP: $newFile\n";

            if ($newFile !== $file) {
                unlink($file);
                updateDbReference($basename, basename($newFile), $tables);
                echo "Original file $basename removed and DB updated.\n";
            }

            imagedestroy($img);
        } catch (Exception $e) {
            echo "❌ Error processing $file: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Resize image proportionally to fit target aspect ratio.
     * No cropping, no padding.
     */
    function resizeToRatio($srcImg, $srcW, $srcH, $targetRatio) {
        $currentRatio = $srcW / $srcH;

        if (abs($currentRatio - $targetRatio) < 0.01) {
            return $srcImg; // Already correct ratio
        }

        if ($currentRatio > $targetRatio) {
            // Too wide → limit by height
            $newH = $srcH;
            $newW = (int) round($srcH * $targetRatio);
        } else {
            // Too tall → limit by width
            $newW = $srcW;
            $newH = (int) round($srcW / $targetRatio);
        }

        $tmp = imagecreatetruecolor($newW, $newH);

        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);

        imagecopyresampled($tmp, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

        return $tmp;
    }

    function updateDbReference($oldName, $newName, $tables) {
        global $pdo;

        foreach ($tables as $table => $columns) {
            foreach ($columns as $col) {
                $sql = "UPDATE $table SET $col = :new WHERE $col = :old";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':new' => $newName, ':old' => $oldName]);
            }
        }
    }

?>