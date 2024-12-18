<?php


namespace RRZE\Expo\CPT;

use function RRZE\Expo\Config\getConstants;

class Booth {

    protected $pluginFile;

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [__CLASS__, 'boothPostType']);
        add_action('cmb2_admin_init', [$this, 'boothFields']);
    }

    // Register Custom Post Type
    public static function boothPostType(){
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

        $capabilities = CPT::makeCapabilities('booth', 'booths');
        $args = [
            'label' => __('Booth', 'rrze-expo'),
            'description' => __('Add and edit booth informations', 'rrze-expo'),
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
            'capability_type'           => 'booth',
            'map_meta_cap'              => true,
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
            'name'      => __('Exposition', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-exposition',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '',
            'options'          => CPT::getPosts('exposition'),
        ]);

        $cmb_general->add_field([
            'name'      => __('Hall', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-hall',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '-1',
            'options_cb'          => [$this, 'getBoothHalls'],
        ]);

        // Contact
        $cmb_contact = new_cmb2_box([
            'id'            => 'rrze-expo-booth-contact',
            'title'         => __('Contact Information', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_contact->add_field([
            'name' => __( 'Name', 'rrze-expo' ),
            //'description' => __( '', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-name',
            'type' => 'text',
        ] );
        $cmb_contact->add_field([
            'name' => __( 'Email Address', 'rrze-expo' ),
            //'description' => __( '', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-email',
            'type' => 'text_email',
        ] );
        $cmb_contact->add_field([
            'name' => __( 'Website', 'rrze-expo' ),
            //'description' => __( '', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-website',
            'type' => 'text_url',
        ] );
        $cmb_contact->add_field( [
            'name'    => __('Show Homepage on:', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'      => 'rrze-expo-booth-website-locations',
            'type'    => 'multicheck_inline',
            'select_all_button' => false,
            'options'    => ['wall' => __('Back wall', 'rrze-expo'),
                'panel' => __('Social Media Panel', 'rrze-expo')],
        ] );
        $cmb_contact->add_field([
            'name' => __( 'More Info', 'rrze-expo' ),
            //'description' => __( '', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-contactinfo',
            'type' => 'textarea_small',
        ] );
        $cmb_contact->add_field( array(
            'name' => __( 'Show Contact Form', 'rrze-expo' ),
            'desc' => __( 'Display a simple contact form where visitors can leave their email address or phone number (optional) and a message or simply ask for beeing contacted. The message is sent directly to the booth´s email address. Information are not stored in the database.' , 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-showcontactform',
            'type' => 'checkbox',
        ) );

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
            'name'    => __('Background Image', 'rrze-expo'),
            'desc'    => __('If no background image is set, the hall background image will be displayed. If no hall background image is set, the foyer background image will be displayed.', 'rrze-expo'),
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
            'name'      => __('Background Image Overlay', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Background Overlay Opacity', 'rrze-expo'),
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
            ],
        ]);
        $cmb_layout->add_field( array(
            'name' => __('Plain textures', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'   => 'rrze-expo-booth-plain-texture',
            'type' => 'checkbox',
            'show_on_cb' => [$this, 'getBoothTemplate'],
        ) );
        $cmb_layout->add_field( [
            'name'    => __('Logo', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'      => 'rrze-expo-booth-logo-locations',
            'type'    => 'multicheck_inline',
            'select_all_button' => false,
            'options_cb'    => [$this, 'getLogoLocations'],
        ] );
        $cmb_layout->add_field( array(
            'name'    => __('Back Wall Color', 'rrze-expo'),
            'id'      => 'rrze-expo-booth-backwall-color',
            'type'    => 'colorpicker',
            'default' => '#ffffff',
            'attributes' => array(
                'data-colorpicker' => json_encode( array(
                    // Iris Options set here as values in the 'data-colorpicker' array
                    'palettes' => CPT::getDefaultColors('dark'),
                ) ),
            ),
            // 'options' => array(
            // 	'alpha' => true, // Make this a rgba color picker.
            // ),
        ) );
        $cmb_layout->add_field(array(
            'name'    => __('Back Wall Image', 'rrze-expo'),
            'desc'    => __('Recommended Resolution: 1135 x 782 px', 'rrze-expo'),
            'id'      => 'rrze-expo-booth-backwall-image',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            // query_args are passed to wp.media's library query.
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                ),
            ),
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ));
        $cmb_layout->add_field( array(
            'name'    => __('Font Color', 'rrze-expo'),
            'desc'    => __('Please make sure there is enough contrast between font and background color.', 'rrze-expo'),
            'id'      => 'rrze-expo-booth-font-color',
            'type'    => 'colorpicker',
            'default' => '#000000',
            'attributes' => array(
                'data-colorpicker' => json_encode( array(
                    // Iris Options set here as values in the 'data-colorpicker' array
                    'palettes' => CPT::getDefaultColors('font-dark'),
                ) ),
            ),
            // 'options' => array(
            // 	'alpha' => true, // Make this a rgba color picker.
            // ),
        ) );
        $cmb_layout->add_field([
            'name'      => __('Font Size', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-font-size',
            'type'      => 'select',
            'default'          => '60',
            'options'          => ['10' => '10',
                '20' => '20',
                '30' => '30',
                '40' => '40',
                '50' => '50',
                '60' => '60',
                '70' => '70',
                '80' => '80',
                '90' => '90',
                '100' => '100'],
        ]);

        // Persona
        for ($i=1; $i<=3; $i++) {
            $cmb_layout->add_field([
                'name'      => __('Persona', 'rrze-expo') . " $i",
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-booth-persona-'.$i,
                'type'      => 'rrze_expo_persona_field',
            ]);
        }

        $cmb_layout->add_field([
            'name'      => __('Decoration Elements', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-booth-decorations',
            'type'      => 'multicheck',
            'options_cb'          => [$this, 'getDecoObjects'],
        ]);

        for ($i=1; $i<=3; $i++) {
            $cmb_layout->add_field([
                'name' => __('Seat', 'rrze-expo') . " $i",
                //'desc'    => __('', 'rrze-expo'),
                'id' => 'rrze-expo-booth-seat-'.$i,
                'type' => 'select',
                'default' => '',
                'options' => ['' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
                    'fau' => __('FAU (dark blue)', 'rrze-expo'),
                    'med' => __('Med (light blue)', 'rrze-expo'),
                    'nat' => __('Nat (green)', 'rrze-expo'),
                    'phil' => __('Phil (yellow)', 'rrze-expo'),
                    'rw' => __('RW (red)', 'rrze-expo'),
                    'tf' => __('Tech (grey)', 'rrze-expo'),],
            ]);
        }


        // AV Media
        $cmb_videos = new_cmb2_box([
            'id'            => 'rrze-expo-booth-video-box',
            'title'         => __('Videos and Video Conferences', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_videos->add_field([
            'name' => __( 'Left Screen', 'rrze-expo' ),
            'description' => __( 'Enter video embedding url, e.g. https://www.fau.tv/webplayer/id/123456.', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-video-left',
            'type' => 'text_url',
            // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );
	    $cmb_videos->add_field([
           'name' => __( 'Left Screen Video Title', 'rrze-expo' ),
           //'description' => __( '', 'rrze-expo' ),
           'id'   => 'rrze-expo-booth-video-left-title',
           'type' => 'text',
           // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );
	    $cmb_videos->add_field([
           'name' => __( 'Right Screen', 'rrze-expo' ),
           //'description' => __( '', 'rrze-expo' ),
           'id'   => 'rrze-expo-booth-video-right',
           'type' => 'text_url',
           // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );
	    $cmb_videos->add_field([
            'name' => __( 'Right Screen Video Title', 'rrze-expo' ),
            'description' => __( 'Enter video embedding url, e.g. https://www.fau.tv/webplayer/id/123456.', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-video-right-title',
            'type' => 'text',
            // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );
        $cmb_videos->add_field([
            'name' => __( 'Table Screen (Live Chat)', 'rrze-expo' ),
            'description' => __( 'Enter video conference or chat tool link.', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-video-table',
            'type' => 'text_url',
            // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );

        $cmb_videos->add_field([
            'name' => __( 'Schedule Location', 'rrze-expo' ),
            'description' => __( 'The schedule lists live talks related to this booth. It replaces either the roll up or one of the video locations.', 'rrze-expo' ),
            'id'   => 'rrze-expo-booth-schedule-location',
            'type' => 'select',
            'default' => 'none',
            'options' => [
                'none' => __( 'No Schedule', 'rrze-expo' ),
                'rollup' => __( 'Roll Up', 'rrze-expo' ),
                'left-screen' => __( 'Left Screen', 'rrze-expo' ),
                'right-screen' => __( 'Right Screen', 'rrze-expo' ),
            ],
        ] );

        $cmb_videos->add_field( array(
            'name' => __('Gallery', 'rrze-expo'),
            'desc' => '',
            'id'   => 'rrze-expo-booth-gallery',
            'type' => 'file_list',
            // 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
            'query_args' => array( 'type' => 'image' ), // Only images attachment
            // Optional, override default text strings
            'text' => array(
                'add_upload_files_text' => __('Add or Upload Files', 'rrze-expo'), // default: "Add or Upload Files"
                'remove_image_text' => __('Remove Image', 'rrze-expo'), // default: "Remove Image"
                'file_text' => __('File', 'rrze-expo').':', // default: "File:"
                'file_download_text' => __('Download', 'rrze-expo'), // default: "Download"
                'remove_text' => __('Remove ', 'rrze-expo'), // default: "Remove"
            ),
        ) );


        // Rollups
        $cmb_rollups = new_cmb2_box([
            'id'            => 'rrze-expo-booth-rollup-box',
            'title'         => __('Rollups', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $rollup_group_id = $cmb_rollups->add_field( [
            'id'          => 'rrze-expo-booth-rollups',
            'type'        => 'group',
            'description' => __( 'Choose up to 2 roll-up images.', 'rrze-expo' ),
            'options'     => array(
                'group_title'       => __( 'Roll-up {#}', 'rrze-expo' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'        => __( 'Add Another Roll-up', 'rrze-expo' ),
                'remove_button'     => __( 'Remove Roll-up', 'rrze-expo' ),
                'sortable'          => true,
                // 'closed'         => true, // true to have the groups closed by default
                // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'rrze-expo' ), // Performs confirmation before removing group.
            ),
        ] );
        $cmb_rollups->add_group_field($rollup_group_id, [
            'name'    => __('Image', 'rrze-expo'),
            'id'      => 'file',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            // query_args are passed to wp.media's library query.
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/svg+xml',
                ),
            ),
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ]);
        $cmb_rollups->add_group_field($rollup_group_id,[
            'name' => __('Clickable', 'rrze-expo'),
            'desc'    => __('Roll-up image opens in a lightbox for detailed view and is listed in the info box.', 'rrze-expo'),
            'id'   => 'clickable',
            'type' => 'checkbox',
        ]);


        // Flyer
        $cmb_flyer = new_cmb2_box([
            'id'            => 'rrze-expo-booth-flyer-box',
            'title'         => __('PDF Flyers', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $flyer_group_id = $cmb_flyer->add_field( [
            'id'          => 'rrze-expo-booth-flyer',
            'type'        => 'group',
            'description' => __( 'Choose up to 4 PDF flyers.', 'rrze-expo' ),
            'options'     => array(
                'group_title'       => __( 'Flyer {#}', 'rrze-expo' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'        => __( 'Add Another Flyer', 'rrze-expo' ),
                'remove_button'     => __( 'Remove Flyer', 'rrze-expo' ),
                'sortable'          => true,
                // 'closed'         => true, // true to have the groups closed by default
                // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'rrze-expo' ), // Performs confirmation before removing group.
            ),
        ] );
        $cmb_flyer->add_group_field($flyer_group_id, [
            'name'    => __('PDF File', 'rrze-expo'),
            'id'      => 'pdf',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            // query_args are passed to wp.media's library query.
            'query_args' => array(
                'type' => array(
                    'application/pdf',
                ),
            ),
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ]);
        $cmb_flyer->add_group_field($flyer_group_id, [
            'name'    => __('Preview Image', 'rrze-expo'),
            'id'      => 'preview',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            // query_args are passed to wp.media's library query.
            'query_args' => array(
                'type' => array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                ),
            ),
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ]);


        // Social Media
        $cmb_social_media = new_cmb2_box([
            'id'            => 'rrze-expo-booth-social-media-box',
            'title'         => __('Social Media', 'rrze-expo'),
            'object_types'  => ['booth'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
         $social_media_group_id = $cmb_social_media->add_field( [
            'id'          => 'rrze-expo-booth-social-media',
            'type'        => 'group',
            //'description' => __( '', 'rrze-expo' ),
            'options'     => array(
                'group_title'       => __( 'Social Media Item {#}', 'rrze-expo' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'        => __( 'Add Another Item', 'rrze-expo' ),
                'remove_button'     => __( 'Remove Item', 'rrze-expo' ),
                'sortable'          => true,
                // 'closed'         => true, // true to have the groups closed by default
                // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'rrze-expo' ), // Performs confirmation before removing group.
            ),
        ] );
        $socialMedia = $constants['social-media'];
        foreach ($socialMedia as $soMeName => $soMeUrl) {
            if ($soMeName != 'website') {
                $soMeOptions[$soMeName] = ucfirst($soMeName);
            }
        }
        $cmb_social_media->add_group_field($social_media_group_id, [
            'name'             => __('Social Media Type', 'rrze-expo'),
            //'desc'             => 'Select an option',
            'id'               => 'medianame',
            'type'             => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => 'custom',
            'options'          => $soMeOptions,
        ]);
        $cmb_social_media->add_group_field($social_media_group_id, [
            'type'  => 'text_medium',
            'name'  => __('User Name', 'rrze-expo'),
            'id'    => 'username',
            'description' => __('For YouTube Channels, please include the channel part of your account URL (e.g. "channel/a1b2c3" or "c/d4e5f6").', 'rrze-expo'),
        ]);
    }

    function getBoothHalls($field) {
        $boothID = $field->object_id;
        $expoID = get_post_meta($boothID, 'rrze-expo-booth-exposition', true);
        $halls = CPT::getPosts('hall', $expoID);
        return $halls;
    }

    function getDecoObjects($field) {
        $boothID = $field->object_id;
        $template = get_post_meta($boothID, 'rrze-expo-booth-template', true);
        if ($template == '')
            return;
        $constants = getConstants();
        $objects = $constants['template_elements']['booth'.$template]['deco'];
        return $objects;
    }

    function getLogoLocations($field) {
        $boothID = $field->object_id;
        $template = get_post_meta($boothID, 'rrze-expo-booth-template', true);
        if ($template == '')
            return;
        $constants = getConstants();
        $logos = $constants['template_elements']['booth'.$template]['logo'];
        $locations = [];
        foreach ($logos as $location => $data) {
            $locations[$location] = $data['title'];
        }
        return $locations;
    }

    function getBoothTemplate($field) {
        $boothID = $field->object_id;
        $template = get_post_meta($boothID, 'rrze-expo-booth-template', true);
        return ($template == '2');
    }
}
