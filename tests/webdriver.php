<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

use Pehape\Services\WebPageService;
use Facebook\WebDriver\WebDriverBy;

$driver = new WebPageService();
$driver->Chrome()->Create();
$driver->get('https://www.ipchicken.com/');
$driver->Bind('OnFindElement', function($el){
  echo $el->getText() . "\n";
});
$driver->findElement(WebDriverBy::tagName('b'));
$driver->quit();
