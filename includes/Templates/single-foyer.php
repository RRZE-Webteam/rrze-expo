<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

$cpt = new CPT('');
CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $foyerId = get_the_ID();
        $meta = get_post_meta($foyerId);
        ?>

        <div id="rrze-expo-foyer" class="foyer" style="background-image: url('<?php echo get_the_post_thumbnail_url($foyerId, 'full');?>');">

            <?php
            $templateNo = CPT::getMeta($meta, 'rrze-expo-foyer-template');
            $twitter = CPT::getMeta($meta, 'rrze-expo-foyer-twitter');
            $facebook = CPT::getMeta($meta, 'rrze-expo-foyer-facebook');
            $instagram = CPT::getMeta($meta, 'rrze-expo-foyer-instagram');
            $youtube = CPT::getMeta($meta, 'rrze-expo-foyer-youtube');
            if ($templateNo != '' && file_exists(WP_PLUGIN_DIR . '/rrze-expo/assets/img/foyer_template_' . absint($templateNo) . '.svg')) {
                $svg = file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/foyer_template_' . absint($templateNo) . '.svg');
                $svgPatterns = [
                    '/twitter.com/',
                    '/facebook.com/',
                    '/instagram.com/',
                    '/youtube.com/',
                ];
                $svgReplacements = [
                    '/twitter.com/' . $twitter['username'],
                    '/facebook.com/' . $facebook['username'],
                    '/instagram.com/' . $instagram['username'],
                    '/youtube.com/' . $youtube['username'],
                ];
                $svg = preg_replace($svgPatterns, $svgReplacements, $svg);

                echo $svg;
            } ?>
            <a href="#rrze-expo-foyer-content" id="scrolldown"><?php _e('Read more','rrze-expo');?></a>
        </div>

        <div id="rrze-expo-foyer-content" name="rrze-expo-foyer-content" class="">

            <?php the_content(); ?>

        </div>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
