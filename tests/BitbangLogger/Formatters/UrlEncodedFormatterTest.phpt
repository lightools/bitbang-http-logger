<?php

namespace Lightools\Tests;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\ArrayDumper;
use Lightools\BitbangLogger\Formatters\UrlEncodedFormatter;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class UrlEncodedFormatterTest extends TestCase {

    public function testCanFormat() {
        $arrayMessage = Mockery::mock(Message::class);
        $arrayMessage->shouldReceive('getBody')->once()->andReturn([]);
        $arrayMessage->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage1 = Mockery::mock(Message::class);
        $stringMessage1->shouldReceive('getBody')->once()->andReturn('a=b');
        $stringMessage1->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage2 = Mockery::mock(Message::class);
        $stringMessage2->shouldReceive('getBody')->once()->andReturn('a=b');
        $stringMessage2->shouldReceive('getHeader')->once()->with('content-type')->andReturn('application/x-www-form-urlencoded; charset=UTF-8');

        $dumper = Mockery::mock(ArrayDumper::class);
        $formatter = new UrlEncodedFormatter($dumper);

        Assert::false($formatter->canFormat($arrayMessage));
        Assert::false($formatter->canFormat($stringMessage1));
        Assert::true($formatter->canFormat($stringMessage2));
    }

    public function testFormat() {
        $array = [
            'key1' => 'value',
            'key2' => [
                'deepkey' => 'deepvalue'
            ],
        ];
        $body = http_build_query($array);

        $dumper = Mockery::mock(ArrayDumper::class);
        $dumper->shouldReceive('toString')->once()->with($array)->andReturn('dummy');
        $formatter = new UrlEncodedFormatter($dumper);

        Assert::same('dummy', $formatter->format($body));
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new UrlEncodedFormatterTest)->run();
