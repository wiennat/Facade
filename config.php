<?php
define('FACEBOOK_APP_ID', '');
define('FACEBOOK_APP_SECRET', '');
define('BASE_URL', '');

$scope = "publish_stream,user_photos,friends_photos,user_status,friends_status,user_videos,friends_videos";
require('facebook-php-sdk/src/facebook.php');

Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;

?>