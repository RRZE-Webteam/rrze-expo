<?php


namespace RRZE\Expo\CPT;

defined('ABSPATH') || exit;

class CPT
{
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded()
    {
        $booth = new Booth($this->pluginFile);
        $booth->onLoaded();

        $hall = new Hall($this->pluginFile);
        $hall->onLoaded();

        add_filter('cmb2_render_social-media', [$this, 'cmb2_render_social_media_field_callback'], 10, 5);

    }

    public function activation()
    {
        $booth = new Booth($this->pluginFile);
        $booth->booth_post_type();

        $hall = new Hall($this->pluginFile);
        $hall->booth_post_type();
    }

    public function cmb2_render_social_media_field_callback($field, $value, $object_id, $object_type, $field_type) {
        // make sure we specify each part of the value we need.
        $value = wp_parse_args( $value, array(
            'show' => '',
            'username' => '',
            'order' => '',
        ) );
        ?>
        <div><label for="<?php echo $field_type->_id( '_show' ); ?>"><?php _e('Show Icon', 'rrze-expo');?></label>
            <?php echo $field_type->checkbox( array(
                'name'  => $field_type->_name( '[show]' ),
                'id'    => $field_type->_id( '_show' ),
                'value' => $value['show'],
                //'desc'  => '',
            ) ); ?>
            <label for="<?php echo $field_type->_id( '_username' ); ?>'" style="margin-left: 20px;"><?php _e('User Name', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'text',
                'name'  => $field_type->_name( '[username]' ),
                'id'    => $field_type->_id( '_username' ),
                'value' => $value['username'],
                'class' => 'medium-text',
                //'desc'  => '',
            ) ); ?>
            <label for="<?php echo $field_type->_id( '_order' ); ?>'" style="margin-left: 20px;"><?php _e('Order', 'rrze-expo');?></label>
            <?php echo $field_type->input( array(
                'type'  => 'number',
                'name'  => $field_type->_name( '[order]' ),
                'id'    => $field_type->_id( '_order' ),
                'value' => $value['order'],
                'class' => 'small-text',
                //'desc'  => '',
            ) ); ?>
        </div>
        <?php
    }

    public static function getPosts(string $postType): array
    {
        $posts = get_posts([
            'post_type' => $postType,
            'post_statue' => 'publish',
            'nopaging' => true,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        if (empty($posts)) {
            return [];
        }
        $result = [];
        foreach ($posts as $post) {
            $result[$post->ID] = $post->post_title;
        }
        return $result;
    }

}
