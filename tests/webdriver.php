<?php
require_once "../vendor/autoload.php";

use Pehape\Bases\BaseStdClass;

class A extends BaseStdClass {

  protected function initLoad(){
    if (isset($this->_properties['@attributes'])) {
      $this->Set($this->_properties['@attributes']);
      unset($this->_properties['@attributes']);
    }
  }

  public function findNodeById(string $id)
  {
      foreach ($this->node as $value) {
        if ($value->id === $id) {
          return $value;
        }
      }
  }
}

$a = new A();
$a->loadXML('map.osm');
$ref = $a->way[0]->nd[0]->ref;
var_dump($a->findNodeById($ref));
exit;
ob_start(function($c){
  file_put_contents('map.json', $c);
});
var_dump($a->way);
ob_end_flush();

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
