<?php


namespace RRZE\Expo\CPT;


class Hall {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [$this, 'hallPostType']);
        add_action('cmb2_admin_init', [$this, 'hallFields']);
    }

    // Register Custom Post Type
    public function hallPostType(){
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
            'featured_image'        => _x('Hall Logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set hall logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove hall logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as hall logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Hall archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into hall', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this hall', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter halls list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Halls list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Halls list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $capabilities = CPT::makeCapabilities('exposition', 'expositions');
        $args = [
            'label' => __('Hall', 'rrze-expo'),
            'description' => __('Add and edit hall informations', 'rrze-expo'),
            'labels' => $labels,
            'supports'                  => ['title', 'editor', 'author', 'thumbnail'],
            'hierarchical'              => false,
            'public'                    => true,
            'show_ui'                   => true,
            'show_in_menu'              => 'edit.php?post_type=exposition',
            'show_in_nav_menus'         => true,
            'show_in_admin_bar'         => true,
            'menu_icon'                 => 'dashicons-store',
            'can_export'                => true,
            'has_archive'               => false,
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capabilities'              => $capabilities,
            'map_meta_cap'              => true,
        ];

        register_post_type('hall', $args);
    }

    public function hallFields() {
        $cmb = new_cmb2_box([
            'id'            => 'rrze-expo-hall-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['hall'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb->add_field([
            'name'      => __('Exposition', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-hall-exposition',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '',
            'options'          => CPT::getPosts('exposition'),
        ]);

        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            $optionsMenu[$menu->term_id] = $menu->name;
        }
        $cmb->add_field([
            'name'      => __('Hall Menu', 'rrze-expo'),
            'desc'    => __('If no menu is set, booths will be ordered alphabetically.', 'rrze-expo'),
            'id'        => 'rrze-expo-hall-menu',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '',
            'options'          => $optionsMenu,
        ]);

        // Background Image
        $cmb_background = new_cmb2_box([
            'id'            => 'rrze-expo-hall-background',
            'title'         => __('Background Image', 'rrze-expo'),
            'object_types'  => ['hall'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_background->add_field(array(
            'name'    => __('Background Image', 'rrze-expo'),
            'desc'    => __('If no background image is set, the foyer background image will be displayed.', 'rrze-expo'),
            'id'      => 'rrze-expo-hall-background-image',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            // query_args are passed to wp.media's library query.
            'query_args' => array(
                //'type' => 'application/pdf', // Make library only display PDFs.
                // Or only allow gif, jpg, or png images
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                ),
            ),
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ));
        $cmb_background->add_field([
            'name'      => __('Background Image Overlay', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-hall-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Background Overlay Opacity', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-hall-overlay-opacity',
            'type'      => 'select',
            'default'          => '30',
            'options'          => ['0'  => '0%',
                '0.1' => '10%',
                '0.2' => '20%',
                '0.3' => '30%',
                '0.4' => '40%',
                '0.5' => '50%',
                '0.6' => '60%',
                '0.7' => '70%',
                '0.8' => '80%',
                '0.9' => '90%',
                '1.0' => '100%'],
        ]);

    }
}
