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

        add_filter('single_template', [$this, 'includeSingleTemplate']);
        add_action('wp_footer', [$this, 'svgToFooter']);
        add_action('wp_head', [$this, 'cssToFooter']);
    }

    public function activation()
    {
        $booth = new Booth($this->pluginFile);
        $booth->booth_post_type();

        $hall = new Hall($this->pluginFile);
        $hall->booth_post_type();
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
        if (!in_array($post->post_type,  ['booth', 'hall', 'foyer']))
            return;
        // Booth Template
        $templateNo = get_post_meta($post->ID,'rrze-expo-booth-template', true);
        if ($templateNo != '' && file_exists(WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg')) {
            echo file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg');
        }
        // Icons
        $icons = [
            'chevron-left',
            'chevron-right',
            'chevron-down',
            'chevron-up',
            'chevron-double-up'
        ];
        echo '<svg style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><defs>';
        foreach ($icons as $icon) {
            $iconSvg = file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/'.$icon.'.svg');
            echo str_replace(['<svg xmlns="http://www.w3.org/2000/svg"', '</svg>'], ['<symbol id="'.$icon.'"', '</symbol>'], $iconSvg);
        }
        echo '</defs></svg>';
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
                    fill: $backwallColor;
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
        if ($post->post_type == 'foyer') {
             $foyerID = $post->ID;
        } else {
            if ($post->post_type == 'booth') {
                $hallID = get_post_meta($post->ID, 'rrze-expo-booth-hall', true);
            } elseif ($post->post_type == 'hall') {
                $hallID = $post->ID;
            }
            $foyerID = get_post_meta($hallID, 'rrze-expo-hall-foyer', true);
        }
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
            <nav id="skiplinks" aria-label="<?php _e('Skiplinks', 'fau-events'); ?>">
                <ul class="jumplinks">
                    <li><a href="#page-start" data-target="#page-start" data-firstchild="0" class="skiplink-content"><?php _e('Go to content area', 'fau-events'); ?></a></li>
                    <li><a href="#desktop-search" data-target="#desktop-search .searchform input" data-firstchild="1" class="skiplink-search"><?php _e('Go to search', 'fau-events'); ?></a></li>
                    <li><a href="#desktop-navigation" data-target="#desktop-navigation ul li a" data-firstchild="1" class="skiplink-nav"><?php _e('Go to main navigation', 'fau-events'); ?></a></li>
                </ul>
            </nav>
            <header id="masthead" class="site-header" role="banner">
                <div id="rrze-expo-header-content" class="site-header-content">
                    <div class="branding" id="logo" role="banner" itemscope itemtype="http://schema.org/Organization">
                        <p class="sitetitle">
                            <?php
                            if ( $post->post_type != 'foyer' ) {
                                echo '<a href="'.get_permalink($foyerID).'">';
                            }
                            echo get_the_post_thumbnail($foyerID);
                            echo get_the_title($foyerID);
                            if ( $post->post_type != 'foyer' ) {
                                echo '</a>';
                            }
                            ?>
                        </p>
                    </div>
                </div><!-- .site-header-content -->
            </header>
        <?php
    }

    public static function expoFooter() {

    }

    public static function expoNav() {
        global $post;
        if ($post->post_type == 'foyer') {
            $foyerID = $post->ID;
        } else {
            if ($post->post_type == 'booth') {
                $hallID = get_post_meta($post->ID, 'rrze-expo-booth-hall', true);
            } elseif ($post->post_type == 'hall') {
                $hallID = $post->ID;
            }
            $foyerID = get_post_meta($hallID, 'rrze-expo-hall-foyer', true);
        }
        if (in_array($post->post_type, ['booth', 'hall'])) { ?>
            <nav class="booth-nav"><ul>
                <?php if ($post->post_type == 'booth') {
                    $boothId = $post->ID;
                    $boothIDsOrdered = CPT::getBoothOrder($boothId);
                    $orderNo = array_search($boothId, $boothIDsOrdered);
                    if ($orderNo > 0) {
                    $prevBoothID = $boothIDsOrdered[$orderNo-1]; ?>
                    <li class="prev-booth">
                        <a href="<?php echo get_permalink($prevBoothID);?>" class="">
                            <svg height="40" width="40" aria-hidden="true"><use xlink:href="#chevron-left"></use></svg>
                            <span class="nav-prev-text"><?php echo __('Previous Booth','rrze-expo') . ':<br />' . get_the_title($prevBoothID);?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if (($orderNo + 1) < count($boothIDsOrdered)) {
                    $nextBoothID = $boothIDsOrdered[($orderNo + 1)]; ?>
                    <li class="next-booth">
                        <a href="<?php echo get_permalink($nextBoothID);?>" class="">
                            <svg height="40" width="40" aria-hidden="true"><use xlink:href="#chevron-right"></use></svg>
                            <span class="nav-next-text"><?php echo __('Next Booth','rrze-expo') . ':<br />' . get_the_title($nextBoothID);?></span>
                        </a>
                    </li>
                <?php }
                    $hallLink = get_permalink($hallID);
                    $hallText = __('Back to Hall', 'rrze-expo') . ': ' . get_the_title($hallID);
                    echo "<li class='hall-link'><a class='backlink-hall' href='$hallLink'><svg height='16' width='16'><use xlink:href='#chevron-up'></use></svg> $hallText</a></li>";
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
                'meta_key'  => 'rrze-expo-booth-hall',
                'meta_value'    => $hallID,
                'orderby'   => 'title',
                'order'     => 'ASC',
                'fields'    => 'ids',
            ]);
        }
        return $boothIDs;
    }
}
