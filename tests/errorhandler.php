<?php
require_once "../vendor/autoload.php";

use Pehape\Services\CURLService;

$curl = new CURLService();
$curl->SetUrl("https://www.google.com/");
$curl->SetTimeOut(5);
$curl->Bind('OnProxy', function(){
  echo "Setting dummy proxy to produce error..." . PHP_EOL;
  return ["IP" => "127.0.0.1", "Port" => 8080];
});
$curl->Bind('OnError', function($message, $exception){
  echo $message . PHP_EOL;
  echo "Throwing exception..." . PHP_EOL;
  throw $exception;
});
$curl->Execute();
