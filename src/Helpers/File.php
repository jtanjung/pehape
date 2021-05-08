<?php namespace Pehape\Helpers;

/**
 * File
 * @package Pehape\Helpers
 */
class File {

    /**
     * List files on a directory
     *
     * @param string $path
     * @return array
     */
  	public static function Ls( $path )
  	{
  	}

    /**
     * Get file base name
     *
     * @param string $file_path
     * @return string
     */
  	public static function BaseName( $file_path )
  	{
    		return pathinfo( basename( $file_path ), PATHINFO_FILENAME );
  	}

    /**
     * Get file extension
     *
     * @param string $file_path
     * @return string
     */
  	public static function Extension( $file_path )
  	{
    		return pathinfo( $file_path, PATHINFO_EXTENSION );
  	}

    /**
     * Get root directory name
     *
     * @param string $path
     * @return string
     */
  	public static function RootDir( $value )
  	{
    		$path = trim( $value, '/' );
    		$path = explode( '/', $path );
    		return @$path[0];
  	}

    /**
     * Format file size
     *
     * @param int $value
     * @param precision $value
     * @return string
     */
  	public static function FormatFileSize( $bytes, $precision = 2 )
  	{
    		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

    		$bytes = max( $bytes, 0 );
    		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
    		$pow = min( $pow, count( $units ) - 1 );

    		// Uncomment one of the following alternatives
    		$bytes /= pow( 1024, $pow );
    		// $bytes /= (1 << (10 * $pow));

    		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
  	}

    /**
     * Scan folder for specific files
     *
     * @param string $path
     * @param string $exts
     * @return array
     */
  	public static function ScanFiles( $path, $exts = FALSE )
  	{
    		$dir_iterator = new \RecursiveDirectoryIterator( $path );
    		$iterator = new \RecursiveIteratorIterator(
    			$dir_iterator,
    			\RecursiveIteratorIterator::SELF_FIRST
    		);

    		if( ! $exts ){
    			return $iterator;
    		}

    		$extensions = $exts;
    		if( is_string( $extensions ) ){
    			$extensions = array( $exts );
    		}

    		$extensions = array_map(function( $value ){
    			return strtoupper( $value );
    		}, $extensions );

    		$result = array();
    		foreach( $iterator as $file ){
    			$file_path = (string)$file;
    			$ext = strtoupper( self::Extension( $file_path ) );
    			if( ! in_array( $ext, $extensions ) ){
    				continue;
    			}

    			array_push( $result, basename( $file_path ) );
    		}

    		return $result;
  	}

}
