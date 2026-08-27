<?php
    include __DIR__ .'/../config/config.php';

    // Select all rows from the sitemap table
    $query = $pdo->query('
        SELECT * 
        FROM sitemap 
        WHERE url_type IN ("General", "tv", "radio", "soaps", "radiocategory", "videos", "news", "tv_schedules")
        ORDER BY id ASC
    ');

    // Change the working directory to the correct location
    chdir(BASE_ROOT_PATH);


    // Generate the sitemap.xml file in the current directory
    $xmlFilePath = 'sitemap.xml'; // Save sitemap in current directory

    // Generate the sitemap.xml file
    $xml = new XMLWriter();
    $xml->openURI('sitemap.xml'); // Replace with your desired file path
    $xml->startDocument('1.0', 'UTF-8');
    $xml->startElement('urlset');
    $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    // Loop through each row and add it to the sitemap.xml file
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $xml->startElement('url');
    $xml->writeElement('loc', $row['url']);
    
    // Assuming your datetime variable is stored in $datetime_variable
    $date = new DateTime($row['last_modified']);
    $lastmod = $date->format('Y-m-d\TH:i:s+00:00');

    $xml->writeElement('lastmod', $lastmod);
    $xml->writeElement('changefreq', $row['change_frequency']);
    $xml->writeElement('priority', $row['priority']);
    $xml->endElement();
    }

    // Close the urlset element and the document
    $xml->endElement();
    $xml->endDocument();

    // Set the content type to XML
    header('Content-type: application/xml');

    // Output the sitemap.xml file
    readfile($xmlFilePath); // Replace with your desired file path
?>