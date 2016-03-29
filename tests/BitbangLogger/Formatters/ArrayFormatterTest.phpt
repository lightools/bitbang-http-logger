<?php

namespace Lightools\Tests;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\Formatters\ArrayFormatter;
use Lightools\BitbangLogger\PostDataDumper;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class ArrayFormatterTest extends TestCase {

    public function testCanFormat() {
        $stringMessage = Mockery::mock(Message::class);
        $stringMessage->shouldReceive('getBody')->once()->andReturn('[]');

        $arrayMessage = Mockery::mock(Message::class);
        $arrayMessage->shouldReceive('getBody')->once()->andReturn([]);

        $dumper = Mockery::mock(PostDataDumper::class);
        $formatter = new ArrayFormatter($dumper);

        Assert::true($formatter->canFormat($arrayMessage));
        Assert::false($formatter->canFormat($stringMessage));
    }

    public function testFormat() {
        $array = [];
        $dumper = Mockery::mock(PostDataDumper::class);
        $dumper->shouldReceive('toString')->once()->with($array)->andReturn($formattedArray = '[]');
        $formatter = new ArrayFormatter($dumper);

        Assert::same($formattedArray, $formatter->format($array));
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new ArrayFormatterTest)->run();
