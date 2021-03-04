<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
//get_header();
?>

<main>
    <?php
    CPT::expoNav();
    while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $boothId = get_the_ID();
        $meta = get_post_meta($boothId);
        /*print "<pre>";
        //var_dump($meta);
        var_dump(CPT::getMeta($meta, 'rrze-expo-booth-rollups'));
        print "</pre>";*/
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
        ?>

        <h1 class="sr-only screen-reader-text"><?php echo get_the_title(); ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg class="expo-booth" role="img" viewBox="0 0 1920 1080" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use xlink:href="#backwall" x="60" y="0" fill="<?php echo CPT::getMeta($meta, 'rrze-expo-booth-backwall-color');?>"/>
                <use xlink:href="#table" x="600" y="550" />
                <?php
                echo '<text x="90" y="80" font-size="60" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.get_the_title().'</text>';
                // Logo
                if (has_post_thumbnail()){
                    $logoLocations = CPT::getMeta($meta, 'rrze-expo-booth-logo-locations');
                    if (in_array('wall', $logoLocations)) {
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin" width="400" height="240"  x="1344" y="60" />';
                    }
                    if (in_array('table', $logoLocations)) {
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin" width="400" height="240"  x="700" y="600" />';
                    }
                }
                // Videos
                $videos = CPT::getMeta($meta, 'rrze-expo-booth-video');
                if ($videos != '') {
                    if (isset($videos[0])) {
                        echo '<use xlink:href="#video" x="350" y="120" />';
                    }
                    if (isset($videos[1])) {
                        echo '<use xlink:href="#video" x="700" y="120" />';
                    }
                }

                // Roll-Ups
                $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
                if ($rollups != '') {
                    if (isset($rollups[0])) {
                        $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'medium');
                        echo '<a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '">
                        <image xlink:href="' . $rollupData0[0] . '" width="320" height="800" x="0" y="120" />
                        </a>';
                    }
                    if (isset($rollups[1])) {
                        $rollupData1 = wp_get_attachment_image_src($rollups[1]['file_id'], 'medium');
                        echo '<a href="' . $rollups[1]['file'] . '" title="' . get_the_title($rollups[1]['file_id']) . '">
                        <image xlink:href="' . $rollupData1[0] . '" width="320" height="800" x="1600" y="120" fill="#dedede"/>
                        </a>';
                    }

                }

                // Flyers
                $flyers = CPT::getMeta($meta, 'rrze-expo-booth-flyer');
                if ($flyers != '') {
                    echo '<g><use xlink:href="#flyer_stand" x="350" y="400" />';
                    foreach ($flyers as $i => $flyer) {
                        $translateY = 410 + $i * (270 + 20);
                        echo '<a href="' . $flyer['pdf'] . '" title="' . get_the_title($flyer['pdf_id']) . '">
                        <image xlink:href="' . $flyer['preview'] . '" width="180" height="270" x="360" y="' . $translateY . '" />
                        </a>';
                    }
                    echo '</g>';
                }

                // Social Media
                $socialMedia = CPT::getMeta($meta, 'rrze-expo-booth-social-media');
                if ($socialMedia != '') {
                    echo '<g><use xlink:href="#some_panel" x="1450" y="500" />';
                    $socialMediaData = $constants['social-media'];
                    foreach ($socialMedia as $order => $media) {
                        if (!isset($media['medianame']) || !isset($media['username']))
                            continue;
                        $translateY = 520 + ($order * 20);
                        echo '<a href="' . trailingslashit($socialMediaData[$media['medianame']]) . $media['username'] . '">
                            <use xlink:href="#' . $media['medianame'] . '" x="1470" y="' . $translateY . '" />
                            </a>';
                    }
                    echo '</g>';
                }
                // echo do_shortcode('[fauvideo url="https://www.fau.tv/webplayer/id/96195"]');
                ?>
            </svg>
            <?php if ($hasContent) { ?>
                <a href="#rrze-expo-booth-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                    <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                    <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
                </a>
            <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
        <div id="rrze-expo-booth-content" name="rrze-expo-booth-content" class="">

            <?php the_content(); ?>

        </div>

    <?php }

        endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
