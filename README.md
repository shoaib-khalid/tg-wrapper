<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## First Timer Setup
This Instruction is for the first timer setup.

1. Install Composer (Make sure to have PHP 7.4 & above - Recommended)
2. Clone this repo to your local
3. Dependency Manager : run `composer install` in your cmd. 
```
$ composer install
```
4. run `php artisan serve` in your cmd go to localhost:8000 or http://127.0.0.1:8000 (default at port 8000 can be change if require)
```
$ php artisan serve
```

## Required PHP extensions
Below extension need to be enable / installed in php.ini

```
# php -m | grep "<extension_name>" ## for linux
# php -m | Select-String "<extension_name>" ## for windows

extension=fileinfo
extension=mbstring
extension=openssl
extension=mongodb
```

Suggested mongodb extension version : [php_mongodb-1.9.1-8.0-ts-vs16-x64](https://windows.php.net/downloads/pecl/Releases/mongodb/1.9.1/) <br>
Reference : [Installing the MongoDB PHP Driver on Windows - Manual - PHP](https://www.php.net/manual/en/mongodb.installation.windows.php)

## DB Migration

Use this instruction to run existing Migration file.

1. create new DB at MySQL named as medusa (medusa name is default, if you changed to other name, u will need to reconfigure .ENV file)
2. run `php artisan migrate:refresh` in your cmd to re-run all the migration files.
```
$ php artisan migrate:refresh
```

## Faker/Factories

Use this to run some faker data.

1. PHP tinker : run `php artisan tinker` in your cmd. 
```
$ php artisan tinker
```
2. create 5 dummy data of user faker : run `User::factory()->count(5)->create()`
```
$ User::factory()->count(5)->create()
```
3. create 15 dummy data of todo faker : run `Todo::facroty()->count(15)->create()`
```
$ Todo::factory()->count(15)->create()
```


## IMPORTANT! 

If you encounter ERR 500 during serve the project. Follow this instruction!

1. copy .env files : run `cp .env.example .env`.
```
$ cp .env.example .env
```
2. generate key : run `php artisan key:generate`.
```
$ php artisan key:generate
```
3. clear cache : run `php artisan config:cache`.
```
$ php artisan config:cache
```
4. serve again : run `php artisan serve`.
```
$ php artisan serve
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
