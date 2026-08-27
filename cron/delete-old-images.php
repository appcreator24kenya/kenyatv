<?php
    include __DIR__.'/../config/constants.php';
    include __DIR__.'/../config/db.php';

    // $pdo = $pdoNormal;

    // Define folders for each table
    $channelFolder = BASE_ROOT_PATH .'uploads/tv/';
    $showsFolder = BASE_ROOT_PATH .'uploads/shows/';
    $radioFolder = BASE_ROOT_PATH .'uploads/radio/';
    $soapsFolder = BASE_ROOT_PATH .'uploads/soaps/';
    $setbooksFolder = BASE_ROOT_PATH .'uploads/setbooks/';
    $newsFolder = BASE_ROOT_PATH .'uploads/news/';
    $videosFolder = BASE_ROOT_PATH .'uploads/videos/';
    $radioCategoryFolder = BASE_ROOT_PATH .'uploads/radio/category/';

    // Function to ensure folders exist
    function ensureFolderExists($folder) {
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0777, true)) {
                echo "Error creating folder: $folder";
                return false;
            }
        }
        return true;
    }

    // Ensure all folders exist
    ensureFolderExists($channelFolder);
    ensureFolderExists($showsFolder);
    ensureFolderExists($radioFolder);
    ensureFolderExists($soapsFolder);
    ensureFolderExists($setbooksFolder);
    ensureFolderExists($newsFolder);
    ensureFolderExists($videosFolder);
    ensureFolderExists($radioCategoryFolder);

    // Define extensions to skip
    $skipExtensions = ['css', 'js', 'html', 'php'];

    // Function to delete unused files from a specified folder based on database references
    function deleteUnusedFiles($pdo, $table, $columns, $folder, $skipExtensions) {
        // Fetch all image names from the specified columns in the given table
        $columnList = implode(", ", $columns);
        
        try {
            $stmt = $pdo->prepare("SELECT $columnList FROM $table");
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error fetching data from $table: " . $e->getMessage());
            echo "Error fetching data from $table: " . $e->getMessage();
            return false; // Return false if an error occurs
        }

        $usedImages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            foreach ($columns as $column) {
                if ($row[$column]) {
                    $usedImages[] = $row[$column];
                }
            }
        }

        // Get all files in the specified folder
        $files = glob($folder . '*');

        if (empty($files)) {
            return false; // No files found to delete
        }

        $deletedFilesCount = 0; // To track the number of deleted files

        foreach ($files as $file) {
            $fileName = basename($file);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Skip files with extensions in $skipExtensions array
            if (in_array($fileExtension, $skipExtensions)) {
                continue;
            }

            // Check if it's a file (not a directory)
            if (is_file($file)) {
                // Check if file is not in the database references
                if (!in_array($fileName, $usedImages)) {
                    if (unlink($file)) {
                        $deletedFilesCount++; // Increment counter on successful deletion
                        // error_log("Deleted: $fileName from $folder\n", './logfile.log');
                    } else {
                        error_log("Error deleting: $fileName from $folder\n", 3, './logfile.log');
                    }
                }
            }
        }

        return $deletedFilesCount > 0; // Return true if files were deleted, false otherwise
    }

    // Track if any files were deleted across all folders
    $anyFilesDeleted = false;

    // Delete unused files for channels
    if (deleteUnusedFiles($pdo, 'channels', ['channel_icon', 'channel_poster'], $channelFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }
    
    // Delete unused files for shows
    if (deleteUnusedFiles($pdo, 'shows', ['show_icon', 'show_poster'], $showsFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for radios
    if (deleteUnusedFiles($pdo, 'radios', ['radio_icon', 'radio_poster'], $radioFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for radio categories
    if (deleteUnusedFiles($pdo, 'radio_category', ['category_poster'], $radioCategoryFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for SOAPS
    if (deleteUnusedFiles($pdo, 'soaps', ['soap_icon', 'soap_poster'], $soapsFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for setbooks
    if (deleteUnusedFiles($pdo, 'setbooks', ['set_icon', 'set_poster'], $setbooksFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for news
    if (deleteUnusedFiles($pdo, 'news', ['source_icon', 'source_poster'], $newsFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Delete unused files for videos
    if (deleteUnusedFiles($pdo, 'videos', ['video_poster'], $videosFolder, $skipExtensions)) {
        $anyFilesDeleted = true;
    }

    // Output a message based on whether any files were deleted
    if ($anyFilesDeleted) {
        echo "Unused files have been deleted successfully.";
    } else {
        echo "No unused files found to delete.";
    }
?>