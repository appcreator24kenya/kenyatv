<?php
    include __DIR__ . '/../config/db.php';
    
    try {
        // 1. Reset all show views to 0
        $resetViewsSql = "UPDATE `kenyali1_kenya_live_tv`.`shows` SET views = 0";
        $pdo->exec($resetViewsSql);
    
        // 2. Update total_shows_count for radios (radio shows only)
        $updateRadioShowsSql = "
            UPDATE radios r
            JOIN (
                SELECT r.id AS radio_id, COUNT(DISTINCT sh.id) AS total_shows
                FROM radios r
                JOIN schedules s ON r.id = s.station_id
                JOIN shows sh ON s.show_id = sh.id
                WHERE sh.type = 'radio' AND s.approved = 1
                GROUP BY r.id
            ) AS radio_counts ON r.id = radio_counts.radio_id
            SET r.total_shows_count = radio_counts.total_shows
        ";
        $pdo->exec($updateRadioShowsSql);
    
        // 3. Update total_shows_count for channels (TV shows only)
        $updateTVShowsSql = "
            UPDATE channels c
            JOIN (
                SELECT c.id AS channel_id, COUNT(DISTINCT sh.id) AS total_shows
                FROM channels c
                JOIN schedules s ON c.id = s.station_id
                JOIN shows sh ON s.show_id = sh.id
                WHERE sh.type = 'tv' AND s.approved = 1
                GROUP BY c.id
            ) AS tv_counts ON c.id = tv_counts.channel_id
            SET c.total_shows_count = tv_counts.total_shows
        ";
        $pdo->exec($updateTVShowsSql);

        // 4. Reset all radio_category counts to 0
        $resetCategorySql = "UPDATE radio_category SET total_radios = 0";
        $pdo->exec($resetCategorySql);

        // 5. Update total_radios per category based on radios table
        $updateCategorySql = "
            UPDATE radio_category rc
            JOIN (
                SELECT category_id, COUNT(*) AS total
                FROM radios
                GROUP BY category_id
            ) r ON rc.id = r.category_id
            SET rc.total_radios = r.total
        ";
        $pdo->exec($updateCategorySql);
    
        echo "✅ Views reset, radio and TV total shows count updated, and radio categories total updated successfully.";
    
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
?>