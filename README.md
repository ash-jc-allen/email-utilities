<p align="center">
<img src="/docs/banner.png" alt="Email Utilities" width="600">
</p>

<p align="center">
<a href="https://packagist.org/packages/ashallendesign/email-utilities"><img src="https://img.shields.io/packagist/v/ashallendesign/email-utilities.svg?style=flat-square" alt="Latest Version on Packagist"></a>
<a href="https://packagist.org/packages/ashallendesign/email-utilities"><img src="https://img.shields.io/packagist/dt/ashallendesign/email-utilities.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ashallendesign/email-utilities"><img src="https://img.shields.io/packagist/php-v/ashallendesign/email-utilities?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://github.com/ash-jc-allen/email-utilities/blob/master/LICENSE"><img src="https://img.shields.io/github/license/ash-jc-allen/email-utilities?style=flat-square" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
    * [Requirements](#requirements)
    * [Install the Package](#install-the-package)
- [Usage](#usage)
    * [The `Email` Class](#email-class)
        + [Disposable Email Addresses](#disposable-email-addresses)
        + [Role-based Email Addresses](#role-based-email-addresses)
        + [Checking the Domain of an Email Address](#checking-the-domain-of-an-email-address)
            - [`domainIs` Method](#domainis-method)
            - [`domainIsNot` Method](#domainisnot-method)
    * [Validation Rules](#validation-rules)
      + [`EmailDomainIs` Rule](#emaildomainis-rule)
      + [`EmailDomainIsNot` Rule](#emaildomainisnot-rule)
- [Config](#config)
    * [Disposable Email Domains List](#disposable-email-domains-list)
    * [Role Accounts List](#role-accounts-list)
- [Testing](#testing)
- [Security](#security)
- [Contribution](#contribution)
- [Changelog](#changelog)
- [Credits](#credits)
- [License](#license)

## Overview

A small Laravel package that can be used for interacting with email addresses.

## Installation

### Requirements

The package has been developed and tested to work with the following minimum requirements:

- PHP 8.4
- Laravel 12.0

### Install the Package

You can install the package via Composer:

```bash
$ composer require ashallendesign/email-utilities
```

After installing the package, you can then publish the configuration file using the following command:

```bash
$ php artisan php artisan vendor:publish --tag=email-utilities-config
```

Running this command will create a `config/email-utilities.php` file.

## Usage

## The `Email` Class

The package provides an `AshAllenDesign\EmailUtilities\Email` class that can be used to interact with email addresses.

You can create a new instance of it by passing an email address to the constructor:

```php
use AshAllenDesign\EmailUtilities\Email;

$email = new Email('hello@example.com');
```

### Disposable Email Addresses

You can check whether a given email address is deemed to be disposable/temporary (meaning it's provided by a disposable email address provider) by using the `isDisposable()` method:

```php
use AshAllenDesign\EmailUtilities\Email;

new Email('hello@0-mail.com')->isDisposable(); // true
new Email('hello@laravel.com')->isDisposable(); // false
```

The package's list of disposable domains is defined in the `AshAllenDesign\EmailUtilities\Lists\DisposableDomainList` class. You can output a list of all the disposable email address domains by using the `get()` method:

```php
use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;

$disposableEmailDomains = DisposableEmailDomains::get();

// [
//     '0-mail.com',
//     '027168.com',
//     '062e.com',
//     ...
// ]
```

The list of disposable email address providers is sourced from [https://github.com/disposable-email-domains/disposable-email-domains](https://github.com/disposable-email-domains/disposable-email-domains). It's worth remembering that new domains are being used all the time, so it's possible that some disposable email addresses may not be detected. So please use this functionality with that in mind.

> [!NOTE]
> If you wish to keep the list of disposable email domains up-to-date, please check the [Config > Disposable Email Domains List](#disposable-email-domains-list) section below. We're providing a nice `FetchDisposableEmailDomains` Artisan command which you can schedule.

### Role-based Email Addresses

You may want to check whether a given email address is role-based. Role-based email addresses are those that are not specific to an individual, but rather to a role or function within an organisation. Examples include `admin@`, `support@`, `info@` and `sales@`.

To do this, you can use the `isRoleAccount()` method:

```php
use AshAllenDesign\EmailUtilities\Email;

new Email('sales@example.com')->isRoleAccount(); // true
new Email('ash@example.com')->isRoleAccount(); // false
```

Similar to the disposable email address domains, the package's list of role-based email address prefixes is defined in the `AshAllenDesign\EmailUtilities\Lists\RoleAccountList` class. You can output a list of all the role-based email address prefixes by using the `get()` method:

```php
use AshAllenDesign\EmailUtilities\Lists\RoleAccountList;

$roleAccountList = RoleAccountList::get();

// [
    // 'admin',
    // 'administrator',
    // 'contact',
    // ...
// ]
```

Please remember that this list is not exhaustive, so it may not detect all role-based email-addresses.

### Checking the Domain of an Email Address

#### `domainIs` Method

The `AshAllenDesign\EmailUtilities\Email` class also provides a `domainIs` method which can be used to check whether the domain of an email address matches a given pattern. This is useful if you want to check whether an email address belongs to a specific domain or set of domains.

The beauty of this method is that it supports wildcard (`*`) patterns, so it allows for more flexible matching.

For example:

```php
use AshAllenDesign\EmailUtilities\Email;

new Email('hello@example.com')->domainIs(['example.com']); // true
new Email('hello@example.com')->domainIs(['example.com', 'test.com']); // true
new Email('hello@example.com')->domainIs(['example*']); // true
new Email('hello@example.com')->domainIs(['ex*le.com']); // true
new Email('hello@example.com')->domainIs(['ex*le.com']); // true

new Email('hello@example.com')->domainIs(['example']); // false
new Email('hello@example.com')->domainIs(['test.com']); // false
```

#### `domainIsNot` Method

Similarly, the `AshAllenDesign\EmailUtilities\Email` class also provides a `domainIsNot` method which can be used to check whether the domain of an email address does not match a given pattern.

For example:

```php
use AshAllenDesign\EmailUtilities\Email;

new Email('hello@example.com')->domainIsNot(['example.com']); // false
new Email('hello@example.com')->domainIsNot(['example.com', 'test.com']); // false
new Email('hello@example.com')->domainIsNot(['example*']); // false
new Email('hello@example.com')->domainIsNot(['ex*le.com']); // false
new Email('hello@example.com')->domainIsNot(['ex*le.com']); // false

new Email('hello@example.com')->domainIsNot(['example']); // true
new Email('hello@example.com')->domainIsNot(['test.com']); // true
```

### Validation Rules

> [!NOTE]
> Please note, the validation rules that are included with this package don't validate that a value is actually an email address. These rules are intended to be used in conjunction with Laravel's built-in `email` validation rule ([https://laravel.com/docs/12.x/validation#rule-email](https://laravel.com/docs/12.x/validation#rule-email)).

#### `EmailDomainIs` Rule

The package provides an `AshAllenDesign\EmailUtilities\Rules\EmailDomainIs` validation rule that can be used to validate that the domain of an email address matches a given pattern. This is useful if you want to ensure that an email address belongs to a specific domain or set of domains, such as only allowing email addresses from your own organisation.

It uses the `AshAllenDesign\EmailUtilities\Email::domainIs` method under the hood, so it supports wildcard (`*`) patterns.

You can use the rule like so:

```php
use AshAllenDesign\EmailUtilities\Rules\EmailDomainIs;

$request->validate([
    'email' => ['required', 'email', new EmailDomainIs(patterns: ['example.com', '*.example.com'])],
]);
```

In this particular example, we've hardcoded the allowed domain pattern, but you may want to load this from a configuration file or the database instead.

#### `EmailDomainIsNot` Rule

Similar to the `EmailDomainIs` rule, the package also provides an `AshAllenDesign\EmailUtilities\Rules\EmailDomainIsNot` validation rule that can be used to validate that the domain of an email address does not match a given pattern. This is useful if you want to ensure that an email address does not belong to a specific domain, such as a list of known disposable email address providers.

You can use the rule like so:

```php
use AshAllenDesign\EmailUtilities\Rules\EmailDomainIsNot;

$request->validate([
    'email' => ['required', 'email', new EmailDomainIsNot(patterns: ['disposable.com', '*.disposable.com'])],
]);
```

This validation rule also comes with a handy `disposable` method so you can quickly add a rule to prevent disposable email addresses from being used:

```php
use AshAllenDesign\EmailUtilities\Rules\EmailDomainIsNot;

$request->validate([
    'email' => ['required', 'email', EmailDomainIsNot::disposable()],
]);
```

## Config

The package provides several options that can be configured via the published configuration file located at `config/email-utilities.php`.

### Disposable Email Domains List

By default, the package uses a built-in list of disposable email address domains defined in the `AshAllenDesign\EmailUtilities\Lists\DisposableDomainList` class. Over time, this list may change as new disposable email address providers are created.

However, you can maintain your own list of disposable domains by setting the `disposable_email_list_path` configuration option like so:

```php
'disposable_email_list_path' => storage_path('app/disposable-domains.txt'),
```

You can also publish the package's built-in list to your application by running the following command:

```bash
$ php artisan vendor:publish --tag=email-utilities-lists
```

This will create a `disposable-domains.txt` file in your application's root directory. You can then modify this file as needed and update the `disposable_email_list_path` configuration option to point to this file. Running this command will also publish a `role-accounts.json` file that you can use to maintain your own list of role-based email address prefixes.

Once you have configured `disposable_email_list_path`, we recommend to keep the list updated via our Artisan command:

```bash
$ php artisan email-utilities:fetch-disposable-domains
```

You may schedule this in your `routes/console.php` to run on a daily basis:

```php
Schedule::command(\AshAllenDesign\EmailUtilities\Commands\FetchDisposableEmailDomains::class)
  ->daily()
  ->emailOutputOnFailure('myapp@example.com');
```

### Role Accounts List

Similar to the disposable email domains list, by default, the package uses a built-in list of role-based email address prefixes defined in the `AshAllenDesign\EmailUtilities\Lists\RoleAccountList` class. However, you can maintain and provide your own list by setting the `role_account_list_path` configuration option like so:

```php
'role_accounts_list_path' => './storage/app/role_account_list.json',
```

## Testing

To run the package's unit tests, run the following command:

``` bash
$ composer test
```

To run Larastan for the package, run the following command:

```bash
$ composer larastan
```

## Security

If you find any security related issues, please contact me directly at [mail@ashallendesign.co.uk](mailto:mail@ashallendesign.co.uk) to report it.

## Contribution

If you wish to make any changes or improvements to the package, feel free to make a pull request.

To contribute to this package, please use the following guidelines before submitting your pull request:

- Write tests for any new functions that are added. If you are updating existing code, make sure that the existing tests
  pass and write more if needed.
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards.
- Make all pull requests to the `main` branch.

## Changelog

Check the [CHANGELOG](CHANGELOG.md) to get more information about the latest changes.

## Credits

- [Ash Allen](https://ashallendesign.co.uk)
- [Jess Allen](https://jesspickup.co.uk) (Logo)
- [All Contributors](https://github.com/ash-jc-allen/email-utilities/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support Me

If you've found this package useful, please consider buying a copy of [Battle Ready Laravel](https://battle-ready-laravel.com) to support me and my work.

Every sale makes a huge difference to me and allows me to spend more time working on open-source projects and tutorials.

To say a huge thanks, you can use the code **BATTLE20** to get a 20% discount on the book.

[👉 Get Your Copy!](https://battle-ready-laravel.com)

[![Battle Ready Laravel](https://ashallendesign.co.uk/images/custom/sponsors/battle-ready-laravel-horizontal-banner.png)](https://battle-ready-laravel.com)
