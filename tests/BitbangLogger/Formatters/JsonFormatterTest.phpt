<?php

namespace Lightools\Tests;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\Formatters\JsonFormatter;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class JsonFormatterTest extends TestCase {

    public function testCanFormat() {
        $arrayMessage = Mockery::mock(Message::class);
        $arrayMessage->shouldReceive('getBody')->once()->andReturn([]);
        $arrayMessage->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage1 = Mockery::mock(Message::class);
        $stringMessage1->shouldReceive('getBody')->once()->andReturn('{}');
        $stringMessage1->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage2 = Mockery::mock(Message::class);
        $stringMessage2->shouldReceive('getBody')->once()->andReturn('{}');
        $stringMessage2->shouldReceive('getHeader')->once()->with('content-type')->andReturn('application/json; charset=UTF-8');

        $formatter = new JsonFormatter();

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
        $body = json_encode($array);
        $formatter = new JsonFormatter();

        $expected = <<<EOS
{
    "key1": "value",
    "key2": {
        "deepkey": "deepvalue"
    }
}
EOS;

        Assert::same($expected, $formatter->format($body));
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new JsonFormatterTest)->run();
