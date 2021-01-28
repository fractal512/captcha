# Simple Captcha for Laravel 5+
A very simple stateless captcha for [Laravel 5+](http://www.laravel.com/). Basically it's a simple Laravel package wrapper for my old captcha script written a long time ago.

## Installation
The Captcha can be installed via [Composer](http://getcomposer.org) by requiring the
`fractal512/captcha` package and setting the `minimum-stability` to `dev` (required for Laravel 5) in your
project's `composer.json`.

```json
{
    "require": {
        "laravel/framework": "5.0.*",
        "fractal512/captcha": "~1.0"
    },
    "minimum-stability": "dev"
}
```

or

Require this package with composer:
```
composer require fractal512/captcha
```

Update your packages with ```composer update``` or install with ```composer install```.

In Windows, you'll need to include the GD2 DLL `php_gd2.dll` in php.ini. And you also need include `php_fileinfo.dll` and `php_mbstring.dll` to fit the requirements of `fractal512/captcha`'s dependencies.

## Registration in Laravel
Register the Captcha Service Provider in the `providers` key in `config/app.php`.

```php
    'providers' => [
        // ...
        'Fractal512\Captcha\CaptchaServiceProvider',
    ]
```
for Laravel 5.1+
```php
    'providers' => [
        // ...
        Fractal512\Captcha\CaptchaServiceProvider::class,
    ]
```

Register facade for the captcha package in the `aliases` key in `config/app.php`.

```php
    'aliases' => [
        // ...
        'Captcha' => 'Fractal512\Captcha\Facades\Captcha',
    ]
```
for Laravel 5.1+
```php
    'aliases' => [
        // ...
        'Captcha' => Fractal512\Captcha\Facades\Captcha::class,
    ]
```

## Configuration
Publish package `config.php` file to apply your own settings.

```$ php artisan vendor:publish```

`config/captcha.php`

```php
return [
    'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    'expire' => 300,
    'attempts' => 10,
    'default' => [],
    'numbers' => [
        'characters' => '0123456789'
    ],
    'letters' => [
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ],
    'uppercase' => [
        'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ],
    'lowercase' => [
        'characters' => 'abcdefghijklmnopqrstuvwxyz'
    ]
];
```
Configuration options:
* `characters` - set of characters used be captcha (available presets: `default`, `numbers`, `letters`, `uppercase`, `lowercase`);
* `expire` - captcha expiration time in seconds;
* `attempts` - number of attempts per minute to refresh the captcha image.

## Usage Example
```php

    // base_path() . "/routes/web.php"
    Route::any('captcha-example', function() {
        if (request()->getMethod() == 'POST') {
            $rules = ['captcha' => 'required|captcha'];
            $validator = validator()->make(request()->all(), $rules);
            if ($validator->fails()) {
                echo '<p style="color: #ff0000;">Verification failed!</p>';
            } else {
                echo '<p style="color: #00ff00;">Verification passed!</p>';
            }
        }
    
        $form = '<form method="post" action="">';
        $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
        $form .= '<p>';
        $form .= captcha_img('default', ['id' => 'captcha-img']);
        $form .= '<a href="#" onclick="document.getElementById(\'captcha-img\').src = \'/captcha/default?\' + Date.now()">Refresh</a>';
        $form .= '</p>';
        $form .= '<p><input type="text" name="captcha"></p>';
        $form .= '<p><button type="submit" name="check">Check captcha</button></p>';
        $form .= '</form>';
        return $form;
    });
```

## Helpers and Facade
### Return Image
```php
captcha();
```
or using facade
```php
Captcha::create();
```

### Return URL
```php
captcha_src();
```
or using facade
```php
Captcha::src('default');
```

### Return HTML
```php
captcha_img();
```
or using facade
```php
Captcha::img();
```

## To use other configurations
```php
captcha_img('numbers');

Captcha::img('uppercase');
```
etc.


## Links
* Wrapper used from [Captcha for Laravel 5/6/7](https://github.com/mewebstudio/captcha)
