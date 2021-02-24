<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;
use function RRZE\Expo\Config\getShortcodeSettings;



/**
 * Shortcode
 */
class Shortcode
{

    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    /**
     * Settings-Objekt
     * @var object
     */
    private $settings = '';

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile, $settings)
    {
        $this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        //add_action( 'admin_enqueue_scripts', [$this, 'enqueueGutenberg'] );
        //add_action( 'init',  [$this, 'initGutenberg'] );
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode('rrze_expo_shortcode', [$this, 'shortcodeOutput'], 10, 2);
    }

    /**
     * Enqueue der Skripte.
     */
    public function enqueueScripts()
    {
        wp_register_style('rrze-expo-shortcode', plugins_url('assets/css/shortcode.css', plugin_basename($this->pluginFile)));
        wp_register_script('rrze-expo-shortcode', plugins_url('assets/js/shortcode.js', plugin_basename($this->pluginFile)));
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {
        // merge given attributes with default ones
        $atts_default = array();
        foreach( $this->settings as $k => $v ){
            if ( $k != 'block' ){
                $atts_default[$k] = $v['default'];
            }
        }
        $atts = shortcode_atts( $atts_default, $atts );

        $content = '';

        $display = $shortcode_atts['display'] == 'true' ? true : false;

        $output = '';

        if ($display) {
            $output = '<span class="expo-shortcode expo-shortcode-display" data-display="true">[shortcode display]</span>';
        } else {
            $output = '<span class="expo-shortcode" data-display="false">[shortcode hidden]</span>';
        }

        wp_enqueue_style('rrze-expo-shortcode');
        wp_enqueue_script('rrze-expo-shortcode');

        return $output;
    }

    public function isGutenberg(){
        if ( ! function_exists( 'register_block_type' ) ) {
            return false;
        }

        // check if RRZE-Settings if classic editor is enabled
        $rrze_settings = (array) get_option( 'rrze_settings' );
        if ( isset( $rrze_settings['writing'] ) ) {
            $rrze_settings = (array) $rrze_settings['writing'];
            if ( isset( $rrze_settings['enable_classic_editor'] ) && $rrze_settings['enable_classic_editor'] ) {
                return false;
            }
        }

        return true;
    }

    public function fillGutenbergOptions() {
        // Example:
        // fill select id ( = glossary )
        $glossaries = get_posts( array(
            'posts_per_page'  => -1,
            'post_type' => 'glossary',
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        $this->settings['id']['field_type'] = 'multi_select';
        $this->settings['id']['default'] = array(0);
        $this->settings['id']['type'] = 'array';
        $this->settings['id']['items'] = array( 'type' => 'number' );
        $this->settings['id']['values'][] = ['id' => 0, 'val' => __( '-- all --', 'rrze-expo' )];
        foreach ( $glossaries as $glossary){
            $this->settings['id']['values'][] = [
                'id' => $glossary->ID,
                'val' => str_replace( "'", "", str_replace( '"', "", $glossary->post_title ) )
            ];
        }

        return $this->settings;
    }


    public function initGutenberg() {
        if (! $this->isGutenberg()){
            return;
        }

        // get prefills for dropdowns
        // $this->settings = $this->fillGutenbergOptions();

        // register js-script to inject php config to call gutenberg lib
        $editor_script = $this->settings['block']['blockname'] . '-block';
        $js = '../assets/js/' . $editor_script . '.js';

        wp_register_script(
            $editor_script,
            plugins_url( $js, __FILE__ ),
            array(
                'RRZE-Gutenberg',
            ),
            NULL
        );
        wp_localize_script( $editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings );

        // register block
        register_block_type( $this->settings['block']['blocktype'], array(
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcodeOutput'],
            'attributes' => $this->settings
            )
        );
    }

    public function enqueueGutenberg(){
        if (! $this->isGutenberg()){
            return;
        }

        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url( '../assets/js/gutenberg.js', __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            NULL
        );
    }

}
