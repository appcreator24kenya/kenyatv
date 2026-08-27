<?php
    include __DIR__ . '/../config/config.php'; 

    $uploadDir = BASE_ROOT_PATH .'uploads/shows/';
    $imageQuality = 85;

    // echo "Upload directory: $uploadDir\n";
    if (!is_dir($uploadDir)) {
        echo "Directory does not exist!\n";
    }
    if (!is_writable($uploadDir)) {
        echo "Upload directory is not writable!\n";
    }


    function safeFilename($name) {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
    }

    function downloadAndConvertImage($url, $savePath, $targetWidth, $targetHeight, $quality = 85) {
        echo "Trying to download image from: $url\n";

        $imgData = @file_get_contents($url);
        if (!$imgData) {
            echo "Failed to download: $url\n";
            return false;
        }

        $sourceImage = @imagecreatefromstring($imgData);
        if (!$sourceImage) {
            echo "Failed to create image from string\n";
            return false;
        }

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled(
        $resized, $sourceImage,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        imagesx($sourceImage), imagesy($sourceImage)
        );

        if (!is_writable(dirname($savePath))) {
            echo "Cannot write to directory: " . dirname($savePath) . "\n";
            return false;
        }

        $result = imagewebp($resized, $savePath, $quality);

        if ($result) {
            echo "Saved image to: $savePath\n";
        } else {
            echo "Failed to save image to: $savePath\n";
        }

        imagedestroy($sourceImage);
        imagedestroy($resized);

        return $result;
    }


    $sql = "SELECT id, show_name, show_icon, show_poster, show_icon_url, show_poster_url FROM shows";
    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $safeName = safeFilename($row['show_name']);

        $update = [];
        $updateParams = [];
        $types = '';

        // ICON: Square (400x400)
        $iconFilename = "{$id}-icon-{$safeName}.webp";
        $iconPath = $uploadDir . $iconFilename;

        if (empty($row['show_icon']) && !file_exists($iconPath) && !empty($row['show_icon_url'])) {
            if (downloadAndConvertImage($row['show_icon_url'], $iconPath, 400, 400, $imageQuality)) {
                $update[] = "show_icon = ?";
                $updateParams[] = $iconFilename;
                $types .= 's';
                echo "Saved icon: $iconFilename\n";
            }
        }

        // POSTER: Rectangle (800x450)
        $posterFilename = "{$id}-poster-{$safeName}.webp";
        $posterPath = $uploadDir . $posterFilename;

        if (empty($row['show_poster']) && !file_exists($posterPath) && !empty($row['show_poster_url'])) {
            if (downloadAndConvertImage($row['show_poster_url'], $posterPath, 800, 450, $imageQuality)) {
                $update[] = "show_poster = ?";
                $updateParams[] = $posterFilename;
                $types .= 's';
                echo "Saved poster: $posterFilename\n";
            }
        }

        if (!empty($update)) {
            $updateSQL = "UPDATE shows SET " . implode(', ', $update) . " WHERE id = ?";
            $stmt = $mysqli->prepare($updateSQL);
            if ($stmt) {
                $types .= 'i';
                $updateParams[] = $id;
                $stmt->bind_param($types, ...$updateParams);
                $stmt->execute();
                $stmt->close();
                echo "Updated DB for ID $id\n";
            }
        }
    }

    $mysqli->close();
    echo "All done.\n";

?>