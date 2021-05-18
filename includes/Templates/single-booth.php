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
        $templateNo = CPT::getMeta($meta, 'rrze-expo-booth-template');
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
        $accentColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color');
        $wallSettings = $constants['template_elements']['template'.$templateNo]['wall'];
        $title = get_the_title();
        $fontSize = CPT::getMeta($meta, 'rrze-expo-booth-font-size');
        ?>
        <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg version="1.1" class="expo-booth template-<?php echo CPT::getMeta($meta, 'rrze-expo-booth-template'); ?>" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php echo '<rect x="'.$wallSettings['x'].'" y="'.$wallSettings['y'].'" width="'.$wallSettings['width'].'" height="'.$wallSettings['height'].'" style="fill:'. $accentColor.'"/>'; ?>
                <use class="backwall" xlink:href="#backwall-color" />
                <use xlink:href="#table" />
                <?php
                $titleSettings = $constants['template_elements']['template'.$templateNo]['title'];
                if (strpos($title, '<br>') != false) {
                    $titleParts = explode('<br>', $title);
                    $title = '<tspan>' . implode('</tspan><tspan x="'.$titleSettings['x'].'" dy="'.($fontSize*1.12).'">', $titleParts) . '</tspan>';
                }
                echo '<text x="'.$titleSettings['x'].'" y="'.$titleSettings['y'].'" font-size="'.$fontSize.'" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.$title.'</text>';
                // Logo
                if (has_post_thumbnail()){
                    $logoLocations = CPT::getMeta($meta, 'rrze-expo-booth-logo-locations');
                    if (in_array('wall', $logoLocations)) {
                        $logoSettingsWall = $constants['template_elements']['template'.$templateNo]['logo-wall'];
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin meet" width="'.$logoSettingsWall['width'].'" height="'.$logoSettingsWall['height'].'"  x="'.$logoSettingsWall['x'].'" y="'.$logoSettingsWall['y'].'" />';
                    }
                    if (in_array('table', $logoLocations)) {
                        $logoSettingsTable = $constants['template_elements']['template'.$templateNo]['logo-table'];
                        echo '<image xlink:href="'.get_the_post_thumbnail_url().'" preserveAspectRatio="xMidYMin meet" width="'.$logoSettingsTable['width'].'" height="'.$logoSettingsTable['height'].'"  x="'.$logoSettingsTable['x'].'" y="'.$logoSettingsTable['y'].'" />';
                    }
                }
                // Videos
                $videos['left'] = CPT::getMeta($meta, 'rrze-expo-booth-video-left');
                $videos['right'] = CPT::getMeta($meta, 'rrze-expo-booth-video-right');
                $videos['table'] = CPT::getMeta($meta, 'rrze-expo-booth-video-table');
                foreach ($videos as $location => $url) {
                    if ($url != '') {
                        if ($location == 'table') {
                            echo '<use class="video-tablet" xlink:href="#tablet" />';
                        }
                        $videoSettings = $constants['template_elements']['template'.$templateNo]['video_'.$location];
                        if (is_plugin_active( 'rrze-video/rrze-video.php' ) || is_plugin_active_for_network('rrze-video/rrze-video.php')) {
                            echo '<foreignObject class="video" x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" width="'.$videoSettings['width'].'" height="'.$videoSettings['height'].'">' . do_shortcode('[fauvideo url="'.$url.'"]') . '</foreignObject>';
                        } else {
                            echo '<a href="'.$url.'">
                                <rect class="video" x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" width="'.$videoSettings['width'].'" height="'.$videoSettings['height'].'" fill="333"></rect>
                                <path x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" width="'.$videoSettings['width'].'" height="'.$videoSettings['height'].'" fill="#e6e6e6" d="M371.7 238l-176-107c-15.8-8.8-35.7 2.5-35.7 21v208c0 18.4 19.8 29.8 35.7 21l176-101c16.4-9.1 16.4-32.8 0-42zM504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256z"/>
                                </a>';
                        }
                    }
                }
                /*if ($videos != '') {
                    if (isset($videos[0])) {
                        $video1Settings = $constants['template_elements']['template'.$templateNo]['video1'];
                        if (is_plugin_active( 'rrze-video/rrze-video.php' ) || is_plugin_active_for_network('rrze-video/rrze-video.php')) {
                            echo '<foreignObject class="video" x="'.$video1Settings['x'].'" y="'.$video1Settings['y'].'" width="320" height="200">' . do_shortcode('[fauvideo url="'.$videos[0]['url'].'"]') . '</foreignObject>';
                        } else {
                            echo '<a href="'.$videos[0]['url'].'"><rect class="video" width="320" height="180" x="'.$video1Settings['x'].'" y="'.$video1Settings['y'].'" fill="333"></rect><path x="'.$video1Settings['x'].'" y="'.$video1Settings['y'].'" fill="#e6e6e6" d="M371.7 238l-176-107c-15.8-8.8-35.7 2.5-35.7 21v208c0 18.4 19.8 29.8 35.7 21l176-101c16.4-9.1 16.4-32.8 0-42zM504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256z"/></a>';
                        }
                    }
                    if (isset($videos[1])) {
                        $video2Settings = $constants['template_elements']['template'.$templateNo]['video2'];
                        if (is_plugin_active( 'rrze-video/rrze-video.php' ) || is_plugin_active_for_network('rrze-video/rrze-video.php')) {
                            $video2 = do_shortcode('[fauvideo url="'.$videos[1]['url'].'"]');
                            echo '<foreignObject class="video" x="'.$video2Settings['x'].'" y="'.$video2Settings['y'].'" width="320" height="200">'.$video2.'</foreignObject>';
                        } else {
                            echo '<a href="'.$videos[1]['url'].'"><rect class="video" width="320" height="180" x="'.$video2Settings['x'].'" y="'.$video2Settings['y'].'" fill="333"></rect></a>';
                        }
                    }
                }*/

                // Timetable
                if ($timetable != '') {
                    $timetableSettings = $constants['template_elements']['template'.$templateNo]['timetable']; ?>
                    <foreignObject class="timetable" x="2250" y="370" width="300" height="200">
                        <body xmlns="http://www.w3.org/1999/xhtml">
                        <?php echo $timetable; ?>
                        </body>
                    </foreignObject>
                <?php }

                // Flyers
                $flyers = CPT::getMeta($meta, 'rrze-expo-booth-flyer');
                if ($flyers != '') {
                    $flyerSettings = $constants['template_elements']['template'.$templateNo]['flyers'];
                    echo '<g><use xlink:href="#flyer_stand" />';
                    foreach ($flyers as $i => $flyer) {
                        $translateY = $flyerSettings['y'] + $i * ($flyerSettings['height'] + 20);
                        echo '<a href="' . $flyer['pdf'] . '" title="' . get_the_title($flyer['pdf_id']) . '" class="lightbox">';
                        //<rect fill="#fff" width="'.$flyerSettings['width'].'" height="'.$flyerSettings['height'].'" x="'.$flyerSettings['x'].'" y="' . $translateY . '"></rect>
                        if (!array_key_exists('preview', $flyer) || $flyer['preview'] == false) {
                            echo '<use xlink:href="#flyer_default" width="'. $flyerSettings['width'].'" height="'.$flyerSettings['height'].'" x="'.$flyerSettings['x'].'" y="' . $translateY . '"/>';
                        } else {
                            echo '<image xlink:href="' . $flyer['preview'] . '" width="'. $flyerSettings['width'].'" height="'.$flyerSettings['height'].'" x="'.$flyerSettings['x'].'" y="' . $translateY . '" preserveAspectRatio="xMidYMin meet"/>';
                        }
                        echo '</a>';
                    }
                    echo '</g>';
                }

                // Social Media
                $socialMedia = CPT::getMeta($meta, 'rrze-expo-booth-social-media');
                if ($socialMedia != '') {
                    echo '<g><use xlink:href="#some_panel" />';
                    $socialMediaData = $constants['social-media'];
                    $socialMediaSettings = $constants['template_elements']['template'.$templateNo]['social-media'];
                    foreach ($socialMedia as $i => $media) {
                        if (!isset($media['medianame']) || !isset($media['username']))
                            continue;
                        $translateY = $socialMediaSettings['y'] + $i * ($socialMediaSettings['height'] + 10);
                        echo '<a href="' . trailingslashit($socialMediaData[$media['medianame']]) . $media['username'] . '" title="'.ucfirst($media['medianame']).': '.$media['username'].'">
                            <use xlink:href="#' . $media['medianame'] . '" width="'.$socialMediaSettings['width'].'" height="'.$socialMediaSettings['height'].'" x="'.$socialMediaSettings['x'].'" y="' . $translateY . '" class="icon-'.$media['medianame'] .'"  />
                            </a>';
                    }
                    echo '</g>';
                }
                //echo do_shortcode('[fauvideo url="https://www.fau.tv/webplayer/id/96195"]');

                // Roll-Ups
                $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
                if ($rollups != '') {
                    $rollupSettings = $constants['template_elements']['template'.$templateNo]['rollup'];
                    if (isset($rollups[0])) {
                        $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'medium');
                        $rollup1 = '<div class="rollup-content" style="width: 100%; height: 100%; text-align: center;"><img src="' . $rollupData0[0] . '" style=" height: 100%; object-fit: contain;"/></div>';
                        echo '<use xlink:href="#rollup" />';
                        echo '<foreignObject class="rollup-content" width="'.$rollupSettings['width'].'" height="'.$rollupSettings['height'].'" x="'.$rollupSettings['x'].'" y="' . $rollupSettings['y'] . '"><a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '" style="display: block; height: 100%; text-align: center;" class="lightbox"><img src="' . $rollupData0[0] . '" style=" height: 100%; object-fit: contain;"/></a></foreignObject>';
                    }
                }

                // Deco
                $deco = CPT::getMeta($meta, 'rrze-expo-booth-decorations');
                if ($deco != '') {
                    if (in_array('owl', $deco)) {
                        echo '<use xlink:href="#owl" />';
                    }
                    if (in_array('plantsleft', $deco)) {
                        echo '<use xlink:href="#plant1" />';
                    }
                    if (in_array('plantsright', $deco)) {
                        echo '<use xlink:href="#plant" />';
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

            <!--<div class="puls-container">
                <div class="puls-middle"></div>
                <div class="puls"></div>
            </div>-->

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
