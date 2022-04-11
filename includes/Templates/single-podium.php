<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
?>

<main>
    <?php
    $constants = getConstants();
    $isIOS = (strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad'));
    $isSafari = (strpos($_SERVER['HTTP_USER_AGENT'],'Safari') !== false && strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') === false);
    $macFix = ($isIOS === true || $isSafari === true);
    CPT::expoNav();
    while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $podiumID = get_the_ID();
        $meta = get_post_meta($podiumID);
        $expoID = CPT::getMeta($meta, 'rrze-expo-podium-exposition');
        $templateNo = CPT::getMeta($meta, 'rrze-expo-podium-template');
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-podium-background-image');
        $scheduleBackgroundColor = CPT::getMeta($meta, 'rrze-expo-podium-timetable-background-color');
        if ($backgroundImage == '') {
            // If no background image is set -> display exposition background image
            $backgroundImage = get_post_meta($expoID, 'rrze-expo-exposition-background-image', true);
        }
        $title = get_the_title();
        // Schedule
        if (array_key_exists('schedule', $constants['template_elements']['podium'.$templateNo])) {
            $scheduleSettings = $constants['template_elements']['podium'.$templateNo]['schedule'];
        } else {
            $scheduleSettings = $constants['template_elements']['podium'.$templateNo]['video'];
        }

        $scheduleStyle = $scheduleBackgroundColor != '' ? ' style="background-color:'. $scheduleBackgroundColor . '"' : '';
        $schedule = '';
        $timeslots = CPT::getMeta($meta, 'rrze-expo-podium-timeslots');
        //var_dump($timeslots);
        if ($timeslots == '') {
            $timeslots = [];
            $schedule = __('No Talks available', 'rrze-expo');
        } else {
            $schedule .= '<div class="schedule-content" ' . $scheduleStyle . '><h2 style="margin-bottom: .25em;">' . __('Schedule', 'rrze-expo') . '</h2>';
            // group by day
            $i = 0;
            foreach ($timeslots as $timeslot) {
                if (array_key_exists('start', $timeslot)) {
                    $startdateYmd = date('Y-m-d', $timeslot['start']);
                    $startdateTimestamp = strtotime($startdateYmd);
                } else {
                    $startdateTimestamp = 'no-date';
                }
                $timeslotsGrouped[$startdateTimestamp][$i]['starttime'] = array_key_exists('start', $timeslot) ? date('H:i', $timeslot['start']) : '';
                $timeslotsGrouped[$startdateTimestamp][$i]['endtime'] = array_key_exists('end', $timeslot) ? date('H:i', $timeslot['end']) : '';
                $timeslotsGrouped[$startdateTimestamp][$i]['title'] = array_key_exists('title', $timeslot) ? $timeslot['title'] : '';
                $timeslotsGrouped[$startdateTimestamp][$i]['url'] = array_key_exists('url', $timeslot) ? $timeslot['url'] : '';
                $timeslotsGrouped[$startdateTimestamp][$i]['booth'] = array_key_exists('booth', $timeslot) ? $timeslot['booth'] : '';
                $timeslotsGrouped[$startdateTimestamp][$i]['description'] = array_key_exists('description', $timeslot) ? $timeslot['description'] : '';
                $i++;
            }
            if ($templateNo == '2') $schedule .= '[columns]';
            foreach ($timeslotsGrouped as $day => $timeslotDay) {
                if ($templateNo == '2') $schedule .= '[column]';
                $schedule .= '<h3>' . date('d.m.Y', $day) . '</h3>';
                $schedule .= '<table>';
                foreach ($timeslotDay as $id => $timeslotDetails) {
                    $schedule .= '<tr>';
                    $schedule .= '<td style="width: 20%;">' . $timeslotDetails['starttime'] . ' - ' . $timeslotDetails['endtime'] . '</td>';
                    $schedule .= '<td><span class="talk-title">'
                        . (($templateNo == '2' && $timeslotDetails['url'] != '') ? '<a href="'.$timeslotDetails['url'].'">' : '')
                        . $timeslotDetails['title']
                        . (($templateNo == '2' && $timeslotDetails['url'] != '') ? '</a>' : '')
                        . '</span>';
                    if (isset($timeslotDetails['description']) && $timeslotDetails['description'] != '') {
                        $schedule .= '<a data-fancybox data-src="#talk-description-'.$id.'" href="javascript:;" class="trigger-description" aria-label title="'. __('More information about ', 'rrze-expo') . '&quot;' . $timeslotDetails['title'] . '&quot;' . '"><svg class="trigger-description-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#fff" width="30px" height="30px"><path d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" /></svg></a>';
                        $schedule .= '<div style="display: none;" class="talk-description" id="talk-description-'.$id.'">'
                            . '<h1>' . $timeslotDetails['title'] . '</h1>'
                            . '<p style="font-style:italic">' . date('d.m.Y', $day) . ', ' . $timeslotDetails['starttime'] . ' - ' . $timeslotDetails['endtime'] . '</p>'
                            . wpautop(nl2br($timeslotDetails['description'])) . '</div>';
                    }
                    if (isset($timeslotDetails['booth']) && $timeslotDetails['booth'] != '') {
                        if (!is_array($timeslotDetails['booth'])) {
                            $timeslotDetails['booth'] = [$timeslotDetails['booth']];
                        }
                        //$schedule .= '<br />';
                        $boothLinks = [];
                        foreach ($timeslotDetails['booth'] as $boothID) {
                            $boothLinks[] = '<p><a href="' . get_permalink($boothID) . '">' . str_replace('<br>',' ',get_the_title($boothID)) . '</a></p>';
                        }
                        $schedule .= implode(', ', $boothLinks);
                    }
                    $schedule .= '</tr>';
                }
                $schedule .= '</table>';
                if ($templateNo == '2') $schedule .= '[/column]';
            }
            if ($templateNo == '2') $schedule .= '[/columns]';
            $schedule .= '</div>';
        }

        // Video
        if ($templateNo == 1) {
            $videoSettings = $constants['template_elements']['podium' . $templateNo]['video'];
            $now = current_time('timestamp');
            $video = '';
            foreach ($timeslots as $timeslot) {
                if ($timeslot['url'] != '' && $timeslot['start'] <= $now && $timeslot['end'] >= $now) {
                    $url = $timeslot['url'];
                    $rrzeVideoActive = (is_plugin_active('rrze-video/rrze-video.php') || is_plugin_active_for_network('rrze-video/rrze-video.php'));
                    $videoContent = '';
                    // Plugin rrze-video active -> use plugin
                    if (!$macFix && strpos($url, get_home_url()) !== false) {
                        // Videos uploaded in Media
                        $video = '<foreignObject class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . do_shortcode('[video width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '" src="' . $url . '"][/video]') . '</foreignObject>';
                    } elseif (!$macFix && $rrzeVideoActive) {
                        $videoContent = do_shortcode('[fauvideo url="' . $url . '"]');
                        $video = '<foreignObject class="video" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '"><div class="video-container" style="width:100%; height:100%;">' . $videoContent . '</div></foreignObject>';
                    }
                    // Plugin rrze-video not active or source not supported by plugin -> make link
                    if ($macFix || strpos($videoContent, 'Unbekannte Videoquelle')) {
                        $video = '<a href="' . $url . '">
                                <rect class="video" x="' . ($videoSettings['x'] + 5) . '" y="' . ($videoSettings['y'] + 5) . '" width="' . ($videoSettings['width'] - 10) . '" height="' . ($videoSettings['height'] - 10) . '" fill="#333" stroke="#191919" stroke-width="5"></rect>
                                <use xlink:href="#video-play" fill="#ccc" x="' . $videoSettings['x'] . '" y="' . $videoSettings['y'] . '" transform="translate(1270 310) scale(.5)"/>
                                </a>';
                    }
                    break;
                } else {
                    //$video = '<text class="video-error" x="' . ($videoSettings['x'] + 50) . '" y="' . ($videoSettings['y'] + 100) . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . __('Currently no video available', 'rrze-expo') . '</text>';
                }
            }
        }

        ?>

        <div id="rrze-expo-podium" class="podium" style="background-image: url('<?php echo $backgroundImage;?>');">
            <a id="page-start"></a>
            <style>
                #rrze-expo-podium .schedule *, #rrze-expo-podium .schedule h1, #rrze-expo-podium .schedule h2, #rrze-expo-podium .schedule h3 {color: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-font-color'); ?>;}
                #rrze-expo-podium .schedule a {color: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-link-color'); ?>;}
                #rrze-expo-podium .schedule a:hover, #rrze-expo-podium .schedule a:focus, #rrze-expo-podium .schedule a:active {color: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-font-color'); ?>;}
                #rrze-expo-podium .trigger-description-svg {fill: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-link-color'); ?>;}
                #rrze-expo-podium a:hover .trigger-description-svg, #rrze-expo-podium a:focus .trigger-description-svg, #rrze-expo-podium a:active .trigger-description-svg {fill: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-font-color'); ?>;}
                #rrze-expo-podium .schedule {background-color: <?php echo CPT::getMeta($meta, 'rrze-expo-podium-timetable-background-color'); ?>;}
            </style>
            <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
            <svg version="1.1" class="expo-podium template-<?php echo $templateNo; ?>" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php
                // Deco
                $deco = CPT::getMeta($meta, 'rrze-expo-podium-decorations');
                if ($deco != '') {
                    if (in_array('plant1', $deco)) {
                        echo '<use xlink:href="#plant1"/>';
                    }
                    if (in_array('plant2', $deco)) {
                        echo '<use xlink:href="#plant2"/>';
                    }
                } ?>
                <use class="podium" xlink:href="#podium" x="100" />
                <?php
                echo '<foreignObject class="schedule" x="' . $scheduleSettings['x'] . '" y="' . ($scheduleSettings['y']) . '" width="' . $scheduleSettings['width'] . '" height="' . $scheduleSettings['height'] . '">
                    <body xmlns="http://www.w3.org/1999/xhtml">' . do_shortcode($schedule) . '</body>
                </foreignObject>'
                    . '<foreignObject class="schedule schedule-mobile" x="'. $scheduleSettings['x'].'" y="'. ($scheduleSettings['y'] + 2) .'" width="'. $scheduleSettings['width'].'" height="'. $scheduleSettings['height'].'"><a data-fancybox data-src="#schedule-popup" href="javascript:;" class="" href="" style="display: block;width: 100%; height:100%;">' . CPT::pulsatingDot() . '<h2>' . __('Schedule', 'rrze-expo') . '</h2><svg class="" x="'. $scheduleSettings['x'].'" y="'. ($scheduleSettings['y'] + 2) .'" width="'. $scheduleSettings['width'].'" height="'. $scheduleSettings['height'].'"><use xlink:href="#list"/></svg></a></foreignObject>';
                if ($templateNo == 1) {
                    echo $video;
                }
                ?>
            </svg>

            <?php if ($hasContent) { ?>
                <a href="#rrze-expo-podium-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                    <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                    <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
                </a>
            <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
            <div id="rrze-expo-podium-content" name="rrze-expo-podium-content" class="">
                <?php the_content(); ?>
            </div>
        <?php }

            // Schedule Popup Content
            if (!empty($schedule)) {
                echo '<div style="display: none;" id="schedule-popup">' . str_replace(['[columns]','[/columns]','[column]','[/column]'], '', $schedule) . '</div>';
            }
        ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
