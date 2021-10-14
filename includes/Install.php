<?php

namespace JB;

use JB\JustBooks;

defined('ABSPATH') || exit;

class Install
{


    /**
     * create tables.
     *
     * @return void
     */
    private static function setup_environment()
    {
        self::maybe_create_tables();
        self::load_theme_textdomain();
        self::register_post_types();
        self::register_taxonomies();
    }

    /**
     * check if plugin install for first time or not.
     *
     * @return boolean
     */
    public static function is_new_install()
    {
        $book_count = array_sum((array) wp_count_posts('book'));
        return is_null(JustBooks::get_jb_version()) || (0 === $book_count); // we need a better thing to check :) we fix this later
    }
    /**
     * add new Option for JB versioning.
     *
     * @return void
     */
    private static function set_jb_version()
    {
        update_option('jb_books_version', JustBooks::$version);
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
        $table_name = $wpdb->prefix . "books_info";


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
        self::set_jb_version();
        do_action('jb_installed');
    }


    private static function register_post_types()
    {
        add_action('init', array(__CLASS__, 'register_book_post_type'), 5);
    }
    private static function register_taxonomies()
    {
        add_action('init', array(__CLASS__, 'register_publisher_taxonomy'), 5);
        add_action('init', array(__CLASS__, 'register_authors_taxonomy'), 5);
    }


    public static function register_book_post_type()
    {
        if (!is_blog_installed() || post_type_exists('book')) {
            return;
        }
        $labels = array(
            'name'                  => _x('Books', 'Post Type General Name', 'just-books'),
            'singular_name'         => _x('Book', 'Post Type Singular Name', 'just-books'),
            'menu_name'             => __('Books', 'just-books'),
            'name_admin_bar'        => __('Book', 'just-books'),
            'archives'              => __('Item Archives', 'just-books'),
            'attributes'            => __('Item Attributes', 'just-books'),
            'parent_item_colon'     => __('Parent Item:', 'just-books'),
            'all_items'             => __('All Items', 'just-books'),
            'add_new_item'          => __('Add New Item', 'just-books'),
            'add_new'               => __('Add New', 'just-books'),
            'new_item'              => __('New Item', 'just-books'),
            'edit_item'             => __('Edit Item', 'just-books'),
            'update_item'           => __('Update Item', 'just-books'),
            'view_item'             => __('View Item', 'just-books'),
            'view_items'            => __('View Items', 'just-books'),
            'search_items'          => __('Search Item', 'just-books'),
            'not_found'             => __('Not found', 'just-books'),
            'not_found_in_trash'    => __('Not found in Trash', 'just-books'),
            'featured_image'        => __('Featured Image', 'just-books'),
            'set_featured_image'    => __('Set featured image', 'just-books'),
            'remove_featured_image' => __('Remove featured image', 'just-books'),
            'use_featured_image'    => __('Use as featured image', 'just-books'),
            'insert_into_item'      => __('Insert into item', 'just-books'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'just-books'),
            'items_list'            => __('Items list', 'just-books'),
            'items_list_navigation' => __('Items list navigation', 'just-books'),
            'filter_items_list'     => __('Filter items list', 'just-books'),
        );
        $args = array(
            'label'                 => __('Book', 'just-books'),
            'description'           => __('book', 'just-books'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes'),
            'taxonomies'            => array('book_publisher', 'book_authors'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-book-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type('book', $args);
    }

    public static function register_publisher_taxonomy()
    {
        if (!is_blog_installed() || taxonomy_exists('book_publisher')) {
            return;
        }
        $labels = array(
            'name'                       => _x('publishers', 'Taxonomy General Name', 'just-books'),
            'singular_name'              => _x('publisher', 'Taxonomy Singular Name', 'just-books'),
            'menu_name'                  => __('Publisher', 'just-books'),
            'all_items'                  => __('All Items', 'just-books'),
            'parent_item'                => __('Parent Item', 'just-books'),
            'parent_item_colon'          => __('Parent Item:', 'just-books'),
            'new_item_name'              => __('New Item Name', 'just-books'),
            'add_new_item'               => __('Add New Item', 'just-books'),
            'edit_item'                  => __('Edit Item', 'just-books'),
            'update_item'                => __('Update Item', 'just-books'),
            'view_item'                  => __('View Item', 'just-books'),
            'separate_items_with_commas' => __('Separate items with commas', 'just-books'),
            'add_or_remove_items'        => __('Add or remove items', 'just-books'),
            'choose_from_most_used'      => __('Choose from the most used', 'just-books'),
            'popular_items'              => __('Popular Items', 'just-books'),
            'search_items'               => __('Search Items', 'just-books'),
            'not_found'                  => __('Not Found', 'just-books'),
            'no_terms'                   => __('No items', 'just-books'),
            'items_list'                 => __('Items list', 'just-books'),
            'items_list_navigation'      => __('Items list navigation', 'just-books'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy('book_publisher', array('book'), $args);
    }
    public static function register_authors_taxonomy()
    {
        if (!is_blog_installed() || taxonomy_exists('book_authors')) {
            return;
        }
        $labels = array(
            'name'                       => _x('Author', 'Taxonomy General Name', 'just-books'),
            'singular_name'              => _x('Authors', 'Taxonomy Singular Name', 'just-books'),
            'menu_name'                  => __('Authors', 'just-books'),
            'all_items'                  => __('All Items', 'just-books'),
            'parent_item'                => __('Parent Item', 'just-books'),
            'parent_item_colon'          => __('Parent Item:', 'just-books'),
            'new_item_name'              => __('New Item Name', 'just-books'),
            'add_new_item'               => __('Add New Item', 'just-books'),
            'edit_item'                  => __('Edit Item', 'just-books'),
            'update_item'                => __('Update Item', 'just-books'),
            'view_item'                  => __('View Item', 'just-books'),
            'separate_items_with_commas' => __('Separate items with commas', 'just-books'),
            'add_or_remove_items'        => __('Add or remove items', 'just-books'),
            'choose_from_most_used'      => __('Choose from the most used', 'just-books'),
            'popular_items'              => __('Popular Items', 'just-books'),
            'search_items'               => __('Search Items', 'just-books'),
            'not_found'                  => __('Not Found', 'just-books'),
            'no_terms'                   => __('No items', 'just-books'),
            'items_list'                 => __('Items list', 'just-books'),
            'items_list_navigation'      => __('Items list navigation', 'just-books'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy('book_authors', array('book'), $args);
    }

    private static function load_theme_textdomain()
    {
        load_theme_textdomain('just-books', JB_DIR . 'languages');
    }
}
