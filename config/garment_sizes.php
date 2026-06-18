<?php

/**
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║  config/garment_sizes.php                                              ║
 * ║                                                                        ║
 * ║  Single source of truth for all garment size charts.                  ║
 * ║  All measurements are in centimetres (cm).                             ║
 * ║                                                                        ║
 * ║  Structure:                                                            ║
 * ║    garment_type → sizes → size_label → measurement_key → value(s)     ║
 * ║                                                                        ║
 * ║  Usage:                                                                ║
 * ║    config('garment_sizes.charts.tshirt')                               ║
 * ║    config('garment_sizes.charts.hoodie.M.chest_width')                 ║
 * ║    config('garment_sizes.labels')                                      ║
 * ╚══════════════════════════════════════════════════════════════════════════╝
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Measurement labels (Arabic + English) — used in views
    |--------------------------------------------------------------------------
    */
    'measurement_labels' => [
        'chest_width'    => ['ar' => 'عرض الصدر',       'en' => 'Chest Width'],
        'body_length'    => ['ar' => 'طول الوشاح',       'en' => 'Stole Length'],
        'sleeve_length'  => ['ar' => 'طول الكم',         'en' => 'Sleeve Length'],
        'shoulder_width' => ['ar' => 'عرض الكتف',        'en' => 'Shoulder Width'],
        'height_range'   => ['ar' => 'نطاق الطول',       'en' => 'Height Range'],
        'gown_length'    => ['ar' => 'طول الثوب',        'en' => 'Gown Length'],
        'panel_width'    => ['ar' => 'عرض اللوحة',       'en' => 'Panel Width'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Size charts — one entry per garment type
    | All values in cm unless noted (height_range uses "cm–cm" string)
    |--------------------------------------------------------------------------
    */
    'charts' => [

        // ── T-Shirt ──────────────────────────────────────────────────────────
        'tshirt' => [
            'XS' => [
                'chest_width'   => 46,
                'body_length'   => 66,
                'sleeve_length' => 19,
            ],
            'S' => [
                'chest_width'   => 49,
                'body_length'   => 69,
                'sleeve_length' => 20,
            ],
            'M' => [
                'chest_width'   => 52,
                'body_length'   => 72,
                'sleeve_length' => 21,
            ],
            'L' => [
                'chest_width'   => 55,
                'body_length'   => 74,
                'sleeve_length' => 22,
            ],
            'XL' => [
                'chest_width'   => 58,
                'body_length'   => 76,
                'sleeve_length' => 23,
            ],
            '2XL' => [
                'chest_width'   => 62,
                'body_length'   => 78,
                'sleeve_length' => 24,
            ],
            '3XL' => [
                'chest_width'   => 66,
                'body_length'   => 80,
                'sleeve_length' => 25,
            ],
        ],

        // ── Hoodie ───────────────────────────────────────────────────────────
        'hoodie' => [
            'XS' => [
                'chest_width'    => 48,
                'body_length'    => 65,
                'sleeve_length'  => 60,
                'shoulder_width' => 40,
            ],
            'S' => [
                'chest_width'    => 51,
                'body_length'    => 67,
                'sleeve_length'  => 62,
                'shoulder_width' => 42,
            ],
            'M' => [
                'chest_width'    => 54,
                'body_length'    => 70,
                'sleeve_length'  => 64,
                'shoulder_width' => 44,
            ],
            'L' => [
                'chest_width'    => 57,
                'body_length'    => 73,
                'sleeve_length'  => 65,
                'shoulder_width' => 46,
            ],
            'XL' => [
                'chest_width'    => 61,
                'body_length'    => 75,
                'sleeve_length'  => 67,
                'shoulder_width' => 48,
            ],
            '2XL' => [
                'chest_width'    => 65,
                'body_length'    => 77,
                'sleeve_length'  => 68,
                'shoulder_width' => 50,
            ],
            '3XL' => [
                'chest_width'    => 69,
                'body_length'    => 79,
                'sleeve_length'  => 70,
                'shoulder_width' => 52,
            ],
        ],

        // ── Varsity Jacket ───────────────────────────────────────────────────
        'varsity_jacket' => [
            'XS' => [
                'chest_width'    => 50,
                'body_length'    => 63,
                'sleeve_length'  => 59,
                'shoulder_width' => 41,
            ],
            'S' => [
                'chest_width'    => 53,
                'body_length'    => 65,
                'sleeve_length'  => 61,
                'shoulder_width' => 43,
            ],
            'M' => [
                'chest_width'    => 56,
                'body_length'    => 68,
                'sleeve_length'  => 63,
                'shoulder_width' => 45,
            ],
            'L' => [
                'chest_width'    => 59,
                'body_length'    => 71,
                'sleeve_length'  => 65,
                'shoulder_width' => 47,
            ],
            'XL' => [
                'chest_width'    => 63,
                'body_length'    => 73,
                'sleeve_length'  => 67,
                'shoulder_width' => 49,
            ],
            '2XL' => [
                'chest_width'    => 67,
                'body_length'    => 75,
                'sleeve_length'  => 68,
                'shoulder_width' => 51,
            ],
            '3XL' => [
                'chest_width'    => 71,
                'body_length'    => 77,
                'sleeve_length'  => 70,
                'shoulder_width' => 53,
            ],
        ],

        // ── Graduation Robe ──────────────────────────────────────────────────
        'graduation_robe' => [
            'S' => [
                'height_range'  => '155–165',
                'chest_width'   => 52,
                'gown_length'   => 120,
                'sleeve_length' => 58,
            ],
            'M' => [
                'height_range'  => '165–175',
                'chest_width'   => 56,
                'gown_length'   => 126,
                'sleeve_length' => 61,
            ],
            'L' => [
                'height_range'  => '175–183',
                'chest_width'   => 60,
                'gown_length'   => 132,
                'sleeve_length' => 64,
            ],
            'XL' => [
                'height_range'  => '183–190',
                'chest_width'   => 64,
                'gown_length'   => 137,
                'sleeve_length' => 66,
            ],
            '2XL' => [
                'height_range'  => '183–192',
                'chest_width'   => 68,
                'gown_length'   => 140,
                'sleeve_length' => 68,
            ],
            '3XL' => [
                'height_range'  => '188–196',
                'chest_width'   => 72,
                'gown_length'   => 143,
                'sleeve_length' => 70,
            ],
        ],

    ],

        // ── Graduation Stole ─────────────────────────────────────────────────────
        'stole' => [
            'One Size' => [
                'body_length'   => 152,
                'panel_width'   => 15,
            ],
            'Long' => [
                'body_length'   => 167,
                'panel_width'   => 15,
            ],
        ],

];