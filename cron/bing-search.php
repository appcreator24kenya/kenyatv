<?php
    include __DIR__ .'/../config/config.php';

    // Define your host, key, and key location
    $host = 'kenyalivetv.co.ke';
    $key = 'dcb5febaf1fb48f7af3fcf4c923aaabe';
    $keyLocation = BASE_URL . '/'.$key.'.txt';
    $sitemapFile = BASE_ROOT_PATH . 'sitemap.xml';

    // Check if the sitemap file exists
    if (!file_exists($sitemapFile)) {
        die('Error: sitemap.xml file not found at ' . $sitemapFile);
    }

    // Load the sitemap file
    $sitemapContent = file_get_contents($sitemapFile);
    if ($sitemapContent === false) {
        die('Error: Could not read sitemap.xml');
    }

    // Debug: Output the sitemap content
    // echo 'Sitemap content fetched successfully.<br>';
    // echo '<pre>' . htmlspecialchars($sitemapContent) . '</pre>'; // Debug: output sitemap content

    // Parse the XML content
    $xml = simplexml_load_string($sitemapContent);
    if ($xml === false) {
        die('Error: Could not parse sitemap.xml');
    }

    // Extract URLs from the sitemap
    $urlList = [];
    foreach ($xml->url as $url) {
        $urlList[] = (string)$url->loc;
    }

    // Debug: Output the extracted URLs
    // echo 'Extracted URLs:<br>';
    // echo '<pre>' . print_r($urlList, true) . '</pre>';

    // Prepare the data
    $data = [
        'host' => $host,
        'key' => $key,
        'keyLocation' => $keyLocation,
        'urlList' => $urlList
    ];

    // Convert the data to JSON
    $jsonData = json_encode($data);

    // Initialize cURL to send the JSON data to IndexNow
    $ch = curl_init('https://api.indexnow.org/indexnow');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        // Print the response
        echo 'Bing Url Submission Response: ' . $response;
    }

    // Close cURL
    curl_close($ch);

?>