# Laravel URL Shortener
Powerful URL shortening tool using different drivers for your Laravel projects

<p>
<a href="https://packagist.org/packages/codeofdigital/laravel-url-shortener"><img src="https://img.shields.io/packagist/v/codeofdigital/laravel-url-shortener" alt="Latest Version on Packagist"></a>
<a href="https://github.com/codeofdigital/laravel-url-shortener"><img src="https://img.shields.io/github/v/release/codeofdigital/laravel-url-shortener" alt="Latest Release on GitHub"></a>
<a href="https://github.com/codeofdigital/laravel-url-shortener"><img src="https://img.shields.io/github/workflow/status/codeofdigital/laravel-url-shortener/run-tests" alt="Build Status"></a>
<a href="https://packagist.org/packages/codeofdigital/laravel-url-shortener"><img src="https://img.shields.io/packagist/php-v/codeofdigital/laravel-url-shortener" alt="PHP from Packagist"></a>
<a href="https://github.com/codeofdigital/laravel-url-shortener/blob/master/LICENSE.md"><img src="https://img.shields.io/github/license/codeofdigital/laravel-url-shortener" alt="GitHub license"></a>
</p>

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
    - [Requirements](#requirements)
    - [Install Package](#install-package)
    - [Publish Config](#publish-config)
- [Usage](#usage)
    - [Instantiate Shortener](#instantiate-shortener)
    - [Changing driver](#changing-driver)
- [Available Drivers](#available-drivers)
- [Changelog](#changelog)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Overview

A Laravel package that is used to shorten URLs according to your needs using your desired URL shortening drivers.
Every driver will provide different features and function so do please check their documentation
and pricing for different usages.

## Installation

### Requirements
The package has been developed to work with the following versions and minimum requirements:

- PHP 7.2 or higher
- Laravel 6.0 or higher

### Install Pacakge
You can install the package via the latest Composer:

```bash
composer require codeofdigital/laravel-url-shortener
```

### Publish Config
You can then publish the package's config file by using the following command:

```bash
php artisan vendor:publish --provider="CodeOfDigital\LaravelUrlShortener\UrlShortenerServiceProvider"
```


## Usage
The quickest way to get started with creating a shortened URL is by using the snippet below.
The ``` shorten() ``` method will return a shortened URL link, and you can freely use it within your system.
```php
$shortUrl = new UrlShortener();
$shortUrl->shorten('https://example.com');
```

### Instantiate Shortener
The URL Shortener can be retrieved from the container in few ways:
```php
$shortener = app('url.shortener');
// or ...
$shortener = url()->shortener();
```

This package also comes with the URL Shortener facade to instantiate the class:
```php
use CodeOfDigital\LaravelUrlShortener\Facades\UrlShortener;

$shortUrl = UrlShortener::shorten('https://example.com');
```

You can also use dependency injection to inject in one of your controller's method:
```php
use CodeOfDigital\LaravelUrlShortener\UrlShortener;

class MyController extends Controller
{
    public function myFunction(UrlShortener $shortener)
    {
        $shortener->shorten('https://example.com');
    }
}
```

Once you have instantiate the URL Shortener class, you can use the methods and shorten your URLs:
```php
// This will set and create the driver instance
$shortener->driver('own-driver');

// This will return shortened URL in string
$shortener->shorten('https://example.com');

// This will return a promise object which can be used to resolve and retrieve shortened URL later on
$shortener->shortenAsync('https://example.com');

// Methods can be called from Laravel URL components
url()->shorten('https://example.com');

// Or
app('url.shortener')->shorten('https://example.com');

// Methods can be chained as well
$shortener->driver('own-driver')->shorten('https://example.com');
```

The URL Shortener provides the following methods to use:

Method          | Description
----------------|---------------------------------
`shorten`       | Shorten the given URL
`shortenAsync`  | Shorten the given URL asynchronously
`driver`        | Set the driver and create the driver instance

### Changing Driver
You can change the default driver by setting `URL_SHORTENER_DRIVER={driver}` in your environment file
or publish the config file and make your changes there directly.

## Available Drivers
There are total of 8 drivers available in this package. Below is a list of drivers with their respective specs:

Service                        | API Token | Driver name | Analytics | Monetization
-------------------------------|-----------|-------------|-----------|-------------
[Bit.ly](#bitly)               | yes       | `bit_ly`    | yes       | no
[TinyURL](#tinyurl)            | yes       | `tiny_url`  | no        | no
[Shorte.st](#shortest)         | yes       | `shorte_st` | yes       | yes
[Is.gd](#isgd)                 | no        | `is_gd`     | yes       | no
[Cutt.ly](#cuttly)             | yes       | `cutt_ly`   | yes       | no
[HideUri](#hideuri)            | no        | `hide_uri`  | no        | no
[Firebase Links](#firebase)    | yes       | `firebase`  | yes       | no
[Ouo.io](#ouoio)               | no        | `ouo_io`    | yes       | yes

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has been changed in recent versions.

## Security
If you discover any security related issues, please email one of the authors instead of using the issue tracker. You can
find the author emails in the [composer.json](composer.json).

## Credits
- [Bryan Adam Loh](https://github.com/bryanadamloh)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
