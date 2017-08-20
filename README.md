#  Core Location

[![Latest Version](https://img.shields.io/packagist/v/tuupola/corelocation.svg?style=flat-square)](https://packagist.org/packages/tuupola/corelocation)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/corelocation/master.svg?style=flat-square)](https://travis-ci.org/tuupola/corelocation)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/corelocation.svg?style=flat-square)](https://codecov.io/github/tuupola/corelocation)

Proof of concept PHP implementation of [Apple location services protocol](https://appelsiini.net/2017/reverse-engineering-location-services/). This library does **not** do any actual requests. It is used only for creating and parsing requests and responses.


## Install

Install the library using [Composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/corelocation
```
## Usage
### Request

Request class returns a binary string. It is up to reader to figure out what to do with it.

```php
require __DIR__ . "/vendor/autoload.php";

use Tuupola\CoreLocation\Request;

$request = new Request(["aa:aa:aa:aa:aa:aa", "bb:bb:bb:bb:bb:bb"]);
$hex = bin2hex($request->body());
print_r(str_split($hex, 64));

/*
Array
(
    [0] => 00010005656e5f55530013636f6d2e6170706c652e6c6f636174696f6e64000c
    [1] => 382e342e312e313248333231000000010000002c12130a1161613a61613a6161
    [2] => 3a61613a61613a616112130a1162623a62623a62623a62623a62623a62622064
)
*/
```
### Response

Respons class can be used for unserializing the response. It is up to reader to figure out how to get a response.

```php
require __DIR__ . "/vendor/autoload.php";

use Tuupola\CoreLocation\Response;

$data = file_get_contents("response.bin");
$response = (new Response)->fromString($data);

foreach ($response as $router) {
    print_r($router);
}

/*
Array
(
    [mac] => cc:cc:cc:cc:cc:cc
    [latitude] => 27.98785,
    [longitude] => 86.9228374
    [accuracy] => 42
    [channel] => 10
)
...
*/
```


## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ make test
```
``` bash
$ brew install entr
$ make watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.