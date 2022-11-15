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
        add_action( 'cmb2_render_rrze_expo_select_multiple', [$this, 'renderMultipleSelect'], 10, 5 );
        add_filter( 'cmb2_sanitize_rrze_expo_select_multiple', [$this,'sanitizeMultipleSelect'], 10, 2 );
        add_action( 'cmb2_render_rrze_expo_persona_field', [$this, 'renderPersonaField'], 10, 5 );
        add_filter( 'cmb2_sanitize_rrze_expo_persona_field', [$this, 'sanitizePersonaField'], 10, 2 );
        add_action( 'cmb2_render_rrze_expo_text_number', [$this, 'renderTextNumberField'], 10, 5 );
        add_filter( 'cmb2_sanitize_rrze_expo_text_number', [$this, 'sanitizeTextNumberField'], 10, 2 );
        // Allow Exposition as Front Page
        add_filter( 'get_pages', [$this, 'addExpoCptToDropdown'] );
        add_action( 'pre_get_posts', [$this, 'enableFrontPageCPT'] );
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts'], 1000);
        // Skip cache (for Safari video workaround)
        add_filter('rrzecache_skip_cache', function($skip_cache) {
            if (in_array(get_post_type(), ['booth', 'podium'])) {
                return true;
            }
            return $skip_cache;
        });
    }

    public static function activation() {
        Booth::boothPostType();
        Podium::podiumPostType();
        Hall::hallPostType();
        Foyer::foyerPostType();
        Exposition::expositionPostType();
    }

    public static function makeCapabilities($singular = 'exposition', $plural = 'expositions') {
        return [
            'edit_post'          => "edit_$singular",
            'read_post'          => "read_$singular",
            'delete_post'        => "delete_$singular",
            'edit_posts'         => "edit_$plural",
            'edit_others_posts'  => "edit_others_$plural",
            'publish_posts'      => "publish_$plural",
            'read_private_posts'     => "read_private_$plural",
            'delete_posts'           => "delete_$plural",
            'delete_private_posts'   => "delete_private_$plural",
            'delete_published_posts' => "delete_published_$plural",
            'delete_others_posts'    => "delete_others_$plural",
            'edit_private_posts'     => "edit_private_$plural",
            'edit_published_posts'   => "edit_published_$plural",
        ];
    }

    public static function getPosts(string $postType, string $expoID = '', array $exclude = []): array {
        $args = [
            'post_type' => $postType,
            'post_statue' => 'publish',
            'nopaging' => true,
            'orderby' => 'title',
            'order' => 'ASC',
            'exclude' => $exclude,
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

    public function enqueueScripts() {
        if (get_post_type() === 'booth') {
            wp_deregister_script('fau-scripts');
            wp_dequeue_script('fau-scripts');
            wp_deregister_script('ili-fau-templates-main');
            wp_dequeue_script('ili-fau-templates-main');
            wp_deregister_style('ili-fau-templates');
            wp_dequeue_style('ili-fau-templates');
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
        if (!$post)
            return;
        if (!in_array($post->post_type,  ['booth', 'hall', 'podium', 'foyer', 'exposition']))
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
        if (isset($templateDir)) {
            $file = WP_PLUGIN_DIR . $templateDir . 'template.svg';
            if ($file) {
                $svg = file_get_contents($file);
                echo str_replace('xlink:href="', 'xlink:href="'.WP_PLUGIN_URL . $templateDir, $svg);
            }
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
            'paper-plane',
            'list',
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
                            <nav id="skiplinks" aria-label="<?php _e('Skiplinks', 'rrze-expo'); ?>">
                                <ul class="skiplinks">
                                    <li><a href="#page-start" data-target="#page-start" data-firstchild="0" class="skiplink-content"><?php _e('Go to content area', 'rrze-expo'); ?></a></li>
                                    <?php if ($post->post_type == 'booth') { ?>
                                        <li><a href="#booth-navigation" data-target="#desktop-navigation ul li a" data-firstchild="1" class="skiplink-nav"><?php _e('Go to main navigation', 'rrze-expo'); ?></a></li>
                                    <?php } ?>
                                </ul>
                            </nav>
                            <header id="masthead" class="site-header" role="banner">
                                <div id="rrze-expo-header-content" class="rrze-expo-header-content" role="banner">
                                    <?php
                                    if ( $post->post_type != 'exposition' ) {
                                        echo '<a href="'.get_permalink($expoID).'">';
                                    }
                                    echo '<img class="expo-logo" src="'.get_the_post_thumbnail_url($expoID, 'medium').'" alt="' . get_the_title($expoID) . '">';
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
                                    <?php if (is_active_sidebar('sidebar-language_switcher')) { ?>
                                        <aside id="sidebar-language_switcher" class="sidebar-language_switcher widget-area">
                                            <div class="widget-area">
                                                <?php dynamic_sidebar('sidebar-language_switcher'); ?>
                                            </div><!-- .widget-area -->
                                        </aside><!-- .sidebar-page -->
                                    <?php } ?>
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
                        echo '<img class="expo-logo" src="'.get_the_post_thumbnail_url($expoID, 'medium').'" alt="' . get_the_title($expoID) . '">';
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
                        <?php if (is_active_sidebar('sidebar-language_switcher')) { ?>
                            <aside id="sidebar-language_switcher" class="sidebar-language_switcher widget-area">
                                <div class="widget-area">
                                    <?php dynamic_sidebar('sidebar-language_switcher'); ?>
                                </div><!-- .widget-area -->
                            </aside><!-- .sidebar-page -->
                        <?php } ?>
                    </div><!-- .site-header-content -->
                </header>
        <?php }
    }

    public static function expoFooter() {

    }

    public static function expoNav() {
        global $post;
        $postType = $post->post_type;
        $hallID = '';
        $foyerID = '';

        switch ($postType) {
            case 'booth':
                $hallID = get_post_meta($post->ID, 'rrze-expo-booth-hall', true);
                $foyerID = get_post_meta($hallID, 'rrze-expo-hall-foyer', true);
                $labels = [
                    'nav' => __('Booth Navigation', 'rrze-expo'),
                    'next' => __('Next Booth','rrze-expo'),
                    'prev' => __('Previous Booth','rrze-expo'),
                ];
                break;
            case 'hall':
                $foyerID = get_post_meta($post->ID, 'rrze-expo-hall-foyer', true);
                $labels = [
                    'nav' => __('Hall Navigation', 'rrze-expo'),
                    'next' => __('Next Hall','rrze-expo'),
                    'prev' => __('Previous Hall','rrze-expo'),
                ];
                break;
            case 'podium':
                $foyerID = get_post_meta($post->ID, 'rrze-expo-podium-foyer', true);
                $labels = [
                    'nav' => __('Podium Navigation', 'rrze-expo'),
                    'next' => __('Next Podium','rrze-expo'),
                    'prev' => __('Previous Podium','rrze-expo'),
                ];
                break;
            case 'foyer':
                $foyerID = get_post_meta($post->ID, 'rrze-expo-foyer-parent', true);
                $labels = [
                    'nav' => __('Foyer Navigation', 'rrze-expo'),
                    'next' => __('Next Foyer','rrze-expo'),
                    'prev' => __('Previous Foyer','rrze-expo'),
                ];
                break;
        }
        if ($foyerID == '') {
            if ($postType == 'foyer') {
                // Don't display expoNav in foyer if there is no parent foyer
                return;
            } else {
                $expoID = get_post_meta($post->ID, 'rrze-expo-'.$postType.'-exposition', true);
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
        }

        ?>
        <div class="nav-bar">
            <nav id="rrze-expo-navigation" class="<?php echo $postType;?>-nav" aria-label="<?php echo $labels['nav'];?>"><ul>
                <?php if (in_array($postType, ['booth','podium'])) {
                    $itemId = $post->ID;
                    switch ($postType) {
                        case 'booth':
                            $idsOrdered = CPT::getBoothOrder($itemId);
                            break;
                        case 'podium':
                            $idsOrdered = CPT::getPodiumOrder($itemId);
                    }
                    $orderNo = array_search($itemId, $idsOrdered);
                    if ($orderNo > 0) {
                        $prevItemID = $idsOrdered[$orderNo-1]; ?>
                        <li class="prev-<?php echo $postType;?>">
                            <a href="<?php echo get_permalink($prevItemID);?>#rrze-expo-<?php echo $postType;?>" class="">
                                <svg height="16" width="16" aria-hidden="true"><use xlink:href="#chevron-left"></use></svg>
                                <span class="nav-prev-text"><?php echo $labels['prev'] . '<span class="'.$postType.'-title">:<br />' . get_the_title($prevItemID);?></span></span>
                            </a>
                        </li>
                    <?php }
                    if (($orderNo + 1) < count($idsOrdered)) {
                        $nextItemID = $idsOrdered[($orderNo + 1)]; ?>
                        <li class="next-<?php echo $postType;?>">
                            <a href="<?php echo get_permalink($nextItemID);?>#rrze-expo-<?php echo $postType;?>" class="">
                                <span class="nav-next-text"><?php echo $labels['next'] . '<span class="'.$postType.'-title">:<br />' . get_the_title($nextItemID);?></span></span>
                                <svg height="16" width="16" aria-hidden="true"><use xlink:href="#chevron-right"></use></svg>
                            </a>
                        </li>
                    <?php }
                    if ($hallID != '') {
                        $hallLink = get_permalink($hallID);
                        $hallText = get_the_title($hallID);
                        echo "<li class='hall-link'><a class='backlink-hall' href='$hallLink'><svg height='16' width='16'><use xlink:href='#chevron-up'></use></svg> $hallText</a></li>";
                    }
                }
                if ($foyerID != '') {
                    $foyerLink = get_permalink($foyerID);
                    $foyerText = get_the_title($foyerID);
                    echo "<li class='foyer-link'><a class='backlink-foyer' href='$foyerLink'><svg height='14' width='14'><use xlink:href='#chevron-double-up'></use></svg> $foyerText</a></li>";
                }
                ?>
            </ul></nav>

        </div>
        <?php
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
     * returns an array of podium IDs in the same exposition,ordered alphabetically
     * @param int $itemID
     * @return array
     */
    public static function getPodiumOrder($itemID) {
        $postType = get_post_type($itemID);
        $expoID = get_post_meta($itemID, 'rrze-expo-'.$postType.'-exposition', true);

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

    public function renderPersonaField( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        // make sure we specify each part of the value we need.
        $escaped_value = wp_parse_args( $escaped_value, array(
            'persona' => '',
            'skin-color' => '',
            'hair-color'      => '',
        ) );

        echo '<div style="margin-bottom: 1.5em;"><label for="' . $field_type_object->_id( '_persona' ) . '" style="font-weight: bold;margin-right: .5em;">'.__('Persona', 'rrze-expo').' </label>';
        $personas = self::constantOptions('personas', true);
        // TODO: $oldValue für Abwärtskompatibilität – beim nächsten Update entfernen!
        $oldValue = get_post_meta($object_id, $field_type_object->_name(), true);
        $oldValue = is_string($oldValue) ? $oldValue : '';
        $personaOptions = '';
        foreach ( $personas as $k => $v ) {
            $personaOptions .= '<option value="'. $k .'" '. selected( in_array($k, [$escaped_value['persona'], $oldValue]), true, false ) .'>'. $v .'</option>';
        }
        echo $field_type_object->select( [
			'name'    => $field_type_object->_name( '[persona]' ),
			'id'      => $field_type_object->_id( '_persona' ),
			'options' => $personaOptions,
			'desc'    => '',
		] );
        echo '</div>';

        echo '<div style="margin-bottom: 1.5em;"><label for="' . $field_type_object->_id( '_skin-color' ) . '" style="font-weight: bold;margin-right: .5em;">'.__('Skin Color', 'rrze-expo').' </label>';
        $skinColor = '<label style="display:inline-block;padding:5px;border:1px solid #eee;"><input type="radio" class="cmb2-option" name="' . $field_type_object->_name( '[skin-color]' ) . '" id="' . $field_type_object->_id( '_skin-color' ) . '" value="" ' . checked('', $escaped_value['skin-color'], false) . '>' . __('Default', 'rrze-expo') . '</label>';
        $skinColorOptions = self::constantOptions('skin-colors');
        foreach ( $skinColorOptions as $value => $name ) {
            $checked = ( isset($escaped_value['skin-color']) &&  $value == $escaped_value['skin-color'] ) ? 'checked="checked"' : '';
            $skinColor .= '<label style="display:inline-block;background-color:'.esc_attr( $value ).';padding:5px;color:'.esc_attr( $value ).';font-family:monospace,monospace;"><input type="radio" class="cmb2-option" name="' . $field_type_object->_name( '[skin-color]' ) . '" id="' . $field_type_object->_id( '_skin-color' ) . '" value="' . esc_attr( $value ) . '" ' . $checked . '>' . esc_html( $name ) . '</label>';
        }
        echo $skinColor;
        echo '</div>';

        echo '<div><label for="' . $field_type_object->_id( '_hair-color' ) . '" style="font-weight: bold;margin-right: .5em;">'.__('Hair Color', 'rrze-expo').' </label>';
        $hairColor = '<label style="display:inline-block;padding:5px;border:1px solid #eee;"><input type="radio" class="cmb2-option" name="' . $field_type_object->_name( '[hair-color]' ) . '" id="' . $field_type_object->_id( '_hair-color' ) . '" value="" ' . checked('', $escaped_value['hair-color'], false) . '>' . __('Default', 'rrze-expo') . '</label>';
        $hairColorOptions = self::constantOptions('hair-colors');
        foreach ( $hairColorOptions as $value => $name ) {
            $checked = ( isset($escaped_value['hair-color']) &&  $value == $escaped_value['hair-color'] ) ? 'checked="checked"' : '';
            $hairColor .= '<label style="display:inline-block;background-color:'.esc_attr( $value ).';padding:5px;color:'.esc_attr( $value ).';font-family:monospace,monospace;"><input type="radio" class="cmb2-option" name="' . $field_type_object->_name( '[hair-color]' ) . '" id="' . $field_type_object->_id( '_hair-color' ) . '" value="' . esc_attr( $value ) . '" ' . $checked . '>' . esc_html( $name ) . '</label>';
        }
        echo $hairColor;
        echo '</div>';

        echo $field_type_object->_desc( true );
    }

    public function sanitizePersonaField( $override_value, $value ) {

    }

    public static function outputFileList( $file_list_meta_id, $img_size = 'medium' ) {
        // Get the list of files
        $files = get_post_meta( get_the_ID(), $file_list_meta_id, 1 );
        $out = '<div class="file-list-wrap" style="">';
        // Loop through them and output an image
        foreach ( (array) $files as $attachment_id => $attachment_url ) {
            $out .=  '<div class="file-list-image">';
            $out .= '<a href="' . wp_get_attachment_image_url($attachment_id, 'full').'" data-fancybox="booth-gallery">'.$attachment_url.'</a>';
            //$out .= wp_get_attachment_image( $attachment_id, $img_size );
            $out .= '</div>';
            // data-lightbox="booth-gallery"
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Increases or decreases the brightness of a color by a percentage of the current brightness.
     * @param   string  $hexCode        Supported formats: `#FFF`, `#FFFFFF`, `FFF`, `FFFFFF`
     * @param   float   $adjustPercent  A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
     * @return  string
     * @author  maliayas (https://stackoverflow.com/a/54393956)
     */
    public static function adjustBrightness($hexCode, $adjustPercent) {
        $hexCode = ltrim($hexCode, '#');
        if (strlen($hexCode) == 3) {
            $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
        }
        $hexCode = array_map('hexdec', str_split($hexCode, 2));
        foreach ($hexCode as & $color) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil($adjustableLimit * $adjustPercent);

            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }
        return '#' . implode($hexCode);
    }

    public static function makePersonaStyles($skinColor = '', $hairColor = '') {
        $personaStyles = '';
        if ($skinColor != '') {
            $personaStyles .= '--hautfarbe: '.$skinColor.';';
            $personaStyles .= '--hautschatten: ' . CPT::adjustBrightness($skinColor, -0.1) . ';';
            $personaStyles .= '--hautlicht: ' . CPT::adjustBrightness($skinColor, 0.1) . ';';
            $personaStyles .= '--mund: ' . CPT::adjustBrightness($skinColor, -0.2) . ';';
            $personaStyles .= '--nase: ' . CPT::adjustBrightness($skinColor, -0.2) . ';';
        }
        if ($hairColor !='') {
            $personaStyles .= '--haarfarbe: ' . $hairColor . ';';
            $personaStyles .= '--haarschatten: ' . CPT::adjustBrightness($hairColor, -0.3) . ';';
            $personaStyles .= '--haarlicht: ' . CPT::adjustBrightness($hairColor, 0.2) . ';';
            if (in_array($hairColor, ['#009966', '#3399FF'])) {
                $personaStyles .= '--augenbrauen: ' . ($skinColor != '' ? CPT::adjustBrightness($skinColor, -0.5) : CPT::adjustBrightness('#F1C27D', -0.5)) . ';';
            } else {
                $personaStyles .= '--augenbrauen: ' . CPT::adjustBrightness($hairColor, -0.5) . ';';
            }
        }
        return $personaStyles;
    }

    public static function renderTextNumberField( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	    echo $field_type_object->input( array( 'class' => 'cmb2-text-small', 'type' => 'number' ) );
    }
    public static function sanitizeTextNumberField( $null, $new ) {
	    $new = preg_replace( "/[^0-9]/", "", $new );
	    return $new;
    }

    /*
     * Makes options array for CMB2 radio, checkbox and select options
     */
    public static function constantOptions($constant, $assoc = false) {
        $constants = getConstants();
        $optionsRaw = $constants[$constant];
        $options = [];
        foreach ($optionsRaw as $k => $v) {
            if (!is_string($v))
                continue;
            if ($assoc === true) {
                $options[$k] = $v;
            } else {
                $options[$v] = $v;
            }
        }
        return $options;
    }

    public static function setCapsToRoles() {
        $roles = array('editor','administrator');
        $capTypes = [
            'booth' => ['singular' => 'booth',
              'plural'  => 'booths'],
            'podium' => ['singular' => 'podium',
              'plural'  => 'podiums'],
            'hall' => ['singular' => 'hall',
              'plural'  => 'halls'],
            'foyer' => ['singular' => 'foyer',
              'plural'  => 'foyers'],
            'exposition' => ['singular' => 'exposition',
              'plural'  => 'expositions'],
            ];
        foreach ($capTypes as $cpt => $capType) {
            $caps[$cpt] = CPT::makeCapabilities($capType['singular'], $capType['plural']);
        }

        foreach($roles as $role) {
	        $role = get_role($role);
	        if (isset($role)) {
	            foreach($caps as $cpt ) {
	                foreach($cpt as $capability) {
	                    $role->add_cap( $capability );
                    }
	            }
	        }
	    }
    }

    function addExpoCptToDropdown( $pages ){
        global $wp_customize;
        if (is_admin() && isset( $wp_customize )) {
            $args = array(
                'post_type' => 'exposition'
            );
            $items = get_posts($args);
            $pages = array_merge($pages, $items);
        }
        return $pages;
    }

    function enableFrontPageCPT( $query ){
        global $wp_customize;
        if (is_admin() && isset( $wp_customize )) {
            if(isset($query->query_vars['post_type']) && '' == $query->query_vars['post_type'] && 0 != $query->query_vars['page_id'])
                $query->query_vars['post_type'] = array( 'page', 'exposition' );
        }
    }

    public static function getDefaultColors($type = 'dark') {
        if (strpos($type, '-') !== false) {
            $parts = explode('-', $type);
            $type = $parts[1];
            $font = true;
        } else {
            $font = false;
        }
        $constants = getConstants();
        $colors = isset($constants['colors'][$type]) ? $constants['colors'][$type] : [];
        if ($font) {
            array_unshift($colors, '#000000', '#ffffff');
        }
        return $colors;
    }
}
