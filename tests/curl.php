<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

use Pehape\Services\CURLService;

$curl = new CURLService();
//$curl->SetProxy('176.113.73.101', 3128);

//Browse URL
//$curl->SetUrl("https://www.ipchicken.com/");

//Download URL
$curl->SetUrl("https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb");

//Progress listener
$curl->Bind('OnProgress', function(){
  $args = func_get_args();
  if ($args[0]->RatioReceived > 0) {
    echo "\r" . $args[0]->RatioReceived . '%(' . $args[0]->BytesReceived . '/' . $args[0]->BytesReceivedTotal . ')';
  }
});

//Browse page
//$curl->Execute();
//echo $curl->GetResponse() . "\n";

//Download file
set_time_limit(0);
echo "Downloading, please wait...\n";
$curl->Download(realpath('../dirs/temps') . '/google-chrome.deb');
echo "\n";
