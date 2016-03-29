<?php

namespace Lightools\Tests;

use Bitbang\Http\Message;
use DOMDocument;
use Lightools\BitbangLogger\Formatters\XmlFormatter;
use Lightools\Xml\XmlLoader;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class XmlFormatterTest extends TestCase {

    public function testCanFormat() {
        $arrayMessage = Mockery::mock(Message::class);
        $arrayMessage->shouldReceive('getBody')->once()->andReturn([]);
        $arrayMessage->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage1 = Mockery::mock(Message::class);
        $stringMessage1->shouldReceive('getBody')->once()->andReturn('<root/>');
        $stringMessage1->shouldReceive('getHeader')->once()->with('content-type')->andReturnNull();

        $stringMessage2 = Mockery::mock(Message::class);
        $stringMessage2->shouldReceive('getBody')->once()->andReturn('<root/>');
        $stringMessage2->shouldReceive('getHeader')->once()->with('content-type')->andReturn('application/soap+xml; charset=utf-8');

        $xmlLoader = Mockery::mock(XmlLoader::class);
        $formatter = new XmlFormatter($xmlLoader);

        Assert::false($formatter->canFormat($arrayMessage));
        Assert::false($formatter->canFormat($stringMessage1));
        Assert::true($formatter->canFormat($stringMessage2));
    }

    public function testFormat() {
        $xml = '<root><key1>value</key1><key2><deepkey>deepvalue</deepkey></key2></root>';

        $dom = new DOMDocument();
        $dom->loadXml($xml);

        $xmlLoader = Mockery::mock(XmlLoader::class);
        $xmlLoader->shouldReceive('loadXml')->once()->with($xml)->andReturn($dom);

        $formatter = new XmlFormatter($xmlLoader);

        $expected = <<<EOS
<?xml version="1.0"?>
<root>
  <key1>value</key1>
  <key2>
    <deepkey>deepvalue</deepkey>
  </key2>
</root>

EOS;

        Assert::same($expected, $formatter->format($xml));
    }

    protected function tearDown() {
        parent::tearDown();

        Mockery::close();
    }

}

(new XmlFormatterTest)->run();
