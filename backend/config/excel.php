<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default disk
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default disk that should be used
    | by the package. And you can also add additional disks as needed.
    |
    */

    'disk' => env('EXCEL_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default queue
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default queue that should be used
    | by the package. And you can also add additional queues as needed.
    |
    */

    'queue' => env('EXCEL_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Default cache
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default cache that should be used
    | by the package. And you can also add additional caches as needed.
    |
    */

    'cache' => [
        'enable' => env('EXCEL_CACHE_ENABLE', true),
        'driver' => env('EXCEL_CACHE_DRIVER', null),
        'ttl' => env('EXCEL_CACHE_TTL', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default settings
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default settings that should be used
    | by the package. And you can also add additional settings as needed.
    |
    */

    'exports' => [

        /*
        |--------------------------------------------------------------------------
        | Chunk size
        |--------------------------------------------------------------------------
        |
        | When using FromQuery, the query is automatically chunked.
        | Here you can specify the chunk size.
        |
        */

        'chunk_size' => 1000,

        /*
        |--------------------------------------------------------------------------
        | Temporary files
        |--------------------------------------------------------------------------
        |
        | Export jobs can optionally write to temporary files. This is useful
        | for exports that are too large to hold in memory. You can specify
        | the path to the temporary file here.
        |
        */

        'temp_path' => sys_get_temp_dir(),

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        |
        | Configure the CSV settings used by the package.
        |
        */

        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet properties
        |--------------------------------------------------------------------------
        |
        | Configure the worksheet properties used by the package.
        |
        */

        'properties' => [
            'creator' => 'Laravel Excel',
            'lastModifiedBy' => 'Laravel Excel',
            'title' => 'Spreadsheet',
            'description' => 'Default Laravel Excel Spreadsheet',
            'subject' => 'Spreadsheet',
            'keywords' => 'laravel,spreadsheet,excel',
            'category' => 'Excel',
            'manager' => 'Laravel Excel',
            'company' => 'Laravel Excel',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extension detector
    |--------------------------------------------------------------------------
    |
    | Configure here which writer/reader instance should be used when the package
    | needs to guess the correct type based on the extension alone.
    |
    */

    'extension_detector' => [
        'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
        'xlsm' => \Maatwebsite\Excel\Excel::XLSX,
        'xltx' => \Maatwebsite\Excel\Excel::XLSX,
        'xltm' => \Maatwebsite\Excel\Excel::XLSX,
        'xls' => \Maatwebsite\Excel\Excel::XLS,
        'xlt' => \Maatwebsite\Excel\Excel::XLS,
        'ods' => \Maatwebsite\Excel\Excel::ODS,
        'ots' => \Maatwebsite\Excel\Excel::ODS,
        'slk' => \Maatwebsite\Excel\Excel::SLK,
        'xml' => \Maatwebsite\Excel\Excel::XML,
        'gnumeric' => \Maatwebsite\Excel\Excel::GNUMERIC,
        'htm' => \Maatwebsite\Excel\Excel::HTML,
        'html' => \Maatwebsite\Excel\Excel::HTML,
        'csv' => \Maatwebsite\Excel\Excel::CSV,
        'tsv' => \Maatwebsite\Excel\Excel::TSV,

        /*
        |--------------------------------------------------------------------------
        | PDF extension
        |--------------------------------------------------------------------------
        |
        | Configure here which Pdf driver should be used by default.
        |
        */
        'pdf' => \Maatwebsite\Excel\Excel::MPDF,
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Binder
    |--------------------------------------------------------------------------
    |
    | PhpSpreadsheet offers a way to hook into the process of a value being
    | written to a cell. In there some assumptions are made on how the
    | value should be formatted. If you want to change those defaults,
    | you can implement your own default value binder.
    |
    | Possible value binders:
    |
    | [x] Maatwebsite\Excel\DefaultValueBinder::class
    | [x] Maatwebsite\Excel\StringValueBinder::class
    | [x] Maatwebsite\Excel\BooleanValueBinder::class
    | [x] Maatwebsite\Excel\NullValueBinder::class
    |
    */

    'value_binder' => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | By default PhpSpreadsheet keeps all cell values in memory, however when
    | dealing with large files, this might result into memory issues. If you
    | want to mitigate that, you can configure a cache driver here.
    | When using the illuminate driver, it will store each value in the
    | cache store. This can slow down the process, because it needs to
    | store each value. You can use the "batch" store if you want to
    | only persist to the store when the memory limit is reached.
    |
    | Default: illuminate
    | Supported: memory|illuminate|batch
    |
    */

    'cache' => [
        'driver' => env('EXCEL_CACHE_DRIVER', 'memory'),
        'batch' => [
            'memory_limit' => 60000,
        ],
        'illuminate' => [
            'store' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction handler
    |--------------------------------------------------------------------------
    |
    | By default the import is wrapped in a transaction. This is useful
    | for when an import may fail and you want to retry it. With the
    | transactions, the previous import gets rolled-back and can be
    | retried. You can disable the transaction handler by setting this to null.
    |
    | Supported handlers: null|db
    |
    */

    'transactions' => [
        'handler' => env('EXCEL_TRANSACTIONS_HANDLER', 'db'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tmp path
    |--------------------------------------------------------------------------
    |
    | When uploading files through the web interface, PhpSpreadsheet first store
    | the uploaded file in a temporary location. Here you can configure that
    | location. If you set this to null, the default system temp path will be used.
    |
    */

    'temporary_files' => [
        'local_path' => env('EXCEL_TEMPORARY_FILES_LOCAL_PATH', storage_path('framework/cache/laravel-excel')),
        'remote_disk' => env('EXCEL_TEMPORARY_FILES_REMOTE_DISK', null),
        'remote_prefix' => env('EXCEL_TEMPORARY_FILES_REMOTE_PREFIX', null),
        'force_resync_remote' => env('EXCEL_TEMPORARY_FILES_FORCE_RESYNC_REMOTE', null),
    ],

]; 