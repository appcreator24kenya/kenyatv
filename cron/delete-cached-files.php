<?php
    include __DIR__ . '/../config/config.php';

    $apicachefolder = BASE_ROOT_PATH . 'api/cache/';
    $uicachefolder = BASE_ROOT_PATH . 'cache/';

    function deleteFilesInFolder($folderPath) {
        if (!is_dir($folderPath)) {
            echo "Folder not found: $folderPath<br>";
            return false;
        }

        $files = glob($folderPath . '*'); // get all files in the folder

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    // First, delete API cache
    if (deleteFilesInFolder($apicachefolder)) {
        echo "API cache cleared successfully.<br>";

        // Then, delete UI cache
        if (deleteFilesInFolder($uicachefolder)) {
            echo "UI cache cleared successfully.";
        } else {
            echo "Failed to clear UI cache.";
        }

    } else {
        echo "Failed to clear API cache. UI cache not cleared.";
    }
?>