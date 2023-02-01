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
$driver->Bind('OnMouseMove', function($coordinate){
  $x = $coordinate->onPage()->getX();
  $y = $coordinate->onPage()->getY();
  echo "Mouse coordinate = $x, $y \n";
});
$driver->Chrome()->Create()->Window(false);
$driver->get('https://www.google.com/');
$doodle = $driver->findElement(WebDriverBy::tagName('img'));
$driver->HumanMouseLike($doodle);
$filename = realpath('../dirs/temps') . '/google.png';
$driver->takeScreenshot($filename);
$driver->quit();

