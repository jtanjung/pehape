<?php namespace Pehape\Constants;

/**
 * Class WebDriver
 * @package Pehape\Constants
 */
class WebDriver {

    /**
     * Chrome driver executable file name
     */
    public static $CHROME = "chromedriver";

    /**
     * Firefox driver executable file name
     */
    public static $FIREFOX = "geckodriver";

    /**
     * Opera driver executable file name
     */
    public static $OPERA = "operadriver";

    /**
     * Chrome browser function name
     */
    public static $chrome = "Chrome";

    /**
     * Firefox browser function name
     */
    public static $firefox = "FireFox";

    /**
     * Opera browser function name
     */
    public static $opera = "Opera";

    /**
     * HTML tags use for random selection
     */
    public static $selectors = [
      'div', 'span', 'label', 'img', 
      'table', 'th', 'tr', 'td', 'thead', 'tbody', 'tfoot',
      'input', 'select', 'button'
    ];


}
