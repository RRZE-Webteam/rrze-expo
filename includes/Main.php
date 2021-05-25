<?php

namespace RRZE\Expo;

defined('ABSPATH') || exit;

use RRZE\Expo\Settings;
use RRZE\Expo\Shortcode;
use RRZE\Expo\CPT\CPT;


/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        // Shortcode-Klasse wird instanziiert.
        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Posttypes
        $cpt = new CPT($this->pluginFile);
        $cpt->onLoaded();

        // Logo Image Size
        add_image_size('expo-logo', 512, 512, false);
    }

    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-expo', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
    }
}
