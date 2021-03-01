<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $boothId = get_the_ID();
        $meta = get_post_meta($boothId);
        $constants = getConstants();
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-booth-background-image');
        if ($backgroundImage == '') {
            // If no background image is set -> take hall background image
            $hall = CPT::getMeta($meta, 'rrze-expo-booth-hall');
            $backgroundImage = get_post_meta($hall, 'rrze-expo-hall-background-image', true);
            if ($backgroundImage == '') {
                // If no booth and no hall background image are set -> take foyer background image
                $foyer = get_post_meta($hall, 'rrze-expo-hall-foyer', true);
                $backgroundImage = get_post_meta($foyer, 'rrze-expo-foyer-background-image', true);
            }
        }
        $soMeDefaults = [
            'username' => '',
            'order' => 0,];
        ?>

        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">

            <?php
            $templateNo = CPT::getMeta($meta, 'rrze-expo-booth-template');
            if ($templateNo != '' && file_exists(WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg')) {
                $socialMedia = $constants['social-media'];
                $svgPatterns = [];
                $svgReplacements = [];
                foreach ($socialMedia as $soMeName => $soMeUrl) {
                    $soMeMeta = wp_parse_args(CPT::getMeta($meta, 'rrze-expo-booth-' . $soMeName), $soMeDefaults);
                    if ($soMeMeta['username'] != '') {
                        $svgPatterns[] = $soMeUrl;
                        $svgReplacements[] = $soMeUrl.$soMeMeta['username'];
                    }
                }
                if(has_post_thumbnail()){
                    $svgPatterns[] = 'PLACEHOLDER_LOGO_URL';
                    $svgReplacements[] = get_the_post_thumbnail_url();
                }
                $svg = file_get_contents(WP_PLUGIN_DIR . '/rrze-expo/assets/img/booth_template_' . absint($templateNo) . '.svg');
                $svg = str_replace($svgPatterns, $svgReplacements, $svg);
                echo $svg;
            } ?>

            <ul class="booth-nav">
                <li class="prev-booth"><a href="#" class=""><?php _e('Previous Booth','rrze-expo');?></a></li>
                <li class="next-booth"><a href="#" class=""><?php _e('Next Booth','rrze-expo');?></a></li>
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
