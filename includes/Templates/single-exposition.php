<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
?>

<main class="rrze-expo" itemscope itemtype="https://schema.org/Event">
    <?php
    $isIOS = (strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad'));
    $isSafari = (strpos($_SERVER['HTTP_USER_AGENT'],'Safari') !== false && strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') === false);
    $macFix = ($isIOS === true || $isSafari === true);

    while ( have_posts() ) : the_post();
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
        ?>

        <div id="rrze-expo-exposition" class="exposition" style="background-image: url('<?php echo $backgroundImage;?>'); color: #000;">
            <a id="page-start"></a>
            <svg version="1.1" class="expo-exposition" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <?php
                for ($i = 1; $i <= 3; $i++) {
                    $flag = CPT::getMeta($meta, 'rrze-expo-exposition-flag' . $i);
                    if ($flag != '') {
                        $flagSettings = $constants['template_elements']['exposition']['flag' . $i];
                        echo ' <use xlink:href="#flag" class="flag-'. $i . '" transform="translate(' . $flagSettings['x'] . ' ' . $flagSettings['y'] . ') scale(.8)"/>';
                        echo '<foreignObject class="flag-content" width="' . ($flagSettings['width'] * .8) . '" height="' . ($flagSettings['height'] * .76) . '" x="' . ($flagSettings['x'] + 2) . '" y="' . ($flagSettings['y'] + 21) . '"><img src="' . $flag . '" style=" height: 100%; object-fit: contain; object-position: 50% 0;"/></foreignObject>';
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
                    </foreignObject>';
                if ($macFix) {
                    echo '<use xlink:href="#mouse-pointer" class="mouse-pointer" fill="#fff" transform="translate(2210 360) scale(.1)" stroke="#333" stroke-width="15" />';
                } else {
                    echo '<foreignObject x="'. ($panelSettings['x'] + $panelSettings['width'] - 70).'" y="'. ($panelSettings['y'] + 100) .'" width="60" height="60">
                        <body xmlns="http://www.w3.org/1999/xhtml">' . CPT::pulsatingDot() . '</body>
                    </foreignObject>';
                }
                    echo $foyerLinkClose;
                ?>

                <use class="bench-1" xlink:href="#bench" transform="translate(990 840) scale(.8)"/>
                <use class="bench-2" xlink:href="#bench" transform="translate(2950 860) scale(-.8 .8)" />

                <?php
                // Personas
                $personas = $constants['template_elements']['exposition']['persona'];
                $numPersonas = count($personas);
                for ($i=1; $i<=$numPersonas; $i++) {
                    $persona[$i] = CPT::getMeta($meta, 'rrze-expo-exposition-persona-'.$i);
                    $personaSettings = $personas[$i];
                        if ($persona[$i] != '') {
                        $file = WP_PLUGIN_DIR . '/rrze-expo/assets/img/template-assets/'.$persona[$i].'.svg';
                        if ($file) {
                        $svg = file_get_contents($file);
                        echo str_replace('<svg ', '<svg x="'.$personaSettings['x'].'" y="'.$personaSettings['y'].'" width="'.$personaSettings['width'].'" height="'.$personaSettings['height'].'" ', $svg);
                        }
                    }
                }
                ?>

            </svg>

        <?php if ($hasContent) { ?>
            <a href="#rrze-expo-exposition-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
            </a>
        <?php } ?>
        </div>

        <?php
        echo '<div id="rrze-expo-exposition-content" name="rrze-expo-exposition-content" class="" itemprop="description">';
        if ($hasContent) {
            echo '<div class="rrze-expo-exposition-text">';
            the_content();
            echo '</div>';
        }
        $orga = CPT::getMeta($meta,'rrze-expo-exposition-organisation');
        $orga2 = CPT::getMeta($meta,'rrze-expo-exposition-organisation2');
        $street = CPT::getMeta($meta,'rrze-expo-exposition-street');
        $postalCode = CPT::getMeta($meta,'rrze-expo-exposition-postalcode');
        $locality = CPT::getMeta($meta,'rrze-expo-exposition-locality');
        $orgaURL = get_home_url();
        $hasContact = strlen($orga.$orga2.$street.$postalCode.$locality) > 0 ? true : false;

        // Hidden Structured Data
        echo '<meta itemprop="name" content="'.get_the_title().'">';
        echo '<meta itemprop="image" content="'.get_the_post_thumbnail_url($expositionId, 'medium').'">';
        echo '<meta itemprop="eventAttendanceMode" content="https://schema.org/OnlineEventAttendanceMode">';
        echo '<meta itemprop="eventStatus" content="https://schema.org/EventScheduled">';
        echo '<span itemprop="location" itemscope itemtype="https://schema.org/VirtualLocation">'.'<span itemprop="url" content="'.get_permalink().'">'.'</span></span>';

        if ($hasContact) {
            echo '<div class="rrze-expo-exposition-organizer" itemprop="organizer" itemscope itemtype="https://schema.org/Organization">';
            echo ($orga != '' ? '<h2>' . __('Organizer', 'rrze-expo') . ': </h2><span itemprop="name">' . $orga . '</span>' : '');
            echo ($orga2 != '' ? '<br />'.$orga2 : '');
            echo ($street != '' ? '<br /><span itemprop="streetAddress">'.$street.'</span>' : '');
            echo ($postalCode != '' ? '<br /><span itemprop="postalCode">'.$postalCode.'</span>' : '');
            echo ($locality != '' ? ' <span itemprop="addressLocality">'.$locality.'</span>' : '');
            echo '<meta itemprop="url" content="' . $orgaURL . '">';
            echo '</div>';
        }

        $startDateRaw =  CPT::getMeta($meta,'rrze-expo-exposition-startdate');
        $endDateRaw =  CPT::getMeta($meta,'rrze-expo-exposition-enddate');
        if (!empty($startDateRaw)) {
            $startDate = '<meta itemprop="startDate" content="'.date('Y-m-d', $startDateRaw).'" />';
        }
        if (!empty($endDateRaw)) {
            $endDate = '<meta itemprop="endDate" content="'.date('Y-m-d', $endDateRaw).'" />';
        } else {
            $endDate = '<meta itemprop="endDate" content="'.date('Y-m-d', $startDateRaw).'" />';
        }
        echo $startDate . $endDate;

        echo '</div>';

    endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
