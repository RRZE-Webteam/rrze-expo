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
        'title'         => __('RRZE Expo', 'rrze-expo'),
    ];
}

/**
 * Gibt die Einstellungen der Inhaltshilfe zurück.
 * @return array [description]
 */
function getHelpTab()
{
    return [
        /*[
            'id'        => 'rrze-expo-help',
            'content'   => [
                '<p>' . __('Here comes the Context Help content.', 'rrze-expo') . '</p>'
            ],
            'title'     => __('Overview', 'rrze-expo'),
            'sidebar'   => sprintf('<p><strong>%1$s:</strong></p><p><a href="https://blogs.fau.de/webworking">RRZE Webworking</a></p><p><a href="https://github.com/RRZE Webteam">%2$s</a></p>', __('For more information', 'rrze-expo'), __('RRZE Webteam on Github', 'rrze-expo'))
        ]*/
    ];
}

/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections()
{
    return [
        /*[
            'id'    => 'basic',
            'title' => __('Basic Settings', 'rrze-expo')
        ],
        [
            'id'    => 'advanced',
            'title' => __('Advanced Settings', 'rrze-expo')
        ]*/
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields()
{
    return [
        /*'basic' => [
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
        ]*/
    ];
}


/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings(){
	return [
		/*'block' => [
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
        ]*/
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
            'website' => '',
        ],
        'template_elements' => [
            'booth1' => [
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
                'logo' => [
                    'wall' => [
                        'title' => __('Logo on Back Wall', 'rrze-expo'),
                        'x' => 2250,
                        'y' => 235,
                        'width' => 300,
                        'height' => 240,
                    ],
                    'table' => [
                        'title' => __('Logo on Table', 'rrze-expo'),
                        'x' => 1650,
                        'y' => 830,
                        'width' => 540,
                        'height' => 160,
                    ],
                ],
                'website' => [
                    'x' => 1615,
                    'y' => 622,
                    'width' => 1135,
                    'height' => 781,
                ],
                'social-media' => [
                    'title' => __('Social Media Panel', 'rrze-expo'),
                    'direction' => 'portrait',
                    'color' => true,
                    'x' => 1499,
                    'y' => 615,
                    'width' => 50,
                    'height' => 50,
                ],
                'video_left' => [
                    'title' => __('Video Screen Left', 'rrze-expo'),
                    'x' => 1490,
                    'y' => 370,
                    'width' => 320,
                    'height' => 200,
                ],
                'video_right' => [
                    'title' => __('Video Screen Right', 'rrze-expo'),
                    'x' => 1840,
                    'y' => 370,
                    'width' => 320,
                    'height' => 200,
                ],
                'video_table' => [
                    'title' => __('Video Screen Table', 'rrze-expo'),
                    'x' => 1840,
                    'y' => 370,
                    'width' => 100,
                    'height' => 60,
                ],
                'gallery' => [
                    'title' => __('Gallery', 'rrze-expo'),
                    'x' => 1975,
                    'y' => 765,
                    'width' => 100,
                    'height' => 60,
                ],
                'schedule' => [
                    'title' => __('Our Talks', 'rrze-expo'),
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
                    'plant1' => __('Plant 1 Right', 'rrze-expo'),
                    'plant2' => __('Plant 2 Right', 'rrze-expo'),
                    'plant3' => __('Plant 1 Left (Replaces Roll Up)', 'rrze-expo'),
                    'plant4' => __('Plant 2 Left (Replaces Roll Up)', 'rrze-expo'),
                    //'seats-left' => __('Seats left side', 'rrze-expo'),
                    //'seats-right' => __('Seats right side', 'rrze-expo'),
                    //'owl' => __('Owl', 'rrze-expo'),
                ],
                'persona' => [
                    1 => [
                        'x' => 2100,
                        'y' => 400,
                        'width' => 300,
                        'height' => 620,
                    ],
                    2 => [
                        'x' => 2210,
                        'y' => 450,
                        'width' => 300,
                        'height' => 610,
                    ],
                    3 => [
                        'x' => 2370,
                        'y' => 450,
                        'width' => 300,
                        'height' => 600,
                    ],
                ],
                'seat' => [
                    1 => [
                        'x' => 1300,
                        'y' => 850,
                        'width' => 410,
                        'height' => 220,
                    ],
                    2 => [
                        'x' => 2350,
                        'y' => 840,
                        'width' => 410,
                        'height' => 220,
                    ],
                    3 => [
                        'x' => 2650,
                        'y' => 855,
                        'width' => 410,
                        'height' => 220,
                    ],
                ],
            ],
            'booth2' => [
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
                'logo' => [
                    'panel' => [
                        'title' => __('Side Panel', 'rrze-expo'),
                        'x' => 1062,
                        'y' => 176,
                        'width' => 335,
                        'height' => 121,
                    ],
                    'wall' => [
                        'title' => __('Back Wall', 'rrze-expo'),
                        'x' => 2250,
                        'y' => 235,
                        'width' => 300,
                        'height' => 240,
                    ],
                ],
                'website' => [
                    'x' => 1492,
                    'y' => 610,
                    'width' => 1135,
                    'height' => 781,
                ],
                'social-media' => [
                    'title' => __('Social Media Panel', 'rrze-expo'),
                    'direction' => 'landscape',
                    'color' => false,
                    'x' => 1585,
                    'y' => 104,
                    'width' => 40,
                    'height' => 40,
                ],
                'video_left' => [
                    'title' => __('Video Screen Left', 'rrze-expo'),
                    'x' => 1490,
                    'y' => 370,
                    'width' => 320,
                    'height' => 200,
                ],
                'video_right' => [
                    'title' => __('Video Screen Right', 'rrze-expo'),
                    'x' => 1840,
                    'y' => 370,
                    'width' => 320,
                    'height' => 200,
                ],
                'video_table' => [
                    'title' => __('Video Screen Table', 'rrze-expo'),
                    'x' => 1840,
                    'y' => 370,
                    'width' => 100,
                    'height' => 60,
                ],
                'gallery' => [
                    'title' => __('Gallery', 'rrze-expo'),
                    'x' => 1715,
                    'y' => 760,
                    'width' => 100,
                    'height' => 60,
                ],
                'schedule' => [
                    'title' => __('Our Talks', 'rrze-expo'),
                    'x' => 2250,
                    'y' => 370,
                    'width' => 300,
                    'height' => 180,
                ],
                'rollup' => [
                    'title' => __('Roll Up', 'rrze-expo'),
                    'x' => 1063,
                    'y' => 337,
                    'width' => 335,
                    'height' => 544,
                ],
                'flyers' => [
                    'title' => __('Flyers', 'rrze-expo'),
                    'x' => 2637,
                    'y' => 420,
                    'width' => 160,
                    'height' => 200,
                ],
                'deco' => [
                    'plant1' => __('Plant 1 Right', 'rrze-expo'),
                    'plant2' => __('Plant 2 Right', 'rrze-expo'),
                    'plant3' => __('Plant 1 Left (Replaces Roll Up)', 'rrze-expo'),
                    'plant4' => __('Plant 2 Left (Replaces Roll Up)', 'rrze-expo'),
                    //'seats-left' => __('Seats left side', 'rrze-expo'),
                    //'seats-right' => __('Seats right side', 'rrze-expo'),
                    //'owl' => __('Owl', 'rrze-expo'),
                ],
                'persona' => [
                    1 => [
                        'x' => 2050,
                        'y' => 400,
                        'width' => 300,
                        'height' => 620,
                    ],
                    2 => [
                        'x' => 2210,
                        'y' => 450,
                        'width' => 300,
                        'height' => 610,
                    ],
                    3 => [
                        'x' => 2370,
                        'y' => 450,
                        'width' => 300,
                        'height' => 600,
                    ],
                ],
                'seat' => [
                    1 => [
                        'x' => 1250,
                        'y' => 855,
                        'width' => 410,
                        'height' => 220,
                    ],
                    2 => [
                        'x' => 1670,
                        'y' => 845,
                        'width' => 410,
                        'height' => 220,
                    ],
                    3 => [
                        'x' => 2600,
                        'y' => 855,
                        'width' => 410,
                        'height' => 220,
                    ],
                ],
            ],
            'foyer' => [
                'board1' => [
                    'x' => 1100,
                    'y' => 15,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(1100 70) scale(.15) rotate(-45)'
                ],
                'board2' => [
                    'x' => 1100,
                    'y' => 215,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(1115 308) scale(.15) rotate(-90)'
                ],
                'board3' => [
                    'x' => 1100,
                    'y' => 415,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(1153 525) scale(.15) rotate(-135)'
                ],
                'board4' => [
                    'x' => 2420,
                    'y' => 15,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(2917 17) scale(.15) rotate(45)'
                ],
                'board5' => [
                    'x' => 2420,
                    'y' => 215,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(2955 232) scale(.15) rotate(90)'
                ],
                'board6' => [
                    'x' => 2420,
                    'y' => 415,
                    'width' => 554,
                    'height' => 171,
                    'arrow-position' => 'translate(2975 470) scale(.15) rotate(135)'
                ],
                'board-center' => [
                    'x' => 1762,
                    'y' => 0,
                    'width' => '551',
                    'height' => '585',
                    'arrow-position' => 'translate(3038 688) scale(.2) rotate(135)'
                ],
                'table' => [
                    'x' => -80,
                    'y' => 0,
                    'width' => '',
                    'height' => '',
                ],
                'tablet' => [
                    'x' => 2080,
                    'y' => 618,
                    'width' => 173,
                    'height' => 102,
                ],
                'info-icon' => [
                    'x' => 1780,
                    'y' => 790,
                    'width' => .35,
                    'height' => .35,
                ],
                'logo' => [
                    'x' => 1780,
                    'y' => 780,
                    'width' => 500,
                    'height' => 200,
                ],
                'social-media' => [
                    'title' => __('Social Media Panel', 'rrze-expo'),
                    'direction' => 'portrait',
                    'color' => true,
                    'x' => 1560,
                    'y' => 600,
                    'width' => 50,
                    'height' => 50,
                ],
                'persona' => [
                    1 => [
                        'x' => 930,
                        'y' => 420,
                        'width' => 300,
                        'height' => 620,
                    ],
                    2 => [
                        'x' => 2190,
                        'y' => 400,
                        'width' => 300,
                        'height' => 600,
                    ],
                    3 => [
                        'x' => 2820,
                        'y' => 450,
                        'width' => 300,
                        'height' => 610,
                    ],
                ],
                'seat' => [
                    1 => [
                        'x' => 1140,
                        'y' => 850,
                        'width' => 410,
                        'height' => 220,
                    ],
                    2 => [
                        'x' => 2175,
                        'y' => 840,
                        'width' => 410,
                        'height' => 220,
                    ],
                    3 => [
                        'x' => 2550,
                        'y' => 855,
                        'width' => 410,
                        'height' => 220,
                    ],
                ],
            ],
            'exposition' => [
                'panel' => [
                    'x' => 1300,
                    'y' => 240,
                    'width' => 969,
                    'height' => 468,
                ],
                'flag1' => [
                    'x' => 2440,
                    'y' => 210,
                    'width' => 199,
                    'height' => 529,
                ],
                'flag2' => [
                    'x' => 2650,
                    'y' => 210,
                    'width' => 199,
                    'height' => 529,
                ],
                'flag3' => [
                    'x' => 2860,
                    'y' => 210,
                    'width' => 199,
                    'height' => 529,
                ],
                'persona' => [
                    1 => [
                        'x' => 970,
                        'y' => 430,
                        'width' => 300,
                        'height' => 620,
                    ],
                    3 => [
                        'x' => 1100,
                        'y' => 480,
                        'width' => 300,
                        'height' => 610,
                    ],
                    2 => [
                        'x' => 1240,
                        'y' => 480,
                        'width' => 300,
                        'height' => 600,
                    ],
                    4 => [
                        'x' => 2100,
                        'y' => 410,
                        'width' => 300,
                        'height' => 620,
                    ],
                    5 => [
                        'x' => 2230,
                        'y' => 450,
                        'width' => 300,
                        'height' => 610,
                    ],
                    6 => [
                        'x' => 2380,
                        'y' => 460,
                        'width' => 300,
                        'height' => 600,
                    ],
                ],
            ],
            'podium1' => [
                'schedule' => [
                    'x' => 1050,
                    'y' => 205,
                    'width' => 450,
                    'height' => 780,
                ],
                'video' => [
                    'x' => 1640,
                    'y' => 204,
                    'width' => 1135,
                    'height' => 780,
                ],
                'deco' => [
                    'plant1' => __('Plant 1', 'rrze-expo'),
                    'plant2' => __('Plant 2', 'rrze-expo'),
                ],
            ],
            'podium2' => [
                'video' => [
                    'x' => 1165,
                    'y' => 182,
                    'width' => 1567,
                    'height' => 685,
                ],
                'deco' => [
                    'plant1' => __('Plant 1', 'rrze-expo'),
                    'plant2' => __('Plant 2', 'rrze-expo'),
                ],
            ],
        ],
        'skin-colors' => [ // Source: https://www.schemecolor.com/real-skin-tones-color-palette.php
            '#FFDBAC',
            '#F1C27D',
            '#E0AC69',
            '#C68642',
            '#8D5524',
        ],
        'hair-colors' => [
            '#F2DA91',
            '#EB5B28',
            '#B5441B',
            '#754C29',
            '#603913',
            '#3C2415',
            '#1A1A1A',
            '#CFCCC3',
            '#3399FF',
            '#009966',
        ],
        'personas' => ['' => __('No Character', 'rrze-expo'),
            'business-1' => __('Business Character', 'rrze-expo') . " 1",
            'business-2' => __('Business Character', 'rrze-expo') . " 2",
            'business-3' => __('Business Character', 'rrze-expo') . " 3",
            'business-4' => __('Business Character', 'rrze-expo') . " 4",
            'speaker-1' => __('Speaker', 'rrze-expo') . " 1",
            'speaker-2' => __('Speaker', 'rrze-expo') . " 2",
            'speaker-3' => __('Speaker', 'rrze-expo') . " 3",
            'speaker-4' => __('Speaker', 'rrze-expo') . " 4",
            'speaker-5' => __('Speaker', 'rrze-expo') . " 5",
            'speaker-6' => __('Speaker', 'rrze-expo') . " 6",
            'student-1' => __('Student', 'rrze-expo') . " 1",
            'student-2' => __('Student', 'rrze-expo') . " 2",
            'student-3' => __('Student', 'rrze-expo') . " 3",
            'student-4-fau' => __('FAU Student', 'rrze-expo') . " 1",
            'student-5-fau' => __('FAU Student', 'rrze-expo') . " 2",
            'student-6-fau' => __('FAU Student', 'rrze-expo') . " 3",
            'student-7-fau' => __('FAU Student', 'rrze-expo') . " 4",],
        'colors' => [
            'dark' => [
                '#04316A',
                '#FDB735',
                '#C50F3C',
                '#18B4F1',
                '#7BB725',
                '#8C9FB1',
            ],
            'darker' => [
                '#004A9F',
                '#e87722',
                '#971B2F',
                '#005287',
                '#266141',
                '#2F586E',
            ],
            'light' => [
                '#CED9E7',
                '#F4E8CB',
                '#F2DED1',
                '#B9D9EC',
                '#CFE0D8',
                '#D3DDE6',
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

