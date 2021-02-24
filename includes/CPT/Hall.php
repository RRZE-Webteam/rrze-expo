<?php


namespace RRZE\Expo\CPT;


class Hall {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [$this, 'hall_post_type']);
        add_action('cmb2_admin_init', [$this, 'hall_fields']);
    }

    // Register Custom Post Type
    public function hall_post_type(){
        $labels = [
            'name'                  => _x('Halls', 'Post type general name', 'rrze-expo'),
            'singular_name'         => _x('Hall', 'Post type singular name', 'rrze-expo'),
            'menu_name'             => _x('Halls', 'Admin Menu text', 'rrze-expo'),
            'name_admin_bar'        => _x('Hall', 'Add New on Toolbar', 'rrze-expo'),
            'add_new'               => __('Add New', 'rrze-expo'),
            'add_new_item'          => __('Add New Hall', 'rrze-expo'),
            'new_item'              => __('New Hall', 'rrze-expo'),
            'edit_item'             => __('Edit Hall', 'rrze-expo'),
            'view_item'             => __('View Hall', 'rrze-expo'),
            'all_items'             => __('All Halls', 'rrze-expo'),
            'search_items'          => __('Search Halls', 'rrze-expo'),
            'not_found'             => __('No Halls found.', 'rrze-expo'),
            'not_found_in_trash'    => __('No Halls found in Trash.', 'rrze-expo'),
            'featured_image'        => _x('Hall Background Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set hall background image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove hall background image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as hall background image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Hall archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into hall', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this hall', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter halls list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Halls list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Halls list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $args = [
            'label' => __('Hall', 'rrze-expo'),
            'description' => __('Add and edit hall informations', 'rrze-expo'),
            'labels' => $labels,
            'supports'                  => ['title', 'editor', 'author', 'thumbnail'],
            'hierarchical'              => false,
            'public'                    => true,
            'show_ui'                   => true,
            'show_in_menu'              => 'edit.php?post_type=booth',
            'show_in_nav_menus'         => true,
            'show_in_admin_bar'         => true,
            'menu_icon'                 => 'dashicons-store',
            'can_export'                => true,
            'has_archive'               => 'hall',
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capability_type'           => 'hall',
            'map_meta_cap'              => true
        ];

        register_post_type('hall', $args);
    }

    public function hall_fields() {

    }

}
