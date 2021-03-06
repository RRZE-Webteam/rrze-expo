<?php


namespace RRZE\Expo\CPT;

defined('ABSPATH') || exit;

use RRZE\Expo\Config;
use function RRZE\Expo\Config\getConstants;use function RRZE\Expo\Config\getThemeGroup;

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

        $podium = new Podium($this->pluginFile);
        $podium->onLoaded();

        $hall = new Hall($this->pluginFile);
        $hall->onLoaded();

        $foyer = new Foyer($this->pluginFile);
        $foyer->onLoaded();

        $exposition = new Exposition($this->pluginFile);
        $exposition->onLoaded();

        add_filter('single_template', [$this, 'includeSingleTemplate']);
        add_action('wp_footer', [$this, 'svgToFooter']);
        add_action('wp_head', [$this, 'cssToFooter']);
        if ( function_exists( 'wpel_init' ) ) {
            add_action( 'wpel_apply_settings', [$this, 'disableWPExternalLinks'], 10 );
        }
        add_action( 'cmb2_render_select_multiple', [$this, 'renderMultipleSelect'], 10, 5 );
        add_filter( 'cmb2_sanitize_select_multiple', [$this,'sanitizeMultipleSelect'], 10, 2 );
    }

    public function activation()
    {
        $booth = new Booth($this->pluginFile);
        $booth->boothPostType();

        $podium = new Podium($this->pluginFile);
        $podium->podiumPostType();

        $hall = new Hall($this->pluginFile);
        $hall->hallPostType();

        $expo = new Exposition($this->pluginFile);
        $expo->expositionPostType();

    }

    public static function makeCapabilities($singular = 'exposition', $plural = 'expositions') {
        return [
            'edit_post'      => "edit_$singular",
            'read_post'      => "read_$singular",
            'delete_post'        => "delete_$singular",
            'edit_posts'         => "edit_$plural",
            'edit_others_posts'  => "edit_others_$plural",
            'publish_posts'      => "publish_$plural",
            'read_private_posts'     => "read_private_$plural",
            'read'                   => "read",
            'delete_posts'           => "delete_$plural",
            'delete_private_posts'   => "delete_private_$plural",
            'delete_published_posts' => "delete_published_$plural",
            'delete_others_posts'    => "delete_others_$plural",
            'edit_private_posts'     => "edit_private_$plural",
            'edit_published_posts'   => "edit_published_$plural",
            'create_posts'           => "edit_$plural",
        ];
    }
    public static function getPosts(string $postType, string $expoID = ''): array {
        $args = [
            'post_type' => $postType,
            'post_statue' => 'publish',
            'nopaging' => true,
            'orderby' => 'title',
            'order' => 'ASC',
        ];
        if ($expoID != '') {
            $args['meta_key'] = 'rrze-expo-'.$postType.'-exposition';
            $args['meta_value'] = $expoID;
        }
        $posts = get_posts($args);
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
            case 'podium':
                return dirname($this->pluginFile) . '/includes/Templates/single-podium.php';
            case 'hall':
                return dirname($this->pluginFile) . '/includes/Templates/single-hall.php';
            case 'foyer':
                return dirname($this->pluginFile) . '/includes/Templates/single-foyer.php';
            case 'exposition':
                return dirname($this->pluginFile) . '/includes/Templates/single-exposition.php';
        }
        return $singleTemplate;
    }

    public static function svgToFooter() {
        global $post;
        if (!in_array($post->post_type,  ['booth', 'podium', 'foyer', 'exposition']))
            return;
        switch ($post->post_type) {
            case 'booth':
                $templateNo = get_post_meta($post->ID,'rrze-expo-booth-template', true);
                $templateDir = '/rrze-expo/assets/img/booth-' . absint($templateNo).'/';
                break;
            case 'podium':
                $templateNo = get_post_meta($post->ID,'rrze-expo-podium-template', true);
                $templateDir = '/rrze-expo/assets/img/podium-' . absint($templateNo).'/';
                break;
            case 'foyer':
                $templateDir = '/rrze-expo/assets/img/foyer/';
                break;
            case 'exposition':
                $templateDir = '/rrze-expo/assets/img/expo/';
        }
        $file = WP_PLUGIN_DIR . $templateDir . 'template.svg';
        if ($file) {
            $svg = file_get_contents($file);
            echo str_replace('xlink:href="', 'xlink:href="'.WP_PLUGIN_URL . $templateDir, $svg);
        }

        // Icons
        $icons = [
            'chevron-left',
            'chevron-right',
            'chevron-down',
            'chevron-up',
            'chevron-double-up',
            'facebook',
            'instagram',
            'twitter',
            'xing',
            'website',
            'linkedin',
            'link',
            'youtube',
        ];
        echo '<svg style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><defs>';
        foreach ($icons as $icon) {
            if (file_exists(WP_PLUGIN_DIR . '/rrze-expo/assets/img/'.$icon.'.svg')) {
                $iconSvg = file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/'.$icon.'.svg');
            } else {
                $iconSvg = file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/link.svg');
            }
            echo str_replace(['<svg xmlns="http://www.w3.org/2000/svg"', '</svg>'], ['<symbol id="'.$icon.'"', '</symbol>'], $iconSvg);
        }
        echo '</defs></svg>';
    }

    public function cssToFooter() {
        global $post;
        if (!$post || !in_array($post->post_type, ['booth', 'hall', 'podium', 'foyer', 'exposition']))
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
                echo "svg.template-1 .backwall {
                    fill: $backwallColor;
                }
                svg #akzentpult {
                    fill: $backwallColor;
                }
                svg.template-2 .backwall {
                    fill: $backwallColor;
                }
                svg.template-2 .backwall-front {
                    fill: #dedede;
                }";
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
            // Podium
            case 'podium':
                // Background Image
                $backgroundColor = (CPT::getMeta($meta, 'rrze-expo-podium-overlay-color') == 'light' ? '#fff' : '#000');
                $opacity = CPT::getMeta($meta, 'rrze-expo-podium-overlay-opacity');
                echo ".rrze-expo .podium:after {
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
            case 'exposition':
                // Background Image
                $backgroundColor = (CPT::getMeta($meta, 'rrze-expo-exposition-overlay-color') == 'light' ? '#fff' : '#000');
                $opacity = CPT::getMeta($meta, 'rrze-expo-exposition-overlay-opacity');
                echo ".rrze-expo .exposition:after {
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
        if ($post->post_type == 'exposition') {
            $expoID = $post->ID;
        } else {
            $expoID = get_post_meta($post->ID, 'rrze-expo-'.$post->post_type.'-exposition', true);
        }
        $theme = wp_get_theme();
        $themeGroup = getThemeGroup($theme->Name);
        switch ($themeGroup) {
            case 'events':
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

                    <body <?php body_class('fau-events rrze-expo'); ?>>
                        <div class="container-all">
                            <nav id="skiplinks" aria-label="<?php _e('Skiplinks', 'fau-events'); ?>">
                                <ul class="skiplinks">
                                    <li><a href="#page-start" data-target="#page-start" data-firstchild="0" class="skiplink-content"><?php _e('Go to content area', 'fau-events'); ?></a></li>
                                    <li><a href="#desktop-search" data-target="#desktop-search .searchform input" data-firstchild="1" class="skiplink-search"><?php _e('Go to search', 'fau-events'); ?></a></li>
                                    <li><a href="#desktop-navigation" data-target="#desktop-navigation ul li a" data-firstchild="1" class="skiplink-nav"><?php _e('Go to main navigation', 'fau-events'); ?></a></li>
                                </ul>
                            </nav>
                            <header id="masthead" class="site-header" role="banner">
                                <div id="rrze-expo-header-content" class="rrze-expo-header-content" role="banner">
                                    <?php
                                    if ( $post->post_type != 'exposition' ) {
                                        echo '<a href="'.get_permalink($expoID).'">';
                                    }
                                    echo '<img class="expo-logo" src="'.get_the_post_thumbnail_url($expoID, 'medium').'">';
                                    echo '<div><p class="expo-title">' . get_the_title($expoID) . '</p>';
                                    $subtitle = get_post_meta($expoID, 'rrze-expo-exposition-subtitle', true);
                                    if ($subtitle != '') {
                                        echo '<p class="expo-subtitle">' . $subtitle . '</p>';
                                    }
                                    echo '</div>';
                                    if ( $post->post_type != 'exposition' ) {
                                        echo '</a>';
                                    }
                                    ?>
                                </div><!-- .site-header-content -->
                            </header>
                <?php
                break;
            case 'fau':
            default:
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
            <body <?php body_class('rrze-expo'); ?>>
            <div class="container-all">
                <a name="pagewrapper"></a>
                <nav id="skiplinks" aria-label="<?php _e('Skiplinks', 'rrze-expo'); ?>">
                    <ul class="jumplinks">
                        <li><a href="#page-start" data-target="#page-start" data-firstchild="0" class="skiplink-content"><?php _e('Go to content area', 'rrze-expo'); ?></a></li>
                        <li><a href="#desktop-search" data-target="#desktop-search .searchform input" data-firstchild="1" class="skiplink-search"><?php _e('Go to search', 'rrze-expo'); ?></a></li>
                        <li><a href="#desktop-navigation" data-target="#desktop-navigation ul li a" data-firstchild="1" class="skiplink-nav"><?php _e('Go to main navigation', 'rrze-expo'); ?></a></li>
                    </ul>
                </nav>
                <header id="masthead" class="site-header" role="banner">
                    <div id="rrze-expo-header-content" class="rrze-expo-header-content" role="banner">
                        <?php
                        if ( $post->post_type != 'exposition' ) {
                            echo '<a href="'.get_permalink($expoID).'">';
                        }
                        echo '<img class="expo-logo" src="'.get_the_post_thumbnail_url($expoID, 'medium').'">';
                        echo '<div><p class="expo-title">' . get_the_title($expoID) . '</p>';
                        $subtitle = get_post_meta($expoID, 'rrze-expo-exposition-subtitle', true);
                        if ($subtitle != '') {
                            echo '<p class="expo-subtitle">' . $subtitle . '</p>';
                        }
                        echo '</div>';
                        if ( $post->post_type != 'exposition' ) {
                            echo '</a>';
                        }
                        ?>
                    </div><!-- .site-header-content -->
                </header>
        <?php }
    }

    public static function expoFooter() {

    }

    public static function expoNav() {
        global $post;
        $foyerID = '';
        $expoID = '';
        if ($post->post_type == 'foyer') {
            $foyerID = $post->ID;
        } else {
            if ($post->post_type == 'booth') {
                $hallID = get_post_meta($post->ID, 'rrze-expo-booth-hall', true);
            } elseif ($post->post_type == 'hall') {
                $hallID = $post->ID;
            }
            $expoID = get_post_meta($post->ID, 'rrze-expo-'.$post->post_type.'-exposition', true);
            if ($expoID != '') {
                $foyer = get_posts([
                    'post_type'     => 'foyer',
                    'status'        => 'publish',
                    'meta_key'      => 'rrze-expo-foyer-exposition',
                    'meta_value'    => $expoID,
                    'posts_per_page'   => 1,
                    'fields'        => 'ids'
                ]);
                if (!empty($foyer)) {
                    $foyerID = $foyer[0];
                } else {
                    $foyerID = '';
                }
            }
        }
        if (in_array($post->post_type, ['booth', 'hall', 'podium'])) { ?>
            <nav class="booth-nav" aria-label="<?php _e('Booth Navigation', 'rrze-expo');?>"><ul>
                <?php if ($post->post_type == 'booth') {
                    $boothId = $post->ID;
                    $boothIDsOrdered = CPT::getBoothOrder($boothId);
                    $orderNo = array_search($boothId, $boothIDsOrdered);
                    if ($orderNo > 0) {
                        $prevBoothID = $boothIDsOrdered[$orderNo-1]; ?>
                        <li class="prev-booth">
                            <a href="<?php echo get_permalink($prevBoothID);?>#rrze-expo-booth" class="">
                                <svg height="16" width="16" aria-hidden="true"><use xlink:href="#chevron-left"></use></svg>
                                <span class="nav-prev-text"><?php echo __('Previous Booth','rrze-expo') . '<span class="booth-title">:<br />' . get_the_title($prevBoothID);?></span></span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if (($orderNo + 1) < count($boothIDsOrdered)) {
                        $nextBoothID = $boothIDsOrdered[($orderNo + 1)]; ?>
                        <li class="next-booth">
                            <a href="<?php echo get_permalink($nextBoothID);?>#rrze-expo-booth" class="">
                                <span class="nav-next-text"><?php echo __('Next Booth','rrze-expo') . '<span class="booth-title">:<br />' . get_the_title($nextBoothID);?></span></span>
                                <svg height="16" width="16" aria-hidden="true"><use xlink:href="#chevron-right"></use></svg>
                            </a>
                        </li>
                    <?php }
                    if ($hallID != '') {
                        $hallLink = get_permalink($hallID);
                        $hallText = __('Back to Hall', 'rrze-expo') . ': ' . get_the_title($hallID);
                        echo "<li class='hall-link'><a class='backlink-hall' href='$hallLink'><svg height='16' width='16'><use xlink:href='#chevron-up'></use></svg> $hallText</a></li>";
                    }
                }
                if ($foyerID != '') {
                    $foyerLink = get_permalink($foyerID);
                    $foyerText = __('Back to Foyer', 'rrze-expo');
                    echo "<li class='foyer-link'><a class='backlink-foyer' href='$foyerLink'><svg height='14' width='14'><use xlink:href='#chevron-double-up'></use></svg> $foyerText</a></li>";
                }
                ?>
            </ul></nav>
            <?php }
    }

    /**
     * getBoothOrder
     * returns an array of booth IDs in the same hall, ordered by corresponding hall menu order, or alphabetically if no hall menu is set
     * @param int $itemID (may be a hall OR a booth ID)
     * @return array
     */
    public static function getBoothOrder($itemID) {
        $postType = get_post_type($itemID);
        if ($postType == 'booth') {
            $hallID = get_post_meta($itemID, 'rrze-expo-booth-hall', true);
        } else {
            $hallID = $itemID;
        }

        $boothIDs = [];
        // If there is a menu for that hall -> get booth order by menu order
        $menuID = get_post_meta($hallID, 'rrze-expo-hall-menu', true);
        if ($menuID != '') {
            $items = wp_get_nav_menu_items(absint($menuID));
            foreach ( $items as $item) {
                if ($item->menu_item_parent == 0) {
                    $boothIDs[] = $item->object_id;
                }
            }
        } else {
            // If there is no hall menu -> get booths of this hall ordered alphabetically
            $boothIDs = get_posts([
                'post_type' => 'booth',
                'status'    => 'publish',
                'numberposts' => '-1',
                'meta_key'  => 'rrze-expo-booth-hall',
                'meta_value'    => $hallID,
                'orderby'   => 'title',
                'order'     => 'ASC',
                'fields'    => 'ids',
            ]);
        }
        return $boothIDs;
    }

    /**
     * getHallOrder
     * returns an array of hall IDs in the same exposition, ordered by foyer menu order, or alphabetically if no menu is set
     * @param int $itemID
     * @return array
     */
    public static function getHallOrder($itemID) {
        $postType = get_post_type($itemID);
        $expoID = get_post_meta($itemID, 'rrze-expo-'.$postType.'-exposition', true);
        $hallIDs = [];

        // If post type is foyer and foyer menu is set -> get hall order by menu order
        if ($postType == 'foyer') {
            $menuID = get_post_meta($itemID, 'rrze-expo-foyer-menu-halls', true);
            if ($menuID != '') {
                $items = wp_get_nav_menu_items(absint($menuID));
                foreach ( $items as $item) {
                    if ($item->menu_item_parent == 0) {
                        $hallIDs[] = $item->object_id;
                    }
                }
                return $hallIDs;
            }

        }

        // If hall Array is (still) empty -> get halls of this exposition ordered alphbetically
        $hallIDs = get_posts([
            'post_type' => 'hall',
            'status'    => 'publish',
            'numberposts' => '-1',
            'meta_key'  => 'rrze-expo-hall-exposition',
            'meta_value'    => $expoID,
            'orderby'   => 'title',
            'order'     => 'ASC',
            'fields'    => 'ids',
        ]);
        return $hallIDs;
    }

    /**
     * getPodiumOrder
     * returns an array of hall IDs in the same exposition, ordered by foyer menu order, or alphabetically if no menu is set
     * @param int $itemID
     * @return array
     */
    public static function getPodiumOrder($itemID) {
        $postType = get_post_type($itemID);
        $expoID = get_post_meta($itemID, 'rrze-expo-'.$postType.'-exposition', true);
        $podiumIDs = [];

        // If post type is foyer and foyer menu is set -> get podium order by menu order
        if ($postType == 'foyer') {
            $menuID = get_post_meta($itemID, 'rrze-expo-foyer-menu-podiums', true);
            if ($menuID != '') {
                $items = wp_get_nav_menu_items(absint($menuID));
                foreach ( $items as $item) {
                    if ($item->menu_item_parent == 0) {
                        $podiumIDs[] = $item->object_id;
                    }
                }
                return $podiumIDs;
            }

        }

        // If hall Array is (still) empty -> get halls of this exposition ordered alphbetically
        $podiumIDs = get_posts([
            'post_type' => 'podium',
            'status'    => 'publish',
            'numberposts' => '-1',
            'meta_key'  => 'rrze-expo-podium-exposition',
            'meta_value'    => $expoID,
            'orderby'   => 'title',
            'order'     => 'ASC',
            'fields'    => 'ids',
        ]);
        return $podiumIDs;
    }

    public static function pulsatingDot() {
        return '<div class="puls-container">
                <div class="puls-middle"></div>
                <div class="puls"></div>
                </div>';
    }

    public function disableWPExternalLinks() {
        $ignoredCPTs = ['booth', 'podium', 'hall', 'foyer', 'exposition'];
        if ( in_array( get_post_type(), $ignoredCPTs ) ) {
          return false;
        }
        return true;
    }

    public function renderMultipleSelect( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        if (!is_array($escaped_value)) {
            $escaped_value = [$escaped_value];
        }
        $select_multiple = '<select multiple name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . '"';
        foreach ( $field->args['attributes'] as $attribute => $value ) {
            $select_multiple .= " $attribute=\"$value\"";
        }
        $select_multiple .= ' />';

        foreach ( $field->options() as $value => $name ) {
            $selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'selected="selected"' : '';
            $select_multiple .= '<option class="cmb2-option" value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
        }

        $select_multiple .= '</select>';
        $select_multiple .= $field_type_object->_desc( true );

        echo $select_multiple; // WPCS: XSS ok.
    }

    public function sanitizeMultipleSelect( $override_value, $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $saved_value ) {
                $value[$key] = sanitize_text_field( $saved_value );
            }
            return $value;
        }
        return;
    }

}
