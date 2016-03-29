<?php

namespace Lightools\Tests;

use Bitbang\Http\Request;
use Bitbang\Http\Response;
use Lightools\BitbangLogger\Formatters\IFormatter;
use Lightools\BitbangLogger\HttpLogger;
use Lightools\BitbangLogger\Writers\IWriter;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class HttpLoggerTest extends TestCase {

    public function testRequest() {

        $requestHeaders = [
            'accept' => ['application/json', 'application/xml'],
            'accept-encoding' => ['gzip']
        ];

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getMultiHeaders')->once()->andReturn($requestHeaders);
        $request->shouldReceive('getBody')->once()->andReturnNull();
        $request->shouldReceive('getMethod')->once()->andReturn(Request::GET);
        $request->shouldReceive('getUrl')->once()->andReturn('https://example.com');

        $requestBodyMatcher = function ($body) {
            $expected = "GET https://example.com HTTP/1.1\n";
            $expected .= "accept: application/json\n";
            $expected .= "accept: application/xml\n";
            $expected .= 'accept-encoding: gzip';

            Assert::same($expected, trim($body));
            return TRUE;
        };

        $writer = Mockery::mock(IWriter::class);
        $writer->shouldReceive('write')->once()->with($request, Mockery::on($requestBodyMatcher));

        $logger = new HttpLogger($writer);
        $logger->onRequest($request);
    }

    public function testResponse() {

        $responseBody = '{ "it works?" : true }';
        $responseHeaders = [
            'content-type' => ['application/json']
        ];

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('getMultiHeaders')->once()->andReturn($responseHeaders);
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);
        $response->shouldReceive('getCode')->once()->andReturn(Response::S200_OK);

        $responseBodyMatcher = function ($body) use ($responseBody) {
            $expected = "HTTP/1.1 200\n";
            $expected .= "content-type: application/json\n";
            $expected .= "\n";
            $expected .= $responseBody;

            Assert::same($expected, trim($body));
            return TRUE;
        };

        $writer = Mockery::mock(IWriter::class);
        $writer->shouldReceive('write')->once()->with($response, Mockery::on($responseBodyMatcher));

        $logger = new HttpLogger($writer);
        $logger->onResponse($response);
    }

    public function testFormatters() {

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getBody')->andReturn($requestBody = 'body');
        $request->shouldReceive('getMultiHeaders')->andReturn([]);
        $request->shouldIgnoreMissing();

        $writer = Mockery::mock(IWriter::class);
        $writer->shouldReceive('write')->once();

        $formatter1 = Mockery::mock(IFormatter::class);
        $formatter1->shouldReceive('canFormat')->once()->with($request)->andReturn(FALSE);

        $formatter2 = Mockery::mock(IFormatter::class);
        $formatter2->shouldReceive('canFormat')->once()->with($request)->andReturn(FALSE);

        $formatter3 = Mockery::mock(IFormatter::class);
        $formatter3->shouldReceive('canFormat')->once()->with($request)->andReturn(TRUE);
        $formatter3->shouldReceive('format')->once()->with($requestBody)->andReturn('formatted body');

        $formatter4 = Mockery::mock(IFormatter::class);
        $formatter4->shouldNotReceive('canFormat');

        $logger = new HttpLogger($writer);
        $logger->registerFormatter($formatter1);
        $logger->registerFormatter($formatter2);
        $logger->registerFormatter($formatter3);
        $logger->registerFormatter($formatter4);

        Assert::noError(function () use ($logger, $request) {
            $logger->onRequest($request);
        });
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new HttpLoggerTest)->run();
