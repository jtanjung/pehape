<?php namespace Pehape\Models;

use Pehape\Bases\BaseObject;

/**
 * Class EventCallback
 * @package Pehape\Models
 */
class EventCallback extends BaseObject {

    /**
     * Object context
     * @var object
     */
  	public $Context;

    /**
     * Object method to be called
     * @var function
     */
  	public $Method;

    /**
     * Function parameter/argument
     * @var array
     */
  	public $Argument = NULL;

}
