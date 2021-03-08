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
        ?>

        <div id="rrze-expo-exposition" class="exposition" style="background-image: url('<?php echo $backgroundImage;?>');">
        <?php echo '<a href="'. get_permalink($foyerID[0]).'">'.__('Enter the foyer', 'rrze-expo').'</a>'; ?>

        <?php if ($hasContent) { ?>
            <a href="#rrze-expo-exposition-content" id="scrolldown" title="<?php _e('Read more','rrze-expo');?>">
                <svg height='50' width='80' class="scroll-down-icon"><use xlink:href='#chevron-down'/></svg>
                <span class="sr-only screen-reader-text"><?php _e('Read more','rrze-expo');?></span>
            </a>
        <?php } ?>
        </div>

        <?php if ($hasContent) { ?>
            <div id="rrze-expo-exposition-content" name="rrze-expo-exposition-content" class="">
                <?php the_content(); ?>
            </div>
        <?php } ?>

    <?php endwhile; ?>

</main>

<?php

wp_enqueue_style('rrze-expo');

//CPT::expoFooter();
get_footer();
