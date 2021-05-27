<?php


namespace RRZE\Expo\CPT;


class Podium {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [$this, 'podiumPostType']);
        add_action('cmb2_admin_init', [$this, 'podiumFields']);
    }

    // Register Custom Post Type
    public function podiumPostType(){
        $labels = [
            'name'                  => _x('Podiums', 'Post type general name', 'rrze-expo'),
            'singular_name'         => _x('Podium', 'Post type singular name', 'rrze-expo'),
            'menu_name'             => _x('Podiums', 'Admin Menu text', 'rrze-expo'),
            'name_admin_bar'        => _x('Podium', 'Add New on Toolbar', 'rrze-expo'),
            'add_new'               => __('Add New', 'rrze-expo'),
            'add_new_item'          => __('Add New Podium', 'rrze-expo'),
            'new_item'              => __('New Podium', 'rrze-expo'),
            'edit_item'             => __('Edit Podium', 'rrze-expo'),
            'view_item'             => __('View Podium', 'rrze-expo'),
            'all_items'             => __('All Podiums', 'rrze-expo'),
            'search_items'          => __('Search Podiums', 'rrze-expo'),
            'not_found'             => __('No Podiums found.', 'rrze-expo'),
            'not_found_in_trash'    => __('No Podiums found in Trash.', 'rrze-expo'),
            'featured_image'        => _x('Podium Logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set podium logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove podium logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as podium logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Podium archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into podium', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this podium', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter podiums list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Podiums list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Podiums list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $capabilities = CPT::makeCapabilities('exposition', 'expositions');
        $args = [
            'label' => __('Podium', 'rrze-expo'),
            'description' => __('Add and edit podium informations', 'rrze-expo'),
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
            'has_archive'               => 'podium',
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capabilities'              => $capabilities,
            'map_meta_cap'              => true,
        ];

        register_post_type('podium', $args);
    }

    public function podiumFields() {
        global $post;
        $cmb = new_cmb2_box([
            'id'            => 'rrze-expo-podium-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['podium'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb->add_field([
            'name'      => __('Exposition', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-podium-exposition',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '',
            'options'          => CPT::getPosts('exposition'),
        ]);

        // Background Image
        $cmb_background = new_cmb2_box([
            'id'            => 'rrze-expo-podium-background',
            'title'         => __('Background Image', 'rrze-expo'),
            'object_types'  => ['podium'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_background->add_field(array(
            'name'    => __('Background Image', 'rrze-expo'),
            'desc'    => __('If no background image is set, the foyer background image will be displayed.', 'rrze-expo'),
            'id'      => 'rrze-expo-podium-background-image',
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
            'id'        => 'rrze-expo-podium-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Background Overlay Opacity', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-podium-overlay-opacity',
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

        // Timetable
        $cmb_timetable = new_cmb2_box([
            'id'            => 'rrze-expo-podium-timetable',
            'title'         => __('Timetable', 'rrze-expo'),
            'object_types'  => ['podium'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $video_group_id = $cmb_timetable->add_field( [
            'id'          => 'rrze-expo-podium-timeslots',
            'type'        => 'group',
            //'description' => __( 'Add up to 3 video embedding urls, e.g. https://www.fau.tv/webplayer/id/123456. Display: 1 - top left screen, 2- top right screen, 3 - table monitor.', 'rrze-expo' ),
            'options'     => array(
                'group_title'       => __( 'Timeslot {#}', 'rrze-expo' ), // since version 1.1.4, {#} gets replaced by row number
                'add_button'        => __( 'Add Another Timeslot', 'rrze-expo' ),
                'remove_button'     => __( 'Remove Timeslot', 'rrze-expo' ),
                'sortable'          => true,
                // 'closed'         => true, // true to have the groups closed by default
                // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'rrze-expo' ), // Performs confirmation before removing group.
            ),
        ] );
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __('Title', 'rrze-expo'),
            'id' => 'title',
            'type' => 'text',
        ]);
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __( 'Start', 'rrze-expo' ),
            'id'   => 'start',
            'type' => 'text_datetime_timestamp',
            'date_format' => 'd.m.Y',
            'time_format' => 'H:i',
            'attributes' => array(
                'data-timepicker' => json_encode(
                    array(
                        'timeFormat' => 'HH:mm',
                        'stepMinute' => 5,
                    )
                ),
            ),
        ] );
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __( 'End', 'rrze-expo' ),
            'id'   => 'end',
            'type' => 'text_datetime_timestamp',
            'date_format' => 'd.m.Y',
            'time_format' => 'H:i',
            'attributes' => array(
                'data-timepicker' => json_encode(
                    array(
                        'timeFormat' => 'HH:mm',
                        'stepMinute' => 5,
                    )
                ),
            ),
        ] );
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __( 'Video URL', 'rrze-expo' ),
            'id'   => 'url',
            'type' => 'text_url',
            // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __('Short description', 'rrze-expo'),
            'id' => 'description',
            'type' => 'textarea_small',
        ]);
        $cmb_timetable->add_group_field($video_group_id, [
            'name' => __( 'Related Booth', 'rrze-expo' ),
            'id'   => 'booth',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '-1',
            'options_cb'          => [$this, 'getExpoBooths'],
        ] );
    }

    function getExpoBooths($field) {
        $podiumID = $field->object_id;
        $expoID = get_post_meta($podiumID, 'rrze-expo-podium-exposition', true);
        $booths = CPT::getPosts('booth', $expoID);
        return $booths;
    }
}
