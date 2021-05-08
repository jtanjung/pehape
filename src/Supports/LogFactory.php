<?php namespace Pehape\Supports;

use Pehape\Exceptions\LogException;

/**
 * Class LogFactory
 * @package Pehape\Supports
 */
class LogFactory {

    /**
     * Log file path
     * @var string
     */
  	public $FilePath;

    /**
     * Append/write log file
     *
     * @return self
     */
    public function Append( $content )
    {
      	if( ! $this->FilePath ){
      		throw new LogException("Log file has not been defined.");
      	}

      	$log_content = $content;
      	if( is_array( $log_content ) ){
      		$log_content = implode( ' | ', $log_content );
      	}

      	try
      	{

      		@file_put_contents( $this->FilePath, $log_content . PHP_EOL, FILE_APPEND );

      	} catch (\Exception $e) {
      		throw new LogException( $e->getMessage() );
      	}

      	return $this;
  	}

    /**
     * Write log start line
     *
     * @return self
     */
    public function Start()
    {
      	$start_line = "===================================LOG START===================================";
      	return $this->Append( $start_line );
  	}

    /**
     * Write log end line
     *
     * @return self
     */
    public function End()
    {
      	$end_line = "=================================== LOG END ===================================";
      	return $this->Append( $end_line );
  	}

}
