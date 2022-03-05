**[Issues](https://github.com/Codeception/Codeception/issues)** | **[Usage Guide](http://codeception.com/docs/11-Codecoverage)** 

## Remote CodeCoverage for Codeception [![Build Status](https://travis-ci.org/Codeception/c3.svg?branch=2.0)](https://travis-ci.org/Codeception/c3)

This file `c3.php` should be included into the application you are testing in the very first line.
It will start remote code coverage collection. Coverage data will be stored to disk and retrieved by `codeception` when tests from the suite are finished.
This file won't affect your application in any way. It is executed **only** when a special header `X-Codeception-CodeCoverage` is sent. Alternatively, if you use Selenium, special cookie `CODECEPTION_CODECOVERAGE` is used. In any other case your application run as usually with no overheads.

### Local Code Coverage

If you don't run tests on remote server but use a webserver (Apache, Nginx, PhpWebserver) you need `c3.php` installed just the same way.
In this case coverage result will be merged with local code coverage.

### Installation

File `c3.php` should be put in project root, into the same directory where `codeception.yml` config is located.
Also, make sure Codeception is available on remote server either in phar/pear/composer packages.

#### Via Composer

Add to `composer.json`:

```
"require-dev": {
    "codeception/codeception": "3.*",
    "codeception/c3": "2.*"
}
```

C3 installer will copy `c3.php` to the project root.

#### Manually

```
wget https://raw.github.com/Codeception/c3/2.0/c3.php
```

### Setup

Now you should include c3.php in your front script, like `index.php`.

Example file: `web/index.php`:

``` php
<?php

define('C3_CODECOVERAGE_ERROR_LOG_FILE', '/path/to/c3_error.log'); //Optional (if not set the default c3 output dir will be used)
include '/../c3.php';

define('MY_APP_STARTED', true);
// App::start();
?>
```

Now on when is Codeception launched with code coverage enabled you will receive a coverage report from this remote server.

### Configuration

To enable remote (and local) codecoverage by c3.script you should edit global configuration file `codeception.yml`, or one of the suite configuration files.

Example: codeception.yml

``` yml

coverage:
  enabled: true
  remote: true
  include:
    - app/*
  exclude:
    - app/cache/*
```

The `remote` option specifies if you run your application actually runs on another server. If your webserver runs on the same node and uses the same codebase,
disable this option. 

## Predefined Routes

c3 file shouldn't break your application, but there are predefined routes that will be managed by c3.
Codeception will access routes in order to receive collected coverage report in different formats.

* `c3/report/clover`
* `c3/report/serialized`
* `c3/report/html`
* `c3/report/clear`

## Debug

In case you got into troubles and remote debugging still doesn't start you can try the following. Edit `c3.php` file and remove the header check

``` php
// to remove
if (!array_key_exists('HTTP_X_CODECEPTION_CODECOVERAGE', $_SERVER)) {
    return;
}
```
then add this line to the top of file:

``` php
$_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DEBUG'] = 1;
```

now access `http://yourhost/c3/report/clear` url and see if it has errors. Please check that error_reporting is set to E_ALL

## Temp directories

In root of your project `c3tmp` dir will be created during code coverage. 
It will not be deleted after suite ends for testing and debugging purposes.
Serialized data as well as xml and html code coverage reports will be stored there.
