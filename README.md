# LERN (Laravel Exception Recorder and Notifier)
[![Latest Version](https://img.shields.io/github/release/tylercd100/lern.svg?style=flat-square)](https://github.com/tylercd100/lern/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/tylercd100/lern.svg?branch=master)](https://travis-ci.org/tylercd100/lern)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tylercd100/lern/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/lern/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tylercd100/lern/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/lern/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187)
[![Total Downloads](https://img.shields.io/packagist/dt/tylercd100/lern.svg?style=flat-square)](https://packagist.org/packages/tylercd100/lern)

**_LERN from your mistakes_**

LERN is a Laravel 5 package that will record exceptions into a database and will send you a notification.

Currently supported notification channels via [Monolog](https://github.com/Seldaek/monolog)
- Email
- [Pushover](https://pushover.net/)
- [Slack](https://slack.com/)
- [Hipchat](https://www.hipchat.com/)
- [Fleephook](https://fleep.io/)
- [Flowdock](https://www.flowdock.com/)
- [Plivo](https://www.plivo.com/) an SMS messaging service.
- [Twilio](https://www.twilio.com/) an SMS messaging service.
- [Sentry](https://getsentry.com) via [Raven](https://github.com/getsentry/raven-php)
- [Mailgun](https://mailgun.com)

## Version Compatibility

 Laravel  | LERN
:---------|:----------
 5.1.x    | 3.x
 5.2.x    | 3.x
 5.3.x    | 3.x
 5.4.x    | 3.x
 5.5.x    | 4.x
 5.6.x    | 4.x

## Migrating from `3.x` to `4.x`
Make sure that the config file now includes the new `lern.notify.class` and `lern.record.class` settings. Check the [config file](https://github.com/tylercd100/lern/blob/master/config/lern.php) to see how they are used.

## Migrating from `2.x` to `3.x`
Version 3.x introduces the ability to collect more information from the error such as the user_id, url, method, and input data. In order to use 3.x you will need to copy over the new [config file](https://github.com/tylercd100/lern/blob/master/config/lern.php), the migration file and then migrate it.
```php
# This will only copy over the migration file. For the config file you can either include the --force flag (Which will overwrite it) or copy it manually from github 
php artisan vendor:publish --provider="Tylercd100\LERN\LERNServiceProvider"
php artisan migrate
```

## Installation

Version 4.x uses [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery). If you are using 3.x you will need to follow these [instructions.](https://github.com/tylercd100/lern/tree/3.8.2)

Install via [composer](https://getcomposer.org/) - In the terminal:
```bash
composer require tylercd100/lern
```

Then you will need to run these commands in the terminal in order to copy the config and migration files
```bash
php artisan vendor:publish --provider="Tylercd100\LERN\LERNServiceProvider"
```

Before you run the migration you may want to take a look at `config/lern.php` and change the `table` property to a table name that you would like to use. After that run the migration 
```bash
php artisan migrate
```

## Usage
To use LERN modify the report method in the `app/Exceptions/Handler.php` file
```php
public function report(Exception $e)
{
    if ($this->shouldReport($e)) {
    
    	//Check to see if LERN is installed otherwise you will not get an exception.
        if (app()->bound("lern")) {
            app()->make("lern")->handle($e); //Record and Notify the Exception

            /*
            OR...
            app()->make("lern")->record($e); //Record the Exception to the database
            app()->make("lern")->notify($e); //Notify the Exception
            */
        }
    }
	
    return parent::report($e);
}
```

Dont forget to add this to the top of the file 
```php
//If you updated your aliases array in "config/app.php"
use LERN;
//or if you didnt...
use Tylercd100\LERN\Facades\LERN;
```

### Recording
You can call `LERN::record($exception);` to record an Exception to the database.
To query any Exception that has been recorded you can use `ExceptionModel` which is an Eloquent Model
```php
use Tylercd100\LERN\Models\ExceptionModel;
$mostRecentException = ExceptionModel::orderBy('created_at','DESC')->first();
```

To change what is recorded in to the database take a look at `config/lern.php`
```php
'record'=>[
    /**
     * The Model to use
     */
    'model' => \Tylercd100\LERN\Models\ExceptionModel::class,

    /**
     * Database connection to use. Null is the default connection.
     */
    'connection'=>null,

    /**
     * Database table to use
     */
    'table'=>'vendor_tylercd100_lern_exceptions',

    /**
     * Information to store
     */
	'collect'=>[
	    'method'=>false, //When true it will collect GET, POST, DELETE, PUT, etc...
	    'data'=>false, //When true it will collect Input data
	    'status_code'=>true,
	    'user_id'=>false,
	    'url'=>false,
        'ip'=>false,
	],
],
```
Note: If you change `lern.recorder.model` then `lern.recorder.table` and `lern.recorder.connection` will be ignored unless you extend `\Tylercd100\LERN\Models\ExceptionModel::class`

### Notifications
LERN uses the Monolog library to send notifications. If you need more than the supported notification channels, then you can add your own custom Monolog handlers. To start using any of the supported handlers just edit the provided config file `config/lern.php`.


#### Changing the log level programmatically
Some notification services support different log levels. If changing the config value `lern.notify.log_level` is not enough then try it this way:
```php
// Change the log level. 
// Default is: critical
// Options are: debug, info, notice, warning, error, critical, alert, emergency
LERN::setLogLevel("emergency");
```

#### Changing the subject line
Some notification services support a subject line, this is how you change it.
```php
//Change the subject
LERN::setSubject("An Exception was thrown!");
```

#### Changing the body of the notification
LERN publishes a default blade template file that you can find at `resources/views/exceptions/default.blade.php`.
The blade template file is compiled with these values: `$exception` `$url` `$method` `$data` `$user`.
To specify a different blade template file, just edit the config file
```php
'notify'=>[
    'view'=>'exceptions.default',
],
```
##### (deprecated) Using the `LERN::setMessage()` function
Make sure that you set the view config value to null or the `LERN::setMessage()` will not work
```php
'notify'=>[
    'view'=>null,
],
```

#### Custom Monolog Handlers
To use a custom Monolog Handler call the `pushHandler` method
```php
use Monolog\Handler\HipChatHandler;
$handler = new HipChatHandler($token,$room);
LERN::pushHandler($handler);
LERN::notify($exception);
```

## Further Reading and How-Tos
- [Creating relationships between Exceptions and Users](https://github.com/tylercd100/lern/wiki/Creating-relationships-between-exceptions-and-users)

## Roadmap
- Support more Monolog Handlers
- Exception report page or command to easily identify your application's issues.
- Notification rate limiting and/or grouping. 
