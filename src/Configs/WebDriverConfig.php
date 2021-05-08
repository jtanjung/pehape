<?php
namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;
use Pehape\Models\Option;
use Pehape\Helpers\File;

/**
 * Class WebDriverConfig
 * @package Pehape\Configs
 */
class WebDriverConfig extends BaseConfig {

    /**
     * Web driver configuration list
     * @var Option
     */
  	public $Setting;

    /**
     * Class constructor
     *
     * @param array $attributes
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /** Create setting object **/
        $this->Setting = new Option();

        /** Define sys directories **/
        $configdir = realpath( __DIR__ . '/../..' ) . "/dirs/sys";
        $bindir = "$configdir/bin/";

        /** Load configuration from json file **/
        $configs = glob("$configdir/var/*.json", GLOB_BRACE);
        foreach ($configs as $config) {
          $key = File::BaseName($config);
          $setting = file_get_contents($config);
          $this->Setting->{$key} = json_decode($setting, true);

          /** Restructure cli command **/
          $command = $bindir . $this->Setting->{$key}->command;
          $this->Setting->{$key}->command = $command;
        }
    }


}
