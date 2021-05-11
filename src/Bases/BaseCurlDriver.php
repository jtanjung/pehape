<?php namespace Pehape\Bases;

use Pehape\Services\CURLService;
use Pehape\Traits\HasCURLService;
use Pehape\Traits\HasSeedLoader;

/**
 * Class BaseCurlDriver
 * @package Pehape\Bases
 */
abstract class BaseCurlDriver extends BaseDriver
{

    use HasCURLService, HasSeedLoader;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->instance = new CURLService();
    }

}
