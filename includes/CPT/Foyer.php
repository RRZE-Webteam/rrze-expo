<?php


namespace RRZE\Expo\CPT;

use function RRZE\Expo\Config\getConstants;

class Foyer {

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [__CLASS__, 'foyerPostType']);
        add_action('cmb2_admin_init', [$this, 'foyerFields']);
    }

    // Register Custom Post Type
    public static function foyerPostType(){
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
            'featured_image'        => _x('Foyer Logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set foyer Logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove foyer logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as foyer logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Foyer archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into foyer', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this foyer', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter foyers list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Foyers list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Foyers list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $capabilities = CPT::makeCapabilities('foyer', 'foyers');
        $args = [
            'label' => __('Foyer', 'rrze-expo'),
            'description' => __('Add and edit foyer informations', 'rrze-expo'),
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
            'capability_type'           => 'foyer',
            'map_meta_cap'              => true,
        ];

        register_post_type('foyer', $args);
    }

    public function foyerFields() {
        $constants = getConstants();

        // General Information
        $cmb = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb->add_field([
            'name'      => __('Exposition', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-exposition',
            'type'      => 'select',
            'show_option_none' => '&mdash; ' . __('Please select', 'rrze-expo') . ' &mdash;',
            'default'          => '',
            'options'          => CPT::getPosts('exposition'),
        ]);

        $cmb->add_field([
            'name'      => __('Info Panel Content', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-panel-content',
            'type'      => 'wysiwyg',
            'default'          => '',
            /*'options' => [
                'teeny' => true,
            ],*/
        ]);
        $cmb->add_field([
            'name' => __( 'Table Screen (Live Chat)', 'rrze-expo' ),
            'description' => __( 'Enter video conference or chat tool link.', 'rrze-expo' ),
            'id'   => 'rrze-expo-foyer-video-table',
            'type' => 'text_url',
            // 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
        ] );


        $cmb->add_field([
            'name'      => __('Table Icon', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-foyer-table-icon',
            'type'      => 'select',
            'default'          => 'logo',
            'options'          => [ 'none' => __('No Icon', 'rrze-expo'),
                'foyer-logo' => __('Foyer Logo', 'rrze-expo'),
                'expo-logo' => __('Exposition Logo', 'rrze-expo'),
                'info' => __('Info Icon', 'rrze-expo')],
        ]);
        $personas = $constants['template_elements']['foyer']['persona'];
        $numPersonas = count($personas);
        for ($i=1; $i<=$numPersonas; $i++) {
            $cmb->add_field([
                'name'      => __('Persona', 'rrze-expo') . " $i",
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-persona-'.$i,
                'type'      => 'persona_field',
            ]);
        }

        for ($i=1; $i<=3; $i++) {
            $cmb->add_field([
                'name' => __('Seat', 'rrze-expo') . " $i",
                //'desc'    => __('', 'rrze-expo'),
                'id' => 'rrze-expo-foyer-seat-'.$i,
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

        // Direction Board
        $cmb_panels = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-panels',
            'title'         => __('Direction Board', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);

        $boardContent = CPT::getPosts('hall');
        $podiums = CPT::getPosts('podium');
        foreach ($podiums as $k => $v) {
            $boardContent[$k] = $v;
        }
        $boardContent['custom'] = __('Custom Link');
        for ($i=1; $i<7; $i++) {
            $board_content_group_id = $cmb_panels->add_field( [
                'id'          => 'rrze-expo-foyer-board-'.$i,
                'type'        => 'group',
                //'description' => __( '', 'rrze-expo' ),
                'repeatable'  => false,
                'options'     => array(
                    'group_title'       => __( 'Direction Board', 'rrze-expo' ) . ' '.$i, // since version 1.1.4, {#} gets replaced by row number
                    'sortable'          => false,
                    // 'closed'         => true, // true to have the groups closed by default
                    // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'rrze-expo' ), // Performs confirmation before removing group.
                ),
            ] );
            $cmb_panels->add_group_field($board_content_group_id, [
                    'name'      => __('Content', 'rrze-expo'),
                    //'desc'    => __('', 'rrze-expo'),
                    'id'        => 'rrze-expo-foyer-board-'.$i.'-content',
                    'type'      => 'select',
                    'show_option_none' => __('No content', 'rrze-expo'),
                    'default'          => '',
                    'options'          => $boardContent,
                ]);
            $cmb_panels->add_group_field($board_content_group_id, [
                'name'      => __('Custom link text', 'rrze-expo'),
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-board-'.$i.'-text',
                'type'      => 'text',
                'sanitization_cb' => [$this, 'textSanitize'],
            ]);
            $cmb_panels->add_group_field($board_content_group_id, [
                'name'      => __('Custom link', 'rrze-expo'),
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-board-'.$i.'-link',
                'type'      => 'text_url',
            ]);
            $cmb_panels->add_group_field( $board_content_group_id, [
                'name'    => __('Board Color', 'rrze-expo'),
                'id'      => 'rrze-expo-foyer-board-'.$i.'-color',
                'type'    => 'colorpicker',
                'default' => '#ffffff',
                'attributes' => [
                    'data-colorpicker' => json_encode( [
                        'palettes' => [ '#003366', '#A36B0D', '#8d1429', '#0381A2', '#048767', '#6E7881' ],
                    ] ),
                ],
            ] );
            $cmb_panels->add_group_field( $board_content_group_id, [
                'name'    => __('Font Color', 'rrze-expo'),
                'desc'    => __('Please make sure there is enough contrast between font and background color.', 'rrze-expo'),
                'id'      => 'rrze-expo-foyer-board-'.$i.'-font-color',
                'type'    => 'colorpicker',
                'default' => '#000000',
                'attributes' => [
                    'data-colorpicker' => json_encode( [
                        'palettes' => [ '#000000', '#ffffff', '#003366', '#A36B0D', '#8d1429', '#0381A2', '#048767', '#6E7881' ],
                    ] ),
                ],
            ] );
            $cmb_panels->add_group_field( $board_content_group_id, [
                'name'      => __('Font Size', 'rrze-expo'),
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-foyer-board-'.$i.'-font-size',
                'type'      => 'select',
                'default'          => '40',
                'options'          => ['30' => '30',
                    '40' => '40',
                    '50' => '50',
                    '60' => '60',
                    '70' => '70',
                    '80' => '80',
                    '90' => '90'],
            ]);
        }

        // Social Media Panel
        $cmb_social_media = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-social-media-box',
            'title'         => __('Social Media', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $social_media_group_id = $cmb_social_media->add_field( [
            'id'          => 'rrze-expo-foyer-social-media',
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
        ]);

        // Background Image
        $cmb_background = new_cmb2_box([
            'id'            => 'rrze-expo-foyer-background',
            'title'         => __('Background Image', 'rrze-expo'),
            'object_types'  => ['foyer'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_background->add_field(array(
            'name'    => __('Background Image', 'rrze-expo'),
            'desc'    => __('If no background image is set, the exposition background image will be displayed.', 'rrze-expo'),
            'id'      => 'rrze-expo-foyer-background-image',
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
            'id'        => 'rrze-expo-foyer-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Background Overlay Opacity', 'rrze-expo'),
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

    }

    function textSanitize($originalValue, $args, $field) {
        return wp_kses($originalValue, ['br' => [], 'em' => [], 'strong' => [], 'b' => [], 'i' => [], 'sup' => [], 'sub' => []],);
    }

}
