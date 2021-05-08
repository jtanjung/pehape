<?php
namespace Pehape\Constants;

/**
 * Interface ResponseType
 * @package Pehape\Constants
 */
class ResponseType {

    /**
     * Treat response as plain text/html
     */
  	public static $PLAIN = "PLAIN";

    /**
     * Treat response as downloadable file
     */
  	public static $FILE = "FILE";

    /**
     * Treat response as stream
     */
  	public static $STREAM = "STREAM";

}
