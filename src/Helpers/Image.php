<?php namespace Pehape\Helpers;

/**
 * Interface Image
 * @package Pehape\Helpers
 */
class Image {

    /**
     * Convert any image type to JPG
     *
     * @param string $filename
     * @param string $renameto
     * @param int $neww
     * @param int $newh
     * @param boolean $square
     * @param boolean $resize
     * @return boolean
     */
  	public static function ConvertToJPG( $filename, $renameto, $neww = 0, $newh = 0, $square = FALSE , $resize = FALSE )
  	{
    		try
    		{
    			if( ! file_exists( $filename ) )
    				return FALSE;

    			$infofile = explode( "." , $filename );
    			$ext = pathinfo( $filename, PATHINFO_EXTENSION );

    			list( $width , $height ) = getimagesize( $filename );

    			$newimage = "";

    			if( $ext == "gif" ) $newimage = imagecreatefromgif( $filename );
    			elseif( $ext == "png" ) $newimage = imagecreatefrompng( $filename );
    			elseif( $ext == "jpg" || $ext == "jpeg" ) $newimage = imagecreatefromjpeg( $filename );
    			else return FALSE;

    			$color = imagecreatetruecolor( $width , $height );
    			imagecopyresampled( $color , $newimage , 0 , 0 , 0 , 0 , $width , $height , $width , $height );
    			imagejpeg( $color, $renameto );

    			if( ! $resize ){
    				return TRUE;
    			}

    			if( $neww > 0 && $newh > 0 ){
    				return static::Resize( $renameto , $neww , $newh , $square );
    			}

    			return TRUE;
    		}
    		catch(\Exception $e)
    		{
    			error_log($e);
    		}

    		return FALSE;
  	}

    /**
     * Change image dimension
     *
     * @param string $filename
     * @param int $neww
     * @param int $newh
     * @param boolean $square
     * @param boolean $return
     * @param boolean $delete
     * @return mixed
     */
  	public static function Resize( $filename , $neww , $newh , $square , $return = FALSE, $delete = FALSE )
  	{
    		try
    		{
    			if( ! file_exists( $filename ) ) {
    				return FALSE;
    			}

    			if( ! $neww || ! $newh ) {
    				return FALSE;
    			}

    			$new_width = $neww;
    			$new_height = $newh;

    			list( $width , $height ) = getimagesize( $filename );
    			$sratio = $width/$height;
    			$currratio = $neww/$newh;

    			if( ! $square )
    			{
    				if( $currratio > $sratio ) $new_width = $newh * $sratio;
    				else $new_height = $neww/$sratio;
    			}

    			$newimg = imagecreatefromjpeg( $filename );
    			$color = imagecreatetruecolor( $new_width, $new_height );
    			imagecopyresampled( $color , $newimg , 0 , 0 , 0 , 0 , $new_width, $new_height, $width , $height );

    			if( ! $return ){
            if( $delete ){
              unlink( $filename );
            }
    				imagejpeg( $color , $filename );
    				imagedestroy($newimg);
    				return TRUE;
    			}
    			else
    			{
    				return $color;
    			}
    		}
    		catch(\Exception $e)
    		{
    			error_log($e);
    		}

    		return FALSE;
  	}

    /**
     * Draw dots inside image
     *
     * @param string $path
     * @param array $dots
     * @param string $temp
     * @param int $width
     * @param int $height
     * @return mixed
     */
    public static function DrawDot( $path, $dots, $temp = FALSE, $width = 0, $height = 0 )
    {
      	$image_path = $path;
      	$temp_dir = $temp;
      	$is_url = FALSE;

      	if( filter_var( $path, FILTER_VALIDATE_URL ) ){

      		if( $temp_dir ){
      			$temp_dir = "/" . trim( $temp_dir, "/" ) . "/";
      		}

      		$url_info = parse_url( $path );
      		$filename = basename( $url_info[ "path" ] );
      		$image_path = $temp_dir . $filename;

      		$image = @file_get_contents( $path );
      		@file_put_contents( $image_path, $image );
      		$is_url = TRUE;
      	}

      	$new_image_path = $temp_dir . md5( rand(1,19) . time() ) . ".jpg";
      	if( ! static::ConvertToJPG( $image_path, $new_image_path, $width, $height ) ){
      		return FALSE;
      	}

      	$image = imagecreatefromjpeg( $new_image_path );
      	$ellipseColor = imagecolorallocate( $image, 255, 0, 0 );

      	foreach( $dots as $dot ){
      		imagefilledellipse( $image, $dot->cx, $dot->cy, $dot->r, $dot->r, $ellipseColor );
      	}

      	unlink( $new_image_path );
      	imagejpeg( $image, $new_image_path );

      	return basename( $new_image_path );
    }

    /**
     * Load JPG image file
     *
     * @param string $filename
     * @return void
     */
    public static function LoadJPG( $filename )
    {
      	$im = imagecreatefromjpeg( $filename );
      	imagejpeg($im);
      	imagedestroy($im);
    }

    /**
     * Load PNG image file
     *
     * @param string $filename
     * @return void
     */
    public static function LoadPNG( $filename )
    {
      	list( $width, $height ) = getimagesize( $filename );
      	$png = imagecreatetruecolor( $width, $height );
      	imagealphablending( $png, FALSE );
      	imagesavealpha( $png, TRUE );

      	$source = imagecreatefrompng( $filename );
      	imagealphablending ($source, TRUE );

      	imagecopyresampled( $png, $source, 0, 0, 0, 0, $width, $height, $width, $height);
      	imagepng( $png );
      	imagedestroy( $png );
    }

    /**
     * Convert PNG to ICO
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    public static function PNG_to_ICO( $source, $destination )
    {
      	$img = imagecreatefrompng( $source );
      	image2wbmp( $img, $destination );
      	imagedestroy( $img );
    }

    /**
     * Generate image text
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    public static function Text_To_Image( $text, $width = 400, $height = 30 )
    {
        $im = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 221, 221, 221);

        imageSaveAlpha($im, true);
        imageAlphaBlending($im, false);

        $transparent = imageColorAllocateAlpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, $width-1, $height-1, $transparent);
        imageAlphaBlending($im, true);

        $font = 'arial.ttf';
        imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);
        imagettftext($im, 20, 0, 10, 20, $white, $font, $text);

        imagepng($im);
        imagedestroy($im);
    }

}
