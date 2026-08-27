<?php
    // date_default_timezone_set("Africa/Nairobi");

    // Declare the global cache directory variable
    $cacheDir = BASE_ROOT_PATH .'cache/';
    // echo $cacheDir;
    $indexFile = $cacheDir . 'index.php';

    // Check if cache directory exists, if not, create it
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    // Fetch all db table counts or a specific table count
    function fetchCountRowsInAllTables($table = null) {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_db_table_counts.json';
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            $decodedData = json_decode($cachedData, true);
            // If a table name is passed, return that table's count
            if ($table && isset($decodedData[$table])) {
                return $decodedData[$table];
            }
            // Otherwise, return all counts
            return $decodedData;
        }

        $apiUrl = BASE_URL . '/api/general.php?api_key='.API_KEY.'&action=all_db_table_counts';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchCountRowsInAllTables error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty array on error
        }
        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        // If a table name is passed, return that table's count
        if ($table && isset($decodedResponse[$table])) {
            return $decodedResponse[$table];
        }

        // Otherwise, return all counts
        return $decodedResponse;
    }
    function fetchAllSchedules() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_schedules.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/general.php?api_key='.API_KEY.'&action=all_schedules';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllSchedules error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }

    // fetch tv channel , schedules & shows
    function fetchMostViewedShowsByType($station_type) {
        global $cacheDir;

        // Define API endpoints for TV and radio
        $apiEndpoints = [
            'tv' => BASE_URL . "/api/channels.php?api_key=".API_KEY."&action=most_watched_tv_shows",
            'radio' => BASE_URL . "/api/radios.php?api_key=".API_KEY."&action=most_listened_radio_shows",
        ];

        // Check if the station type is valid
        if (!isset($apiEndpoints[$station_type])) {
            return [
                'error' => true,
                'message' => "Invalid station type provided. Valid types are 'tv' or 'radio'.",
            ];
        }

        // Set cache file name based on station type
        $cacheFile = $cacheDir . "most_viewed_{$station_type}_shows.json";
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        // Get the API URL based on the station type
        $apiUrl = $apiEndpoints[$station_type];

        // Initialize cURL
        $ch = curl_init();

        // cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,         // API URL
            CURLOPT_RETURNTRANSFER => true, // Return the response as a string
            CURLOPT_TIMEOUT => 30,          // Timeout after 30 seconds
            CURLOPT_FAILONERROR => true,    // Fail on HTTP errors
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            return [
                'error' => true,
                'message' => "cURL Error: " . $errorMessage,
            ];
        }

        // Check the HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            curl_close($ch);
            return [
                'error' => true,
                'message' => "HTTP Error: Received response code $httpCode",
            ];
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => "JSON Decoding Error: " . json_last_error_msg(),
            ];
        }

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        // Return the decoded response
        return $decodedResponse;
    }

    function fetchTVList() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_channels.json';
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=all_channels';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchTVList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }

    function fetchTVScheduleById($channelId) {

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=get_schedule_for_channel&channel_id='.$channelId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchTVScheduleById error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        return $decodedResponse;
    }
    function fetchAllTVSchedule() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_tv_channels_schedules.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=all_tv_schedule';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllTVSchedule error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function updateActiveTVShowViews($showId) {
        $showId = urlencode($showId);
        
        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=update_tv_shows_views&show_id='.$showId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('updateActiveTVShowViews error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    function fetchNewTVList() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'new_channels.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=new_channels';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchNewTVList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    
    function fetchMostWatchedTV() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_watched_tv.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=most_watched';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchMostWatchedTV error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchRatedTV() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_rated_tv.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=most_rated';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRatedTV error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function storeTVViewsCount($channelId) {
        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=update_views&channel_id='.$channelId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeTVViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }
    function getRecentlyWatchedTV($channels) {
        if (!isset($_COOKIE['recently_listened_tv'])) {
            return [];
        }
        $recentlyWatched = json_decode($_COOKIE['recently_listened_tv'], true);

        // Reverse the recently listened array to get the most recent one first
        $recentlyWatched = array_reverse($recentlyWatched);

        return array_filter($channels, function($channel) use ($recentlyWatched) {
            return in_array($channel['id'], $recentlyWatched);
        });
    }
    function fetchAllTVReviewsData() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_tv_reviews.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/channels.php?api_key='.API_KEY.'&action=all_tv_reviews';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllTVReviewsData error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }


    // fetch_radio_list
    function fetchRadioList() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_radios.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=all_radios';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRadioList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchAllRadioSchedule() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_radio_stations_schedules.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=all_radio_schedule';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllRadioSchedule error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchAllRadioReviewsData() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_radio_reviews.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes
        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=all_radio_reviews';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllRadioReviewsData error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchNewRadioList() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'new_radios.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=new_radios';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchNewRadioList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchMostListenedRadios() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_listened.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=most_listened';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchMostListenedRadios error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchRatedRadios() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_rated_radios.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=most_rated';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRadioCategories error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchRadioCategories() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'radio_categories.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=radio_categories';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRadioCategories error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }

    function updateActiveRadioShowViews($showId) {
        $showId = urlencode($showId);
        
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=update_radio_shows_views&show_id='.$showId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('updateActiveRadioShowViews error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    function fetchRadioRatingsData($radioId) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=get_reviews&radio_id=' .$radioId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        return json_decode($response, true);
    }
    
    function fetchRadioReviewsData($radioId) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=get_reviews&radio_id=' .$radioId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRadioReviewsData error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    function storeRadioReviewsData($radioId, $rating, $userName, $userReview, $user_ip) {
        $user_review_encoded = $userReview;
        
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=store_reviews';
        
        // Data to be sent via POST
        $postData = [
            'radio_id' => $radioId,
            'rating' => $rating,
            'user_name' => $userName,
            'user_review' => $user_review_encoded,
            'user_ip' => $user_ip
        ];
        
        // Log post data for debugging
        // error_log('POST data: ' . print_r($postData, true));
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // Send data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeRadioReviewsData error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
    
        curl_close($ch);
    
        // Log the raw response for debugging
        // error_log('Fetch data API response: ' . $response);
    
        $responseDecoded = json_decode($response, true);
    
        if (isset($responseDecoded['error'])) {
            return $responseDecoded;
        }
    
        return $responseDecoded;
    }

   
    function submitOnlineViewer($item_name, $item_id, $user_ip) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=add_online_viewers';
    
        // Data to be sent via POST
        $postData = [
            'item_name' => $item_name,
            'item_id' => $item_id,
            'user_ip' => $user_ip
        ];
        
        // Log post data for debugging
        // error_log('POST data: ' . print_r($postData, true));
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData)); // Send data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('cURL error: ' . curl_error($ch));
            echo 'Error:' . curl_error($ch);
            return [];
        }
    
        curl_close($ch);
    
        // Log the raw response for debugging
        // error_log('Fetch data API response: ' . $response);
    
        $responseDecoded = json_decode($response, true);
    
        if (isset($responseDecoded['error'])) {
            return $responseDecoded;
        }
    
        return $responseDecoded;
    }
    
    function getOnlineViewers($item_name,$item_id) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=get_online_viewers&item_name=' . urlencode($item_name) . '&item_id=' . $item_id;
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('cURL error: ' . curl_error($ch));
            echo 'Error:' . curl_error($ch);
            return [];
        }
    
        curl_close($ch);
    
        // Log the raw response for debugging
        //error_log('API response: ' . $response);
    
        $responseDecoded = json_decode($response, true);
    
        if (isset($responseDecoded['error'])) {
            return $responseDecoded;
        }
    
        return $responseDecoded;
    }


    function fetchRadioById($radioId) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=get_radio&radio_id=' . $radioId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchRadioById error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        return json_decode($response, true);
    }
    function storeRadioViewsCount($radioId) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=update_views&radio_id='.$radioId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeRadioViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        return json_decode($response, true);
    }
    function storeRadioCategoryViewsCount($categoryId) {
        $apiUrl = BASE_URL .'/api/radios.php?api_key='.API_KEY.'&action=update_radio_category_views&category_id='.$categoryId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeRadioCategoryViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    // fetch videos
    function fetchAllVideos() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_videos.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/videos.php?api_key='.API_KEY.'&action=all_videos';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllVideos error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function storeVideoViewsCount($videoId) {
        $apiUrl = BASE_URL .'/api/videos.php?api_key='.API_KEY.'&action=update_views&video_id='.$videoId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeVideoViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    // fetch news sources
    function fetchNewsList() {
        global $cacheDir;
        
        $cacheFile = $cacheDir . 'all_news.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/news.php?api_key='.API_KEY.'&action=all_news';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchNewsList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }

    // fetch all authors
    function fetchSoapsAuthorsList() {
        global $cacheDir;
        
        $cacheFile = $cacheDir . 'all_soaps_authors'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/general.php?api_key='.API_KEY.'&action=all_soaps_authors';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchSoapsAuthorsList error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }

    // fetch setbooks 
    function fetchAllSetbooks() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_setbooks.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/setbooks.php?api_key='.API_KEY.'&action=all_setbooks';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllSetbooks error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchMostReadSetbooks() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_read_setbooks.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/setbooks.php?api_key='.API_KEY.'&action=most_read_setbooks';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchMostReadSetbooks error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function storeSetbooksViewsCount($setbookId) {
        $apiUrl = BASE_URL .'/api/setbooks.php?api_key='.API_KEY.'&action=update_views&setbook_id='.$setbookId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeSetbooksViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }

    
    // fetch soaps data 
    function fetchAllSoaps() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'all_soaps.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/soaps.php?api_key='.API_KEY.'&action=all_soaps';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchAllSoaps error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchMostReadSoaps() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'most_read_soaps.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/soaps.php?api_key='.API_KEY.'&action=most_read_soaps';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchMostReadSoaps error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    }
    function fetchNewAddedSoaps() {
        global $cacheDir;

        $cacheFile = $cacheDir . 'new_added_soaps.json'; // Cache file path
        $cacheLifetime = 300; // 5 minutes

        // Check if cache directory exists, if not create it
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // Check if cache file exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheLifetime) {
            // Read cached data
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        $apiUrl = BASE_URL .'/api/soaps.php?api_key='.API_KEY.'&action=new_added_soaps';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('fetchNewAddedSoaps error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        // Save the API response to cache
        if ($decodedResponse) {
            file_put_contents($cacheFile, $response);
        }

        return $decodedResponse;
    } 
    function storeSoapsViewsCount($soapId) {
        $apiUrl = BASE_URL .'/api/soaps.php?api_key='.API_KEY.'&action=update_views&soap_id='.$soapId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('storeSoapsViewsCount error: ' . curl_error($ch)); // Log it
            curl_close($ch);
            return []; // Return empty instead of echoing
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }
?>