<?php

namespace JB\admin;

use JB\meta\isbn as isbn;

class Install
{

    public static function init()
    {
        self::setup_envirement();
    }


    public static function setup_envirement()
    {
        self::setup_metaboxes();
    }


    public static function setup_metaboxes()
    {
        (new isbn)->init_metabox();
    }
}
