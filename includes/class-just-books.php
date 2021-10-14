<?php

namespace JB;

defined('ABSPATH') || exit;


class just_books
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
        JB_install::install();


        self::$initialized = true;
    }

    private static function includes()
    {
        require_once(JB_DIR . 'includes/class-jb-install.php');
    }

    public static function get_jb_version()
    {
        return get_option('jb_books_version');
    }
}
