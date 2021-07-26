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
        $hallId = get_the_ID();
        $title = str_replace('<br>', ' ', get_the_title());
        $meta = get_post_meta($hallId);
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-hall-background-image');
        if ($backgroundImage == '') {
            // If no background image is set -> display foyer background image
            $foyer = CPT::getMeta($meta, 'rrze-expo-hall-foyer');
            $backgroundImage = get_post_meta($foyer, 'rrze-expo-foyer-background-image', true);
        } ?>

        <div id="rrze-expo-hall" class="hall" style="background-image: url('<?php echo $backgroundImage;?>');">
            <a id="page-start"></a>
            <?php
            $titleStyle = '';
            $titleColor = CPT::getMeta($meta, 'rrze-expo-hall-font-color');
            if ($titleColor != '') {
                $titleStyle = 'color: ' . $titleColor . ';';
            }
            if ($backgroundImage != '' && $titleColor != '') {
                $hexRGB = str_replace('#', '', $titleColor);
                if(hexdec(substr($hexRGB,0,2))+hexdec(substr($hexRGB,2,2))+hexdec(substr($hexRGB,4,2))> 381){
                    $titleStyle .= ' text-shadow: 0 0 5px #000;'; //bright color -> dark shadow
                }else{
                    $titleStyle .= ' text-shadow: 0 0 5px #fff;'; //dark color -> light shadow
                }
            }
            echo '<h1 class="hall-title" style="' . $titleStyle . '">' . $title . '</h1>';
            $boothIDs = CPT::getBoothOrder($hallId);
            echo '<ul class="hall-menu">';
            foreach ( $boothIDs as $boothID){
                $link = get_permalink($boothID);
                echo '<li>';
                if (has_post_thumbnail($boothID)) {
                    echo '<a class="booth-title" href="'.$link.'#rrze-expo-booth"><img class="booth-logo" src="'.get_the_post_thumbnail_url($boothID, 'small').'"></a>';
                }
                echo '<a class="booth-title" href="'.$link.'#rrze-expo-booth">'.get_the_title($boothID).'</a>';
                echo '</li>';
            }
            echo "</ul>";
            ?>
            <?php if ($hasContent) { ?>
                <a href="#rrze-expo-hall-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                    <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                    <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
                </a>
            <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
            <div id="rrze-expo-hall-content" name="rrze-expo-hall-content" class="">
                <?php the_content(); ?>
            </div>
        <?php } ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
