<?php
define('FACEBOOK_APP_ID', '142993149051614');
define('FACEBOOK_APP_SECRET', '50fd55bb8b671e105380a98a7cf7936d');
define('BASE_URL', 'http://gme3ujaa.joyent.us/facade66/');

$scope = "publish_stream,user_photos,friends_photos,user_status,friends_status,user_videos,friends_videos";
require('facebook-php-sdk/src/facebook.php');

Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;

?>