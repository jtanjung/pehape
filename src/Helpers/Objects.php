<?php namespace Pehape\Helpers;

use Pehape\Models\EventCallback;

/**
 * Interface Object
 * @package Pehape\Helpers
 */
class Objects {

    /**
     * Convert json object to array
     *
     * @param object $value
     * @return array
     */
    public static function JSONToArray( $value )
    {
      	$result = json_encode( $value );
      	return json_decode( $result, TRUE );
    }

    /**
     * Convert array to json object
     *
     * @param array $value
     * @return object
     */
    public static function ArrayToJSON( $value )
    {
      	return json_decode( json_encode( $value ) );
    }

    /**
     * Filter multidemsional array by value
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function FilterMultiArrayByValue( $array, $key, $value )
    {
      	return array_filter( $array, function( $element ) use ( $parameter ){
      		return ($element['key'] == $parameter['value'] );
      	});
    }

    /**
     * Check if an array is sequential(indexed)
     *
     * @param array $array
     * @return boolean
     */
    public static function IsSequentialIndexed( $array )
    {
        return ! is_array($array) ? false : array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Convert given value to an array or object
     *
     * @param mixed $value
     * @param bool $mode
     * @return mixed
     */
    public static function ToList($value, $mode = false)
    {
        if (! is_string($value)) {
          return $value;
        }

        /**
         * Get the first and last char to determine whether the value
         * is a valid JSON string.
         */
        $string = trim($value);
        $first = $string[0];
        $last = substr($string, -1);
        $bracket = $first . $last;

        /** Check if the first and last char are in "[],{}" **/
        if (in_array($bracket, ['[]', '{}'])) {
          return json_decode($string, $mode);
        }

        return $value;
    }

    /**
     * Trim single level array values
     *
     * @param array $array
     * @param string $character_mask
     * @return array
     */
    public static function TrimArray( $array, $character_mask = " \t\n\r\0\x0B" )
    {
      	if( ! is_array( $array ) ){
      		return $array;
      	}

      	return array_map( function( $value )use( $character_mask ){
      		return trim( $value, $character_mask);
      	}, $array );
    }

    /**
     * Append array keys with prefix and/or suffix
     *
     * @param array $array
     * @param string $prefix
     * @param string $suffix
     * @param boolean $recursive
     * @return mixed
     */
    public static function AppendArrayKey( $array, $prefix = false, $suffix = false, $recursive = true )
    {
        if( ! is_array($value) ){
          return $value;
        }

        $result = array();
        foreach( $array as $key => $value ){
          $result["$prefix$key$suffix"] = $recursive ? static::AppendArrayKey($value, $prefix, $suffix, $recursive) : $value;
        }

        return $result;
    }

    /**
     * Trim single level array values
     *
     * @param array $array
     * @param boolean $asc
     * @return array
     */
    public static function SortByValueLength( $array, $asc = true )
    {
      	if( ! is_array( $array ) ){
      		return $array;
      	}

        $result = $array;
        usort($result, function($value1, $value2) use ($asc){
          return ( $asc ? strlen($value1) - strlen($value2) : strlen($value2) - strlen($value1) );
        });

        return $result;
    }

    /**
     * Generate all possible string combination from an array
     *
     * @param array $array
     * @param array reference $return
     * @param string $separator
     * @param string $previously
     * @return boolean
     */
    public static function ArrayPermutation( $array, &$return, $separator = ' ', $previously = false )
    {
      	if( ! is_array( $array ) ){
      		return false;
      	}
        if( ! is_array( $return ) ){
      		$return = array();
      	}

        if( $previously ){
          array_push($return, $previously);
        }

        foreach($array as $key => $value){
          $copy = $array;
          $element = array_splice($copy, $key, 1);

          if( sizeof( $copy ) > 0 ){
            self::ArrayPermutation($copy, $return, $separator, $previously . $separator . $element[0]);
            continue;
          }

          array_push($return, $previously . $separator . $element[0]);
        }

        return true;
    }

    /**
     * Execure event callback
     *
     * @param Pehape\Models\EventCallback $value
     * @param mixed $default
     * @return mixed
     */
    public static function ExecuteEventCallback( EventCallback $value, $default = NULL )
    {
      	if( ! $value instanceof \Pehape\Models\EventCallback ){
      		return $default;
      	}

      	$event = ! is_object( $value->Context ) ? $value->Method : array( $value->Context, $value->Method );
      	if( ! is_callable( $event ) ){
      		return $default;
      	}

      	return is_array( $value->Argument ) ? call_user_func_array( $event, $value->Argument ) : call_user_func( $event );
    }

}
