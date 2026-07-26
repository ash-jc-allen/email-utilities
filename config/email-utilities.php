<?php

return [

    /*
    |-----------------------------------------------------------------------------------
    | Disposable Email Domains List
    |-----------------------------------------------------------------------------------
    |
    | Specify where the disposable email domains list is stored. If `null` is specified,
    | the package's built-in list will be used. Otherwise, provide the relative path
    | to the file, starting from the base path of your app. For example, if the
    | file is stored in `storage/app/disposable_domains.json`, you would
    | specify `storage_path('app/disposable_domains.json')` here.
    |
    */
    'disposable_email_list_path' => null,

    /*
    |-----------------------------------------------------------------------------------
    | Role Accounts List
    |-----------------------------------------------------------------------------------
    |
    | Specify where the role accounts list is stored. If `null` is specified, the
    | package's built-in list will be used. Otherwise, provide the relative path
    | to the file, starting from the base path of your app. For example, if
    | the file is stored in `storage/app/role_accounts.json`, you would
    | specify `storage_path('app/role_accounts.json')` here.
    |
    */
    'role_accounts_list_path' => null,

    /*
    |-----------------------------------------------------------------------------------
    | List Storage
    |-----------------------------------------------------------------------------------
    |
    | Configure where the lists live when they are stored (for example, after
    | running the "email-utilities:fetch-disposable-domains" command). When a
    | store does not hold a list, the package's built-in copy is used.
    |
    | Supported drivers: "file", "disk", "cache", "database".
    |
    | file     - Local file paths. Uses the "disposable_email_list_path" and
    |            "role_accounts_list_path" options above, exactly as previous
    |            versions of this package did.
    | disk     - A Laravel filesystem disk (e.g. "s3"). Useful when the local
    |            filesystem is ephemeral, such as on serverless platforms.
    | cache    - A Laravel cache store.
    | database - A database table. Publish the migration with:
    |            php artisan vendor:publish --tag=email-utilities-migrations
    |
    */
    'storage' => [

        'driver' => 'file',

        'disk' => [
            // The filesystem disk to use. `null` uses your default disk.
            'disk' => null,
            'disposable_email_list_path' => 'email-utilities/disposable-domains.json',
            'role_accounts_list_path' => 'email-utilities/role-accounts.json',
        ],

        'cache' => [
            // The cache store to use. `null` uses your default store.
            //
            // Note: some cache backends cap the size of a single item, and the
            // disposable domains list is a single large value (roughly 220 KB
            // serialized as of 2026, and growing). DynamoDB caps items at
            // 400 KB and Memcached at 1 MB by default; Redis and the database
            // and file stores are effectively unlimited for this purpose.
            'store' => null,
            'prefix' => 'email-utilities',
            // Lifetime in seconds. `null` stores the lists forever.
            'ttl' => null,
        ],

        'database' => [
            // The database connection to use. `null` uses your default connection.
            'connection' => null,
            'table' => 'email_utilities_lists',
        ],

    ],

    /*
    |-----------------------------------------------------------------------------------
    | Validate Config
    |-----------------------------------------------------------------------------------
    |
    | Specify whether the package should validate the configuration values in this
    | file when the package is booted. This helps to catch any misconfigurations
    | early. If the validation fails, the following exception will be thrown:
    |
    | AshAllenDesign\EmailUtilities\Exceptions\ValidationException
    |
    */
    'validate_config' => false,
];
