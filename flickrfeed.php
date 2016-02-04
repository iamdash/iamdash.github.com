<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
require_once './lib/utils.php';
require_once './lib/fns.php';
require_once './lib/phpflickr/phpFlickr.php';

d(getFlickrFeed());
?>