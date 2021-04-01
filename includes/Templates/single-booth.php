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
        $boothID = get_the_ID();
        $meta = get_post_meta($boothID);
        $expoID = CPT::getMeta($meta, 'rrze-expo-booth-exposition');
        /*print "<pre>";
        //var_dump($meta);
        var_dump(CPT::getMeta($meta, 'rrze-expo-booth-decoration-template1'));
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

        // Talks
        $timetable = '';
        $boothTalks = [];
        $podiums = CPT::getPosts('podium', $expoID);
        foreach ($podiums as $id => $podium) {
            $talks = get_post_meta($id, 'rrze-expo-podium-timeslots', true);
            foreach ($talks as $talk) {
                if (isset($talk['booth']) && $talk['booth'] == $boothID) {
                    $boothTalks[$id][] = $talk;
                }
            }
        }
        if (!empty($boothTalks)) {
            $timetable .= '<h2>'.__('Our Talks', 'rrze-expo').'</h2>'
                . '<ul>';
            foreach ($boothTalks as $podiumID => $podium) {
                foreach ($podium as $talk) {
                    $timetable .= '<li>'
                        . date('H:i', $talk['start']).' - '.date('H:i', $talk['end']).': '
                        . '<a href="'.get_permalink($podiumID).'"><span class="talk-title">' . $talk['title'] . '</span>'
                        . ' <span class="talk-location">[' . get_the_title($podiumID) . ']</span></a>'
                        . '';
                    $timetable .= '</li>';
                }
            }
            $timetable .= '</ul>';
        }
        $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
        if ($rollups != '') {
            if (isset($rollups[0])) {
                $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'medium');
                //$rollup1 = '<div class="" style="-webkit-transform: perspective(1500px) rotateY(15deg); transform: perspective(1500px) rotateY(15deg);"><img src="' . $rollupData0[0] . '"/></div>';
                $rollup1 = '<div class="rollup-content" style=""><img src="' . $rollupData0[0] . '"/></div>';
                /*$rollup1 = '<div class="container" style="perspective: 1000px; border: 1px solid black; background: gray; margin: 40px;">
                    <div class="transformed" style="transform: rotateY(50deg);"><img src="' . $rollupData0[0] . '"/></div>
                </div>';*/
            }
            if (isset($rollups[1])) {
                $rollupData1 = wp_get_attachment_image_src($rollups[1]['file_id'], 'medium');
            }

        }
        //echo $rollup1;
        ?>
        <h1 class="sr-only screen-reader-text"><?php echo get_the_title(); ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg version="1.1" class="expo-booth template-<?php echo CPT::getMeta($meta, 'rrze-expo-booth-template'); ?>" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <use class="backwall" xlink:href="#backwall" />
                <use xlink:href="#table" />
                <?php
                echo '<rect x="700" y="120" width="400" height="300" style="fill: transparent; stroke: black; stroke-width: 1px;"/><text x="1450" y="330" font-size="60" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.get_the_title().'</text>';
                // Logo
                if (has_post_thumbnail()){
                    $logoLocations = CPT::getMeta($meta, 'rrze-expo-booth-logo-locations');
                    if (in_array('wall', $logoLocations)) {
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin meet" width="300" height="240"  x="1990" y="280" />';
                    }
                    if (in_array('table', $logoLocations)) {
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin meet" width="480" height="160"  x="1450" y="760" />';
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

                // Timetable
                if ($timetable != '') { ?>
                    <rect x="700" y="120" width="400" height="300" style="fill: #fff; stroke: black; stroke-width: 1px;"/>
                    <foreignObject class="timetable" x="700" y="120" width="400" height="300">
                        <!-- XHTML content goes here -->
                        <body xmlns="http://www.w3.org/1999/xhtml">
                        <?php echo $timetable; ?>
                        </body>
                    </foreignObject>
                <?php }

                // Roll-Ups
                //<image xlink:href="' . $rollupData0[0] . '" width="320" height="800" x="0" y="120" />
                $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
                if ($rollups != '') {
                    if (isset($rollups[0])) {
                        $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'medium');
                        //echo '<a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '" style="display: block;">
                        echo '<use xlink:href="#rollup" />';
                        //<foreignObject class="rollup-content">'.$rollup1.'</foreignObject>
                        //</a>';
                    }
                    if (isset($rollups[1])) {
                        /*$rollupData1 = wp_get_attachment_image_src($rollups[1]['file_id'], 'medium');
                        echo '<a href="' . $rollups[1]['file'] . '" title="' . get_the_title($rollups[1]['file_id']) . '">
                        <image xlink:href="' . $rollupData1[0] . '" width="320" height="800" x="1600" y="120" fill="#dedede"/>
                        </a>';*/
                    }

                }

                // Flyers
                $flyers = CPT::getMeta($meta, 'rrze-expo-booth-flyer');
                if ($flyers != '') {
                    echo '<g><use xlink:href="#flyer_stand" />';
                    /*foreach ($flyers as $i => $flyer) {
                        $translateY = 410 + $i * (270 + 20);
                        echo '<a href="' . $flyer['pdf'] . '" title="' . get_the_title($flyer['pdf_id']) . '">
                        <image xlink:href="' . $flyer['preview'] . '" width="180" height="270" x="360" y="' . $translateY . '" />
                        </a>';
                    }*/
                    echo '</g>';
                }

                // Social Media
                $socialMedia = CPT::getMeta($meta, 'rrze-expo-booth-social-media');
                if ($socialMedia != '') {
                    echo '<g><use xlink:href="#some_panel" />';
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
                // Deco
                $deco = CPT::getMeta($meta, 'rrze-expo-booth-decoration-template1');
                if ($deco != '') {
                    if (in_array('owl', $deco)) {
                        echo '<use xlink:href="#owl" />';
                    }
                    if (in_array('plantsleft', $deco)) {
                        echo '<use xlink:href="#plant1" />';
                    }
                    if (in_array('plantsright', $deco)) {
                        echo '<use xlink:href="#plant2" width="500" height="500" x="1000" y="30"  />';
                    }
                }
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
wp_enqueue_script('rrze-expo');

//CPT::expoFooter();
get_footer();
