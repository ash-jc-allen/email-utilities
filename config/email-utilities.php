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
    | file is stored in `storage/app/disposable-domains.json`, you would
    | specify `storage_path('app/disposable-domains.json')` here.
    |
    | IMPORTANT: Make sure to configure this path if you intend to use the provided
    | `FetchDisposableEmailDomains` Artisan command to update the list.
    |
    */
    // 'disposable_email_list_path' => storage_path('app/disposable-domains.json'),
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
