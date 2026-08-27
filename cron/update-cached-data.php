<?php
    include __DIR__ .'/../config/config.php';
    include __DIR__ .'/../inc/fetch_data.php';

    // Call the fetch and cache function for TV list
    fetchTVList();
    fetchNewTVList();
    fetchMostWatchedTV();
    fetchRatedTV();
    fetchAllTVSchedule();
    fetchMostViewedShowsByType('tv');


    // call radio functions
    fetchRadioList();
    fetchNewRadioList();
    fetchMostListenedRadios();
    fetchRatedRadios();
    fetchRadioCategories();
    fetchAllRadioSchedule();
    fetchMostViewedShowsByType('radio');
    fetchAllRadioReviewsData();

    // call soaps fuctions
    fetchAllSoaps();
    fetchMostReadSoaps();
    fetchNewAddedSoaps();

    // call news function
    fetchNewsList();

    //call videos function
    fetchAllVideos();

    // call setbooks functions
    fetchAllSetbooks();
    fetchMostReadSetbooks();
    
    // call db table counts
    fetchCountRowsInAllTables();



    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/cron/sitemap_script.php
    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/cron/bing-search.php
    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/cron/update-cached-data.php
    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/cron/reset_shows_views.php
    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/cron/update-show-images.php
    // /usr/bin/php -q /var/www/e7fdaef1-1a53-48b8-99e9-04008301f8ab/public_html/tv/submit_tv_rating.php?update_all


    // /usr/local/bin/php /home/kenyali1/public_html/cron/sitemap_script.php
    // /usr/local/bin/php /home/kenyali1/public_html/cron/bing-search.php
    // /usr/local/bin/php /home/kenyali1/public_html/cron/update-show-images.php
    // /usr/local/bin/php /home/kenyali1/public_html/cron/update-cached-data.php
    // /usr/local/bin/php /home/kenyali1/public_html/tv/submit_tv_rating.php?update_all
?>