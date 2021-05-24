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
            <svg version="1.1" class="expo-foyer" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <!--<use class="backwall" xlink:href="#wall" />-->
                <?php $tableSettings = $constants['template_elements']['foyer']['table'];
                echo '<use xlink:href="#table" x="'.$tableSettings['x'].'" y="'.$tableSettings['y'].'" />';
                ?>

                <use xlink:href="#tablet" />
                <?php
                for ($i = 1; $i <= 6; $i++) {
                    $boardContent = CPT::getMeta($meta, 'rrze-expo-foyer-board-'.$i);
                    if ($boardContent == '')
                        continue;
                    if (!empty($boardContent)) {
                        $boardSettings = $constants['template_elements']['foyer']['board'.$i];
                        echo '<g class="foyer-board-'.$i.'">';
                        echo '<use xlink:href="#panel" x="'.$boardSettings['x'].'" y="'.$boardSettings['y'].'" />';
                        echo '<use xlink:href="#arrow" class="board-arrow-'.$i.'" transform="'.$boardSettings['arrow-position'].'" fill="#444" stroke="#000" stroke-width="5" />';
                        if ($boardContent[0]['rrze-expo-foyer-board-'.$i.'-content'] == 'custom') {
                            $linkText = 'Custom';
                        } else {
                            $linkID = $boardContent[0]['rrze-expo-foyer-board-'.$i.'-content'];
                            $linkText = get_the_title($linkID);
                            if (strpos($linkText, '<br>') != false) {
                                $titleParts = explode('<br>', $linkText);
                                //$linkText = '<tspan>' . implode('</tspan><tspan x="'.$titleSettings['x'].'" dy="'.($fontSize*1.12).'">', $titleParts) . '</tspan>';
                                $linkText = '<tspan>' . implode('</tspan><tspan>', $titleParts) . '</tspan>';
                            }
                            //echo '<text x="'.$titleSettings['x'].'" y="'.$titleSettings['y'].'" font-size="'.$fontSize.'" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.$linkText.'</text>';

                            $linkURL = get_permalink($linkID);
                        }
                        if ($i < 4) {
                            $textX = $boardSettings['x'] + 140;
                        } else {
                            $textX = $boardSettings['x'] + 40;
                        }
                        echo '<a href="'.$linkURL.'"><text x="'.$textX.'" y="'.($boardSettings['y'] + 150).'" font-size="40" fill="'.CPT::getMeta($meta, 'rrze-expo-booth-font-color').'" aria-hidden="true">'.$linkText.'</text></a>';
                        echo '</g>';
                    }
                }
                $centerSettings = $constants['template_elements']['foyer']['board-center'];
                echo '<use xlink:href="#panel-mitte" x="'.$centerSettings['x'].'" y="'.$centerSettings['y'].'" />';
                ?><!--<use xlink:href="#icons" />-->
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
