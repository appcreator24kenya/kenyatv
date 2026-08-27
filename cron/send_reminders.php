<?php

    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../data/reminder_debug.log');

    $dataDir = __DIR__ . '/../data';

    // Get all user files
    $files = glob($dataDir . '/user_*.json');

    if (!$files) {
        // error_log("❌ No user reminder files found.");
        exit("No user reminder files.");
    }

    foreach ($files as $file) {

        // error_log("📂 Processing file: {$file}");

        $userData = json_decode(file_get_contents($file), true);

        if (!$userData || empty($userData['subscriberId'])) {
            // error_log("⚠️ Invalid user data in file: {$file}");
            continue;
        }

        $subscriberId = $userData['subscriberId'];
        $timezone = $userData['timezone'] ?? 'Africa/Nairobi';
        $notifyBefore = isset($userData['notify_before']) ? (int)$userData['notify_before'] : 5;

        // error_log("👤 User: {$subscriberId} | TZ: {$timezone} | NotifyBefore: {$notifyBefore}");

        // Set user's timezone
        date_default_timezone_set($timezone);

        $currentDay = date('l');
        $currentTimestamp = time();
        $todayDate = date('Y-m-d');

        // error_log("🕒 Now: " . date('Y-m-d H:i:s', $currentTimestamp) . " | Day: {$currentDay}");

        // Combine TV + Radio
        $tvReminders = $userData['tv'] ?? [];
        $radioReminders = $userData['radio'] ?? [];

        $allReminders = array_merge($tvReminders, $radioReminders);

        $updatedTV = [];
        $updatedRadio = [];

        foreach ($allReminders as $reminder) {

            // error_log("➡️ Checking show: " . ($reminder['showName'] ?? 'Unknown'));

            // Validate required fields
            if (
                empty($reminder['scheduleId']) ||
                empty($reminder['startTime']) ||
                empty($reminder['showScheduleDay'])
            ) {
                // error_log("⚠️ Invalid reminder structure, skipping.");
                storeBack($reminder, $updatedTV, $updatedRadio);
                continue;
            }

            // Skip if today is not in schedule
            if (!in_array($currentDay, $reminder['showScheduleDay'])) {
                // error_log("⏭️ Skipped (day mismatch)");
                storeBack($reminder, $updatedTV, $updatedRadio);
                continue;
            }

            // Build today's start time
            $startDateTime = strtotime($todayDate . ' ' . $reminder['startTime']);

            if (!$startDateTime) {
                // error_log("❌ Invalid start time for show: {$reminder['showName']}");
                storeBack($reminder, $updatedTV, $updatedRadio);
                continue;
            }

            $notifyTimestamp = strtotime("-{$notifyBefore} minutes", $startDateTime);

            // error_log("⏰ Start: " . date('H:i:s', $startDateTime) . " | Notify at: " . date('H:i:s', $notifyTimestamp));

            // Unique daily notification key
            $notifyKey = $todayDate . '_' . $reminder['scheduleId'];

            if (!isset($reminder['notified_log']) || !is_array($reminder['notified_log'])) {
                $reminder['notified_log'] = [];
            }

            if (in_array($notifyKey, $reminder['notified_log'])) {
                // error_log("✅ Already notified today.");
                storeBack($reminder, $updatedTV, $updatedRadio);
                continue;
            }

            // Send within 5-minute window
            if ($currentTimestamp >= $notifyTimestamp && $currentTimestamp <= ($notifyTimestamp + 300)) {

                // error_log("🚀 Sending notification...");

                $sent = sendPush($reminder, $subscriberId, $startDateTime);

                if ($sent) {
                    $reminder['notified_log'][] = $notifyKey;
                    // error_log("✅ Notification sent successfully.");
                } else {
                    // error_log("❌ Failed to send notification.");
                }
            } else {
                // error_log("⌛ Not yet time.");
            }

            storeBack($reminder, $updatedTV, $updatedRadio);
        }

        // Save updated data
        $userData['tv'] = $updatedTV;
        $userData['radio'] = $updatedRadio;
        $userData['updated_at'] = date('c');

        file_put_contents($file, json_encode($userData, JSON_PRETTY_PRINT));
    }


    /** Store reminder back correctly */
    function storeBack($reminder, &$tv, &$radio) {
        $type = $reminder['showType'] ?? 'tv';

        if ($type === 'radio') {
            $radio[] = $reminder;
        } else {
            $tv[] = $reminder;
        }
    }


    /** Send Push Notification with retry */
    function sendPush($reminder, $subscriberId, $startTimestamp) {

        $url = "https://api.webpushr.com/v1/notification/send/sid";

        $stationName = $reminder['stationName'] ?? 'Live Show';

        $targetUrl = !empty($reminder['targetPage'])
            ? $reminder['targetPage']
            : "https://kenyalivetv.co.ke";

        $image = !empty($reminder['showIcon'])
            ? $reminder['showIcon']
            : (!empty($reminder['showPoster'])
                ? $reminder['showPoster']
                : ($reminder['stationIcon'] ?? ''));

        $payload = [
            "title" => $reminder['showName'],
            "message" => "Starting soon on {$stationName} at " . date('h:i A', $startTimestamp),
            "target_url" => $targetUrl,
            "icon" => $reminder['stationIcon'] ?? '',
            "image" => $image,
            "sid" => $subscriberId
        ];

        // error_log("📦 Payload: " . json_encode($payload));

        $headers = [
            "Content-Type: application/json",
            "webpushrKey: 280213dae9998d0caddf22a58a43c9bf",
            "webpushrAuthToken: 119984"
        ];

        // 🔁 Try up to 3 times
        for ($i = 1; $i <= 3; $i++) {

            // error_log("🔁 Attempt {$i} for subscriber {$subscriberId}");

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);

            curl_close($ch);

            if ($error) {
                // error_log("❌ Curl error: {$error}");
                continue;
            }

            // error_log("📡 API Response: {$response}");

            $decoded = json_decode($response, true);

            if (isset($decoded['status']) && $decoded['status'] == 'success') {
                return true; // success
            }

            sleep(1); // wait before retry
        }

        return false; // failed after retries
    }

?>