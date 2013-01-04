## Remote CodeCoverage for Codeception

This file `c3.php` should be included into the application you are testing in the very first line.
It will start remote code coverage collection, then save it to disk, and submit it to local server, when testing ends.
This file won't affect your application in any way. It is executed **only** when a special header `X-Codeception-CodeCoverage` is sent. 
If it's not your aplication will continue normal execution.

### Local Code Coverage

If you don't run tests on remote server, but use webserver (Apache, Nginx, PhpWebserver) you need c3.php installed just the same way.
In this case coverage result will be merged with local code coverage.

### Installation

File `c3.php` should be put in project root, into the same directory where `codeception.yml` config is located.
Also, make sure Codeception is available on remote server eiter in phar/pear/composer packages.

```
wget https://raw.github.com/Codeception/c3/master/c3.php
```

Now you should include c3.php in your front script, like `index.php`.

Example file: `web/index.php`:

``` php
<?php

include '/../c3.php';

define('MY_APP_STARTED', true);
App::start();
?>
```

Now when Codeception launches test suite with enabled code coverage you will receive code coverage data from teh remote server.

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
* `c3/report/clean`

## Temp directories

In root of your project `c3tmp` dir will be created during code coverage. 
It will not be deleted after suite ends for testing and debugging purposes.
Serialized data as well as xml and html code coverage reports will be stores there.





