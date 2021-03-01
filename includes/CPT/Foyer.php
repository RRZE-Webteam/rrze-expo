<?php


namespace RRZE\Expo\CPT;

use function RRZE\Expo\Config\getConstants;

class Foyer {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [$this, 'foyerPostType']);
        add_action('cmb2_admin_init', [$this, 'foyerFields']);
    }

    // Register Custom Post Type
    public function foyerPostType(){
        $labels = [
            'name'                  => _x('Foyers', 'Post type general name', 'rrze-expo'),
            'singular_name'         => _x('Foyer', 'Post type singular name', 'rrze-expo'),
            'menu_name'             => _x('Foyers', 'Admin Menu text', 'rrze-expo'),
            'name_admin_bar'        => _x('Foyer', 'Add New on Toolbar', 'rrze-expo'),
            'add_new'               => __('Add New', 'rrze-expo'),
            'add_new_item'          => __('Add New Foyer', 'rrze-expo'),
            'new_item'              => __('New Foyer', 'rrze-expo'),
            'edit_item'             => __('Edit Foyer', 'rrze-expo'),
            'view_item'             => __('View Foyer', 'rrze-expo'),
            'all_items'             => __('All Foyers', 'rrze-expo'),
            'search_items'          => __('Search Foyers', 'rrze-expo'),
            'not_found'             => __('No Foyers found.', 'rrze-expo'),
            'not_found_in_trash'    => __('No Foyers found in Trash.', 'rrze-expo'),
            'featured_image'        => _x('Foyer Background Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set foyer background image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove foyer background image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as foyer background image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Foyer archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into foyer', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this foyer', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter foyers list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Foyers list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Foyers list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $args = [
            'label' => __('Foyer', 'rrze-expo'),
            'description' => __('Add and edit foyer informations', 'rrze-expo'),
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
            'has_archive'               => 'foyer',
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capability_type'           => 'foyer',
            'map_meta_cap'              => true
        ];

        register_post_type('foyer', $args);
    }

    public function foyerFields() {
        $constants = getConstants();

        // General
        /*$cmb_general = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_general->add_field([
            'name'      => __('Hall', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-hall',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '-1',
            'options'          => CPT::getPosts('hall'),
        ]);*/

        // Layout
        $cmb_layout = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-layout',
            'title'         => __('Layout', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        /*$cmb_layout->add_field([
            'name'      => __('Foyer Template', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-template',
            'type'      => 'select',
            'default'          => '1',
            'options'          => [ '1' => __('Template 1', 'rrze-expo'),
                '2' => __('Template 2', 'rrze-expo'),
                '3' => __('Template 3', 'rrze-expo'),
                '0' => __('Custom Template', 'rrze-expo')
            ],
        ]);
        $decoObjects = $constants['template_elements'];
        foreach ($decoObjects as $templateName => $templateObject) {
            $cmb_layout->add_field([
                'name'      => __('Decoration Elements', 'rrze-expo'),
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-decoration-'.$templateName,
                'type'      => 'multicheck',
                'options'    => $templateObject]);
        }
        $cmb_layout->add_field([
            'name'      => __('Back Wall Color', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-backwall-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [
                '003366' => 'FAU',
                'A36B0D' => 'PhilFak',
                '8d1429' => 'RwFak',
                '0381A2' => 'MedFak',
                '048767' => 'NatFak',
                '6E7881' => 'TechFak',
                'custom' => __('Custom Color', 'rrze-expo'),
            ],
        ]);*/
        $cmb_layout->add_field([
            'name'      => __('Backround Image Overlay', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_layout->add_field([
            'name'      => __('Backround Image Opacity', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-overlay-opacity',
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

        // Social Media
        /*$cmb_social_media = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-social-media',
            'title'         => __('Social Media Panel', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);

        $socialMedia = $constants['social-media'];
        $i = 1;
        foreach ($socialMedia as $soMeName => $soMeUrl) {
            $cmb_social_media->add_field([
                'name'      => ucfirst($soMeName),
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-'.$soMeName,
                'type'      => 'social-media',
                'default'   => $i,
            ]);
            $i++;
        }*/
    }
}
