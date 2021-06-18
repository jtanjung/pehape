<?php namespace Pehape\Services;

use Pehape\Bases\BaseEventClass;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverPoint;
use Pehape\Configs\ProxyConfig;
use Pehape\Traits\HasProxyConfig;
use Pehape\Helpers\Util;
use Pehape\Configs\WebDriverConfig;
use Pehape\Constants\WebDriver;
use Pehape\Constants\Message;

/**
 * Class WebPageService
 * @package Pehape\Services
 */
class WebPageService extends BaseEventClass
{

    use HasProxyConfig;

    /**
     * Remote web capabilities
     * @var DesiredCapabilities
     */
    public $Capabilities;

    /**
     * Remote web driver host url
     * @var string
     */
    protected $host;

    /**
     * Determine whether the browser window will be hidden
     * @var boolean
     */
    protected $show_window = false;

    /**
     * Proxy plugin file name for chrome
     * @var string
     */
    private $plugin;

    /**
     * Connection and request timeout
     * @var int
     */
  	protected $timeout = 0;

    /**
     * Exception instance class name
     * @var string
     */
    protected $exception = "\\Pehape\\Exceptions\\WebDriverException";

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = new WebDriverConfig();
    }

    /**
     * Initiate chrome browser
     *
     * @return self
     */
    public function Chrome()
    {
        /** Check if the driver is running **/
        if (! Util::IsRunning(WebDriver::$CHROME)) {
          /** Run driver **/
          if (!Util::Run($this->config->Setting->chrome->command)) {
            throw new \RuntimeException(
                sprintf(Message::$CLI_FAILED, $this->config->Setting->chrome->command)
            );
          }
        }

        /** Initialize chrome capabilities **/
        $this->Capabilities = DesiredCapabilities::chrome();

        $options = new ChromeOptions();
        $this->plugin = false;

        $this->Proxy = $this->GetProxy();

        if ($this->Proxy instanceof ProxyConfig) {
          /** Initialize sys directories **/
          $tempdir = realpath( __DIR__ . '/../..' ) . "/dirs/temps/";
          $proxydir = realpath( __DIR__ . '/../..' ) . "/dirs/sys/var/proxy";
          $this->plugin = "$tempdir/proxy" . uniqid() . '.zip';

          /** Create proxy zip file **/
          $zip = new \ZipArchive();
          $res = $zip->open($this->plugin, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
          $zip->addFile("$proxydir/manifest.json", 'manifest.json');
          $background = file_get_contents("$proxydir/background.js");

          /** Assign proxy information **/
          $background = str_replace(
            ['%proxy_host', '%proxy_port', '%username', '%password'],
            [$this->Proxy->IP, $this->Proxy->Port, $this->Proxy->UserName, $this->Proxy->Password],
            $background
          );

          $zip->addFromString('background.js', $background);
          $zip->close();

          $options->addExtensions([$this->plugin]);
        }

        /** Set window visibility option **/
        if (! $this->show_window) {
          $options->addArguments(["--headless","--disable-gpu", "--no-sandbox", "--start-maximized"]);
        }
        else {
          $options->addArguments(["--start-maximized"]);
        }

        $this->Capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->host = $this->config->Setting->chrome->host;
        return $this;
    }

    /**
     * Initiate firefox browser
     *
     * @return self
     */
    public function FireFox()
    {
        /** Check if the driver is running **/
        if (! Util::IsRunning(WebDriver::$FIREFOX)) {
          /** Run driver **/
          if (!Util::Run($this->config->Setting->firefox->command)) {
            throw new \RuntimeException(
                sprintf(Message::$CLI_FAILED, $this->config->Setting->firefox->command)
            );
          }
        }

        /** Initialize firefox capabilities **/
        $this->Capabilities = DesiredCapabilities::firefox();

        $this->Proxy = $this->GetProxy();

        if ($this->Proxy instanceof ProxyConfig) {
          /** Set proxy config **/
          $proxy = [
            'proxyType' => $this->Proxy->Type,
            'httpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
            'sslProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
            'ftpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port
          ];
          /** Check if authentication info is present **/
          if ($this->Proxy->UserName && $this->Proxy->Password) {
            $proxy['socksUsername'] = $this->Proxy->UserName;
            $proxy['socksPassword'] = $this->Proxy->Password;
          }
          /** Assign proxy info **/
          $this->Capabilities = new DesiredCapabilities([
              WebDriverCapabilityType::BROWSER_NAME => 'firefox',
              WebDriverCapabilityType::PROXY => $proxy
          ]);
        }

        /** Set window visibility option **/
        if (! $this->show_window) {
          $this->Capabilities->setCapability(
              'moz:firefoxOptions',
             ['args' => ['-headless']]
          );
        }

        $this->host = $this->config->Setting->firefox->host;
        return $this;
    }

    /**
     * Initiate opera browser
     *
     * @return self
     */
    public function Opera()
    {
        /** Check if the driver is running **/
        if (! Util::IsRunning(WebDriver::$OPERA)) {
          /** Run driver **/
          if (!Util::Run($this->config->Setting->opera->command)) {
            throw new \RuntimeException(
                sprintf(Message::$CLI_FAILED, $this->config->Setting->opera->command)
            );
          }
        }

        /** Initialize firefox capabilities **/
        $this->Capabilities = DesiredCapabilities::opera();

        $this->Proxy = $this->GetProxy();

        if ($this->Proxy instanceof ProxyConfig) {
          /** Set proxy config **/
          $proxy = [
            'proxyType' => $this->Proxy->Type,
            'httpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
            'sslProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
            'ftpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port
          ];
          /** Check if authentication info is present **/
          if ($this->Proxy->UserName && $this->Proxy->Password) {
            $proxy['socksUsername'] = $this->Proxy->UserName;
            $proxy['socksPassword'] = $this->Proxy->Password;
          }
          /** Assign proxy info **/
          $this->Capabilities = new DesiredCapabilities([
              WebDriverCapabilityType::BROWSER_NAME => 'opera',
              WebDriverCapabilityType::PROXY => $proxy
          ]);
        }

        /** Set window visibility option **/
        if (! $this->show_window) {
        }

        $this->host = $this->config->Setting->opera->host;
        return $this;
    }

    /**
     * Set hidden state
     *
     * @param bool $value
     * @return self
     */
    public function Window(bool $value)
    {
        $this->show_window = $value;
        return $this;
    }

    /**
     * Set connection and request timeout
     *
     * @param int $value
     * @return self
     */
    public function SetTimeOut(int $value)
    {
        $this->timeout = $value;
        return $this;
    }

    /**
     * Create remote web browser
     *
     * @return self
     */
    public function Create()
    {
        // Notify user callback
        static::__trigger('OnPrepare');
        // Safe create web driver
        $create = $this->__safeCall(function(){
          if ($this->timeout > 0) {
            $this->instance = RemoteWebDriver::create(
              $this->host,
              $this->Capabilities,
              $this->timeout * 1000,
              $this->timeout * 1000
            );
          }
          else {
            $this->instance = RemoteWebDriver::create($this->host, $this->Capabilities);
          }
        });

        // Check for web driver existance
        if (! $create) {
          $this->quit();
        }

        return $this;
    }

    /**
     * Quit remote web browser
     *
     * @return void
     */
    public function quit()
    {
        /** Quit remote browser instance **/
        if ($this->instance) {
          $this->instance->quit();
          $this->instance = null;
        }

        /** Remove proxy plugin temp file **/
        if ($this->plugin) {
          if (file_exists($this->plugin)) {
            unlink($this->plugin);
          }
          $this->plugin = false;
        }

        // Notify user callback
        static::__trigger('OnClose');
    }

    /**
     * Get random element of a web page
     *
     * @param WebDriverElement $value
     * @return WebDriverElement
     */
  	public function RandomElement(WebDriverElement $value = null)
  	{
        // Get a random selector
        $index    = array_rand(WebDriver::$selectors);
        $selector = WebDriver::$selectors[$index];
        // Set the xpath selector
        $selector = '//' . $selector . '[@id!=""]';
        // Find the specific element
        $elements = $this->instance->findElements(WebDriverBy::xpath($selector));
        $element  = false;
        // Get the selected element
        $element = !count($elements) ? $this->RandomElement($value) : $elements[0];
        // Return the result if the paramater is not present
        if (! $value instanceof WebDriverElement) {
          return $element;
        }
        // Check if the element is unique
        if ($value->getTagName() == $element->getTagName() &&
            $value->getID() == $element->getID()) {
            $element = $this->RandomElement($value);
        }

        // Return the element
        return $element;
  	}

    /**
     * Mimic human mouse movement
     *
     * @param WebDriverElement $element
     * @return WebDriverElement
     */
    public function HumanMouseLike(WebDriverElement $element)
    {
        // Set the random coordinates count
        $count = rand(5, 15);
        // Prepare element buffer
        $elements = [];
        // Generate random coordinate
        while ($count > 0) {
          // Push a new element value to the buffer
          $elements[] = $this->RandomElement($element);
          // count decrement
          $count--;
        }

        // Push element to the buffer
        $elements[] = $element;
        // Move the mouse to each coordinates
        foreach ($elements as $elm) {
          // Scroll to the element position
          $this->instance->executeScript("arguments[0].scrollIntoView(true);", [$elm]);
          // Move the mouse pointer
          $this->instance->getMouse()->mouseMove($elm->getCoordinates());
          // Notify the event listener for the new coordinates
          static::__trigger('OnMouseMove', [$elm->getCoordinates()]);
        }

        // Return the element
        return $element;
    }

    /**
     * An alias for quit() method
     *
     * @return void
     */
    public function Close()
    {
        $this->quit();
    }

}
