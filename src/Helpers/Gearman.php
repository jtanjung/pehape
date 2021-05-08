<?php
namespace Pehape\Helpers;

/**
 * Interface Gearman
 * @package Pehape\Helpers
 */
class Gearman {

    /**
     * Check if gearman worker is running
     *
     * @param string $process_name
     * @return mixed
     */
  	public static function IsWorkerRunning( $process_name )
  	{
    		exec( "ps aux|grep 'php $process_name'| grep -v grep|awk '{print $1}'", $output, $ret );

    		if( $ret ){
    			return FALSE;
    		}

    		$i = 0;
    		while( list( , $t ) = each( $output ) ) {
    			if( strpos( $t, " grep php" ) > 0 ){
    				continue;
    			}

    			$i++;
    		}

    		return $i;
  	}

    /**
     * Run gearman worker
  	 *
  	 * @param string $process_name
     * @param int $count
     * @return void
     */
  	public static function RunWorker( $process_name, $count = 1 )
  	{
    		$worker = self::IsWorkerRunning( $process_name );
    		if( $worker === FALSE || $worker == $count ){
    			return;
    		}

    		for( $i = 0; $i < $count - $worker; $i++ ){
    			exec( "php $process_name > /dev/null 2> /dev/null & echo $!", $op );
    			echo "Gearman worker " . ( $i + 1 ) . " running: " . @$op[0] . "\r\n";
    			unset( $op );
    		}
  	}

    /**
     * Terminate gearman worker
     *
  	 * @param string $process_name
     * @param string $function_name
     * @return void
     */
  	public static function TerminateWorker( $process_name, $function_name )
  	{
    		exec( "ps ax|grep 'php $process_name'| grep -v grep|awk '{print $1}'", $output, $ret );

    		if( sizeof( $output ) < 2 ){
    			echo "No worker running \r\n";
    			return FALSE;
    		}

    		$i = 1;
    		while( list( , $t ) = each( $output ) ) {
    			if( strpos( $t, " grep php" ) > 0 ){
    				continue;
    			}

    			$process_info = explode( " ", trim( $t ) );
    			if( @$process_info[0] ){
    				echo "Stopping gearman worker $i: " . @$process_info[0];
    				exec( "kill -9 " . @$process_info[0] );
    				echo " Done";
    				echo "\r\n";
    			}
    			$i++;
    		}

    		exec( "gearadmin --drop-function $function_name" );
  	}

}
