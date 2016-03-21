# LERN (Laravel Exception Recorder and Notifier)
[![Latest Version](https://img.shields.io/github/release/tylercd100/lern.svg?style=flat-square)](https://github.com/tylercd100/lern/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/tylercd100/lern.svg?style=flat-square)](https://packagist.org/packages/tylercd100/lern)

**_LERN from your mistakes_**

LERN is a Laravel 5 package that will record exceptions into a database and will notify you via [Email](https://laravel.com/docs/master/mail), [Pushover](https://pushover.net/) or [Slack](https://slack.com/).


## Installation

Install via [composer](https://getcomposer.org/) - In the terminal:

```bash
composer require tylercd100/lern
```

Now add the following to the `providers` array in your `config/app.php`

```php
Tylercd100\LERN\LERNServiceProvider::class
```

Then you will need to run these commands in the terminal to copy the config and migration files and run the migration

```bash
php artisan vendor:publish --provider="Tylercd100\LERN\LERNServiceProvider"
php artisan migrate
```

And finally you will need to change your `app/Exceptions/Handler.php` file to extend the new handler class

```php
use Tylercd100\LERN\Handler as ExceptionHandler;

class Handler extends ExceptionHandler{
```

## Usage
This package has two parts, recording and notifying.

To ignore certain Exceptions just edit this property found in `app/Exceptions/Handler.php`
```php
protected $dontReport = [
	'Symfony\Component\HttpKernel\Exception\HttpException'
];
```

### Recording
Once you have migrated the provided migration file, recording will happen automatically. You can use the Eloquent Model to query any exception that has been automatically recorded
```php
use Tylercd100\LERN\Model\ExceptionModel;

$mostRecentException = ExceptionModel::orderBy('created_at','DESC')->first()
```

### Notifications
LERN can notify you using Slack, Pushover or Email. To start using any of these just edit the provided config file `config/lern.php`
