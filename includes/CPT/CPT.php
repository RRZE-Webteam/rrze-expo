<?php


namespace RRZE\Expo\CPT;

defined('ABSPATH') || exit;

use function RRZE\Expo\Config\getConstants;

class CPT
{
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = getConstants();
    }

    public function onLoaded()
    {
        $booth = new Booth($this->pluginFile);
        $booth->onLoaded();

        $hall = new Hall($this->pluginFile);
        $hall->onLoaded();

        $foyer = new Foyer($this->pluginFile);
        $foyer->onLoaded();

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
            'order' => '',
        ) );
        //var_dump($value);
        $constants = getConstants();
        $socialMedia = $constants['social-media'];
        $numSocialMedia = count($socialMedia);
        ?>
        <div><label for="<?php echo $field_type->_id( '_username' ); ?>'" style="margin-left: 20px;"><?php _e('User Name', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'text',
                'name'  => $field_type->_name( '[username]' ),
                'id'    => $field_type->_id( '_username' ),
                'value' => $value['username'],
                'class' => 'medium-text',
                //'desc'  => '',
            ) ); ?>
            <label for="<?php echo $field_type->_id( '_order' ); ?>'" style="margin-left: 20px;"><?php _e('Order', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'number',
                'name'  => $field_type->_name( '[order]' ),
                'id'    => $field_type->_id( '_order' ),
                'value' => (!empty($value['order']) ? $value['order'] : $field->args['default']),
                'class' => 'small-text',
                'min'   => '1',
                'max'   => $numSocialMedia,
                'default' => $field->args['default'],
                //'desc'  => '',
            ) );
            ?>
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
            case 'foyer':
                return dirname($this->pluginFile) . '/includes/Templates/single-foyer.php';
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

    public function cssToFooter() {
        global $post;
        if (!in_array($post->post_type, ['booth', 'hall', 'foyer']))
            return;
        $meta = get_post_meta($post->ID);

        echo "<style type='text/css'>";

        switch($post->post_type) {
            // Booth
            case 'booth':
                // Background Image
                $backgroundColor = (CPT::getMeta($meta, 'rrze-expo-booth-overlay-color') == 'light' ? '#fff' : '#000');
                $opacity = CPT::getMeta($meta, 'rrze-expo-booth-overlay-opacity');
                echo ".rrze-expo .booth:after {
                    background-color: $backgroundColor;
                    opacity: $opacity;
                }";
                // Background Wall
                $backwallColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color');
                if ($backwallColor == 'custom') {
                    $backwallColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color-custom');
                }
                echo "svg.expo-booth #backwall {
                    fill: #$backwallColor;
                }";
                // Social Media Panel
                $soMeDefaults = ['show' => '',
                    'username' => '',
                    'order' => 0,];
                $constants = getConstants();
                $socialMedia = $constants['social-media'];
                $i = 0;
                foreach ($socialMedia as $soMeName => $soMeUrl) {
                    $soMeMeta =  wp_parse_args( CPT::getMeta($meta, 'rrze-expo-booth-'.$soMeName), $soMeDefaults);
                    if ($soMeMeta['username'] != '') {
                        $display = 'block';
                        $translate = (isset($soMeMeta['order']) ? $soMeMeta['order'] - 1 : $i).'%';
                        $translate = ((int)$translate * 3.5) .'%';
                    } else {
                        $display = 'none';
                        $translate = '';
                    }
                    echo "svg.expo-booth #$soMeName {
                        display: $display;
                        transform: translateY($translate);
                    }";
                    $i++;
                }
                break;
            // Hall
            case 'hall':
                // Background Image
                $backgroundColor = (CPT::getMeta($meta, 'rrze-expo-hall-overlay-color') == 'light' ? '#fff' : '#000');
                $opacity = CPT::getMeta($meta, 'rrze-expo-hall-overlay-opacity');
                echo ".rrze-expo .hall:after {
                    background-color: $backgroundColor;
                    opacity: $opacity;
                }";
                break;
            // Foyer
            case 'foyer':
                // Background Image
                $backgroundColor = (CPT::getMeta($meta, 'rrze-expo-foyer-overlay-color') == 'light' ? '#fff' : '#000');
                $opacity = CPT::getMeta($meta, 'rrze-expo-foyer-overlay-opacity');
                echo ".rrze-expo .foyer:after {
                    background-color: $backgroundColor;
                    opacity: $opacity;
                }";
                break;
        }
        if (!has_post_thumbnail($post->ID)) {
            echo '.rrze-expo #booth_logo {
                display: none;
            }';
        }

        echo "</style>";
    }

    public static function expoHeader() {
        global $post;
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
            <header id="masthead" class="site-header" role="banner">
                <div class="site-header-content">
                    <?php if ($post->post_type == 'booth') {
                        $hall = get_post_meta($post->ID, 'rrze-expo-booth-hall', true);
                        if ($hall != '') {
                            $link = get_permalink($hall);
                            $text = __('Back to Hall', 'rrze-expo');
                            echo "<a class='backlink' href='$link'>$text</a>";
                        }
                    } elseif ($post->post_type == 'booth') {
                        $foyer = get_post_meta($post->ID, 'rrze-expo-foyer-hall', true);
                        if ($foyer != '') {
                            $link = get_permalink($foyer);
                            $text = __('Back to Foyer', 'rrze-expo');
                            echo "<a href='$link' title='$text'></a>";
                        }

                    } ?>
                </div><!-- .site-header-content -->
            </header>


        <?php
    }

    public static function expoFooter() {

    }
}
