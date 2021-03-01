<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

$cpt = new CPT('');
CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $hallId = get_the_ID();
        $meta = get_post_meta($hallId);
        $menu = CPT::getMeta($meta, 'rrze-expo-hall-menu');
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-hall-background-image');
        if ($backgroundImage == '') {
            // If no background image is set -> take foyer background image
            $foyer = CPT::getMeta($meta, 'rrze-expo-hall-foyer');
            $backgroundImage = get_post_meta($foyer, 'rrze-expo-foyer-background-image', true);
        } ?>

        <div id="rrze-expo-hall" class="hall" style="background-image: url('<?php echo $backgroundImage;?>');">

            <?php
            if ($menu != '-1') {
                wp_nav_menu([
                    'menu' => absint($menu),
                    'container' => false,
                    //'items_wrap' => '<div id="nav"><ul class="nav">%3$s</ul></div>',
                    'depth' => 1]);
            }
            ?>
            <a href="#rrze-expo-hall-content" id="scrolldown"><?php _e('Read more','rrze-expo');?></a>
        </div>

        <div id="rrze-expo-hall-content" name="rrze-expo-hall-content" class="">

            <?php the_content(); ?>

        </div>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
