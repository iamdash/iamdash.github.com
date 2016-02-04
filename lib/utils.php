<?php

function d($a){
	echo '<pre style="font-family:\'Lucida Console\';font-size: 12px;padding: 20px;background: #fc0">'.print_r($a, true).'</pre>';
}

function obfuscateEmail($address){
	$link = 'mailto:' . $address;
     $obfuscatedLink = "";
     for ($i=0; $i<strlen($link); $i++){
         $obfuscatedLink .= "&#" . ord($link[$i]) . ";";
     }
     return  $obfuscatedLink;
}


function curl_download($Url){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
    // Now set some options (most are optional)
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // Download the given URL, and return output
    $output = curl_exec($ch);
    // Close the cURL resource, and free system resources
    curl_close($ch);
    return $output;
}

function make_cache_path($path) {
        //Test if path exist
        if (is_dir($path) || file_exists($path)) return;
        //No, create it
        mkdir($path, 0777, true);
    }