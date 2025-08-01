<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // New disk configuration for public images
        'public_images' => [
            'driver' => 'local',
            'root' => public_path('images'),
            'url' => env('APP_URL') . '/images',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public css
        'public_css' => [
            'driver' => 'local',
            'root' => public_path('css/themes'),
            'url' => env('APP_URL') . '/css/themes',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public images themes
        'public_images_themes' => [
            'driver' => 'local',
            'root' => public_path('images/themes'),
            'url' => env('APP_URL') . '/images/themes',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public images slides
        'public_images_slides' => [
            'driver' => 'local',
            'root' => public_path('images/slides'),
            'url' => env('APP_URL') . '/images/slides',
            'visibility' => 'public',
            'throw' => false,
        ],

        // new disk configuration for public images jumbotron
        'public_images_jumbotron' => [
            'driver' => 'local',
            'root' => public_path('images/jumbotrons'),
            'url' => env('APP_URL') . '/images/jumbotrons',
            'visibility' => 'public',
            'throw' => false,
        ],

        // new disk configuration for public images adzan
        'public_images_adzan' => [
            'driver' => 'local',
            'root' => public_path('images/adzan'),
            'url' => env('APP_URL') . '/images/adzan',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public logo
        'public_images' => [
            'driver' => 'local',
            'root' => public_path('images/logo'),
            'url' => env('APP_URL') . '/images/logo',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public images profile
        'public_images_profile' => [
            'driver' => 'local',
            'root' => public_path('images/profiles'),
            'url' => env('APP_URL') . '/images/profiles',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public sounds musik
        'public_sounds_musik' => [
            'driver' => 'local',
            'root' => public_path('sounds/musik'),
            'url' => env('APP_URL') . '/sounds/musik',
            'visibility' => 'public',
            'throw' => false,
        ],

        // New disk configuration for public sounds adzan
        'public_sounds_adzan' => [
            'driver' => 'local',
            'root' => public_path('sounds/adzan'),
            'url' => env('APP_URL') . '/sounds/adzan',
            'visibility' => 'public',
            'throw' => false,
        ],

        'cloudinary' => [
            'driver' => 'cloudinary',
            'key' => env('CLOUDINARY_KEY'),
            'secret' => env('CLOUDINARY_SECRET'),
            'cloud' => env('CLOUDINARY_CLOUD_NAME'),
            'url' => env('CLOUDINARY_URL'),
            'secure' => env('CLOUDINARY_SECURE', true),
            'prefix' => env('CLOUDINARY_PREFIX', 'masjid_audios'),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
