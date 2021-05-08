<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

use Pehape\Services\CURLService;

$curl = new CURLService();

/********************************BROWSE PAGE**********************************/
//You can assign proxy information directly to the curl service
//$curl->SetProxy('176.113.73.101', 3128);

//Or you can provide the proxy information when needed. The return value from
//"OnProxy" event listener will be used as proxy information.
//$curl->Bind('OnProxy', function(){
  //Return value should be an array or Pehape\Configs\ProxyConfig
//  return ["IP" => "127.0.0.1", "Port" => 8080];
  //Return value also could be look like this:
  //return new ProxyConfig(["IP" => "127.0.0.1", "Port" => 8080]);
//});
//$curl->SetUrl("https://www.ipchicken.com/");
//$curl->Execute();
//echo $curl->GetResponse() . "\n";
//return;

/*******************************DOWNLOAD FILE*********************************/
$curl->SetUrl("https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb");
//Progress listener
$curl->Bind('OnProgress', function(){
  $args = func_get_args();
  if ($args[0]->RatioReceived > 0) {
    echo "\r" . $args[0]->RatioReceived . '%(' . $args[0]->BytesReceived . '/' . $args[0]->BytesReceivedTotal . ')';
  }
});

set_time_limit(0);
echo "Downloading, please wait...\n";
$curl->Download(realpath('../dirs/temps') . '/google-chrome.deb');
echo "\n";
