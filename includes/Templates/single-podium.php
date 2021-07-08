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
        $schedule = '';
        $timeslots = CPT::getMeta($meta, 'rrze-expo-podium-timeslots');
        //var_dump($timeslots);
        if ($timeslots == '') {
            $timeslots = [];
            $schedule = __('No Talks available', 'rrze-expo');
        } else {
            $schedule .= '<div class="schedule-content"><h2>' . __('Schedule', 'rrze-expo') . '</h2>';
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
                $i++;
            }
            foreach ($timeslotsGrouped as $day => $timeslotDay) {
                $schedule .= '<h3>' . date('d.m.Y', $day) . '</h3>';
                $schedule .= '<table>';
                foreach ($timeslotDay as $timeslotDetails) {
                    $schedule .= '<tr>';
                    $schedule .= '<td>' . $timeslotDetails['starttime'] . ' - ' . $timeslotDetails['endtime'] . '</td>';
                    $schedule .= '<td><span class="talk-title">' . $timeslotDetails['title'] . '</span>';
                    if (isset($timeslotDetails['booth']) && $timeslotDetails['booth'] != '') {
                        if (!is_array($timeslotDetails['booth'])) {
                            $timeslotDetails['booth'] = [$timeslotDetails['booth']];
                        }
                        $schedule .= '<br />';
                        $boothLinks = [];
                        foreach ($timeslotDetails['booth'] as $boothID) {
                            $boothLinks[] = '<a href="' . get_permalink($boothID) . '">' . str_replace('<br>',' ',get_the_title($boothID))   . '</a>';
                        }
                        $schedule .= implode(', ', $boothLinks);
                    }
                    $schedule .= '</tr>';
                }
                $schedule .= '</table>';
            }
            $schedule .= '</div>';
        }

        // Video
        $videoSettings = $constants['template_elements']['podium'.$templateNo]['video'];
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
                                <rect class="video" x="' . ($videoSettings['x'] + 5) . '" y="' . ($videoSettings['y'] + 5) . '" width="' . ($videoSettings['width'] -10) . '" height="' . ($videoSettings['height'] - 10) . '" fill="#333" stroke="#191919" stroke-width="5"></rect>
                                <use xlink:href="#video-play" fill="#ccc" x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" transform="translate(1270 310) scale(.5)"/>
                                </a>';
                }
                break;
            } else {
                //$video = '<text class="video-error" x="' . ($videoSettings['x'] + 50) . '" y="' . ($videoSettings['y'] + 100) . '" width="' . $videoSettings['width'] . '" height="' . $videoSettings['height'] . '">' . __('Currently no video available', 'rrze-expo') . '</text>';
            }
        }


        ?>

        <div id="rrze-expo-podium" class="podium" style="background-image: url('<?php echo $backgroundImage;?>');">
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
                if ($templateNo == 1 || ($templateNo == 2 && $video == '')) {
                    echo '<foreignObject class="schedule" x="' . $scheduleSettings['x'] . '" y="' . ($scheduleSettings['y']) . '" width="' . $scheduleSettings['width'] . '" height="' . $scheduleSettings['height'] . '">
                        <body xmlns="http://www.w3.org/1999/xhtml">' . $schedule . '</body>
                    </foreignObject>';
                }
                echo $video;

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
        <?php } ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
