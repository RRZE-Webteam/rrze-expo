<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
?>

<main class="rrze-expo" itemscope itemtype="https://schema.org/Event">
    <?php while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $expositionId = get_the_ID();
        $meta = get_post_meta($expositionId);
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-exposition-background-image');
        $foyerID = get_posts([
            'post_type'     => 'foyer',
            'status'        => 'publish',
            'meta_key'      => 'rrze-expo-foyer-exposition',
            'meta_value'    => $expositionId,
            'posts_per_page'   => 1,
            'fields'        => 'ids'
        ]);
        $constants = getConstants();
        // Hidden Structured Data
        echo '<meta itemprop="name" content="'.get_the_title().'">';
        echo '<meta itemprop="image" content="'.get_the_post_thumbnail_url($expositionId, 'medium').'">';
        echo '<meta itemprop="eventAttendanceMode" content="https://schema.org/OnlineEventAttendanceMode">';
        echo '<meta itemprop="eventStatus" content="https://schema.org/EventScheduled">';
        echo '<span itemprop="location" itemscope itemtype="https://schema.org/VirtualLocation">'.'<span itemprop="url" content="'.get_permalink().'">'.'</span>';

        $orga = '<span itemprop="name">' . CPT::getMeta($meta,'rrze-expo-exposition-organisation') . '</span>';
        $orga2 = CPT::getMeta($meta,'rrze-expo-exposition-organisation2');
        $street = '<meta itemprop="streetAddress" content="'.CPT::getMeta($meta,'rrze-expo-exposition-street').'">';
        $postalCode = '<meta itemprop="postalCode" content="'.CPT::getMeta($meta,'rrze-expo-exposition-postalcode').'">';
        $locality = '<meta itemprop="addressLocality" content="'.CPT::getMeta($meta,'rrze-expo-exposition-locality').'">';
        $orgaURL = '<meta itemprop="url" content="' . get_home_url() . '">';

        echo '<div itemprop="organizer" itemscope itemtype="https://schema.org/Organization">'
            . __('Organizer', 'rrze-expo') . ': ' . $orga
            . $street.$postalCode.$locality.$orgaURL
            . '</div>';

        $startDateRaw =  CPT::getMeta($meta,'rrze-expo-exposition-startdate');
        $endDateRaw =  CPT::getMeta($meta,'rrze-expo-exposition-enddate');
        $startDate = '';
        $endDate = '';
        if (!empty($startDateRaw)) {
            $startDate = '<span class="expo-start-date" itemprop="startDate" content="'.date('Y-m-d', $startDateRaw).'">'.date_i18n(get_option('date_format'), $startDateRaw).'</span>';
        }
        if (!empty($endDateRaw)) {
            $endDate = '<span class="expo-end-date" itemprop="endDate" content="'.date('Y-m-d', $endDateRaw).'">'.date_i18n(get_option('date_format'), $endDateRaw).'</span>';
        }
        if (!empty($startDateRaw)) {
            $date = $startDate;
            if (!empty($endDateRaw) && ($startDateRaw != $endDateRaw)) {
                $date .= ' - ' . $endDate;
            } else {
                $date .= '<meta itemprop="endDate" content="'.date('Y-m-d', $startDateRaw).'">';
            }
        } elseif (!empty($endDateRaw)) {
            $date = $endDate;
        } else {
            $date = '';
        }
        echo $date;
        ?>

        <div id="rrze-expo-exposition" class="exposition" style="background-image: url('<?php echo $backgroundImage;?>'); color: #000;">
            <svg version="1.1" class="expo-exposition" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php
                for ($i = 1; $i <= 3; $i++) {
                    $flag = CPT::getMeta($meta, 'rrze-expo-exposition-flag' . $i);
                    if ($flag != '') {
                        $flagSettings = $constants['template_elements']['exposition']['flag' . $i];
                        echo ' <use xlink:href="#flag" class="flag-'. $i . '" transform="translate(' . $flagSettings['x'] . ' ' . $flagSettings['y'] . ') scale(.8)"/>';
                        echo '<foreignObject class="flag-content" width="' . ($flagSettings['width'] * .8) . '" height="' . ($flagSettings['height'] * .76) . '" x="' . ($flagSettings['x'] + 2) . '" y="' . ($flagSettings['y'] + 21) . '"><a href="' . $flag . '" style="display: block; height: 100%; text-align: center;" class="lightbox"><img src="' . $flag . '" style=" height: 100%; object-fit: contain; object-position: 50% 0;"/></a></foreignObject>';
                    }
                }
                $panelSettings = $constants['template_elements']['exposition']['panel'];
                $panelText = CPT::getMeta($meta, 'rrze-expo-exposition-panel-content');
                if (!empty($foyerID)) {
                    $foyerLinkOpen = '<a href="' . get_permalink($foyerID[0]) . '" title="' . __('Enter the foyer', 'rrze-expo') . '">';
                    $foyerLinkClose = '</a>';
                } else {
                    $foyerLinkOpen = '';
                    $foyerLinkClose = '';
                }

                echo '<use class="panel" xlink:href="#panel" transform="translate(1300 240) scale(.95)"/>'
                    .$foyerLinkOpen
                    .'<foreignObject class="main-panel" x="'. ($panelSettings['x'] + 82).'" y="'. ($panelSettings['y'] + 94) .'" width="'. ($panelSettings['width'] * .92).'" height="'. ($panelSettings['height'] * .92).'">
                        <body xmlns="http://www.w3.org/1999/xhtml"><div class="panel-content">' . do_shortcode($panelText) . '</div></body>
                    </foreignObject>'
                    . $foyerLinkClose;
                ?>

                <use class="bench-1" xlink:href="#bench" transform="translate(990 840) scale(.8)"/>
                <use class="bench-2" xlink:href="#bench" transform="translate(2950 860) scale(-.8 .8)" />
            </svg>

        <?php if ($hasContent) { ?>
            <a href="#rrze-expo-exposition-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
            </a>
        <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
            <div id="rrze-expo-exposition-content" name="rrze-expo-exposition-content" class="" itemprop="description">
                <?php the_content(); ?>
            </div>
        <?php } ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
