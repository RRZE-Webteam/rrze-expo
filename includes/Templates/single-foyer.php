<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\CPT\CPT;

$cpt = new CPT('');
CPT::expoHeader();
?>

<main class="rrze-expo">
    <?php while ( have_posts() ) : the_post();
        $foyerId = get_the_ID();
        $meta = get_post_meta($foyerId);
        $backgroundImage = CPT::getMeta($meta, 'rrze-expo-foyer-background-image');
        ?>

        <div id="rrze-expo-foyer" class="foyer" style="background-image: url('<?php echo $backgroundImage;?>');">

            <a href="#rrze-expo-foyer-content" id="scrolldown"><?php _e('Read more','rrze-expo');?></a>
        </div>

        <div id="rrze-expo-foyer-content" name="rrze-expo-foyer-content" class="">

            <?php the_content(); ?>

        </div>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
