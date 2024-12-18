<?php


namespace RRZE\Expo\CPT;

use function RRZE\Expo\Config\getConstants;

class Exposition {

    protected $pluginFile;

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($this->pluginFile) . 'vendor/cmb2/init.php';
    }

    public function onLoaded(){
        add_action('init', [__CLASS__, 'expositionPostType']);
        add_action('cmb2_admin_init', [$this, 'expositionFields']);
    }

    // Register Custom Post Type
    public static function expositionPostType(){
        $labels = [
            'name'                  => _x('Expositions', 'Post type general name', 'rrze-expo'),
            'singular_name'         => _x('Exposition', 'Post type singular name', 'rrze-expo'),
            'menu_name'             => _x('Expositions', 'Admin Menu text', 'rrze-expo'),
            'name_admin_bar'        => _x('Exposition', 'Add New on Toolbar', 'rrze-expo'),
            'add_new'               => __('Add New Exposition', 'rrze-expo'),
            'add_new_item'          => __('Add New Exposition', 'rrze-expo'),
            'new_item'              => __('New Exposition', 'rrze-expo'),
            'edit_item'             => __('Edit Exposition', 'rrze-expo'),
            'view_item'             => __('View Exposition', 'rrze-expo'),
            'all_items'             => __('All Expositions', 'rrze-expo'),
            'search_items'          => __('Search Expositions', 'rrze-expo'),
            'not_found'             => __('No Expositions found.', 'rrze-expo'),
            'not_found_in_trash'    => __('No Expositions found in Trash.', 'rrze-expo'),
            'featured_image'        => _x('Exposition Logo', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'set_featured_image'    => _x('Set exposition Logo', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'remove_featured_image' => _x('Remove exposition logo', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'use_featured_image'    => _x('Use as exposition logo', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'rrze-expo'),
            'archives'              => _x('Exposition archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'rrze-expo'),
            'insert_into_item'      => _x('Insert into exposition', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'rrze-expo'),
            'uploaded_to_this_item' => _x('Uploaded to this exposition', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'rrze-expo'),
            'filter_items_list'     => _x('Filter expositions list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'rrze-expo'),
            'items_list_navigation' => _x('Expositions list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'rrze-expo'),
            'items_list'            => _x('Expositions list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'rrze-expo'),
        ];

        $capabilities = CPT::makeCapabilities('exposition', 'expositions');
        $args = [
            'label' => __('Exposition', 'rrze-expo'),
            'description' => __('Add and edit exposition informations', 'rrze-expo'),
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
            'has_archive'               => false,
            'exclude_from_search'       => true,
            'publicly_queryable'        => true,
            'delete_with_user'          => false,
            'show_in_rest'              => false,
            'capabilities'              => $capabilities,
            'capability_type'           => 'exposition',
            'map_meta_cap'              => true,
        ];

        register_post_type('exposition', $args);
    }

    public function expositionFields() {
        $constants = getConstants();

        $cmb = new_cmb2_box([
            'id'            => 'rrze-expo-exposition-general',
            'title'         => __('General Information', 'rrze-expo'),
            'object_types'  => ['exposition'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);

        $cmb->add_field( array(
            'name'    => __('Event Subtitle', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            //'default' => '',
            'id'      => 'rrze-expo-exposition-subtitle',
            'type'    => 'text',
        ) );

        $cmb->add_field([
            'name' => __( 'Start (Date/Time)', 'rrze-expo' ),
            'id'   => 'rrze-expo-exposition-startdate',
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

        $cmb->add_field([
            'name' => __( 'Start (Date/Time)', 'rrze-expo' ),
            'id'   => 'rrze-expo-exposition-enddate',
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

        $cmb->add_field( array(
            'name'    => __('Organisation', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-organisation',
            'type'    => 'text',
        ) );

        $cmb->add_field( array(
            'name'    => __('Organisation 2', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-organisation2',
            'type'    => 'text',
        ) );

        $cmb->add_field( array(
            'name'    => __('Street', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-street',
            'type'    => 'text',
        ) );

        $cmb->add_field( array(
            'name'    => __('Postal Code', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-postalcode',
            'type'    => 'text',
        ) );

        $cmb->add_field( array(
            'name'    => __('Locality', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-locality',
            'type'    => 'text',
        ) );

        // Background Image
        $cmb_background = new_cmb2_box([
            'id'            => 'rrze-expo-exposition-background',
            'title'         => __('Layout', 'rrze-expo'),
            'object_types'  => ['exposition'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_background->add_field(array(
            'name'    => __('Background Image', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-background-image',
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
            'id'        => 'rrze-expo-exposition-overlay-color',
            'type'      => 'select',
            'default'          => 'light',
            'options'          => [ 'light' => __('Light', 'rrze-expo'),
                'dark' => __('Dark', 'rrze-expo')],
        ]);
        $cmb_background->add_field([
            'name'      => __('Background Overlay Opacity', 'rrze-expo'),
            //'desc'    => __('', 'rrze-expo'),
            'id'        => 'rrze-expo-exposition-overlay-opacity',
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

        $personas = $constants['template_elements']['exposition']['persona'];
        $numPersonas = count($personas);
        for ($i=1; $i<=$numPersonas; $i++) {
            $cmb_background->add_field([
                'name'      => __('Persona', 'rrze-expo') . " $i",
                //'desc'    => __('', 'rrze-expo'),
                'id'        => 'rrze-expo-exposition-persona-'.$i,
                'type'      => 'rrze_expo_persona_field',
            ]);
        }

        // Info
        $cmb_elements = new_cmb2_box([
            'id'            => 'rrze-expo-exposition-elements',
            'title'         => __('Information Material', 'rrze-expo'),
            'object_types'  => ['exposition'],
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ]);
        $cmb_elements->add_field([
            'name'      => __('Welcome Panel Content', 'rrze-expo'),
            'id'        => 'rrze-expo-exposition-panel-content',
            'type'      => 'wysiwyg',
            'default'          => '',
            /*'options' => [
                'teeny' => true,
            ],*/
        ]);
        $cmb_elements->add_field( [
            'name'    => __('Welcome Panel Background Color', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-panel-background-color',
            'type'    => 'colorpicker',
            'default' => '#efefef',
            'attributes' => [
                'data-colorpicker' => json_encode( [
                    // Iris Options set here as values in the 'data-colorpicker' array
                    'palettes' => CPT::getDefaultColors('light'),
                ] ),
            ],
            // 'options' => array(
            // 	'alpha' => true, // Make this a rgba color picker.
            // ),
        ] );
        $cmb_elements->add_field( [
            'name'    => __('Welcome Panel Font Color', 'rrze-expo'),
            'id'      => 'rrze-expo-exposition-panel-font-color',
            'type'    => 'colorpicker',
            'default' => '#000000',
            'attributes' => [
                'data-colorpicker' => json_encode( [
                    // Iris Options set here as values in the 'data-colorpicker' array
                    'palettes' => CPT::getDefaultColors('font-dark'),
                ] ),
            ],
            // 'options' => array(
            // 	'alpha' => true, // Make this a rgba color picker.
            // ),
        ] );
        for ($i=1; $i<4; $i++) {
            $cmb_elements->add_field([
                'name'    => __('Flag', 'rrze-expo') . ' ' . $i,
                'id'      => 'rrze-expo-exposition-flag'.$i,
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
        }
    }
}
