<?php
namespace Pehape\Helpers;

/**
 * Interface Text
 * @package Pehape\Helpers
 */
class Text {

    /**
     * Remove all new lines
     *
     * @param string $text
     * @return string
     */
  	public static function CleanWhiteSpace( $text )
  	{
    		$result = preg_replace( "/[\n\r]/", "", $text );
    		$result = preg_replace( '!\s+!', ' ', $result );
    		$result = preg_replace( "!>\s+<!is", "><", $result );
    		return $result;
  	}

    /**
     * Remove all comments line
     *
     * @param string $text
     * @return string
     */
  	public static function CleanComment( $text )
  	{
        $result = preg_replace('!/\*.*?\*/!s', '', $text);
        $result = preg_replace('/\n\s*\n/', "\n", $result);
    		return $result;
  	}

    /**
     * Decode to ISO-8859-2
     *
     * @param string $text
     * @return string
     */
  	public static function ISO_8859_2( $text )
  	{
    		$result = quoted_printable_decode( $text );
    		$result = iconv( 'ISO-8859-2', 'UTF-8', $result );
    		return $result;
  	}

    /**
     * Decode to UTF8 based
     *
     * @param string $text
     * @return string
     */
  	public static function Clean_UTF8( $text )
  	{
    		$result = htmlentities( $text, ENT_QUOTES, "UTF-8" );
    		return preg_replace( "/[\n\r]/", "", $result );
  	}

    /**
     * Convert hex to char
     *
     * @param string $text
     * @return string
     */
  	public static function HexToChar( $text )
  	{
    		return preg_replace_callback( "(\\\\x([0-9a-f]{2}))i", function( $a ) { return chr( hexdec( $a[ 1 ] ) ); }, $text );
  	}

    /**
     * Convert unicode to html tag
     *
     * @param string $text
     * @return string
     */
  	public static function HtmlEntities( $text )
  	{
    		return preg_replace_callback(
    			"/(&#[0-9]+;)/",
    			function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); },
    			$text
    		);
  	}

    /**
     * Grab single text between two string using regex pattern
     *
     * @param string $pattern
     * @param string $start
     * @param string $end
     * @param string $text
     * @return string
     */
  	public static function GrabText( $pattern, $start, $end, $text )
  	{
    		preg_match( "/$start$pattern$end/", $text, $result );
    		return isset( $result[ 1 ] ) ? $result[ 1 ] : FALSE;
  	}

    /**
     * Grab all texts between two string using regex pattern
     *
     * @param string $pattern
     * @param string $start
     * @param string $end
     * @param string $text
     * @return string
     */
  	public static function GrabTextAll( $pattern, $start, $end, $text )
  	{
    		preg_match_all( "/$start$pattern$end/", $text, $result );
    		return isset( $result[ 1 ] ) ? $result[ 1 ] : FALSE;
  	}

    /**
     * Grab all texts between two string using regex pattern
     *
     * @param string $start
     * @param string $end
     * @param string $text
     * @param boolean $all
     * @return mixed
     */
  	public static function StringBetween( $text, $start, $end, $all = FALSE )
  	{
    		preg_match_all( '!' . $start . '(.*?)' . $end . '!is', $text, $result );
    		$strings = isset( $result[ 1 ] ) ? $result[ 1 ] : FALSE;

    		if( ! $strings ){
    			return FALSE;
    		}

    		if( ! $all ){
    			$strings = $strings[ 0 ];
    		}

    		return $strings;
  	}

    /**
     * Grab text inside <H1/> html tag
     *
     * @param string $html
     * @return string
     */
  	public static function HTML_H1( $html )
  	{
    		if( ! is_string( $html ) ){
    			return NULL;
    		}

    		$result = preg_match_all( '!<h1>(.*?)<\/h1>!is', $html, $matches );
    		if( ! $result ){
    			return NULL;
    		}

    		$result = preg_replace( '/(&#.+?;)/', '', $matches[ 1 ][ 0 ] );
    		$result = str_replace( "&#8203;", "",  $result );
    		$result = str_replace( "\xE2\x80\x8C", "", $result );
    		$result = str_replace( "\xE2\x80\x8B", "", $result );
    		$result = str_replace( '&nbsp;', ' ', $result );
    		$result = preg_replace( '/(&.+?;)/', '', $result );
    		$result = preg_replace( "/<(.*?)>/", "", $result );

    		return $result;
  	}

    /**
     * Grab youtube url from html
     *
     * @param string $html
     * @param int $count
     * @param boolean $single
     * @return mixed
     */
  	public static function HTML_Youtube( $html, $count = 1, $single = TRUE )
  	{
    		if( ! is_string( $html ) ){
    			return FALSE;
    		}
        
        $result = preg_match_all( '!<iframe(.*?)><\/iframe>!is', $html, $matches );
  			if( ! $result ){
  				return FALSE;
  			}

  			$results = $matches[ 1 ];
  			$results = array_map(function( $value ){
  				$result = \Pehape\Helpers\Text::StringBetween( $value, 'src="https://www.youtube.com/embed/', '"' );
  				return $result ? $result : \Pehape\Helpers\Text::StringBetween( $value, "src='", "'" );
  			}, $results );

        if( $single ){
          return \Pehape\Helpers\URL::YoutubeThumbnail($results[0], $count);
        }

        $sources = $results;
        $results = array();
        foreach($sources as $source){
          $results[$source] = \Pehape\Helpers\URL::YoutubeThumbnail($source, $count);
        }

        return $results;
  	}

    /**
     * Grab image source attribute value
     *
     * @param string $html
     * @param boolean $single
     * @return mixed
     */
  	public static function HTML_Image( $html, $single = TRUE )
  	{
    		if( ! is_string( $html ) ){
    			return FALSE;
    		}

    		$result = preg_match_all( '!<img(.*?)\/>!is', $html, $matches );
    		if( ! $result ){
    			return self::HTML_Youtube($html, 1, $single);
    		}

    		$results = $matches[ 1 ];
    		$results = array_map(function( $value ){
    			$result = \Pehape\Helpers\Text::StringBetween( $value, 'src="', '"' );
    			return $result ? $result : \Pehape\Helpers\Text::StringBetween( $value, "src='", "'" );
    		}, $results );

    		return $single ? @$results[ 0 ] : $results;
  	}

    /**
     * Clean html tag
     *
     * @param string $html
     * @return string
     */
  	public static function Clean_HTML_TAG( $html )
  	{
    		return preg_replace( "/<(.*?)>/", "", $html );
  	}

    /**
     * Replace specific word in a string with provided source
     *
     * @param string $format
     * @param array|object $source
     * @return string
     */
  	public static function Format_Text( $format, $data )
  	{
        $source = $data;
        if( ! is_object($source) ){
          $source = Objects::JSONToArray($source);
        }
        if( ! is_array($source) ){
          return false;
        }
        if( is_array($format) ){
          $result = array();
          foreach( $format as $key => $value ){
            $result[$key] = static::Format_Text($value, $source);
          }
          return $result;
        }

        $formatter = str_replace('%', '', $format);
        $keywords = preg_split("/[\s,]+/", $formatter);

        $strings = array();
        foreach( $keywords as $text ){
          if( @$source[$text] ){
            array_push($strings, @$source[$text]);
            continue;
          }
          $marker = explode('.', $text);
          if( sizeof($marker) > 1 ){
            $value = $source;
            foreach($marker as $mark){
              $value = @$value[$mark];
            }
            array_push($strings, $value);
          }
        }

        $formatter = preg_replace("~%[a-zA-Z0-9_.]+\b~", '%s', $format);
        return vsprintf($formatter, $strings);
  	}

    /**
     * Collect uppercase chars from string as an initial phrase
     *
     * @param string $text
     * @param string $separator
     * @return mixed
     */
  	public static function InitialChars( $text, $separator = '' )
  	{
        preg_match_all('/[A-Z]/', $text, $matches, PREG_OFFSET_CAPTURE);
        if( ! @$matches[0] ){
          return false;
        }

        $matches = array_values($matches[0]);
        $matches = array_map(function($value){
          return $value[0];
        }, $matches);

        return implode($separator, $matches);
  	}

    /**
     * Get the first paragraph of html tag <p/>
     *
     * @param string $html
     * @param int $min_lenth
     * @return string
     */
  	public static function FirstParagraph( $html, $min_lenth = 50 )
  	{
    		if( ! is_string( $html ) ){
    			return FALSE;
    		}

    		$result = preg_match_all( '!<p?.*>(.*?)<\/p>!is', $html, $matches );
    		if( ! $result ){
    			return NULL;
    		}

    		$result = NULL;

    		foreach( $matches[ 1 ] as $match ){
    			$str = preg_replace( "/<(.*?)>/", "", $match );
    			if( strlen( $str ) >= $min_lenth ){
    				$result = $match;
    				break;
    			}
    		}

    		if( ! $result ){
    			foreach( $matches[ 1 ] as $match ){
    				$str = preg_replace( "/<(.*?)>/", "", $match );
    				if( ! empty( $str ) ){
    					$result = $match;
    					break;
    				}
    			}
    		}

    		if( $result ){
    			$result = preg_replace( '/(&#.+?;)/', '', $result );
    			$result = str_replace( "&#8203;", "",  $result );
    			$result = str_replace( "\xE2\x80\x8C", "", $result );
    			$result = str_replace( "\xE2\x80\x8B", "", $result );
    			$result = str_replace( '&nbsp;', ' ', $result );
    			$result = preg_replace( '/(&.+?;)/', '', $result );
    			$result = preg_replace( "/<(.*?)>/", "", $result );
    		}

    		return $result;
  	}

  /**
   * Minify html
    *
   * @param string $html
   * @return string
   */
  	public static function MinifyHTML( $html )
  	{
      		$re = '%# Collapse whitespace everywhere but in blacklisted elements.
      			(?>             # Match all whitespans other than single space.
      			  [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
      			| \s{2,}        # or two or more consecutive-any-whitespace.
      			) # Note: The remaining regex consumes no text at all...
      			(?=             # Ensure we are not in a blacklist tag.
      			  [^<]*+        # Either zero or more non-"<" {normal*}
      			  (?:           # Begin {(special normal*)*} construct
      				<           # or a < starting a non-blacklist tag.
      				(?!/?(?:textarea|pre|script)\b)
      				[^<]*+      # more non-"<" {normal*}
      			  )*+           # Finish "unrolling-the-loop"
      			  (?:           # Begin alternation group.
      				<           # Either a blacklist start tag.
      				(?>textarea|pre|script)\b
      			  | \z          # or end of file.
      			  )             # End alternation group.
      			)  # If we made it here, we are not in a blacklist tag.
      			%Six';

      		$new_html = preg_replace( $re, " ", $html );
      		return $new_html === NULL ? $html : $new_html;
  	}

    /**
     * Convert word document to HTML
     *
     * @param string $path
     * @return string
     */
  	public static function Doc_File_To_HTML( $path )
  	{
    		// Create new ZIP archive
    		$text = FALSE;
    		$zip = new \ZipArchive;
    		$dataFile = 'word/document.xml';
    		// Open received archive file
    		if (true === $zip->open($path)) {
    			// If done, search for the data file in the archive
    			if (($index = $zip->locateName($dataFile)) !== false) {
    				// If found, read it to the string
    				$data = $zip->getFromIndex($index);
    				$xml = \DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
    				// Return data without XML formatting tags

    				$contents = explode('\n',strip_tags($xml->saveXML()));
    				$text = '';
    				foreach($contents as $i=>$content) {
    					$text .= $contents[$i];
    				}
    			}
    			$zip->close();
    		}

    		// In case of failure return empty string
    		return $text;
  	}

    /**
     * Get image inside word document
     *
     * @param string $path
     * @return string
     */
  	public static function Doc_Image( $path )
  	{
    		// Create new ZIP archive
    		$text = FALSE;
    		$zip = new \ZipArchive;
    		$dataFile = 'word/media';
    		// Open received archive file
    		if (true === $zip->open($path)) {
    			// If done, search for the data file in the archive
    			if (($index = $zip->locateName($dataFile)) !== false) {
    				// If found, read it to the string
    				$data = $zip->getFromIndex($index);
    			}
    			$zip->close();
    		}

    		// In case of failure return empty string
    		return $text;
  	}

    /**
     * Correct and convert an invalid json string into json object
     *
     * @param string $text
     * @return object
     */
  	public static function StringToJSON( $value )
  	{
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

}
