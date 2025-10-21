<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Team Size Configurations
    |--------------------------------------------------------------------------
    |
    | This option controls the minimum and maximum team sizes for different
    | academic levels in the PFE system.
    |
    */

    'sizes' => [
        'licence' => [
            'min' => 1, // Changed from 2 to 1 to allow single member teams
            'max' => 4, // Increased from 3 to 4
        ],
        'master' => [
            'min' => 1,
            'max' => 4, // Increased from 2 to 4 to allow larger master teams
        ],
        'doctorat' => [
            'min' => 1,
            'max' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Academic Level
    |--------------------------------------------------------------------------
    |
    | The default academic level to use when not specified.
    |
    */

    'default_level' => 'licence',
];