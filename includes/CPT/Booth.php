<?php


namespace RRZE\Expo\CPT;

use function RRZE\Expo\Config\getConstants;

class Booth {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [$this, 'boothPostType']);
        add_action('cmb2_admin_init', [$this, 'boothFields']);
    }

    // Register Custom Post Type
    public function boothPostType(){
        $labels = [
            'name'                  => _x('Booths', 'Post type general name', 'rrze-expo'),
            'singular_name'         => _x('Booth', 'Post type singular name', 'rrze-expo'),
            'menu_name'             => _x('Booths', 'Admin Menu text', 'rrze-expo'),
            'name_admin_bar'        => _x('Booth', 'Add New on Toolbar', 'rrze-expo'),
            'add_new'               => __('Add New', 'rrze-expo'),
            'add_new_item'          => __('Add New Booth', 'rrze-expo'),
            'new_item'              => __('New Booth', 'rrze-expo'),
            'edit_item'             => __('Edit Booth', 'rrze-expo'),
            'view_item'             => __('View Booth', 'rrze-expo'),
            'all_items'             => __('All Booths', 'rrze-expo'),
            'search_items'          => __('Search Booths', 'rrze-expo'),
            'not_found'             => __('No Booths found.', 'rrze-expo'),
            'not_found_in_trash'    => __('No Booths found in Trash.', 'rrze-expo'),
            'featured_image'        => _x('Booth Logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set booth logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove booth logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as booth logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Booth archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into booth', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this booth', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter booths list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Booths list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Booths list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $args = [
            'label' => __('Booth', 'rrze-expo'),
            'description' => __('Add and edit booth informations', 'rrze-expo'),
            'labels' => $labels,
            'supports'                  => ['title', 'editor', 'author', 'thumbnail'],
            'hierarchical'              => false,
            'public'                    => true,
            'show_ui'                   => true,
            'show_in_menu'              => true,
            'show_in_nav_menus'         => true,
            'show_in_admin_bar'         => true,
            'menu_icon'                 => 'dashicons-store',
            'can_export'                => true,
            'has_archive'               => 'booth',
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capability_type'           => 'booth',
            'map_meta_cap'              => true
        ];

        register_post_type('booth', $args);
    }

    public function boothFields() {
        $constants = getConstants();

        // General
        $cmb_general = new_cmb2_box([
            'id'            => 'rrze-expo-booth-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_general->add_field([
            'name'      => __('Hall', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-hall',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '-1',
            'options'          => CPT::getPosts('hall'),
        ]);

        // Background Image
        $cmb_background = new_cmb2_box([
            'id'            => 'rrze-expo-booth-background',
            'title'         => __('Background Image', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_background->add_field(array(
            'name'    => __('Background Image', 'rrze-rsvp'),
            'desc'    => __('If no background image is set, the hall background image will be displayed. If no hall background image is set, the foyer background image will be displayed.', 'rrze-rsvp'),
            'id'      => 'rrze-expo-booth-background-image',
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
            'name'      => __('Backround Image Overlay', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Backround Image Opacity', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-overlay-opacity',
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

        // Layout
        $cmb_layout = new_cmb2_box([
            'id'            => 'rrze-expo-booth-layout',
            'title'         => __('Layout', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_layout->add_field([
            'name'      => __('Booth Template', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-template',
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
                'id'        => 'rrze-expo-booth-decoration-'.$templateName,
                'type'      => 'multicheck',
                'options'   => $templateObject,
            ]);
        }
        $cmb_layout->add_field([
            'name'      => __('Back Wall Color', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-backwall-color',
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
        ]);

        // Social Media
        $cmb_social_media = new_cmb2_box([
            'id'            => 'rrze-expo-booth-social-media',
            'title'         => __('Social Media Panel', 'rrze-expo'),
            'object_types'  => ['booth'],
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
                'id'        => 'rrze-expo-booth-'.$soMeName,
                'type'      => 'social-media',
                'default'   => $i,
            ]);
            $i++;
        }
    }
}
