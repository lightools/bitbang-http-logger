## Introduction

This library allows you to log all HTTP traffic performed via clients in bitbang/http library.

## Installation

```sh
$ composer require lightools/bitbang-http-logger
```

## Basic usage

```php
$writer = new Lightools\BitbangLogger\Writers\DefaultWriter(__DIR__ . '/logs/http');
$logger = new Lightools\BitbangLogger\HttpLogger($writer);

$client = new Bitbang\Http\Clients\CurlClient();
$client->onRequest([$logger, 'onRequest']);
$client->onResponse([$logger, 'onResponse']);

$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=Praha&destinations=Brno';
$request = new Bitbang\Http\Request(Bitbang\Http\Request::GET, $url);

try {
    $client->process($request);
} catch (Bitbang\Http\BadResponseException $e) {
    // process exception
}
```

### Logging

Example above will log into file ```./logs/http/2016-03-22/17-25-00-44956900_56fa569cc8c8d.txt``` (contents shown below).
You can see that request and response are in the same file and some timestamps are included above both HTTP messages.
This behavior is done by ```DefaultWriter``` and you can easily change it by implementing own ```IWritter```
(you may want to save HTTP request and response separately, change the file naming or store logs in database).
DefaultWriter also saves whole HTTP communication when some redirect occurs - all subsequent HTTP requests will be stored in one file.

```
(17-25-00-44956900)
GET https://maps.googleapis.com/maps/api/distancematrix/json?origins=Praha&destinations=Brno HTTP/1.1
expect:
connection: keep-alive
user-agent: Bitbang/0.3.0 (cURL)


(17-25-00-77731500)
HTTP/1.1 200
content-type: application/json; charset=UTF-8
date: Tue, 22 Mar 2016 16:25:27 GMT
expires: Wed, 23 Mar 2016 16:25:27 GMT
cache-control: public, max-age=86400
content-encoding: gzip
server: mafe
x-xss-protection: 1; mode=block
x-frame-options: SAMEORIGIN
alternate-protocol: 443:quic,p=1
alt-svc: quic=":443"; ma=2592000; v="31,30,29,28,27,26,25"
transfer-encoding: chunked

{
   "destination_addresses" : [ "Brno, Česká republika" ],
   "origin_addresses" : [ "Praha, Česká republika" ],
   "rows" : [
      {
         "elements" : [
            {
               "distance" : {
                  "text" : "205 km",
                  "value" : 205461
               },
               "duration" : {
                  "text" : "2 hod, 0 min",
                  "value" : 7217
               },
               "status" : "OK"
            }
         ]
      }
   ],
   "status" : "OK"
}
```

## Formatting

By default, HTTP body is printed to log as it was set to Request or Response object,
but you can register formatters to make it prettier and more readable.
All you need is to implement ```IFormatter``` interface and register it in logger.
This library is shipped with few handy formatters - for array (typical POST), JSON, XML or for urlencoded HTTP body.
ArrayFormatter also supports quite common CURLFile.

The first formatter that is able to format HTTP message is used, so theoretically it may matter in which order you register them.
Here is example how to use all default formatters:

```php
$xmlLoader = new Lightools\Xml\XmlLoader();
$arrayDumper = new Lightools\BitbangLogger\PostDataDumper();

$arrayFormatter = new Lightools\BitbangLogger\Formatters\ArrayFormatter($arrayDumper);
$urlFormatter = new Lightools\BitbangLogger\Formatters\UrlEncodedFormatter($arrayDumper);
$jsonFormatter = new Lightools\BitbangLogger\Formatters\JsonFormatter(); // requires nette/utils to work
$xmlFormatter = new Lightools\BitbangLogger\Formatters\XmlFormatter($xmlLoader); // requires lightools/xml to work

$writer = new Lightools\BitbangLogger\Writers\DefaultWriter(__DIR__ . '/logs/http');
$logger = new Lightools\BitbangLogger\HttpLogger($writer);
$logger->registerFormatter($arrayFormatter);
$logger->registerFormatter($urlFormatter);
$logger->registerFormatter($jsonFormatter);
$logger->registerFormatter($xmlFormatter);
```

## Limitations

Logged HTTP headers may not contain all really sent headers
because some of them might be modified by proxies or even by curl library itself (e.g. very handy CURLOPT_ENCODING).

## How to run tests

```sh
$ vendor/bin/tester tests
```
