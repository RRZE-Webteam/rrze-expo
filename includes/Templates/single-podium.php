<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

CPT::expoHeader();
?>

<main>
    <?php
    CPT::expoNav();
    while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $podiumID = get_the_ID();
        $meta = get_post_meta($podiumID);
        $expoID = CPT::getMeta($meta, 'rrze-expo-podium-exposition');
        $menu = CPT::getMeta($meta, 'rrze-expo-podium-menu');
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-podium-background-image');
        if ($backgroundImage == '') {
            // If no background image is set -> display exposition background image
            $backgroundImage = get_post_meta($expoID, 'rrze-expo-exposition-background-image', true);
        } ?>

        <div id="rrze-expo-podium" class="podium" style="background-image: url('<?php echo $backgroundImage;?>');">
            <h1><?php the_title(); ?></h1>
            <div class="timetable">
                <h2><?php _e('Timetable', 'rrze-expo');?></h2>
                <?php
                    $timeslots = CPT::getMeta($meta, 'rrze-expo-podium-timeslots');
                    //var_dump($timeslots);
                    if ($timeslots == '') {
                        echo __('No Sessions available', 'rrze-expo');
                    } else {
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
                        var_dump($timeslotsGrouped);
                        foreach ($timeslotsGrouped as $day => $timeslotDay) {
                            echo '<h3>' . date('d.m.Y', $startdateTimestamp) . '</h3>';
                            echo '<table>';
                            foreach ($timeslotDay as $timeslotDetails) {
                                echo '<tr>';
                                echo '<td>' . $timeslotDetails['starttime'] . ' - ' . $timeslotDetails['endtime'] . '</td>';
                                echo '<td><span class="talk-title">' . $timeslotDetails['title'] . '</span>';
                                if (isset($timeslotDetails['booth'])) {
                                    echo '<br /><a href="' . get_permalink($timeslotDetails['booth']) . '">' . get_the_title($timeslotDetails['booth']) . '</a>';
                                }
                                echo '</tr>';
                            }
                            echo '</table>';
                        }
                    }
                ?>
            </div>
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
