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

Currently supported notification channels via [tylercd100/laravel-notify](https://github.com/tylercd100/laravel-notify)
- Email
- [Pushover](https://pushover.net/)
- [Slack](https://slack.com/)
- [Hipchat](https://www.hipchat.com/)
- [Fleephook](https://fleep.io/)
- [Flowdock](https://www.flowdock.com/)
- [Plivo](https://www.plivo.com/) an SMS messaging service.
- [Twilio](https://www.twilio.com/) an SMS messaging service.

## Migrating from `2.x` to `3.x`
Version 3.x introduces the ability to collect more information from the error such as the user_id, url, method, and input data. In order to use 3.x you will need to copy over the new [config file](https://github.com/tylercd100/lern/blob/master/config/lern.php), the migration file and then migrate it.
```php
# This will only copy over the migration file. For the config file you can either include the --force flag (Which will overwrite it) or copy it manually from github 
php artisan vendor:publish --provider="Tylercd100\LERN\LERNServiceProvider"
php artisan migrate
```

## Installation

Install via [composer](https://getcomposer.org/) - In the terminal:
```bash
composer require tylercd100/lern
```

Now add the following to the `providers` array in your `config/app.php`
```php
Tylercd100\LERN\LERNServiceProvider::class
```

and this to the `aliases` array in `config/app.php`
```php
"LERN" => "Tylercd100\LERN\Facades\LERN",
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
	    LERN::handle($e); //Record and Notify the Exception
	    /*
	    OR...
	    LERN::record($e); //Record the Exception to the database
	    LERN::notify($e); //Notify the Exception
	    */
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
	'table'=>'vendor_tylercd100_lern_exceptions',
	'collect'=>[
	    'method'=>false, //When true it will collect GET, POST, DELETE, PUT, etc...
	    'data'=>false, //When true it will collect Input data
	    'status_code'=>true,
	    'user_id'=>false,
	    'url'=>false,
	],
],
```

### Notifications
LERN uses the Monolog library to send notifications. If you need more than the supported notification channels, then you can add your own custom Monolog handlers. To start using any of the supported handlers just edit the provided config file `config/lern.php`.

#### Custom Monolog Handlers
To use a custom Monolog Handler call the `pushHandler` method
```php
use Monolog\Handler\HipChatHandler;
$handler = new HipChatHandler($token,$room);
LERN::pushHandler($handler);
LERN::notify($exception);
```

#### Changing the text
```php
//Change the subject
LERN::setSubject("An Exception was thrown!");

//Change the message body
LERN::setMessage(function($exception){
    $msg = "";

    //Get the route
    $url = Request::url();
    $method = Request::method();
    if($url) {
        $msg.="URL: {$method}@{$url}".PHP_EOL;
    }

    //Get the User
    $user = Auth::user();
    if($user) {
        $msg.="User: #{$user->id} {$user->first_name} {$user->last_name}".PHP_EOL;
    }

    //Exception
    $msg.=get_class($exception).":{$exception->getFile()}:{$exception->getLine()} {$exception->getMessage()}".PHP_EOL;

    //Input
    $input = Input::all();
    if(!empty($input)){
        $msg.="Data: ".json_encode($input).PHP_EOL;
    }

    //Trace
    $msg.=PHP_EOL."Trace: {$exception->getTraceAsString()}";
    return $msg;
});

LERN::notify($e); //Notify the Exception

```

## Further Reading and How-Tos
- [Creating relationships between Exceptions and Users](https://github.com/tylercd100/lern/wiki/Creating-relationships-between-exceptions-and-users)

## Roadmap
- Support more Monolog Handlers
- Exception report page or command to easily identify your application's issues.
- Notification rate limiting and/or grouping. 
