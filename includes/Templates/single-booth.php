<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;
use function RRZE\Expo\Config\getConstants;

CPT::expoHeader();
//get_header();
?>

<main>
    <?php while ( have_posts() ) : the_post();
        $boothId = get_the_ID();
        $meta = get_post_meta($boothId);
        $constants = getConstants();
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-booth-background-image');
        if ($backgroundImage == '') {
            // If no background image is set -> take hall background image
            $hall = CPT::getMeta($meta, 'rrze-expo-booth-hall');
            $backgroundImage = get_post_meta($hall, 'rrze-expo-hall-background-image', true);
            if ($backgroundImage == '') {
                // If no booth and no hall background image are set -> take foyer background image
                $foyer = get_post_meta($hall, 'rrze-expo-hall-foyer', true);
                $backgroundImage = get_post_meta($foyer, 'rrze-expo-foyer-background-image', true);
            }
        }
        $soMeDefaults = [
            'username' => '',
            'order' => 0,];
        ?>

        <h1 class="sr-only screen-reader-text"><?php echo the_title(); ?></h1>
        <div id="rrze-expo-booth" class="booth" style="background-image: url('<?php echo $backgroundImage;?>');">
            <svg class="expo-booth" role="img" viewBox="0 0 1920 1080" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <use xlink:href="#backwall" x="60" y="0" />
                <use xlink:href="#video" x="200" y="60" />
                <use xlink:href="#video" x="700" y="60" />
                <use xlink:href="#flyer_stand" x="0" y="400" />
                <use xlink:href="#flyer" x="10" y="410" />
                <use xlink:href="#flyer" x="10" y="700" />
                <use xlink:href="#table" x="600" y="550" />
                <use xlink:href="#some_panel" x="1800" y="500" />
                <?php
                    $socialMedia = $constants['social-media'];
                    $i = 0;
                    foreach ($socialMedia as $soMeName => $soMeUrl) {
                        $soMeMeta = wp_parse_args(CPT::getMeta($meta, 'rrze-expo-booth-' . $soMeName), $soMeDefaults);
                        if ($soMeMeta['username'] != '') {
                            $translateY = (isset($soMeMeta['order']) ? $soMeMeta['order'] - 1 : $i);
                            $translateY = 520 + ((int)$translateY * 80);
                            echo "<a href='$soMeUrl'>
                                <use xlink:href='#$soMeName' x='1820' y='$translateY' />
                            </a>";
                        }
                    }
                    if (has_post_thumbnail()){
                        echo '<use xlink:href="#booth_logo" x="1344" y="60" />
                              <use xlink:href="#booth_logo" x="700" y="600" />';

                    } ?>
            </svg>
            <?php
            $boothIDsOrdered = CPT::getBoothOrder($boothId);
            $orderNo = array_search($boothId, $boothIDsOrdered);
            ?>
            <ul class="booth-nav">
                <?php if ($orderNo > 0) {
                    $prevBoothID = $boothIDsOrdered[$orderNo-1]; ?>
                    <li class="prev-booth">
                        <a href="<?php echo get_permalink($prevBoothID);?>" class="">
                            <svg height="40" width="40" aria-hidden="true"><use xlink:href="#chevron-left"></use></svg>
                            <span class="nav-prev-text"><?php echo __('Previous Booth','rrze-expo') . ':<br />' . get_the_title($prevBoothID);?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if (($orderNo + 1) < count($boothIDsOrdered)) {
                    $nextBoothID = $boothIDsOrdered[($orderNo + 1)]; ?>
                    <li class="next-booth">
                        <a href="<?php echo get_permalink($nextBoothID);?>" class="">
                            <svg height="40" width="40" aria-hidden="true"><use xlink:href="#chevron-right"></use></svg>
                            <span class="nav-next-text"><?php echo __('Next Booth','rrze-expo') . ':<br />' . get_the_title($nextBoothID);?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <a href="#rrze-expo-booth-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>"><svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'><span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span></a>
        </div>

        <div id="rrze-expo-booth-content" name="rrze-expo-booth-content" class="">

            <?php the_content(); ?>

        </div>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
