<?php
require_once "../vendor/autoload.php";

use Pehape\Services\WebPageService;
use Facebook\WebDriver\WebDriverBy;

$driver = new WebPageService();
$driver->Bind('OnFindElement', function($el){
  // "alt" attribute of google doodle
  echo "Today Doodle = '" . $el->getAttribute('alt') . "'\n";
});
// Error handler
$driver->Bind('OnError', function($message, $exception){
  echo $message . PHP_EOL;
  echo "Throwing exception..." . PHP_EOL;
  throw $exception;
});
$driver->Bind('OnPrepare', function(){
  echo "Preparing request... \n";
});
$driver->Bind('OnClose', function(){
  echo "Request complete... \n";
});
$driver->Chrome()->Create();
$driver->get('https://www.google.com/');
$driver->findElement(WebDriverBy::tagName('img'));
$driver->quit();
