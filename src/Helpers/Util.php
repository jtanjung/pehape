<?php namespace Pehape\Helpers;

/**
 * Util
 * @package Pehape\Helpers
 */
class Util {

    /**
     * Get PHP version as integer
     * @return int
     */
  	public static function PHP_Version()
  	{
    		$php_version = str_replace( '.', '', PHP_VERSION );
    		return (int)$php_version;
  	}

    /**
     * Generate random string with specific length
     * @param int $length
     * @return string
     */
  	public static function RandomString( $length = 19 )
  	{
    		$characters = '0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$result = FALSE;

    		$i = 0;
    		while( $i < $length ) {
    			$result .= substr( $characters, mt_rand( 0, strlen( $characters ) - 1 ), 1 );
    			$i++;
    		}

    		return $result;
  	}

    /**
     * Format time value to string
     *
     * @param int $value
     * @param array $words
     * @param array $formats
     * @param int $precision
     * @return string
     */
  	public static function Elapsed_Format( $value, $words = array(), $precision = 1 )
  	{
    		$time = time() - $value;

    		if( floor( $time / 86400 ) == 1 ){
    			return @$words[ 'yesterday' ]; //yesterday
    		}

    		if( floor( $time / 86400 ) > 1 ){

    			$year = date( 'Y', $value );

    			if( $year == date( 'Y' ) ){
    				return date( @$words[ 'same_year' ], $value ); //d M
    			}

    			return date( @$words[ 'diff_year' ], $value ); //'d M Y'

    		}

    		$times[ @$words[ 'hours' ] ] = 3600;
    		$times[ @$words[ 'minutes' ] ] = 60;
    		$times[ @$words[ 'seconds' ] ] = 1;

    		$i = 0;
    		foreach( $times as $key => $val ){

    			$elapsed = floor( $time / $val );

    			if( $elapsed ){
    				$i++;
    			}

    			$time = $i >= $precision ? 0 : $time - $elapsed * $val;
    			$elapsed = $elapsed ? $elapsed . ' ' . $key . ' ' : '';
    			@$result .= $elapsed;

    		}

    		$result = strtolower( @$result );
    		return $result ? @sprintf( @$words[ 'elapsed' ], $result ) : @$words[ 'just_now' ];
  	}

    /**
     * Get OS environtment
     *
     * @return string
     */
  	public static function OS()
  	{
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'Windows' : PHP_OS;
  	}

    /**
     * Check if a process is running
     *
     * @param string $value
     * @return boolean
     */
  	public static function IsRunning($value)
  	{
    		exec("ps aux|grep '$value'| grep -v grep|awk '{print $1}'", $output, $return);
        return $return === 0 ? count($output) : false;
  	}

    /**
     * Run a background process
     *
     * @param string $value
     * @return boolean
     */
  	public static function Run($value)
  	{
        if (self::OS() === 'Windows') {
          // Run a windows process
          $command = strpos($value, ' >NUL') === false ? $value . ' >NUL 2>NUL' : $value;
          pclose(popen('start /B cmd /C "' . $command . '"', 'r'));
          return true;
        }

        // Run a linux process
        $command = strpos($value, ' > /dev/null') === false ? $value . ' > /dev/null 2>/dev/null &"' : $value;
        exec($command, $output, $return);
        return $return === 0;
  	}

}
