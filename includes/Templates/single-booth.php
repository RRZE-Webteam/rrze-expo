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

        <h1 class="sr-only screen-reader-text"><?php echo the_title(); ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg class="expo-booth" role="img" viewBox="0 0 1920 1080" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use xlink:href="#backwall" x="60" y="0" />
                <use xlink:href="#video1" x="200" y="60" />
                <use xlink:href="#video2" x="700" y="60" />
                <use xlink:href="#flyer_stand" x="0" y="400" />
                <use xlink:href="#table" x="600" y="550" />
                <use xlink:href="#some_panel" x="1800" y="500" />
                <a xlink:href="https://twitter.com/">
                    <use xlink:href="#twitter" x="1820" y="520" />
                </a>
                <a xlink:href="https://www.facebook.com/">
                    <use xlink:href="#facebook" x="1820" y="620" />
                </a>
                <a xlink:href="https://instagram.com/">
                    <use xlink:href="#instagram" x="1820" y="720" />
                </a>
                <a xlink:href="https://youtube.com/">
                    <use xlink:href="#youtube" x="1820" y="820" />
                </a>
                <use xlink:href="#booth_logo" x="1344" y="60" />
                <use xlink:href="#booth_logo" x="700" y="600" />
            </svg>


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
