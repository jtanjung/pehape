<?php namespace Pehape\Helpers;

/**
 * Interface URL
 * @package Pehape\Helpers
 */
class URL {

    /**
     * Extract query string from an URL
     * @param string $value
     * @return mixed
     */
  	public static function QueryString( $value )
  	{
    		$url_info = parse_url( $value );
    		if( ! isset( $url_info[ "query" ] ) ){
    			return FALSE;
    		}

    		parse_str( $url_info[ "query" ], $result );
    		return $result;
  	}

    /**
     * Clear query string from url and get only url path
     * @param string $value
     * @return string
     */
  	public static function PlainURL( $value )
  	{
    		$url_info = parse_url( $value );
    		return @$url_info[ "scheme" ] . '://' . @$url_info[ "host" ] . @$url_info[ "path" ];
  	}

    /**
     * Get youtube video thumbnail
     * @param string $url
     * @param int $count
     * @return string|array
     */
  	public static function YoutubeThumbnail( $url, $count = 1 )
  	{
        $video_id = self::YoutubeVideoId($url);

        if( $count < 2 ){
  				return "https://img.youtube.com/vi/$video_id/0.jpg";
  			}

        $result = array(); $i = 0;
        while($i < $count){
          $result[] = "https://img.youtube.com/vi/$video_id/$i.jpg";
          $i++;
        }

        return $result;
  	}

    /**
     * Get youtube video id
     * @param string $url
     * @return string
     */
  	public static function YoutubeVideoId( $url )
  	{
        $result = self::QueryString($url);
        $basename = basename($url);
        if( $result === false ){
          return $basename == 'watch' ? false : $basename;
        }
        return $basename == 'watch' && @$result['v'] ? $result['v'] : $basename;
  	}

    /**
     * Get only domain name without subdomain
     * @param string $value
     * @return string
     */
  	public static function DomainName( $value )
  	{
        $value = strtolower($value);

      	if( filter_var($value, FILTER_VALIDATE_IP) ){
          return $value;
        }

      	$chunks = array_slice(array_filter(explode('.', $value, 4), function($value){
      		return $value !== 'www';
      	}), 0); //rebuild array indexes

        if( count($chunks) < 2 ){
          return $value;
        }

        if( count($chunks) == 2 ){

          $chunk = array_shift($chunks);

      		if( strpos(join('.', $chunks), '.') === false &&
              in_array(@$chunks[0], array('localhost','test','invalid')) === false ) // not a reserved domain
      		{
      			array_unshift($chunks, $chunk);
      		}

          return join('.', $chunks);
        }

        $count = count($chunks);
        $_sub = explode('.', $count === 4 ? $chunks[3] : $chunks[2]);

        if( count($_sub) === 2 ){
          array_shift($chunks);
          if ($count === 4){
            array_shift($chunks);
          }

          return join('.', $chunks);
        }

        if( count($_sub) === 1 ){
          $removed = array_shift($chunks);
          if( strlen($_sub[0]) === 2 && $count === 3 ){
            array_unshift($chunks, $removed);
          }
          else
          {
            $tlds = array(
              'aero',
              'arpa',
              'asia',
              'biz',
              'cat',
              'com',
              'coop',
              'edu',
              'gov',
              'info',
              'jobs',
              'mil',
              'mobi',
              'museum',
              'name',
              'net',
              'org',
              'post',
              'pro',
              'tel',
              'travel',
              'xxx',
            );

            if( count($chunks) > 2 && in_array($_sub[0], $tlds) !== false ){
              array_shift($chunks);
            }
          }
          return join('.', $chunks);
        }

      	return $value;
  	}
}
