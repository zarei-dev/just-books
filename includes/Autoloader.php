<?php
defined('ABSPATH') || exit;
define('JB_DIR', plugin_dir_path(__DIR__));

if (is_readable(JB_DIR . '/vendor/autoload.php')) {
    require JB_DIR . '/vendor/autoload.php';
}
