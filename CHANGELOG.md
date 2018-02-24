# Changelog

All notable changes to `LERN` will be documented in this file.

### 4.4.1
- Prevent view from being output to the console

### 4.4.0
- Added ability to use a custom Model
- Added ability to change database connection for the default Model
- Added `lern.recorder.model` and `lern.recorder.connection` to the config

### 4.3.0
- Added setLogLevel and getLogLevel functions

### 4.2.2
- Backwards compatibility fix for custom Recorder/Notifier classes

### 4.2.1
- Fixed Auto Package Discovery

### 4.2.0
- Added the ability to use Custom Recorder and Notifier classes
- Added IP option to the config to collect IP addresses

### 4.1.1
- Small typo

### 4.1.0
- Auto Package Discovery

### 4.0.0
- Added support for Laravel 5.5
- Removed support for Laravel 5.4, 5.3, 5.2, 5.1 (Please use 3.x)

### 3.8.2
- Lowered version requirement for php to 5.5.9

### 3.8.1
- Lowered version requirement for orchestra/testbench which was preventing laravel 5.1 from installing

### 3.8.0
- Fixes [#44](https://github.com/tylercd100/lern/issues/44)
- Support for Laravel 5.1

### 3.7.5
- Fixed [#42](https://github.com/tylercd100/lern/issues/42)

### 3.7.4
- Updated to tylercd100/laravel-notify@^1.8.5 which sets the content type of SMTP emails to text/html instead of text/plain

### 3.7.3
- Changed minimum phpunit version requirement

### 3.7.2
- Fixed a typo in one of the unit tests

### 3.7.1
- Quick fix! The system would seize with no exception

### 3.7.0
- Added the ability to use a blade template
- Blade templates are now the default way to style your exception notification

### 3.6.6
- Updated to tylercd100/laravel-notify@^1.8.4 which allows newline characters in Fleephook, Hipchat, Pushover, Raven, and Slack.

### 3.6.5
- Updated to tylercd100/laravel-notify@^1.8.2 which sets the content type of emails to text/html instead of text/plain

### 3.6.4
- Updated to tylercd100/laravel-notify@^1.8.1 which allows newline characters in emails

### 3.6.3
- Ignore columns that are null

### 3.6.2
- Fixed Tests for Laravel 5.2

### 3.6.1
- Fixed Tests for Laravel 5.3

### 3.6.0
- Added a log_level option in the config to set the desired log level

### 3.5.0
- Added Mailgun support

### 3.4.0
- Set context using a callback/closure (Thanks to [@qodeboy](https://github.com/qodeboy) for suggestion)

### 3.3.1
- Added default config values for Raven/Sentry

### 3.3.0
- Extracted notification functions into its [own package](https://github.com/tylercd100/laravel-notify)

### 3.2.2
- Accidently forgot to merge PR for 3.2.1, this release has the 3.2.1 fixes

### 3.2.1
- Fixed issue when trying to store an exception code that is not an integer

### 3.2.0
- Added option to remove certain keys from the input data. Please look at the excludeKeys options in the new [config file](https://github.com/tylercd100/lern/blob/3.2.0/config/lern.php)

### 3.1.4
- Check if the exception code is an integer

### 3.1.3
- Use Attribute Casting in Exception Model

### 3.1.2
- Check to make sure 'smtp' config value is set before checking its value

### 3.1.1
- Added Try/Catch statements to prevent infinite loops

### 3.1.0
- Added SMTP support

### 3.0.2
- Fixed issue with rolling back migration files

### 3.0.1
- Fixed Pushover sounds

### 3.0.0
- When enabled in the config file you can now collect:
    - user_id - The id of the currently logged in user.
    - method - Then method of the request: GET, POST, DELETE, PUT, etc...
    - url - The full URL of the request.
    - data - The input data of the request, if any.

*__Reason for Major release: 3.0.0 introduces a new migration file and structure changes that could cause issues for 2.x users__*

### 2.3.0
- Added support for [Twilio](https://www.twilio.com/) an SMS messaging service.

### 2.2.2
- Fixed config for Plivo

### 2.2.1
- Changed dependancy from `tylercd100/monolog-plivo` to `tylercd100/monolog-sms` which is the same package with a different name

### 2.2.0
- Added support for [Plivo](https://www.plivo.com/) an SMS messaging service.

### 2.1.3
- Scutinizer Code Coverage
- Fixed Docblocks
- Added more Unit Tests (60% -> 94% Coverage)
- Changed dev dependencies

### 2.1.2
- Improved config file with env functions and comments. 
- Fixed some handlers by increasing the log level from ERROR to CRITICAL

### 2.1.1
- Fixed HipChat by making it use api v2

### 2.1.0
- Added Hipchat, Flowdock and Fleephook support

### 2.0.0
- Added a `LERN` facade
- Monolog\Logger Support
    - With the ability to use custom Logger and Handler instances
- Custom table name for migration (see the new config file)

### 1.1.0
- Added Slack

### 1.0.0
- Initial release and connected with packagist
