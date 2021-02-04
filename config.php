<?php
define('DB_DSN', '');
define('DB_PREFIX', 'spp_');

define('CLEAN_URIS', true);
define('PATH', '/dev/spp/z');
define('DIR', '/data/www/www.laacz.lv/dev/spp/z');

define('RECENT_POSTS', 5);

$time = mktime(20, 50, 35, 12, 7, 1980);
#strtotime('December 7, 1958, 8:50 PM, 35');

define('LOCALE', 'lv_LV'); 
define('DATE_FORMAT_LONG', '%d. %B %y, %H:%M');

define('CACHE_ENABLED', true);
define('CACHE_DIR', DIR . '/cache');
define('CACHE_TIMEOUT', 60 * 60 * 30);

define('COMMENTS_PREVIEW_REQUIRED', true);

define('COOKIES_EXPIRATION', 365 * 24 * 60 * 60); // approx 1 year
?>
