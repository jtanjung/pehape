<?php namespace Pehape\Services;

use Pehape\Bases\BaseEventClass;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Chrome\ChromeOptions;
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
          $tempdir = realpath( __DIR__ . '/..' ) . "/dirs/temps/";
          $proxydir = realpath( __DIR__ . '/..' ) . "/dirs/sys/var/proxy";
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
          $options->addArguments(["--headless","--disable-gpu", "--no-sandbox"]);
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
          /** Assign proxy info **/
          $this->Capabilities = new DesiredCapabilities([
              WebDriverCapabilityType::BROWSER_NAME => 'firefox',
              WebDriverCapabilityType::PROXY => [
                'proxyType' => 'manual',
                'httpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
                'sslProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
                'ftpProxy' => $this->Proxy->IP . ':' . $this->Proxy->Port,
                'socksUsername' => $this->Proxy->UserName,
                'socksPassword' => $this->Proxy->Password
              ]
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
     * Create remote web browser
     *
     * @return self
     */
    public function Create()
    {
        $this->instance = RemoteWebDriver::create($this->host, $this->Capabilities);
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
