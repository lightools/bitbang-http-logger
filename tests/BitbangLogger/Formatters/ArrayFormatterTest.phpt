<?php

namespace Lightools\Tests;

use Bitbang\Http\Message;
use CURLFile;
use Lightools\BitbangLogger\ArrayDumper;
use Lightools\BitbangLogger\Formatters\ArrayFormatter;
use Mockery;
use Tester\Assert;
use Tester\FileMock;
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

        $dumper = Mockery::mock(ArrayDumper::class);
        $formatter = new ArrayFormatter($dumper);

        Assert::true($formatter->canFormat($arrayMessage));
        Assert::false($formatter->canFormat($stringMessage));
    }

    public function testFormat() {
        $curlFile = Mockery::mock(CURLFile::class);
        $curlFile->shouldAllowMockingMethod('getFilename');
        $curlFile->shouldReceive('getFilename')->once()->andReturn(FileMock::create('file contents'));
        $array = [
            'key1' => 'value',
            'key2' => $curlFile,
        ];
        $arrayChecker = function (array $array) {
            Assert::same('value', $array['key1']);
            Assert::same('file contents', $array['key2']);
            return TRUE;
        };
        $dumper = Mockery::mock(ArrayDumper::class);
        $dumper->shouldReceive('toString')->once()->with(Mockery::on($arrayChecker))->andReturn($formattedArray = '[]');
        $formatter = new ArrayFormatter($dumper);

        Assert::same($formattedArray, $formatter->format($array));
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new ArrayFormatterTest)->run();
