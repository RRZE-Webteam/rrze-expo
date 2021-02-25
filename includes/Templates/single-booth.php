<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $boothId = get_the_ID();
        $meta = get_post_meta($boothId);
        ?>

        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo get_the_post_thumbnail_url($boothId, 'full');?>');">

            <?php
            $templateNo = CPT::getMeta($meta, 'rrze-expo-booth-template');
            if ($templateNo != '' && file_exists(WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg')) {
                include WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg';
            } ?>
            <ul class="booth-nav">
                <li><a href="" class=""><?php _e('Previous Booth','rrze-expo');?></a></li>
                <li><a href="" class=""><?php _e('Next Booth','rrze-expo');?></a></li>
            </ul>
            <a href="#rrze-expo-booth-content" id="scrolldown"><?php _e('Read more','rrze-expo');?></a>
        </div>

        <div id="rrze-expo-booth-content" name="rrze-expo-booth-content" class="">

            <?php the_content(); ?>

        </div>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
