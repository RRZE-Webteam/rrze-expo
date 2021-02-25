<?php


namespace RRZE\Expo\CPT;

defined('ABSPATH') || exit;

class CPT
{
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded()
    {
        $booth = new Booth($this->pluginFile);
        $booth->onLoaded();

        $hall = new Hall($this->pluginFile);
        $hall->onLoaded();

        add_filter('cmb2_render_social-media', [$this, 'cmb2_RenderSocialMediaFieldCallback'], 10, 5);
        //add_filter('cmb2_sanitize_social-media', [$this, 'cmb2_sanitizeSoMeCheckbox'], 10, 2 );
        add_filter('single_template', [$this, 'includeSingleTemplate']);
        //add_action('wp_footer', [$this, 'svgToFooter']);
        add_action('wp_head', [$this, 'cssToFooter']);
    }

    public function activation()
    {
        $booth = new Booth($this->pluginFile);
        $booth->booth_post_type();

        $hall = new Hall($this->pluginFile);
        $hall->booth_post_type();
    }

    public function cmb2_RenderSocialMediaFieldCallback($field, $value, $object_id, $object_type, $field_type) {
        // make sure we specify each part of the value we need.
        $value = wp_parse_args( $value, array(
            'show' => '',
            'username' => '',
            //'order' => '',
        ) );
        //var_dump($value);
        ?>
        <div><label for="<?php echo $field_type->_id( '_show' ); ?>"><?php _e('Show Icon', 'rrze-expo');?></label>
            <?php echo $field_type->checkbox( array(
                'name'  => $field_type->_name( '[show]' ),
                'id'    => $field_type->_id( '_show' ),
                'default' => '',
                //'sanitization_cb'  => 'cmb2_sanitizeSoMeCheckbox',
            ) ); ?>
            <label for="<?php echo $field_type->_id( '_username' ); ?>'" style="margin-left: 20px;"><?php _e('User Name', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'text',
                'name'  => $field_type->_name( '[username]' ),
                'id'    => $field_type->_id( '_username' ),
                'value' => $value['username'],
                'class' => 'medium-text',
                //'desc'  => '',
            ) ); /*?>
            <label for="<?php echo $field_type->_id( '_order' ); ?>'" style="margin-left: 20px;"><?php _e('Order', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'number',
                'name'  => $field_type->_name( '[order]' ),
                'id'    => $field_type->_id( '_order' ),
                'value' => $value['order'],
                'class' => 'small-text',
                //'desc'  => '',
            ) ); */?>
        </div>
        <?php
    }

    public function cmb2_sanitizeSoMeCheckbox($override_value, $value) {
        if (!isset($value['show']) || is_null($value['show']) || empty($value['show'])) {
            unset( $value['show'] );
            $value['show'] = 'off';
        } else {
            $value['show'] = 'on';
        }
        return $value;
    }

    public static function getPosts(string $postType): array {
        $posts = get_posts([
            'post_type' => $postType,
            'post_statue' => 'publish',
            'nopaging' => true,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        if (empty($posts)) {
            return [];
        }
        $result = [];
        foreach ($posts as $post) {
            $result[$post->ID] = $post->post_title;
        }
        return $result;
    }

    public static function getMeta($meta, $key) {
        if (!isset($meta[$key]))
            return '';
        if (strpos($meta[$key][0], 'a:',0) === 0) {
            return unserialize($meta[$key][0]);
        } else {
            return $meta[$key][0];
        }
    }

    public function includeSingleTemplate($singleTemplate) {
        global $post;
        switch ($post->post_type) {
            case 'booth':
                return dirname($this->pluginFile) . '/includes/Templates/single-booth.php';
            case 'hall':
                return dirname($this->pluginFile) . '/includes/Templates/single-hall.php';
        }
        return $singleTemplate;
    }

    public static function svgToFooter() {
        global $post;
        if ($post->post_type != 'booth')
            return;
        $templateNo = get_post_meta($post->ID, 'rrze-expo-booth-template', true);
        if ($templateNo == '')
            return;
        include WP_PLUGIN_DIR.'/rrze-expo/assets/img/booth_template_'.absint($templateNo).'.svg';
    }

    public static function cssToFooter() {
        global $post;
        if ($post->post_type != 'booth')
            return;
        $meta = get_post_meta($post->ID);
        $soMeDefaults = ['show' => '',
            'username' => ''];
        $twitter = wp_parse_args( CPT::getMeta($meta, 'rrze-expo-booth-twitter'), $soMeDefaults);
        $facebook = wp_parse_args( CPT::getMeta($meta, 'rrze-expo-booth-facebook'), $soMeDefaults);
        $instagram = wp_parse_args( CPT::getMeta($meta, 'rrze-expo-booth-instagram'), $soMeDefaults);
        $youtube = wp_parse_args( CPT::getMeta($meta, 'rrze-expo-booth-youtube'), $soMeDefaults);
        $backwallColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color');
        if ($backwallColor == 'custom') {
            $backwallColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color-custom');
        }
        ?>
        <style type="text/css">
            .rrze-expo .booth:after {
                background-color: <?php echo (CPT::getMeta($meta, 'rrze-expo-booth-overlay-color') == 'light' ? '#fff' : '#000');?>;
                opacity: <?php echo CPT::getMeta($meta, 'rrze-expo-booth-overlay-opacity');?>;
                }
            svg.expo-booth #backwall {
                fill: #<?php echo $backwallColor;?>
            }
            svg.expo-booth #twitter {
                fill: #0059b3;
                --some-display-twitter: <?php echo ($twitter['show'] == 'on' && $twitter['username'] != '' ? 'block' : 'none');?>;
            }
            svg.expo-booth #facebook {
                fill: #f00;
                --some-display-facebook: <?php echo ($facebook['show'] == 'on' && $facebook['username'] != '' ? 'block' : 'none');?>;
            }
            svg.expo-booth #instagram {
                fill: #f00;
                --some-display-facebook: <?php echo ($instagram['show'] == 'on' && $instagram['username'] != '' ? 'block' : 'none');?>;
            }
            svg.expo-booth #youtube {
                fill: #f00;
                --some-display-facebook: <?php echo ($youtube['show'] == 'on' && $youtube['username'] != '' ? 'block' : 'none');?>;
            }
        </style>
        <?php
    }

    public static function expoHeader() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?> class="no-js">
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="profile" href="http://gmpg.org/xfn/11">
            <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
            <?php wp_head(); ?>
        </head>
        <body <?php body_class(''); ?>>
        <div class="container-all">
            <nav id="skiplinks" aria-label="<?php _e('Skiplinks', 'fau-events'); ?>">
                <ul class="jumplinks">
                    <li><a href="#page-start" data-target="#page-start" data-firstchild="0" class="skiplink-content"><?php _e('Go to content area', 'fau-events'); ?></a></li>
                    <li><a href="#desktop-search" data-target="#desktop-search .searchform input" data-firstchild="1" class="skiplink-search"><?php _e('Go to search', 'fau-events'); ?></a></li>
                    <li><a href="#desktop-navigation" data-target="#desktop-navigation ul li a" data-firstchild="1" class="skiplink-nav"><?php _e('Go to main navigation', 'fau-events'); ?></a></li>
                </ul>
            </nav>
            <header id="masthead" class="site-header cf" role="banner">
                <div class="site-header-content">

                </div><!-- .site-header-content -->
            </header>


        <?php
    }

    public static function expoFooter() {

    }
}
