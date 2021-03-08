<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

$cpt = new CPT('');
CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $hasContent = (get_the_content() != '');
        $foyerId = get_the_ID();
        $meta = get_post_meta($foyerId);
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-foyer-background-image');
        ?>

        <div id="rrze-expo-foyer" class="foyer" style="background-image: url('<?php echo $backgroundImage;?>');">
            <?php
            //echo "x";
            $hallIDs = CPT::getHallOrder($foyerId);
            //echo "y";
            echo '<ul class="foyer-menu">';
            foreach ( $hallIDs as $hallID){
                echo '<li>';
                /*if (has_post_thumbnail($boothID)) {
                    echo '<img class="booth-logo" src="'.get_the_post_thumbnail_url($boothID, 'small').'">';
                }*/
                echo '<a class="booth-title" href="'.get_permalink($hallID).'">'.get_the_title($hallID).'</a>';
                echo '</li>';
            }
            echo "</ul>";
            ?>

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
