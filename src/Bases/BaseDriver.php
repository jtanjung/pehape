<?php namespace Pehape\Bases;

use Pehape\Traits\HasCache;
use Pehape\Configs\ProxyConfig;
use Pehape\Models\Option;
use Pehape\Helpers\File;

/**
 * Class BaseDriver
 * @package Pehape\Bases
 */
abstract class BaseDriver extends BaseEventClass
{

    use HasCache;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cache_dir = realpath( File::ClassPath($this) . '/../..' ) . "/cache/";

        /** Load and reconstruct cache object **/
        if ($this->LoadCache()->cache) {
          foreach ($this->cache as &$value) {
            $value = new ProxyConfig($value);
          }
        }
        else {
          $this->cache = new Option();
        }
    }

}
