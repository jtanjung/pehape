# Install

Via Composer

```
composer require jtanjung/pehape
```

# Contents

This package contain several webdriver executable files which are needed by WebPageService to run its functionalities.
As for the default webdrivers, please refer to these 3 different browser below:

* Chrome v90.x
* Firefox v75.0
* Opera v75.x

## Links

For other version of the webdriver, please follow these links below:

* [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads)
* [GeckoDriver for Firefox](https://github.com/mozilla/geckodriver/releases)
* [OperaDriver](https://github.com/operasoftware/operachromiumdriver/releases)

Note: The version of the browser and its driver must be compatible!

## Example

If you decide to use a different version of the browser and its version, you may use this code below as a reference:

```php
use Pehape\Services\WebPageService;

$service = new WebPageService();
//$service->config->Setting->firefox->command = '{driver folder}/geckodriver'; // Geckodriver use 4444 as a default port
$service->config->Setting->chrome->command = '{driver folder}/chromedriver --port=4443'; // Set port 4443 for chrome
//$service->config->Setting->opera->command = '{driver folder}/operadriver --port=4445'; // Set port 4445 for opera

/** If you decide to use different ports, you also must change the host config value. e.g below: **/
/*
$service->config->Setting->chrome->command = '{driver folder}/chromedriver --port=5000';
//$service->config->Setting->opera->command = '{driver folder}/operadriver --port=5001';
$service->config->Setting->chrome->host = "http://localhost:5000";
//$service->config->Setting->opera->host = "http://localhost:5001";
*/

$service->Chrome()->Create();
$service->get('https://www.google.com/');
$service->quit();
```

See more examples [here](https://github.com/jtanjung/pehape/tree/main/tests)
