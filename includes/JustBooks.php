<?php

namespace JB;

use JB\Install as Install;

defined('ABSPATH') || exit;


class JustBooks
{
    /**
     * Just Book version.
     *
     * @var string
     */
    public static $version = '0.1.0';


    /**
     * check for preventing duplicate run
     *
     * @var boolean
     */
    private static $initialized = false;

    private function __construct()
    {
        //escape
    }


    public static function init()
    {
        if (self::$initialized)
            return;
        self::includes();
        Install::install();


        self::$initialized = true;
    }

    private static function includes()
    {
    }

    public static function get_jb_version()
    {
        return get_option('jb_books_version');
    }
}
