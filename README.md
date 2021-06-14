# Laravel URL Shortener
Powerful URL shortening tool using different drivers for your Laravel projects

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
    - [Requirements](#requirements)
    - [Install Package](#install-package)
    - [Publish Config](#publish-config)
- [Usage](#usage)
    - [Instantiate Shortener](#instantiate-shortener)
    - [Changing driver](#changing-driver)
- [License](#license)

## Overview

A Laravel package that is used to shorten URLs according to your needs using your desired URL shortening drivers.
Every driver will provide different features and function so do please check their documentation
and pricing for different usages.

## Installation

### Requirements
The package has been developed to work with the following versions and minimum requirements:

- PHP 7.2 or higher
- Laravel 5.5 or higher

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

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
