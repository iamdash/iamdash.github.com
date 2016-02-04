<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
#require_once './lib/image_cacher.php';
#require_once './lib/phpflickr/phpFlickr.php';
require_once './lib/utils.php';
require_once './lib/fns.php';

$twitter = new Twitter('_iamdash',1);
$tweets = $twitter->getTweets();
d($tweets);
?>