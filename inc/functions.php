<?php
    // Function to get user IP
    function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    // Fetch live video ID from YouTube channel
    function getLiveVideoIdFromChannel($channelUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $channelUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // Fake user agent like a browser
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) 
            AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36");
    
        $html = curl_exec($ch);
        curl_close($ch);
    
        if (!$html) {
            return null;
        }
    
        // Find first "watch?v=VIDEOID"
        if (preg_match('/watch\?v=([a-zA-Z0-9_-]{11})/', $html, $matches)) {
            return $matches[1];
        }
    
        return null;
    }
    
    function getChatEmbedUrl($chat_provider, $channelUrl) {
        $domain = $_SERVER['HTTP_HOST'];
    
        switch ($chat_provider) {
            case 'youtube':
                $videoId = getLiveVideoIdFromChannel($channelUrl);
                if ($videoId) {
                    return "https://www.youtube.com/live_chat?v={$videoId}&embed_domain={$domain}";
                }
                return null;
    
            // Add other providers in the future
            case 'twitch':
                // Example placeholder
                return "https://www.twitch.tv/embed/CHANNEL_NAME/chat?parent={$domain}";
    
            default:
                return null;
        }
    }
    
    // Function to get user's timezone (fallback to Nairobi if not available)
    function GetUserIPInfo() {
        // $apiUrl = 'https://ipapi.co/json/';
        // $apiUrl = 'https://ipwho.is';
        $apiUrl = 'https://api.ipapi.is/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('GetUserIPInfo error: ' . curl_error($ch)); // Log it
            return [];
        }
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);

        return $decodedResponse;
    }
    // Get user timezone using IP geolocation
    function getUserTimezone() {
        // 1. Check if timezone cookie exists (set by JS earlier)
        if (!empty($_COOKIE['timezone'])) {
            return $_COOKIE['timezone'];
        }

        // 2. Try fetching from IP API
        // $ipInfo = GetUserIPInfo(); // Your existing IP API + caching function
        // if (!empty($ipInfo['location']['timezone'])) {
        //     return $ipInfo['location']['timezone'];
        // }

        // 3. Final fallback
        return 'Africa/Nairobi';
    }

    // Convert time from UTC to local timezone
    function convertFromUTC($time, $timezone = null) {
        if ($timezone === null) {
            $timezone = getUserTimezone(); // Get timezone from IP
        }

        try {
            $dateTime = new DateTime($time, new DateTimeZone('UTC')); // UTC source
            $dateTime->setTimezone(new DateTimeZone($timezone));
            return $dateTime->format('H:i:s'); // Local time
        } catch (Exception $e) {
            error_log("Timezone conversion error: " . $e->getMessage());
            return $time; // Return original time on error
        }
    }
    
    
    // format number 1k , 2M ...
    function formatNumber($number) {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 3) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        } else {
            return $number;
        }
    }
    
    // set time ago eg. 1 min ago , 2 days ago ...
    function timeAgo($timestamp) {
        // Convert timestamp to Unix time
        $eventTime = strtotime($timestamp);
        if (!$eventTime) {
            return 'Invalid timestamp';
        }
    
        // Calculate the time difference in seconds
        $timeDifference = time() - $eventTime;
    
        // If the event happened in the future or less than 1 second ago
        if ($timeDifference < 1) {
            return 'just now';
        }
    
        // Define time units in seconds
        $units = [
            31536000 => 'year',
            2592000  => 'month',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second',
        ];
    
        // Loop through each unit to find the appropriate time difference
        foreach ($units as $secs => $unit) {
            if ($timeDifference >= $secs) {
                $value = floor($timeDifference / $secs);
                return $value . ' ' . $unit . ($value > 1 ? 's' : '') . ' ago';
            }
        }
    }

    // convert to slug
    function convertToSlug($string) {

        $string = str_replace(':', '', $string);
        
        // $string = str_replace("'", '', $string);
        
        // Convert to lowercase
        $string = strtolower($string);
        
        // Remove special characters and replace spaces with hyphens
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);  // Remove unwanted characters
        $string = preg_replace('/\s+/', ' ', $string);           // Replace multiple spaces with a single space
        $string = preg_replace('/\s/', '-', $string);            // Replace spaces with hyphens
        
        // Trim hyphens from the beginning and end of the string
        $string = trim($string, '-');
        
        return $string;
    }    

    // Adsense auto ads insertion
    function getAdsenseAd($adType, $adFormat = 'auto') {
        // Check if ads should be displayed
        if (defined('ADS_ENVIRONMENT') && ADS_ENVIRONMENT === 'testing') {
            // Return a placeholder or empty string if in testing environment
            return "<!-- Ads are disabled in testing environment -->";
        }
        // Replace this with your actual AdSense client ID
        $adsenseClient = 'ca-pub-5305283315141425';
    
        // Define ad unit IDs for different ad types
        $adUnitIds = [
            'square' => '9533462470',
            'horizontal' => '9533462470',
            'vertical' => '7220449895',
            'in_feed' => '5512185355'
        ];
        
        // Layout key for fluid ads (specific for in_feed)
        $adLayoutKey = '-hx+b+2-9h+hu'; 
    
        // Initialize an empty variable for the ad code
        $adCode = '';
    
        // Use if-else to check the ad type and generate the respective ad code
        if ($adType === 'square') {
            $adUnitId = $adUnitIds['square'];
            $adCode = "
            <ins class='adsbygoogle'
                 style='display:block'
                 data-ad-client='{$adsenseClient}'
                 data-ad-slot='{$adUnitId}'
                 data-ad-format='{$adFormat}'></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>";
        } elseif ($adType === 'horizontal') {
            $adUnitId = $adUnitIds['horizontal'];
            $adCode = "
            <ins class='adsbygoogle'
                 style='display:block'
                 data-ad-client='{$adsenseClient}'
                 data-ad-slot='{$adUnitId}'
                 data-ad-format='{$adFormat}'
                 data-full-width-responsive='true'></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>";
        } elseif ($adType === 'vertical') {
            $adUnitId = $adUnitIds['vertical'];
            $adCode = "
            <ins class='adsbygoogle'
                 style='display:block'
                 data-ad-client='{$adsenseClient}'
                 data-ad-slot='{$adUnitId}'
                 data-ad-format='autorelaxed'
                 data-full-width-responsive='true'></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>";
        } elseif ($adType === 'in_feed') {
            $adUnitId = $adUnitIds['in_feed'];
            $adCode = "
            <ins class='adsbygoogle'
                 style='display:block'
                 data-ad-client='{$adsenseClient}'
                 data-ad-slot='{$adUnitId}'
                 data-ad-layout-key='{$adLayoutKey}'
                 data-ad-format='fluid'
                 data-full-width-responsive='true'></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>";
        } else {
            return "Invalid ad type specified.";
        }
    
        // Return the generated ad code
        return $adCode;
    }
    // Example usage:
    //echo getAdsenseAd('square', 'auto');
    //echo getAdsenseAd('horizontal', 'auto');
    //echo getAdsenseAd('vertical', 'auto');
    //echo getAdsenseAd('in_feed', 'fluid');
?>