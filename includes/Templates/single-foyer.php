<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

$cpt = new CPT('');
CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $foyerId = get_the_ID();
        $meta = get_post_meta($foyerId);
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-foyer-background-image');
        $title = get_the_title();
        $constants = getConstants();
        ?>

        <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
        <div id="rrze-expo-foyer" class="foyer" style="background-image: url('<?php echo $backgroundImage;?>');">
            <style>
                #rrze-expo-foyer .main-panel *, #rrze-expo-foyer .main-panel h1, #rrze-expo-foyer .main-panel h2, #rrze-expo-foyer .main-panel h3 {color: <?php echo CPT::getMeta($meta, 'rrze-expo-foyer-panel-font-color'); ?>;}
                #rrze-expo-foyer .main-panel a {color: <?php echo CPT::getMeta($meta, 'rrze-expo-foyer-panel-link-color'); ?>;}
                #rrze-expo-foyer .main-panel  {background-color: <?php echo CPT::getMeta($meta, 'rrze-expo-foyer-panel-background-color'); ?>;}
            </style>
            <a id="page-start"></a>
            <svg version="1.1" class="expo-foyer" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <!--<use class="backwall" xlink:href="#wall" />-->
                <?php

                // Direction Boards
                for ($i = 1; $i <= 6; $i++) {
                    $linkID = '';
                    $linkURL = '';
                    $boardContent = CPT::getMeta($meta, 'rrze-expo-foyer-board-'.$i);
                    if ($boardContent == '' || empty($boardContent))
                        continue;
                    if (isset($boardContent[0]['rrze-expo-foyer-board-'.$i.'-content'])) {
                        $boardSettings = $constants['template_elements']['foyer']['board'.$i];
                        $fontColor = (isset($boardContent[0]['rrze-expo-foyer-board-'.$i.'-font-color']) ? $boardContent[0]['rrze-expo-foyer-board-'.$i.'-font-color'] : '#333');
                        $fontSize = $boardContent[0]['rrze-expo-foyer-board-'.$i.'-font-size'];
                        if ($boardContent[0]['rrze-expo-foyer-board-'.$i.'-content'] == 'custom') {
                            $linkText = isset($boardContent[0]['rrze-expo-foyer-board-'.$i.'-text']) ? $boardContent[0]['rrze-expo-foyer-board-'.$i.'-text'] : '';
                            $linkURL = isset($boardContent[0]['rrze-expo-foyer-board-'.$i.'-link']) ? $boardContent[0]['rrze-expo-foyer-board-'.$i.'-link'] : '';
                        } else {
                            $linkID = $boardContent[0]['rrze-expo-foyer-board-'.$i.'-content'];
                            $linkText = get_the_title($linkID);
                            /*if (strpos($linkText, '<br>') != false) {
                                $titleParts = explode('<br>', $linkText);
                                $linkText = '<tspan>' . implode('</tspan><tspan>', $titleParts) . '</tspan>';
                            }*/
                            $linkURL = $linkID != '' ? get_permalink($linkID) : '';
                        }
                        echo '<g class="foyer-board-'.$i.'">';
                        if ($linkURL != '') {
                            echo '<a href="'.$linkURL.'">';
                        }
                        if ($i < 4) {
                            echo '<use xlink:href="#panel-left" x="'.$boardSettings['x'].'" y="'.$boardSettings['y'].'" />';
                            $textX = $boardSettings['x'] + 120;
                        } else {
                            echo '<use xlink:href="#panel-right" x="'.$boardSettings['x'].'" y="'.$boardSettings['y'].'" />';
                            $textX = $boardSettings['x'] + 40;
                        }
                        $textY = $boardSettings['y'] + 80 + ($fontSize/2.5);
                        if (array_key_exists('rrze-expo-foyer-board-'.$i.'-color', $boardContent[0])) {
                            echo '<rect class="foyer-board" x="' . $boardSettings['x'] . '" y="' . $boardSettings['y'] . '" width="' . $boardSettings['width'] . '" height="' . $boardSettings['height'] . '" rx="4" ry="4" style="fill:' . $boardContent[0]['rrze-expo-foyer-board-' . $i . '-color'] . '"/>';
                        }
                        echo '<use xlink:href="#arrow" class="board-arrow-'.$i.'" transform="'.$boardSettings['arrow-position'].'" fill="'.$fontColor.'" />';
                        if (strpos($linkText, '<br>') !== false) {
                            $linkTextParts = explode('<br>', $linkText);
                            $linkText = '<tspan>' . implode('</tspan><tspan x="'.$textX.'" dy="'.($fontSize * 1.12).'">', $linkTextParts) . '</tspan>';
                            $textY -= ($fontSize * 0.5);
                        }
                        echo '<text x="' . $textX . '" y="' . $textY . '" font-size="' . $fontSize . '" fill="' . $fontColor . '" aria-hidden="true">' . $linkText . '</text>';
                        if ($linkURL != '') {
                            echo '</a>';
                        }
                        echo '</g>';
                    }
                }
                $centerSettings = $constants['template_elements']['foyer']['board-center'];
                $centerText = CPT::getMeta($meta, 'rrze-expo-foyer-panel-content');
                echo '<use xlink:href="#panel-mitte" x="'.$centerSettings['x'].'" y="'.$centerSettings['y'].'" />
                    <foreignObject class="main-panel" x="'. ($centerSettings['x']).'" y="'. ($centerSettings['y']) .'" width="'. ($centerSettings['width']).'" height="'. ($centerSettings['height']).'">
                        <body xmlns="http://www.w3.org/1999/xhtml"><div class="panel-content">' . do_shortcode(wpautop($centerText)) . '</div></body>
                    </foreignObject>';

                // Personas
                $personas = $constants['template_elements']['foyer']['persona'];
                $numPersonas = count($personas);
                $personaStyles = '';
                for ($i=1; $i<=$numPersonas; $i++) {
                    $personaRaw = CPT::getMeta($meta, 'rrze-expo-foyer-persona-'.$i);
                    // TODO: Abwärtskompatibilität – beim nächsten Update entfernen!
                    if (!is_array($personaRaw)) {
                        $persona[$i]['persona'] = CPT::getMeta($meta, 'rrze-expo-foyer-persona-' . $i);
                    } else {
                        $persona[$i] = $personaRaw;
                    }
                    $personaSettings = $personas[$i];
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

                // Table
                $tableSettings = $constants['template_elements']['foyer']['table'];
                echo '<use xlink:href="#table" x="'.$tableSettings['x'].'" y="'.$tableSettings['y'].'" />';

                // Table Screen
                $videoTable = CPT::getMeta($meta, 'rrze-expo-foyer-video-table');
                if ($videoTable != '') {
                    $videoSettings = $constants['template_elements']['foyer']['tablet'];
                    echo '<a href="' . $videoTable . '"><use class="video-tablet" xlink:href="#tablet" x="'.$videoSettings['x'].'" y="'.$videoSettings['y'].'" /></a>';
                }

                // Table Icon
                switch (CPT::getMeta($meta, 'rrze-expo-foyer-table-icon')) {
                    case 'info':
                        $iconSettings = $constants['template_elements']['foyer']['info-icon'];
                        echo '<path transform="translate('.$iconSettings['x'].' '.$iconSettings['y'].') scale('.$iconSettings['width'].' '.$iconSettings['height'].')" fill="#fff" stroke="#bbb" stroke-width="5" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"/>';
                        break;
                    case 'foyer-logo':
                        if (has_post_thumbnail()){
                            $iconSettings = $constants['template_elements']['foyer']['logo'];
                            //echo '<image xlink:href="'.get_the_post_thumbnail_url($foyerId, 'expo-logo').'" width="'.$iconSettings['width'].'" height="'.$iconSettings['height'].'"  x="'.$iconSettings['x'].'" y="'.$iconSettings['y'].'" />';
                            echo '<svg width="'.$iconSettings['width'].'" height="'.$iconSettings['height'].'"  x="'.$iconSettings['x'].'" y="'.$iconSettings['y'].'">
                            <image xlink:href="'.get_the_post_thumbnail_url($foyerId, 'expo-logo').'" />
                            </svg>';
                        }
                        break;
                    case 'expo-logo':
                        $iconSettings = $constants['template_elements']['foyer']['logo'];
                        $expoID = CPT::getMeta($meta, 'rrze-expo-foyer-exposition');
                        echo '<svg class="table-logo" width="'.$iconSettings['width'].'" height="'.$iconSettings['height'].'"  x="'.$iconSettings['x'].'" y="'.$iconSettings['y'].'">
                            <image xlink:href="'.get_the_post_thumbnail_url($expoID, 'expo-logo').'" preserveAspectRatio="xMinYMin" />
                            </svg>';
                        break;
                    case 'none':
                    default:
                        break;
                }

                // Social Media
                $socialMedia = CPT::getMeta($meta, 'rrze-expo-foyer-social-media');
                if ($socialMedia == '')
                    $socialMedia = [];
                if ($socialMedia != []) {
                    $socialMediaData = $constants['social-media'];
                    $socialMediaSettings = $constants['template_elements']['foyer']['social-media'];
                    echo '<g><use xlink:href="#some_panel" x="'.$socialMediaSettings['x'].'" y="'.$socialMediaSettings['y'].'"/>';
                    $iconsX = $socialMediaSettings['x'] + 20;
                    $iconsY = $socialMediaSettings['y'] + 30;
                    foreach ($socialMedia as $i => $media) {
                        if (!isset($media['medianame']) || !isset($media['username']))
                            continue;
                        switch ($socialMediaSettings['direction']) {
                            case 'landscape':
                                $translateX = $iconsX + $i * ($socialMediaSettings['width'] + 10);
                                $translateY = $iconsY;
                                break;
                            case 'portrait':
                            default:
                                $translateX = $iconsX;
                                $translateY = $iconsY + $i * ($socialMediaSettings['height'] + 10);
                        }
                        $class = 'icon-'.$media['medianame'];
                        if ($socialMediaSettings['color'] == true) {
                            $class .= '-color';
                        }
                        echo '<a href="' . trailingslashit($socialMediaData[$media['medianame']]) . $media['username'] . '" title="'.ucfirst($media['medianame']).': '.$media['username'].'">
                            <use xlink:href="#' . $media['medianame'] . '" width="'.$socialMediaSettings['width'].'" height="'.$socialMediaSettings['height'].'" x="'.$translateX.'" y="' . $translateY . '" class="'.$class.'" fill="#fff" stroke="#000" stroke-width="1"/>
                            </a>';
                    }
                    echo '</g>';
                }

                // Seats
                for ($i=1; $i<=3; $i++) {
                    $seat[$i] = CPT::getMeta($meta, 'rrze-expo-foyer-seat-'.$i);
                    $seatSettings = $constants['template_elements']['foyer']['seat'][$i];
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
            <a href="#rrze-expo-foyer-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
            </a>
        <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
            <div id="rrze-expo-foyer-content" name="rrze-expo-foyer-content" class="">
                <?php the_content(); ?>
            </div>
        <?php } ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
