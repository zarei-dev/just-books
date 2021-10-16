<?php

namespace JB\meta;



class isbn extends Meta
{
    public function __construct()
    {
        $this->table_name = 'books_info';
        $this->meta_key = 'isbn';
    }

    /**
     * Generate, render, saving data by using obejct of MetaBox Class.
     *
     * @return void
     */
    public function init_metabox()
    {
        if (is_admin()) {
            (new MetaBox(array(
                'meta_id' => 'isbn',
                'meta_title' => 'ISBN',
                'meta_desc' => 'Enter the book ISBN: ',
                'post_type' => 'book',
                'getter' => (array($this, 'get')),
                'setter' => (array($this, 'set'))
            )))->init();
        }
        return;
    }
}
