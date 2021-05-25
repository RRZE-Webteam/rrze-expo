<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

global $post;

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
        $schedule = '';
        $scheduleLocation = CPT::getMeta($meta, 'rrze-expo-booth-schedule-location');
        if ($scheduleLocation != 'none' && $scheduleLocation != '') {
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
                $schedule .= '<div class="schedule-'.$scheduleLocation.'">'
                    . '<h2>' . __('Our Talks', 'rrze-expo') . '</h2>'
                    . '<ul>';
                foreach ($boothTalks as $podiumID => $podium) {
                    foreach ($podium as $talk) {
                        $schedule .= '<li>'
                            . date('H:i', $talk['start']) . ' - ' . date('H:i', $talk['end']) . ': '
                            . '<a href="' . get_permalink($podiumID) . '"><span class="talk-title">' . $talk['title'] . '</span>'
                            . ' <span class="talk-location">[' . get_the_title($podiumID) . ']</span></a>'
                            . '';
                        $schedule .= '</li>';
                    }
                }
                $schedule .= '</ul></div>';
            }
        }
        $accentColor = CPT::getMeta($meta, 'rrze-expo-booth-backwall-color');
        $wallSettings = $constants['template_elements']['booth'.$templateNo]['wall'];
        $title = get_the_title();
        $fontSize = CPT::getMeta($meta, 'rrze-expo-booth-font-size');
        ?>
        <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg version="1.1" class="expo-booth template-<?php echo CPT::getMeta($meta, 'rrze-expo-booth-template'); ?>" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php echo '<rect x="'.$wallSettings['x'].'" y="'.$wallSettings['y'].'" width="'.$wallSettings['width'].'" height="'.$wallSettings['height'].'" style="fill:'. $accentColor.'"/>'; ?>
                <use class="backwall" xlink:href="#backwall-color" />
                <use xlink:href="#table" />
                <?php
                $titleSettings = $constants['template_elements']['booth'.$templateNo]['title'];
                if (strpos($title, '<br>') != false) {
                    $titleParts = explode('<br>', $title);
                    $title = '<tspan>' . implode('</tspan><tspan x="'.$titleSettings['x'].'" dy="'.($fontSize*1.12).'">', $titleParts) . '</tspan>';
                }
                echo '<text x="'.$titleSettings['x'].'" y="'.$titleSettings['y'].'" font-size="'.$fontSize.'" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.$title.'</text>';

                // Videos
                $videos['left'] = CPT::getMeta($meta, 'rrze-expo-booth-video-left');
                $videos['right'] = CPT::getMeta($meta, 'rrze-expo-booth-video-right');
                $videos['table'] = CPT::getMeta($meta, 'rrze-expo-booth-video-table');
                foreach ($videos as $location => $url) {
                    if ($url != '') {
                        $videoSettings = $constants['template_elements']['booth'.$templateNo]['video_'.$location];
                        if ($location == 'table') {
                            echo '<a href="' . $url . '"><use class="video-tablet" xlink:href="#tablet" /></a>';
                        } else {
                            if ($scheduleLocation == $location.'-screen') {
                                continue;
                            }
                            if (is_plugin_active('rrze-video/rrze-video.php') || is_plugin_active_for_network('rrze-video/rrze-video.php')) {
                                echo '<foreignObject class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . do_shortcode('[fauvideo url="' . $url . '"]') . '</foreignObject>';
                            } else {
                                if ($location == 'left') {
                                    $translate = '1300, 340';
                                } else {
                                    $translate = '1580, 340';
                                }
                                echo '<a href="' . $url . '">
                                <rect class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '" fill="#333" stroke="#191919" stroke-width="5"></rect>
                                <use xlink:href="#video-play" fill="#ccc" x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" transform="translate('.$translate.') scale(.2)"/>
                                </a>';
                            }
                        }
                    }
                }

                // Flyers
                $flyers = CPT::getMeta($meta, 'rrze-expo-booth-flyer');
                if ($flyers != '') {
                    $flyerSettings = $constants['template_elements']['booth'.$templateNo]['flyers'];
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
                if ($socialMedia == '')
                    $socialMedia = [];
                $websiteLocations = CPT::getMeta($meta, 'rrze-expo-booth-website-locations');
                if ($websiteLocations == '')
                    $websiteLocations = [];
                $website = CPT::getMeta($meta, 'rrze-expo-booth-website');
                if ($socialMedia != [] || in_array('panel', $websiteLocations)) {
                    echo '<g><use xlink:href="#some_panel" />';
                    $socialMediaData = $constants['social-media'];
                    $socialMediaSettings = $constants['template_elements']['booth'.$templateNo]['social-media'];
                    $i = 0;
                    foreach ($socialMedia as $i => $media) {
                        if (!isset($media['medianame']) || !isset($media['username']))
                            continue;
                        switch ($socialMediaSettings['direction']) {
                            case 'landscape':
                                $translateX = $socialMediaSettings['x'] + $i * ($socialMediaSettings['width'] + 10);
                                $translateY = $socialMediaSettings['y'];
                                break;
                            case 'portrait':
                            default:
                                $translateX = $socialMediaSettings['x'];
                                $translateY = $socialMediaSettings['y'] + $i * ($socialMediaSettings['height'] + 10);
                        }
                        $class = 'icon-'.$media['medianame'];
                        if ($socialMediaSettings['color'] == true) {
                            $class .= '-color';
                        }
                        echo '<a href="' . trailingslashit($socialMediaData[$media['medianame']]) . $media['username'] . '" title="'.ucfirst($media['medianame']).': '.$media['username'].'">
                            <use xlink:href="#' . $media['medianame'] . '" width="'.$socialMediaSettings['width'].'" height="'.$socialMediaSettings['height'].'" x="'.$translateX.'" y="' . $translateY . '" class="'.$class.'" fill="#fff" stroke="#000" stroke-width="1"/>
                            </a>';
                    }
                    if (in_array('panel', $websiteLocations) && $website != '') {
                        switch ($socialMediaSettings['direction']) {
                            case 'landscape':
                                $translateX = $socialMediaSettings['x'] + $i * ($socialMediaSettings['width'] + 10);
                                $translateY = $socialMediaSettings['y'];
                                break;
                            case 'portrait':
                            default:
                                $translateX = $socialMediaSettings['x'];
                            $translateY = $socialMediaSettings['y'] + $i * ($socialMediaSettings['height'] + 10);
                        }
                        $class = 'icon-website';
                        if ($socialMediaSettings['color'] == true) {
                            $class .= '-color';
                        }
                        echo '<a href="' . $website . '" title="'. __('Go to website', 'rrze-expo') .'">
                            <use xlink:href="#website" width="'.($socialMediaSettings['width'] + 2).'" height="'.($socialMediaSettings['height'] + 2).'" x="'.$translateX.'" y="' . $translateY . '" class="'.$class.'" fill="#fff" stroke="#000" stroke-width="1"/>
                            </a>';
                    }
                    echo '</g>';
                }

                // Roll-Ups
                if ($scheduleLocation != 'rollup') {
                    $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
                    if ($rollups != '') {
                        $rollupSettings = $constants['template_elements']['booth' . $templateNo]['rollup'];
                        if (isset($rollups[0])) {
                            $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'medium');
                            echo '<use xlink:href="#rollup" />';
                            echo '<foreignObject class="rollup-content" width="' . $rollupSettings['width'] . '" height="' . $rollupSettings['height'] . '" x="' . $rollupSettings['x'] . '" y="' . $rollupSettings['y'] . '"><a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '" style="display: block; height: 100%; text-align: center;" class="lightbox"><img src="' . $rollupData0[0] . '" style=" height: 100%; object-fit: contain;"/></a></foreignObject>';
                        }
                    }
                } else {
                    echo '<use xlink:href="#rollup" />';
                }

                // Schedule
                if ($schedule != '') {
                    switch ($scheduleLocation) {
                        case 'rollup':
                            $scheduleSettings = $constants['template_elements']['booth'.$templateNo]['rollup'];
                            break;
                        case 'left-screen':
                            $scheduleSettings = $constants['template_elements']['booth'.$templateNo]['video_left'];
                            break;
                        case 'right-screen':
                            $scheduleSettings = $constants['template_elements']['booth'.$templateNo]['video_right'];
                            break;
                    }
                    echo '<foreignObject class="schedule schedule-'.$scheduleLocation.'" x="'. $scheduleSettings['x'].'" y="'. ($scheduleSettings['y'] + 2) .'" width="'. $scheduleSettings['width'].'" height="'. $scheduleSettings['height'].'">
                        <body xmlns="http://www.w3.org/1999/xhtml">' . $schedule . '</body>
                    </foreignObject>';
                }

                // Logo
                if (has_post_thumbnail()){
                    $logoLocations = CPT::getMeta($meta, 'rrze-expo-booth-logo-locations');
                    foreach ($logoLocations as $logoLocation) {
                        $logoLocationSettings = $constants['template_elements']['booth'.$templateNo]['logo'][$logoLocation];
                        echo '<image xlink:href="'.get_the_post_thumbnail_url($post, 'expo-logo').'" preserveAspectRatio="xMidYMin meet" width="'.$logoLocationSettings['width'].'" height="'.$logoLocationSettings['height'].'"  x="'.$logoLocationSettings['x'].'" y="'.$logoLocationSettings['y'].'" />';
                    }
                }

                // Homepage
                if (in_array('wall', $websiteLocations) && $website != '') {
                    $websiteSettings = $constants['template_elements']['booth'.$templateNo]['logo']['wall'];
                    echo '<text x="'.$websiteSettings['x'].'" y="'.($websiteSettings['y'] + $websiteSettings['height'] + 30).'" font-size="30" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.str_replace(['https://', 'http://'], '', $website).'</text>';
                }

                // Deco
                $deco = CPT::getMeta($meta, 'rrze-expo-booth-decorations');
                if ($deco != '') {
                    if (in_array('plant1', $deco)) {
                        echo '<use xlink:href="#plant1" />';
                    }
                    if (in_array('plant2', $deco)) {
                        echo '<use xlink:href="#plant2" />';
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

        <?php
        echo '<div id="rrze-expo-booth-content" name="rrze-expo-booth-content" class="">';
        $contactName = CPT::getMeta($meta, 'rrze-expo-booth-name');
        $contactEmail = CPT::getMeta($meta, 'rrze-expo-booth-email');
        $contactInfo = CPT::getMeta($meta, 'rrze-expo-booth-contactinfo');
        $hasContact = ($contactName != '' || $website != '' || $contactEmail != '' || $contactInfo != '' ? true : false);
        if ($hasContent) {
            echo '<div class="rrze-expo-booth-text">';

            /*<!--<div class="puls-container">
                <div class="puls-middle"></div>
                <div class="puls"></div>
            </div>-->*/
            the_content();
            echo '</div>';
        }
        if ($hasContact) {
            echo '<div class="rrze-expo-booth-contact">'
                . '<h2>'.__('Contact', 'rrze-expo').'</h2>'
                .'<ul class="booth-contact">'
                . ($videos['table'] != '' ? '<li class="booth-contact-chat"><span class="screen-reader-text">' . __('Live Chat / Video', 'rrze-expo') . ': </span><a href="' . $videos['table'] . '">' . __('Live Chat / Video', 'rrze-expo') . '</a></li>' : '')
                . ($contactName != '' ? '<li class="booth-contact-name">' . $contactName . '</li>' : '')
                . ($contactEmail != '' ? '<li class="booth-contact-email"><span class="screen-reader-text">' . __('Email', 'rrze-expo') . ': </span><a href="mailto:' . $contactEmail . '">' . $contactEmail . '</a></li>' : '')
                . ($website != '' ? '<li class="booth-contact-website"><span class="screen-reader-text">' . __('Website', 'rrze-expo') . ': </span><a href="' . $website . '">' . $website . '</a></li>' : '')
                . '</ul>';
            if (!empty($socialMedia)) {
                echo '<ul class="booth-socialmedia">';
                foreach ($socialMedia as $media) {
                    if (!isset($media['medianame']) || !isset($media['username']))
                        continue;
                    $class = 'icon-'.$media['medianame'];
                    if ($socialMediaSettings['color'] == true) {
                        $class .= '-color';
                    }
                    echo '<li><a href="' . trailingslashit($socialMediaData[$media['medianame']]) . $media['username'] . '" title="'.ucfirst($media['medianame']).': '.$media['username'].'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><use xlink:href="#' . $media['medianame'] . '" class="'.$class.'" /></svg>
                            </a></li>';
                }

                echo '</ul>';
            }
            echo ($contactInfo != '' ? '<div class="contact-info">' . $contactInfo . '</div>' : '')
                . '</div>';
        }
        echo '<div class="rrze-expo-booth-links">'
            . '<h2>'.__('Information Material', 'rrze-expo').'</h2>';
        if ($videos != '') {
            echo '<h3>' . __('Videos', 'rrze-expo') . '</h3>';
            echo '<ul class="booth-links">';
            foreach ($videos as $location => $url) {
                if ($location == 'table')
                    continue;
                echo '<li><a href="' . $url . '" class="lightbox">' . 'Video ' . $location . '</a></li>';
            }
            echo '</ul>';
        }
        if ($flyers != '') {
            echo '<h3>' . __('Flyers', 'rrze-expo') . '</h3>';
            echo '<ul class="booth-links">';
            foreach ($flyers as $flyer) {
                echo '<li><a href="' . $flyer['pdf'] . '" class="lightbox">' . get_the_title($flyer['pdf_id']) . '</a></li>';
            }
            echo '</ul>';
        }
        if (!empty($rollups)) {
            echo '<h3>' . __('Roll Up', 'rrze-expo') . '</h3>';
            echo '<ul class="booth-links">';
            echo '<li><a href="' . $rollups[0]['file'] . '" class="lightbox">' . get_the_title($rollups[0]['file_id']) . '</a></li>';
            echo '</ul>';
        }
        echo '</ul>';

        echo '</div>';

        echo '</div>';

        endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');
wp_enqueue_script('rrze-expo');

//CPT::expoFooter();
get_footer();
