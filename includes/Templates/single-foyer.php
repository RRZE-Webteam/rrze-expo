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
        $title = get_the_title();
        ?>

        <h1 class="sr-only screen-reader-text"><?php echo $title; ?></h1>
        <div id="rrze-expo-foyer" class="foyer" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg version="1.1" class="expo-foyer" role="img" x="0px" y="0px" viewBox="0 0 4096 1080" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use class="floor" xlink:href="#floor" />
                <!--<use class="backwall" xlink:href="#wall" />-->
                <use xlink:href="#table" />
                <use xlink:href="#tablet" />
                <?php
                for ($i = 1; $i <= 6; $i++) {
                    $boardContent = CPT::getMeta($meta, 'rrze-expo-foyer-board-'.$i);
                    if (!empty($boardContent)) {
                        echo '<use xlink:href="#panel-'.$i.'" />';
                        //var_dump($boardContent[0]);
                    }
                }
                ?>
                <use xlink:href="#panel-mitte" />
                <!--<use xlink:href="#icons" />-->
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
