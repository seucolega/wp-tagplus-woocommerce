<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Enabled Plugins
    |--------------------------------------------------------------------------
    |
    | The folder names of the TypeRocket plugins you wish to enable.
    |
    */
    'plugins' => [
        // 'seo',
        // 'dev',
        // 'theme-options',
        // 'builder',
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | Turn on Debugging for TypeRocket. Set to false to disable.
    |
    */
    'debug' => false,

    /*
    |--------------------------------------------------------------------------
    | Seed
    |--------------------------------------------------------------------------
    |
    | A 'random' string of text to help with security from time to time.
    |
    */
    'seed' => 'seed_5c52538e21be4',

    /*
    |--------------------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------------------
    |
    | The icon class to use for the admin.
    |
    */
    'icons' => \TypeRocket\Elements\Icons::class,

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | The main user class.
    |
    */
    'user' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    |
    | The main form class for the builder and matrix APIs to load.
    |
    */
    'form' => \TypeRocket\Elements\Form::class,

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | The templates to use for the TypeRocket theme. Set to false if using
    | a theme or `templates` if using core for templates. Must be using
    | TypeRocket as root.
    |
    */
    'templates' => false,

    /*
    |--------------------------------------------------------------------------
    | Assets Version
    |--------------------------------------------------------------------------
    |
    | The version of TypeRocket core assets. Changing this can help bust
    | browser caches.
    |
    */
    'assets' => '1.0.2',

    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    |
    | Load other configurations
    |
    */
    'configurations' => [
        'paths'
    ]

];
