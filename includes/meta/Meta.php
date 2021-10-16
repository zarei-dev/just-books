<?php

namespace JB\meta;

class Meta
{
    /**
     * table name without prefix
     * @var string
     */
    public $table_name;
    /**
     * Meta Key
     *
     * @var string
     */
    public $meta_key;

    public function __construct()
    {
    }

    /**
     * Get meta by post id
     *
     * @param int $post_id
     * @return void
     */
    public function get(int $post_id)
    {
        $table = $this->table_name;
        $meta_key = $this->meta_key;
        global $wpdb;
        $result = $wpdb->get_results("SELECT `$meta_key` FROM {$wpdb->prefix}$table WHERE `post_id` = $post_id");

        $result = empty($result) ? '' : $result[0]->$meta_key;
        return $result;
    }

    /**
     * If you do not pass the meta_value, then null replaced.
     * if metadata for this post id exist, then try to update data. 
     *
     * @param int $post_id
     * @param string $meta_value
     * @return void
     */
    public function set(int $post_id, string $meta_value = null)
    {
        if (self::get($post_id))
            return self::update($post_id, $meta_value);
        global $wpdb;

        $var = array('post_id' => $post_id);
        if (!is_null($meta_value))
            $var[$this->meta_key] = $meta_value;

        $result = $wpdb->insert($wpdb->prefix . $this->table_name, $var, array('%d', '%d'));
        return $result;
    }

    /**
     * Update the metadata.
     * If there was no metadata for this post_id, then nothing happend. 
     *
     * @param [type] $post_id
     * @param [type] $meta_value
     * @return void
     */
    public function update($post_id, $meta_value = null)
    {
        $table = $this->table_name;
        $meta_key = $this->meta_key;
        global $wpdb;
        $result = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}$table SET `$meta_key` = %s WHERE `post_id` = %d", $meta_value, $post_id));

        return $result;
    }

    /**
     * Delete metadata.
     * If meta_value passed, then check before deleting.
     *
     * @param [type] $post_id
     * @param [type] $meta_value
     * @return void
     */
    public function delete($post_id, $meta_value = null)
    {
        global $wpdb;
        $var = array('post_id' => $post_id);
        if (!is_null($meta_value))
            $var[$this->meta_key] = $meta_value;


        $result = $wpdb->delete($wpdb->prefix . $this->table_name, $var);

        return $result;
    }
}
