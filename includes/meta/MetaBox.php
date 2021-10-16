<?php

namespace JB\meta;

class MetaBox
{
    public $id;
    public $title = 'MetaData';
    public $desc = 'Enter the value: ';
    public $post_type;
    public $get_data_call;
    public $save_data_call;

    /**
     * Required Parameters are: meta_id, post_type, callable getter, callable setter.
     * All Parameters are: meta_id, meta_title, meta_desc, post_type, callable getter, callable setter.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = $args['meta_id'];
        $this->title = $args['meta_title'];
        $this->desc = $args['meta_desc'];
        $this->post_type = $args['post_type'];

        $this->get_data_call = $args['getter'];
        $this->save_data_call = $args['setter'];

        return $this;
    }


    /**
     * Hook MetaBox to load-post if user is admin
     *
     * @return void
     */
    public function init()
    {
        if (is_admin()) {
            add_action('load-post.php', array($this, 'add_action'));
            add_action('load-post-new.php', array($this, 'add_action'));
        }
    }
    /**
     * add action to add_meta_boxes and save_post for some wordpress stuf :)
     *
     * @return void
     */
    public function add_action()
    {
        add_action('add_meta_boxes', array($this, 'add'));
        add_action('save_post',      array($this, 'save'), 10, 2);
    }

    /**
     * add MetaBox to wordpress using add_meta_box.
     *
     * @return void
     */
    public function add()
    {
        add_meta_box(
            $this->id,
            __($this->title, 'just-books'),
            array($this, 'render'),
            $this->post_type,
            'advanced',
            'default'
        );
    }


    /**
     * Render Meta Box content.
     *
     * action: before_render_jb_{$meta_name}_meta
     * action: after_render_jb_{$meta_name}_meta
     * @param WP_Post $post The post object.
     * @return void
     */
    public function render($post)
    {
        wp_nonce_field("jb_{$this->id}_meta_nonce_action", "jb_{$this->id}_meta_nonce");
        do_action("before_render_jb_{$this->id}_meta");

        $value = ($this->get_data_call)($post->ID);
        echo "<label for='jb_{$this->id}_meta'>
                    " . $this->desc . "
                </label>
                <input type='text' id='jb_{$this->id}_meta' name='jb_{$this->id}_meta' value='" . esc_attr($value) . "' size='25' />";

        do_action("after_render_jb_{$this->id}_meta");
    }


    /**
     * Handles saving the meta box.
     * 
     * filter: before_save_jb{$meta_name}_meta
     * action: after_save_jb_{$meta_name}_meta
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save($post_id, $post)
    {
        // Add nonce for security and authentication.
        $nonce_name   = isset($_POST["jb_{$this->id}_meta_nonce"]) ? $_POST["jb_{$this->id}_meta_nonce"] : '';
        $nonce_action = "jb_{$this->id}_meta_nonce_action";

        if (
            !wp_verify_nonce($nonce_name, $nonce_action)
            || wp_is_post_revision($post_id)
            || !current_user_can('edit_post', $post_id)
            || wp_is_post_autosave($post_id)
        )
            return;

        $data = sanitize_text_field($_POST["jb_{$this->id}_meta"]);

        apply_filters("before_save_jb{$this->id}_meta", $post, $data);

        $value = ($this->save_data_call)($post->ID, $data);


        do_action("after_save_jb_{$this->id}_meta", $post, $data);
        return;
    }
}
