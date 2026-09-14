<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
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

    /*
    |--------------------------------------------------------------------------
    | Allowed Uploads
    |--------------------------------------------------------------------------
    |
    | The only file types the system is allowed to store. The "extensions" are
    | the file names uploadFile() is allowed to write on the disk, and the
    | "mimetypes" are the real file contents the requests are allowed to
    | accept. svg is not an option here because it can carry scripts.
    |
    */

    'allowed_uploads' => [

        'extensions' => [
            'image' => ['jpg', 'jpeg', 'png'],
            'video' => ['mp4', 'mkv', 'webm', 'mov', 'avi'],
            'audio' => ['mp3', 'wav', 'm4a'],
            'excel' => ['xlsx', 'xls', 'csv'],
            'pdf' => ['pdf'],
            'archive' => ['zip', 'rar'],
        ],

        'mimetypes' => [
            'image' => ['image/jpeg', 'image/png'],
            'video' => ['video/*'],
            'audio' => ['audio/*'],
            // a csv file is most of the time detected as a plain text file
            'excel' => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'text/plain',
            ],
            'pdf' => ['application/pdf'],
            'archive' => [
                'application/zip',
                'application/x-rar-compressed',
                'application/vnd.rar',
            ],
        ],

    ],
];
