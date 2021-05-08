<?php namespace Pehape\Services;

use Pehape\Bases\BaseEventClass;
use Pehape\Configs\CURLConfig;
use Pehape\Configs\ProxyConfig;
use Pehape\Configs\AuthConfig;
use Pehape\Models\RequestInfo;
use Pehape\Constants\ResponseCode;
use Pehape\Constants\RequestType;
use Pehape\Constants\CURLMessage;
use Pehape\Helpers\URL;
use Pehape\Helpers\Util;
use Pehape\Exceptions\CURLException;
use Pehape\Traits\HasProxyConfig;
use Pehape\Traits\HasAuthConfig;
use Pehape\Models\RequestProgress;

/**
 * Class CURLService
 * @package Pehape\Services
 */
class CURLService extends BaseEventClass
{

    use HasProxyConfig, HasAuthConfig;

    /**
     * CURL Handler
     * @var int
     */
    protected $ch;

    /**
     * File Handler
     * @var int
     */
    protected $fh;

    /**
     * CURL response
     * @var mixed
     */
    protected $response;

    /**
     * CURL response code
     * @var int
     */
    protected $response_code;

    /**
     * CURL HTTP code
     * @var int
     */
    protected $http_code;

    /**
     * CURL error number
     * @var int
     */
    protected $error_number;

    /**
     * CURL error message
     * @var string
     */
    protected $error_string;

    /**
     * CURL request type
     * @var string
     */
    protected $request_type;

    /**
     * CURL request query
     * @var string
     */
    protected $query;

    /**
     * Request id to enable progress handler
     * @var string
     */
    protected $request_id;

    /**
     * Used to store request progress up/down
     * @var RequestProgress
     */
    protected $progress;

    /**
     * Verbose log file name
     * @var string
     */
    protected $verbos_log_file;

    /**
     * Cookie file name
     * @var string
     */
    protected $cookie_file;

    /**
     * Selected user agent
     * @var string
     */
  	protected $userAgent;

    /**
     * List of available user agents
     * @var array
     */
  	protected $agents = array(
  		"Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0",
  		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0",
  		"Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0",
  		"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393",
      "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)",
      "Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)",
      "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",
      "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)",
      "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0; MDDCJS)",
      "Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko",
  	);

    /**
     * Default CURL options
     * @var array
     */
    protected $default_options = array(
        "CURLOPT_URL" => null,
        "CURLOPT_SSL_VERIFYPEER" => 0,
        "CURLOPT_FAILONERROR" => 1,
        "CURLOPT_FOLLOWLOCATION" => 1,
        "CURLOPT_MAXREDIRS" => 5,
        "CURLOPT_RETURNTRANSFER" => 1,
        "CURLOPT_TIMEOUT" => 30,
        "CURLOPT_HEADER" => 0,
        "CURLOPT_IPRESOLVE" => CURL_IPRESOLVE_V4
    );

    /**
     * CURL options
     * @var array
     */
    protected $options;

    /**
     * Number of re-initialization when request failed
     * @var int
     */
    protected $max_retry = 0;

    /**
     * Class constructor
     *
     * @param CURLConfig $config
     */
    public function __construct(CURLConfig $config = null)
    {
        parent::__construct($config);
        $this->ch = $this->verbos_log_file = false;
        $this->reset();
    }

    /**
     * CURL initialization
     */
    protected function initCURL()
    {
        $this->Close();
        $this->ch = curl_init();

        $this->userAgent = $this->agents[array_rand($this->agents)];
        curl_setopt($this->ch, CURLOPT_USERAGENT,   $this->userAgent);

        switch ($this->request_type) {
            case RequestType::$POST:
                curl_setopt($this->ch, CURLOPT_POST, 1);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->query);
                break;
            case RequestType::$CUSTOM:
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->query);
                break;
            case RequestType::$NULL:
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->query);
                break;
            default:
                $url = URL::PlainURL(@$this->options[ "CURLOPT_URL" ]);
                $query = URL::QueryString(@$this->options[ "CURLOPT_URL" ]);

                if ($query) {
                    $query_str = is_array($this->query) ? $this->query : array();
                    $query_str = array_merge($query, $query_str);
                    $this->options[ "CURLOPT_URL" ] = $url . '?' . http_build_query($query_str);
                }

                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
        }

        foreach ($this->options as $key => $val) {
            curl_setopt($this->ch, constant($key), $val);
        }

        if ($this->Auth instanceof AuthConfig) {
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->Auth->UserName . ":" . $this->Auth->Password);
        }

        $this->Proxy = $this->GetProxy();

        if ($this->Proxy instanceof ProxyConfig) {
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->Proxy->Port);
            curl_setopt($this->ch, CURLOPT_PROXY, $this->Proxy->IP);
            curl_setopt($this->ch, CURLOPT_PROXYTYPE, $this->Proxy->Type);

            if($this->Proxy->UserName){
              curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->Proxy->UserName . ":" . $this->Proxy->Password);
            }
        }

        curl_setopt($this->ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($this->ch, CURLOPT_PROGRESSFUNCTION, array($this, 'Progress'));

        if ($this->config instanceof CURLConfig && $this->request_id) {
            $this->cookie_file = $this->config->GetCookieFilePath($this->request_id);
            if ($this->cookie_file) {
                curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie_file);
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie_file);
            }

            $this->verbos_log_file = $this->config->GetLogFilePath($this->request_id);
            if( $this->verbos_log_file ){
              $this->fh = fopen($this->verbos_log_file, 'a');
              curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
              curl_setopt($this->ch, CURLOPT_STDERR, $this->fh);
            }
        }

    }

    /**
     * Change configuration
     *
     * @return void
     */
    protected function DoConfigChange()
    {
        $this->reset();
    }

    /**
     * Callback use to monnitor request progress
     *
     * @param int $total_bytes_down_expected,
     * @param int $bytes_down_so_far,
     * @param int $total_bytes_up_expected,
     * @param int $total_bytes_up_so_far
     */
    protected function Progress(
      $resources,
      $total_bytes_down_expected,
      $bytes_down_so_far,
      $total_bytes_up_expected,
      $total_bytes_up_so_far
    )
    {
        $down_ratio = $up_ratio = 0;
        if ($total_bytes_down_expected > 0) {
            $down_ratio = (float)round(($bytes_down_so_far / $total_bytes_down_expected) * 100);
        }
        if ($total_bytes_up_expected > 0) {
            $up_ratio = (float)round(($total_bytes_up_so_far / $total_bytes_up_expected) * 100);
        }

        $this->progress = new RequestProgress(array(
          "BytesReceived" => $bytes_down_so_far,
          "BytesReceivedTotal" => $total_bytes_down_expected,
          "BytesSent" => $total_bytes_up_so_far,
          "BytesSentTotal" => $total_bytes_up_expected,
          "RatioReceived" => $down_ratio,
          "RatioSent" => $up_ratio
        ));

        $this->__trigger("OnProgress", [$this->progress]);
    }

    /**
     * Execute POST request
     *
     * @param array|object $posts
     * @return self
     */
    public function Post($value)
    {
        return $this->SetPostField($value)->Execute();
    }

    /**
     * Execute GET request
     *
     * @param array|object $posts
     * @return self
     */
    public function Get($value)
    {
        $this->request_type = RequestType::$GET;
        return $this->SetQuery($value)->Execute();
    }

    /**
     * Set CURL options
     *
     * @param array $options
     * @return self
     */
    public function SetOptions($options = array())
    {
        if (empty($options)) {
            return $this;
        }

        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Set CURL request query
     *
     * @param mixed $posts
     * @return self
     */
    public function SetQuery($query)
    {
        $query_field = $query;

        if (is_object($query_field)) {
            $query_field = json_decode(json_encode($query_field), true);
        }

        $this->query = $query_field;
        return $this;
    }

    /**
     * Set CURL post data
     *
     * @param mixed $posts
     * @return self
     */
    public function SetPostField($posts)
    {
        $this->request_type = RequestType::$POST;
        return $this->SetQuery($posts);
    }

    /**
     * Set CURL request type
     *
     * @param string $value
     * @return self
     */
    public function SetRequestType($value)
    {
        $this->request_type = $value;
        return $this;
    }

    /**
     * Set URL target
     *
     * @param string $value
     * @return self
     */
    public function SetUrl(string $value)
    {
        $this->options[ "CURLOPT_URL" ] = $value;
        return $this;
    }

    /**
     * Set request timeout
     *
     * @param int $value
     * @return self
     */
    public function SetTimeOut(int $value)
    {
        $this->options[ "CURLOPT_TIMEOUT" ] = $value;
        return $this;
    }

    /**
     * Set request maximum retry
     *
     * @param int $value
     * @return self
     */
    public function SetMaxRetry(int $value)
    {
        $this->max_retry = $value;
        return $this;
    }

    /**
     * Set request unique id
     *
     * @param string $value
     * @return self
     */
    public function SetRequestId(string $value)
    {
        $this->request_id = $value;
        return $this;
    }

    /**
     * Reset curl parameters
     *
     * @return self
     */
    protected function reset()
    {
        $this->options = $this->default_options;
        $this->query = false;
        $this->request_id = null;
        $this->request_type = RequestType::$GET;
        if ($this->config instanceof CURLConfig) {
          $this->max_retry = $this->config->MaxRetry;
          $this->options[ "CURLOPT_TIMEOUT" ] = $this->config->TimeOut;
        }
        $this->options[ "CURLOPT_URL" ] = null;
        $this->Proxy = $this->Auth = null;

        return $this;
    }

    /**
     * Send CURL request
     *
     * @param string $url
     * @param int $timeout
     * @param int $max_retry
     * @return self
     */
    public function Execute()
    {
        if( ! $this->options[ "CURLOPT_URL" ] ){
          throw new CURLException(CURLMessage::$INVALID_URL);
        }

        if($this->request_id === null){
          $this->request_id = md5(Util::RandomString());
        }

        try {
            $this->initCURL();
            $this->response = curl_exec($this->ch);
            $this->response = trim($this->response);
            $this->http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            $this->error_number = curl_errno($this->ch);
            $this->error_string = curl_error($this->ch);
        } catch (\Exception $e) {
            $this->Close();
            throw new CURLException($e->getMessage());
        }

        $this->response_code = ResponseCode::$OK;

        if (empty($this->response)) {
            if ($this->max_retry > 0) {
                $this->max_retry = $this->max_retry - 1;
                return $this->Execute();
            }
            $this->response_code = ResponseCode::$NO_RESPONSE;
        }

        $this->reset()->Close();
        return $this;
    }

    /**
     * Download file
     *
     * @param string $destination
     * @return self
     */
    public function Download(string $destination)
    {
        try {
            $destination_file = fopen($destination, "wb");
            if ($destination_file) {
                $this->options[ "CURLOPT_FILE" ] = $destination_file;
                $this->options[ "CURLOPT_CONNECTTIMEOUT" ] = 0;
                $this->options[ "CURLOPT_TIMEOUT" ] = 0;
                $result = $this->Execute();
                fclose($destination_file);

                return $result;
            }

        } catch (\Exception $e) {
            $this->Close();
            throw new CURLException($e->getMessage());
        }

        return false;
    }

    /**
     * Get CURL response content
     *
     * @return string
     */
    public function GetResponse()
    {
        return $this->response;
    }

    /**
     * Get CURL response code
     *
     * @return int
     */
    public function GetResponseCode()
    {
        return $this->response_code;
    }

    /**
     * Get CURL http code
     *
     * @return int
     */
    public function GetHTTPCode()
    {
        return $this->http_code;
    }

    /**
     * Get CURL error code
     *
     * @return int
     */
    public function GetErrorCode()
    {
        return $this->error_number;
    }

    /**
     * Get CURL error string message
     *
     * @return string
     */
    public function GetErrorMessage()
    {
        return $this->error_string;
    }

    /**
     * Close CURL hanlder
     *
     * @return self
     */
    public function Close()
    {
        if ($this->ch) {
            curl_close($this->ch);
            $this->ch = false;
        }

        if ($this->fh) {
            fclose($this->fh);
            $this->fh = false;
        }

    }
}
