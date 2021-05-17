<?php

namespace RRZE\Expo\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName()
{
    return 'rrze_expo';
}

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings()
{
    return [
        'page_title'    => __('RRZE Expo', 'rrze-expo'),
        'menu_title'    => __('RRZE Expo', 'rrze-expo'),
        'capability'    => 'manage_options',
        'menu_slug'     => 'rrze-expo',
        'title'         => __('RRZE Expo Settings', 'rrze-expo'),
    ];
}

/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
function getHelpTab()
{
    return [
        [
            'id'        => 'rrze-expo-help',
            'content'   => [
                '<p>' . __('Here comes the Context Help content.', 'rrze-expo') . '</p>'
            ],
            'title'     => __('Overview', 'rrze-expo'),
            'sidebar'   => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-expo'), __('RRZE Webteam on Github', 'rrze-expo'))
        ]
    ];
}

/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections()
{
    return [
        [
            'id'    => 'basic',
            'title' => __('Basic Settings', 'rrze-expo')
        ],
        [
            'id'    => 'advanced',
            'title' => __('Advanced Settings', 'rrze-expo')
        ]
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields()
{
    return [
        'basic' => [
            [
                'name'              => 'text_input',
                'label'             => __('Text Input', 'rrze-expo'),
                'desc'              => __('Text input description.', 'rrze-expo'),
                'placeholder'       => __('Text Input placeholder', 'rrze-expo'),
                'type'              => 'text',
                'default'           => 'Title',
                'sanitize_callback' => 'sanitize_text_field'
            ],
            [
                'name'              => 'number_input',
                'label'             => __('Number Input', 'rrze-expo'),
                'desc'              => __('Number input description.', 'rrze-expo'),
                'placeholder'       => '5',
                'min'               => 0,
                'max'               => 100,
                'step'              => '1',
                'type'              => 'number',
                'default'           => 'Title',
                'sanitize_callback' => 'floatval'
            ],
            [
                'name'        => 'textarea',
                'label'       => __('Textarea Input', 'rrze-expo'),
                'desc'        => __('Textarea description', 'rrze-expo'),
                'placeholder' => __('Textarea placeholder', 'rrze-expo'),
                'type'        => 'textarea'
            ],
            [
                'name'  => 'checkbox',
                'label' => __('Checkbox', 'rrze-expo'),
                'desc'  => __('Checkbox description', 'rrze-expo'),
                'type'  => 'checkbox'
            ],
            [
                'name'    => 'multicheck',
                'label'   => __('Multiple checkbox', 'rrze-expo'),
                'desc'    => __('Multiple checkbox description.', 'rrze-expo'),
                'type'    => 'multicheck',
                'default' => [
                    'one' => 'one',
                    'two' => 'two'
                ],
                'options'   => [
                    'one'   => __('One', 'rrze-expo'),
                    'two'   => __('Two', 'rrze-expo'),
                    'three' => __('Three', 'rrze-expo'),
                    'four'  => __('Four', 'rrze-expo')
                ]
            ],
            [
                'name'    => 'radio',
                'label'   => __('Radio Button', 'rrze-expo'),
                'desc'    => __('Radio button description.', 'rrze-expo'),
                'type'    => 'radio',
                'options' => [
                    'yes' => __('Yes', 'rrze-expo'),
                    'no'  => __('No', 'rrze-expo')
                ]
            ],
            [
                'name'    => 'selectbox',
                'label'   => __('Dropdown', 'rrze-expo'),
                'desc'    => __('Dropdown description.', 'rrze-expo'),
                'type'    => 'select',
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'rrze-expo'),
                    'no'  => __('No', 'rrze-expo')
                ]
            ]
        ],
        'advanced' => [
            [
                'name'    => 'color',
                'label'   => __('Color', 'rrze-expo'),
                'desc'    => __('Color description.', 'rrze-expo'),
                'type'    => 'color',
                'default' => ''
            ],
            [
                'name'    => 'password',
                'label'   => __('Password', 'rrze-expo'),
                'desc'    => __('Password description.', 'rrze-expo'),
                'type'    => 'password',
                'default' => ''
            ],
            [
                'name'    => 'wysiwyg',
                'label'   => __('Advanced Editor', 'rrze-expo'),
                'desc'    => __('Advanced Editor description.', 'rrze-expo'),
                'type'    => 'wysiwyg',
                'default' => ''
            ],
            [
                'name'    => 'file',
                'label'   => __('File', 'rrze-expo'),
                'desc'    => __('File description.', 'rrze-expo'),
                'type'    => 'file',
                'default' => '',
                'options' => [
                    'button_label' => __('Choose an Image', 'rrze-expo')
                ]
            ]
        ]
    ];
}


/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
	return [
		'block' => [
            'blocktype' => 'rrze-expo/SHORTCODE-NAME', // dieser Wert muss angepasst werden
			'blockname' => 'SHORTCODE-NAME', // dieser Wert muss angepasst werden
			'title' => 'SHORTCODE-TITEL', // Der Titel, der in der Blockauswahl im Gutenberg Editor angezeigt wird
			'category' => 'widgets', // Die Kategorie, in der der Block im Gutenberg Editor angezeigt wird
            'icon' => 'admin-users',  // Das Icon des Blocks
            'show_block' => 'content', // 'right' or 'content' : Anzeige des Blocks im Content-Bereich oder in der rechten Spalte
		],
		'Beispiel-Textfeld-Text' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'rrze-expo' ),
			'type' => 'string' // Variablentyp der Eingabe
		],
		'Beispiel-Textfeld-Number' => [
			'default' => 0,
			'field_type' => 'text', // Art des Feldes im Gutenberg Editor
			'label' => __( 'Beschriftung', 'rrze-expo' ),
			'type' => 'number' // Variablentyp der Eingabe
		],
		'Beispiel-Textarea-String' => [
			'default' => 'ein Beispiel-Wert',
			'field_type' => 'textarea',
			'label' => __( 'Beschriftung', 'rrze-expo' ),
			'type' => 'string',
			'rows' => 5 // Anzahl der Zeilen
		],
		'Beispiel-Radiobutton' => [
			'values' => [
				'wert1' => __( 'Wert 1', 'rrze-expo' ), // wert1 mit Beschriftung
				'wert2' => __( 'Wert 2', 'rrze-expo' )
			],
			'default' => 'DESC', // vorausgewählter Wert
			'field_type' => 'radio',
			'label' => __( 'Order', 'rrze-expo' ), // Beschriftung der Radiobutton-Gruppe
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
		'Beispiel-Checkbox' => [
			'field_type' => 'checkbox',
			'label' => __( 'Beschriftung', 'rrze-expo' ),
			'type' => 'boolean',
			'default'   => true // Vorauswahl: Haken gesetzt
        ],
        'Beispiel-Toggle' => [
            'field_type' => 'toggle',
            'label' => __( 'Beschriftung', 'rrze-expo' ),
            'type' => 'boolean',
            'default'   => true // Vorauswahl: ausgewählt
        ],
		'Beispiel-Select' => [
			'values' => [
                [
                    'id' => 'wert1',
                    'val' =>  __( 'Wert 1', 'rrze-expo' )
                ],
                [
                    'id' => 'wert2',
                    'val' =>  __( 'Wert 2', 'rrze-expo' )
                ],
			],
			'default' => 'wert1', // vorausgewählter Wert: Achtung: string, kein array!
			'field_type' => 'select',
			'label' => __( 'Beschriftung', 'rrze-expo' ),
			'type' => 'string' // Variablentyp des auswählbaren Werts
		],
        'Beispiel-Multi-Select' => [
			'values' => [
                [
                    'id' => 'wert1',
                    'val' =>  __( 'Wert 1', 'rrze-expo' )
                ],
                [
                    'id' => 'wert2',
                    'val' =>  __( 'Wert 2', 'rrze-expo' )
                ],
                [
                    'id' => 'wert3',
                    'val' =>  __( 'Wert 3', 'rrze-expo' )
                ],
			],
			'default' => ['wert1','wert3'], // vorausgewählte(r) Wert(e): Achtung: array, kein string!
			'field_type' => 'multi_select',
			'label' => __( 'Beschrifung', 'rrze-expo' ),
			'type' => 'array',
			'items'   => [
				'type' => 'string' // Variablentyp der auswählbaren Werte
			]
        ]
    ];
}

function getConstants() {
    return [
        'social-media' => [
            'twitter' => 'https://twitter.com/',
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://instagram.com/',
            'youtube' => 'https://youtube.com/',
            'xing' => 'https://www.xing.com/',
            'linkedin' => 'https://linkedin.com/',
        ],
        'template_elements' => [
            'template1' => [
                'title' => [
                    'x' => 1490,
                    'y' => 280,
                    'width' => 300,
                    'height' => 240,
                ],
                'wall' => [
                    'x' => 1452,
                    'y' => 203,
                    'width' => 1135,
                    'height' => 781,
                ],
                'logo-wall' => [
                    'title' => __('Logo on Back Wall', 'rrze-expo'),
                    'x' => 2250,
                    'y' => 235,
                    'width' => 300,
                    'height' => 240,
                ],
                'logo-table' => [
                    'title' => __('Logo on Table', 'rrze-expo'),
                    'x' => 1650,
                    'y' => 830,
                    'width' => 540,
                    'height' => 160,
                ],
                'social-media' => [
                    'title' => __('Social Media Panel', 'rrze-expo'),
                    'x' => 1499,
                    'y' => 615,
                    'width' => 50,
                    'height' => 50,
                ],
                'video1' => [
                    'title' => __('Video Screen 1', 'rrze-expo'),
                    'x' => 1490,
                    'y' => 370,
                    'width' => '',
                    'height' => '',
                ],
                'video2' => [
                    'title' => __('Video Screen 2', 'rrze-expo'),
                    'x' => 1840,
                    'y' => 370,
                    'width' => '',
                    'height' => '',
                ],
                'timetable' => [
                    'title' => __('Our Talks', 'rrze-expo'),
                    'x' => 2250,
                    'y' => 370,
                    'width' => 300,
                    'height' => 180,
                ],
                'rollup' => [
                    'title' => __('Roll Up', 'rrze-expo'),
                    'x' => 1090,
                    'y' => 393,
                    'width' => 335,
                    'height' => 574,
                ],
                'flyers' => [
                    'title' => __('Flyers', 'rrze-expo'),
                    'x' => 2637,
                    'y' => 420,
                    'width' => 160,
                    'height' => 200,
                ],
                'deco' => [
                    'plantsleft' => __('Plants left side', 'rrze-expo'),
                    'plantsright' => __('Plants right side', 'rrze-expo'),
                    'seats-left' => __('Seats left side', 'rrze-expo'),
                    'seats-right' => __('Seats right side', 'rrze-expo'),
                    'owl' => __('Owl', 'rrze-expo'),
                ],
            ],
            'template2' => [
                'title' => [
                    'x' => 1490,
                    'y' => 280,
                    'width' => 300,
                    'height' => 240,
                ],
                'wall' => [
                    'x' => 1452,
                    'y' => 203,
                    'width' => 1135,
                    'height' => 781,
                ],
                'logo-wall' => [
                    'title' => __('Logo on Back Wall', 'rrze-expo'),
                    'x' => 2250,
                    'y' => 235,
                    'width' => 300,
                    'height' => 240,
                ],
                'logo-table' => [
                    'title' => __('Logo on Table', 'rrze-expo'),
                    'x' => 1650,
                    'y' => 830,
                    'width' => 540,
                    'height' => 160,
                ],
                'social-media' => [
                    'title' => __('Social Media Panel', 'rrze-expo'),
                    'x' => 1499,
                    'y' => 615,
                    'width' => 50,
                    'height' => 50,
                ],
                'video1' => __('Video Screen 1', 'rrze-expo'),
                'video2' => __('Video Screen 2', 'rrze-expo'),
                'rollup' => [
                    'title' => __('Roll Up', 'rrze-expo'),
                    'x' => 1090,
                    'y' => 393,
                    'width' => 335,
                    'height' => 574,
                ],
                'flyers' => [
                    'title' => __('Flyers', 'rrze-expo'),
                    'x' => 2637,
                    'y' => 420,
                    'width' => 160,
                    'height' => 200,
                ],
                'plantsright' => __('Plants right side', 'rrze-expo'),
                'seats-left' => __('Seats left side', 'rrze-expo'),
                'owl' => __('Owl', 'rrze-expo'),
            ],
        ],
    ];
}

function getThemeGroup($value = '') {
    $themes = [
        'fau' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Philfak',
            'FAU-Medfak',
            'FAU-Techfak',
            'FAU-Natfak',
            'FAU-RWFak',
            'Fau-Blog',
            'FAU-Jobportal',
        ],
        'rrze' => ['rrze-2019'],
        'events' => ['FAU-Events', 'FAU Events'],
    ];
    foreach ($themes as $group=>$theme) {
        if (in_array($value, $theme))
            return $group;
    }
    return false;
}

