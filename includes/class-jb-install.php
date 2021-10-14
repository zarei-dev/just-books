<?php

namespace JB;

defined('ABSPATH') || exit;

class JB_install
{


    /**
     * create tables.
     *
     * @return void
     */
    private static function setup_environment()
    {
        self::maybe_create_tables();
        self::set_jb_version();
    }

    /**
     * check if plugin install for first time or not.
     *
     * @return boolean
     */
    public static function is_new_install()
    {
        $book_count = array_sum((array) wp_count_posts('book'));

        // return is_null(just_books::get_jb_version() || 0 === $book_count);
        return is_null(just_books::get_jb_version()) || (0 === $book_count);
    }
    /**
     * add new Option for JB versioning.
     *
     * @return void
     */
    private static function set_jb_version()
    {
        update_option('jb_books_version', just_books::$version);
    }

    /**
     * maybe create tables.
     *
     * @return void
     */
    private static function maybe_create_tables()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $wpdb->hide_errors();

        $collate = $wpdb->get_charset_collate();
        $table_name = "$wpdb->prefix books_info";


        $sql = "CREATE TABLE $table_name(
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            isbn longtext NOT NULL,
            UNIQUE KEY ID (ID)
          ) $collate;";

        if (self::is_new_install() && !self::is_table_exist($table_name_with_prefix)) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Is table exist?
     *
     * @return void
     */
    private static function is_table_exist($table_name)
    {
        global $wpdb;
        return is_null(!$wpdb->get_var("SHOW TABLES LIKE '$table_name'"));
    }

    /**
     * install JB plugin.
     * 'jb_installed' hook.
     *
     * @return void
     */
    public static function install()
    {

        if (!is_blog_installed()) {
            return;
        }

        self::setup_environment();

        do_action('jb_installed');
    }
}
