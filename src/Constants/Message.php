<?php namespace Pehape\Constants;

/**
 * Class Message
 * @package Pehape\Constants
 */
class Message {

    /**
     * Message for undefined class
     */
    public static $NO_CLASS = "Class '%s' not found";

    /**
     * Message for undefined method
     */
    public static $NO_METHOD = "Call to undefined method %s::%s()";

    /**
     * Message for undefined config
     */
    public static $NO_CONFIG = "Configuration for '%s' has not been defined.";

    /**
     * Message for invalid argument
     */
    public static $INVALID_ARG = "Invalid argument.";

    /**
     * Message for class method not exists
     */
    public static $METHOD_NOT_EXISTS = "Method %s::%s() does not exists.";

    /**
     * Message for private method
     */
    public static $PRIVATE_METHOD = "Call to private method %s::%s() from context";

    /**
     * Message for protected method
     */
    public static $PROTECT_METHOD = "Call to protected method %s::%s() from context";

    /**
     * Message when config file not found
     */
    public static $NO_CONFIG_FILE = "Configuration file '%s' not found. Please make sure the file is exists.";

    /**
     * Message when passed param to Pehape\Bases\BaseEventClass::OnEvent() invalid
     */
    public static $NOT_EVENT = "Invalid argument type for event listener.";

    /**
     * CLI command failure message
     */
    public static $CLI_FAILED = "CLI command '%s' execution failed.";

}
