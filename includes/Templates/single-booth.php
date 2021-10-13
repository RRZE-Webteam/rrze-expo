<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

global $post;

if (isset($_POST['sent']) && $_POST['sent'] == 'true') {
    $mailInfoText = '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("a.trigger-message").trigger("click");
                $("#fancybox-container-1").remove();
            });
        </script>';
    $replyPhone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $replyEmail = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $replyName = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $postMessage = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    if (isset($_POST['copy']) && $_POST['copy'] == 'on' && $replyEmail != '') {
        $copy = true;
    } else {
        $copy = false;
    }
    if ($_POST['email'] != '' || $_POST['phone'] != '') {
        $meta = get_post_meta($post->ID);
        $toEmail = CPT::getMeta($meta, 'rrze-expo-booth-email');
        if ($toEmail == '')
            return;
        $expoID = CPT::getMeta($meta, 'rrze-expo-booth-exposition');
        $expoTitle = get_the_title($expoID);
        $toName = CPT::getMeta($meta, 'rrze-expo-booth-name');
        $to = ($toName != '' ? $toName . ' <' . $toEmail . '>' : $toEmail);
        $subject = '[' . $expoTitle . '] ' . __('Message from contact form', 'rrze-expo');
        $fromEmail = get_bloginfo('admin_email');
        $headers[] = 'From: ' . $fromEmail;
        if ($copy) {
            $headers[] = 'Cc: ' . ' <' . $replyEmail . '>';
        }
        if ($replyEmail != '') {
            $headers[] = 'Reply-To: ' . ($replyName != '' ? $replyName . ' <' . $replyEmail . '>' : $replyEmail);
        }
        $message = '';
        if ($replyName != '') {
            $message .= __('From', 'rrze-expo') . ': ' . $replyName . "\r\n";
        }
        if ($replyEmail != '') {
            $message .= __('Email', 'rrze-expo') . ': ' . $replyEmail . "\r\n";
        }
        if ($replyPhone != '') {
            $message .= __('Phone', 'rrze-expo') . ': ' . $replyPhone . "\r\n";
        }
        if ($postMessage != '') {
            $message .= "\r\n" . __('Message', 'rrze-expo') . ":\r\n" . $postMessage;
        }

        if (wp_mail($to, $subject, $message, $headers)) {
        // if (true) {
            $mailStatus = 'sent';
            $mailInfoText .= do_shortcode('[alert style="success"]' . __('Your message has been sent!', 'rrze-expo') . '[/alert]');
        } else {
            $mailStatus = 'error';
            $mailInfoText .= do_shortcode('[alert style="danger"]' . __('<b>There was an error sending your message.</b><br /> Please try again.', 'rrze-expo') . '[/alert]');
        }
    } else {
        $mailStatus = 'error';
        $mailInfoText .= do_shortcode('[alert style="danger"]'.__('<b>There was an error sending your message.</b><br />Please enter a valid email address or phone number.','rrze-expo') . '[/alert]');
    }
}

CPT::expoHeader();
//get_header();
?>

<main>
    <?php
    CPT::expoNav();
    $isIOS = (strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad'));
    $isSafari = (strpos($_SERVER['HTTP_USER_AGENT'],'Safari') !== false && strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') === false);
    $macFix = ($isIOS === true || $isSafari === true);

    while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $boothID = get_the_ID();
        $meta = get_post_meta($boothID);
        $expoID = CPT::getMeta($meta, 'rrze-expo-booth-exposition');
        $templateNo = CPT::getMeta($meta, 'rrze-expo-booth-template');
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
                    if (!is_array($talk['booth'])) {
                        $talk['booth'] = [$talk['booth']];
                    }
                    if (isset($talk['booth']) && in_array($boothID,$talk['booth'])) {
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
        $wallImage = CPT::getMeta($meta, 'rrze-expo-booth-backwall-image');
        $wallSettings = $constants['template_elements']['booth'.$templateNo]['wall'];
        $title = get_the_title();
        $fontSize = CPT::getMeta($meta, 'rrze-expo-booth-font-size');
        $gallery = CPT::getMeta($meta, 'rrze-expo-booth-gallery');
        if ($templateNo == '2' && CPT::getMeta($meta, 'rrze-expo-booth-plain-texture') == 'on') {
            $pot = 'flower_pot_plain';
            $flyerDisplay = 'flyer_display_plain';
            $sociaMediaPanel = 'some_panel_plain';
            $rollupPanel = 'rollup_plain';
        } else {
            $pot = 'flower_pot';
            $flyerDisplay = 'flyer_display';
            $sociaMediaPanel = 'some_panel';
            $rollupPanel = 'rollup';
        }
        ?>
        <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <a id="page-start"></a>
            <svg version="1.1" class="expo-booth template-<?php echo $templateNo; ?>" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php
                // Back wall
                echo '<rect x="' . $wallSettings['x'] . '" y="' . $wallSettings['y'] . '" width="' . $wallSettings['width'] . '" height="' . $wallSettings['height'] . '" style="fill:' . $accentColor . '" rx="4" ry="4" />';
                if ($wallImage != '') {
                    echo '<image xlink:href="'.$wallImage.'" class="wall-image" width="'.$wallSettings['width'].'" height="'.$wallSettings['height'].'"  x="'.$wallSettings['x'].'" y="'.$wallSettings['y'].'" preserveAspectRatio="xMinYMin meet" />';
                } else {
                    echo '<use class="backwall" xlink:href="#backwall-color" />';
                }

                // Videos
                $videos['left'] = CPT::getMeta($meta, 'rrze-expo-booth-video-left');
                $videos['right'] = CPT::getMeta($meta, 'rrze-expo-booth-video-right');
                $videos['table'] = CPT::getMeta($meta, 'rrze-expo-booth-video-table');
                $rrzeVideoActive = (is_plugin_active('rrze-video/rrze-video.php') || is_plugin_active_for_network('rrze-video/rrze-video.php'));
                foreach ($videos as $location => $url) {
                    if ($url != '') {
                        $videoSettings = $constants['template_elements']['booth'.$templateNo]['video_'.$location];
                        if ($location == 'table') {
                            if ($templateNo == '2' && !empty($gallery)) {
                                continue;
                            } else {
                                echo '<a class="tablet-link" href="' . $url . '"><use class="video-tablet" xlink:href="#tablet" /></a>';
                            }
                        } else {
                            if ($scheduleLocation == $location.'-screen') {
                                continue;
                            }
                            if (!$macFix && strpos($url, get_home_url()) !== false) {
                                // Videos uploaded in Media
                                echo '<foreignObject class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . do_shortcode('[video width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '" src="' . $url . '"][/video]') . '</foreignObject>';
                            } elseif (!$macFix && $rrzeVideoActive) {
                                // RRZE-Video Plugin for fau.tv, Youtube etc.
                                echo '<foreignObject class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . do_shortcode('[fauvideo url="' . $url . '"]') . '</foreignObject>';
                            } else {
                                // Fallback
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

                // Gallery
                if (!empty($gallery)) {
                    $galleryStartId = array_key_first($gallery);
                    $galleryStartURL = reset($gallery);
                    $gallerySettings = $constants['template_elements']['booth'.$templateNo]['gallery'];
                    echo '<a href="' . $galleryStartURL.'" data-fancybox="booth-gallery" class="lightbox">'
                        . '<use class="gallery-tablet" xlink:href="#gallery" />'
                        . '<text x="' . $gallerySettings['x'] . '" y="' . $gallerySettings['y'] . '" font-size="24" fill="#333">'. __('Gallery', 'rrze-expo').'</text>'
                        . '</a>';
                    foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
                        if ($attachment_id == $galleryStartId)
                            continue;
                        echo '<a href="' . wp_get_attachment_image_url($attachment_id, 'full').'" data-fancybox="booth-gallery" style="display: none;" class="lightbox">'.$attachment_url.'</a>';
                    }
                }

                // Logo Wall
                if (has_post_thumbnail()){
                    $logoLocations = CPT::getMeta($meta, 'rrze-expo-booth-logo-locations');
                    if ($logoLocations != '') {
                        foreach ($logoLocations as $logoLocation) {
                            $logoLocationSettings[$logoLocation] = $constants['template_elements']['booth' . $templateNo]['logo'][$logoLocation];
                        }
                        if (in_array('wall', $logoLocations)) {
                            echo '<image xlink:href="' . get_the_post_thumbnail_url($post, 'expo-logo') . '" preserveAspectRatio="xMidYMin meet" width="' . $logoLocationSettings['wall']['width'] . '" height="' . $logoLocationSettings['wall']['height'] . '"  x="' . $logoLocationSettings['wall']['x'] . '" y="' . $logoLocationSettings['wall']['y'] . '" />';
                        }
                    }
                }

                // Personas
                $personaStyles = '';
                for ($i=1; $i<=3; $i++) {
                    $personaRaw = CPT::getMeta($meta, 'rrze-expo-booth-persona-'.$i);
                    // TODO: Abwärtskompatibilität – beim nächsten Update entfernen!
                    if (!is_array($personaRaw)) {
                        $persona[$i]['persona'] = CPT::getMeta($meta, 'rrze-expo-booth-persona-' . $i);
                    } else {
                        $persona[$i] = $personaRaw;
                    }
                    $personaSettings = $constants['template_elements']['booth'.$templateNo]['persona'][$i];
                    if (!empty($persona[$i]['persona'])) {
                        $file = WP_PLUGIN_DIR . '/rrze-expo/assets/img/template-assets/'.$persona[$i]['persona'].'.svg';
                        if ($file) {
                            $svg = file_get_contents($file);
                            echo str_replace('<svg ', '<svg x="'.$personaSettings['x'].'" y="'.$personaSettings['y'].'" width="'.$personaSettings['width'].'" height="'.$personaSettings['height'].'" ', $svg);
                            $skinColor = (isset($persona[$i]['skin-color']) ? $persona[$i]['skin-color'] : '');
                            $hairColor = (isset($persona[$i]['hair-color']) ? $persona[$i]['hair-color'] : '');
                            $personaStyles .= '#'.$persona[$i]['persona'].'-rrze-expo {'
                                . CPT::makePersonaStyles($skinColor, $hairColor)
                                . '}';
                        }
                    }
                }
                if ($personaStyles != '') {
                    echo '<style type="text/css">' . $personaStyles . '</style>';
                }

                // Website Wall
                $website = CPT::getMeta($meta, 'rrze-expo-booth-website');
                $websiteLocations = CPT::getMeta($meta, 'rrze-expo-booth-website-locations');
                if ($websiteLocations == '')
                    $websiteLocations = [];
                if (in_array('wall', $websiteLocations) && $website != '') {
                    $websiteSettings = $constants['template_elements']['booth'.$templateNo]['website'];
                    if ($videos['left'] == '' && $videos['right'] == '' && !in_array($scheduleLocation, ['left-screen', 'right-screen']) ) {
                        $websiteSettings['x'] = $constants['template_elements']['booth'.$templateNo]['video_left']['x'];
                        $websiteSettings['y'] = $constants['template_elements']['booth'.$templateNo]['video_left']['y'] + 50;
                    } elseif ($persona[1] == '' && $persona[2] == '' && $persona[3] == '') {
                        $websiteSettings['x'] = $constants['template_elements']['booth'.$templateNo]['logo']['wall']['x'];
                        $websiteSettings['y'] = $constants['template_elements']['booth'.$templateNo]['logo']['wall']['y'] + $constants['template_elements']['booth'.$templateNo]['logo']['wall']['height'] + 30;
                    }
                    echo '<text x="'.$websiteSettings['x'].'" y="'.($websiteSettings['y']).'" font-size="30" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.str_replace(['https://', 'http://'], '', $website).'</text>';
                }

                //Table
                echo '<use xlink:href="#table" />';
                $titleSettings = $constants['template_elements']['booth'.$templateNo]['title'];
                if (strpos($title, '<br>') != false) {
                    $titleParts = explode('<br>', $title);
                    $title = '<tspan>' . implode('</tspan><tspan x="'.$titleSettings['x'].'" dy="'.($fontSize*1.12).'">', $titleParts) . '</tspan>';
                }
                echo '<text x="'.$titleSettings['x'].'" y="'.$titleSettings['y'].'" font-size="'.$fontSize.'" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.$title.'</text>';

                // Logo Table
                if (has_post_thumbnail() && is_array($logoLocations) && in_array('table', $logoLocations)){
                    echo '<image xlink:href="'.get_the_post_thumbnail_url($post, 'expo-logo').'" preserveAspectRatio="xMidYMin meet" width="'.$logoLocationSettings['table']['width'].'" height="'.$logoLocationSettings['table']['height'].'"  x="'.$logoLocationSettings['table']['x'].'" y="'.$logoLocationSettings['table']['y'].'" />';
                }

                // Deco
                $deco = CPT::getMeta($meta, 'rrze-expo-booth-decorations');
                if ($deco != '') {
                    if (in_array('plant1', $deco)) {
                        echo '<use xlink:href="#'.$pot.'" />';
                        echo '<use xlink:href="#plant1" />';
                    }
                    if (in_array('plant2', $deco)) {
                        echo '<use xlink:href="#'.$pot.'" />';
                        echo '<use xlink:href="#plant2" />';
                    }
                    if (in_array('plant3', $deco)) {
                        echo '<use xlink:href="#'.$pot.'" x="-1780" y="0" />';
                        echo '<use xlink:href="#plant1" x="-1780" y="0"/>';
                    }
                    if (in_array('plant4', $deco)) {
                        echo '<use xlink:href="#'.$pot.'" transform="translate(4100 0), scale(-1 1)" />';
                        echo '<use xlink:href="#plant2" transform="translate(4100 0), scale(-1 1)"/>';
                    }

                }

                // Flyers
                $flyers = CPT::getMeta($meta, 'rrze-expo-booth-flyer');
                if ($flyers != '') {
                    $flyerSettings = $constants['template_elements']['booth'.$templateNo]['flyers'];
                    echo '<g><use xlink:href="#'.$flyerDisplay.'" />';
                    foreach ($flyers as $i => $flyer) {
                        $translateY = $flyerSettings['y'] + $i * ($flyerSettings['height'] + 20);
                        $translateYMac = 430 + $i * ($flyerSettings['height'] + 20);
                        if (array_key_exists('pdf', $flyer) && $flyer['pdf'] != '') {
                            echo '<a href="' . $flyer['pdf'] . '" title="' . get_the_title($flyer['pdf_id']) . '">';
                        }
                        if (!array_key_exists('preview', $flyer) || $flyer['preview'] == false) {
                            echo '<use xlink:href="#flyer_default" width="'. $flyerSettings['width'].'" height="'.$flyerSettings['height'].'" x="'.$flyerSettings['x'].'" y="' . $translateY . '"/>';
                        } else {
                            echo '<image xlink:href="' . $flyer['preview'] . '" width="'. $flyerSettings['width'].'" height="'.$flyerSettings['height'].'" x="'.$flyerSettings['x'].'" y="' . $translateY . '" preserveAspectRatio="xMidYMin meet"/>';
                        }
                        if (array_key_exists('pdf', $flyer) && $flyer['pdf'] != '') {
                            if ($macFix) {
                                echo '<use xlink:href="#mouse-pointer" class="mouse-pointer" fill="#fff" transform="translate(2755 '.$translateYMac.') scale(.09)" stroke="#333" stroke-width="15" />';
                            } else {
                                echo '<foreignObject x="'. ($flyerSettings['x'] + $flyerSettings['width'] - 100).'" y="'. ($translateY-20) .'" width="150" height="150">
                                <body xmlns="http://www.w3.org/1999/xhtml">' . CPT::pulsatingDot() . '</body>
                                </foreignObject>';
                            }
                            echo '</a>';
                        }
                    }
                    echo '</g>';
                }

                // Social Media
                $socialMedia = CPT::getMeta($meta, 'rrze-expo-booth-social-media');
                $contactEmail = CPT::getMeta($meta, 'rrze-expo-booth-email');
                $showContactForm = ((CPT::getMeta($meta, 'rrze-expo-booth-showcontactform') == 'on' && $contactEmail !='') ? true : false);
                if ($socialMedia == '')
                    $socialMedia = [];
                if ($socialMedia != [] || in_array('panel', $websiteLocations) || $showContactForm == true) {
                    echo '<g><use xlink:href="#'.$sociaMediaPanel.'" />';
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
                        $i++;
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
                    if ($showContactForm) {
                        $i++;
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
                        $class = 'icon-paper-plane';
                        if ($socialMediaSettings['color'] == true) {
                            $class .= '-color';
                        }
                        echo '<a data-fancybox data-src="#hidden-content" href="javascript:;" class="trigger-message" title="'. __('Leave us a message', 'rrze-expo') .'">
                            <use xlink:href="#paper-plane" width="'.($socialMediaSettings['width'] + 2).'" height="'.($socialMediaSettings['height'] + 2).'" x="'.$translateX.'" y="' . $translateY . '" class="'.$class.'" fill="#fff" stroke="#000" stroke-width="1"/>
                            </a>';
                    }
                    echo '</g>';
                }

                // Roll-Ups
                $rollupSettings = $constants['template_elements']['booth' . $templateNo]['rollup'];
                if ($scheduleLocation != 'rollup') {
                    $rollups = CPT::getMeta($meta, 'rrze-expo-booth-rollups');
                    if ($rollups != '') {
                        if (isset($rollups[0])) {
                            $rollupData0 = wp_get_attachment_image_src($rollups[0]['file_id'], 'full');
                            $rollupClickable = (isset($rollups[0]['clickable']) && $rollups[0]['clickable'] == 'on');
                            echo '<g class="booth-rollup">'
                                . '<use xlink:href="#'.$rollupPanel.'" />'
                                . '<foreignObject class="rollup-content" width="' . $rollupSettings['width'] . '" height="' . $rollupSettings['height'] . '" x="' . $rollupSettings['x'] . '" y="' . $rollupSettings['y'] . '">';
                            if ($rollupClickable) {
                                echo '<a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '" data-fancybox style="display: block; height: 100%; text-align: center;" class="lightbox">';
                            } else {
                                echo '<div style="height: 100%; text-align: center;">';
                            }
                            echo '<img src="' . $rollupData0[0] . '" style=" height: 100%; object-fit: contain;"/>';
                            if ($rollupClickable) {
                                echo '</a>';
                            } else {
                                echo '</div>';
                            }
                            echo '</foreignObject>';
                            if ($rollupClickable) {
                                if ($macFix) {
                                    echo '<use xlink:href="#mouse-pointer" class="mouse-pointer" fill="#fff" transform="translate(' . ($rollupSettings['x'] + $rollupSettings['width'] - 50) . ' ' . ($rollupSettings['y'] + 20) . ') scale(.1)" stroke="#333" stroke-width="15" />';
                                } else {
                                    echo '<foreignObject x="' . ($rollupSettings['x'] + $rollupSettings['width'] - 150) . '" y="' . ($rollupSettings['y']) . '" width="150" height="150"><body xmlns="http://www.w3.org/1999/xhtml"><a href="' . $rollups[0]['file'] . '" title="' . get_the_title($rollups[0]['file_id']) . '" class="lightbox">' . CPT::pulsatingDot() . '</a></body></foreignObject>';
                                }
                            }
                            echo '</g>';
                        }
                    }
                } else {
                    echo '<use xlink:href="#'.$rollupPanel.'" />';
                }

                /// Logo Panel
                if (has_post_thumbnail() && in_array('panel', $logoLocations)){
                    echo '<image xlink:href="'.get_the_post_thumbnail_url($post, 'expo-logo').'" preserveAspectRatio="xMidYMin meet" width="'.$logoLocationSettings['panel']['width'].'" height="'.$logoLocationSettings['panel']['height'].'"  x="'.$logoLocationSettings['panel']['x'].'" y="'.$logoLocationSettings['panel']['y'].'" />';
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

                // Seats
                for ($i=1; $i<=3; $i++) {
                    $seat[$i] = CPT::getMeta($meta, 'rrze-expo-booth-seat-'.$i);
                    $seatSettings = $constants['template_elements']['booth'.$templateNo]['seat'][$i];
                    if ($seat[$i] != '') {
                        $file = WP_PLUGIN_URL . '/rrze-expo/assets/img/template-assets/beanbag_'.$seat[$i].'_'.$i.'.png';
                        if ($file) {
                            echo '<image xlink:href="'.$file.'" preserveAspectRatio="xMidYMin meet" width="'.$seatSettings['width'].'" height="'.$seatSettings['height'].'"  x="'.$seatSettings['x'].'" y="'.$seatSettings['y'].'" />';
                        }
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
        $contactInfo = CPT::getMeta($meta, 'rrze-expo-booth-contactinfo');
        $hasContact = ($contactName != '' || $website != '' || $contactEmail != '' || $contactInfo != '' ? true : false);
        if ($hasContent) {
            echo '<div class="rrze-expo-booth-text">';
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
            echo ($contactInfo != '' ? '<div class="contact-info">' . $contactInfo . '</div>' : '');
            if ($showContactForm) {
                echo '<p class="form-link"><a data-fancybox data-src="#hidden-content" href="javascript:;" class="trigger-message"> ' . __('Leave us a message', 'rrze-expo') . '</a></p>';
            }
            echo '</div>';
        }

        $infoText = '';
        if ($videos['left'] != '' || $videos['right'] != '') {
            $infoText .= '<h3>' . __('Videos', 'rrze-expo') . '</h3>';
            $infoText .= '<ul class="booth-links">';
            $i = 1;
            foreach ($videos as $location => $url) {
                if ($location == 'table' || $url == '')
                    continue;
                $infoText .= '<li><a href="' . $url . '">' . 'Video ' . $i . '</a></li>';
                $i++;
            }
            $infoText .= '</ul>';
        }
        if ($flyers != '') {
            $infoText .= '<h3>' . __('Flyers', 'rrze-expo') . '</h3>';
            $infoText .= '<ul class="booth-links">';
            foreach ($flyers as $flyer) {
                if (array_key_exists('pdf', $flyer) && $flyer['pdf'] != '') {
                    $infoText .= '<li><a href="' . $flyer['pdf'] . '">' . get_the_title($flyer['pdf_id']) . '</a></li>';
                }
            }
            $infoText .= '</ul>';
        }
        if (!empty($rollups) && $rollupClickable) {
            $infoText .= '<h3>' . __('Roll Up', 'rrze-expo') . '</h3>';
            $infoText .= '<ul class="booth-links">';
            $infoText .= '<li><a href="' . $rollups[0]['file'] . '" class="lightbox">' . get_the_title($rollups[0]['file_id']) . '</a></li>';
            $infoText .= '</ul>';
        }
        if ($infoText != '') {
            echo '<div class="rrze-expo-booth-links">'
                . '<h2>'.__('Information Material', 'rrze-expo').'</h2>'
                . $infoText
                . '</div>';
        }

        echo '</div>';

        // Contact Form
        if ($showContactForm) {
            $mailStatus = (isset($mailStatus)) ? $mailStatus : '';
            $mailInfoText = (isset($mailInfoText) ? $mailInfoText : '');
            echo '<div style="display: none;" id="hidden-content">' . $mailInfoText;
            if ($mailStatus != 'sent') {
                echo '<form method="post" name="contact_form" action="#" style="max-width: 600px;">
                        <h2 style="font-size:1.5em;margin-bottom: 1em;">' . __('Send a message to ', 'rrze-expo') . ($contactName != '' ? $contactName . ' (' : '') . $title . ($contactName != '' ? ')' : '') . '</h2>
                        <p>
                            <label>' . __('Your Name', 'rrze-expo') . ':<br />
                            <input type="text" name="name" style="width:100%;" value="' . (isset($replyName) ? $replyName : '') . '"></label>
                        </p>
                        <p>
                            <label>' . __('Your Email Address', 'rrze-expo') . ':<br />
                            <input type="email" name="email" style="width:100%;" value="' . (isset($replyEmail) ? $replyEmail : '') . '"></label>
                        </p>
                        <p>
                            <label>' . __('Your Phone Number', 'rrze-expo') . ':<br />
                            <input type="text" name="phone" style="width:100%;" value="' . (isset($replyPhone) ? $replyPhone : '') . '"></label>
                        </p>
                        <p>
                            <label>' . __('Your Message', 'rrze-expo') . ':<br />
                            <textarea name="message" style="width:100%;">' . (isset($postMessage) ? $postMessage : '') . '</textarea>
                            </label>
                        </p>
                        <p>
                            <label>
                            <input type="checkbox" value="on" name="copy" style="margin-right:5px;" '. checked((isset($copy) && $copy == true), true, false) . '>' . __('Send a copy of the message to my email address.', 'rrze-expo') . '
                            </label>
                        </p>
                        <input type="hidden" name="sent" value="true" />
                        <input type="submit" value="' . __('Submit', 'rrze-expo') . '">
                    </form>
                </div>';
            }
            echo '</div>';
        }

        if (!empty($gallery)) {
            wp_dequeue_script('fau-scripts');
            wp_enqueue_script('jquery-fancybox');
            wp_enqueue_style('rrze-elements');
            echo '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $.fancybox.defaults.loop = true;
            });
            </script>';
        }

    endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');
wp_enqueue_script('rrze-expo');
wp_enqueue_style('rrze-elements');

//CPT::expoFooter();
get_footer();
