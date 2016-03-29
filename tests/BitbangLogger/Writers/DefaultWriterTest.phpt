<?php

namespace Lightools\Tests;

use Bitbang\Http\Request;
use Bitbang\Http\Response;
use Lightools\BitbangLogger\Writers\DefaultWriter;
use Mockery;
use Nette\Utils\FileSystem;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class DefaultWriterTest extends TestCase {

    /**
     * @var string
     */
    private $logDir;

    protected function setUp() {
        parent::setUp();

        $this->logDir = __DIR__ . '/logs/';
        $this->cleanLogDir();
    }

    public function testWrite() {
        date_default_timezone_set('Europe/Prague');

        $response = Mockery::mock(Response::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->with(DefaultWriter::CHAIN_ID_HEADER)->andReturn(FALSE);
        // $request->shouldReceive('setHeader')->once()->with(DefaultWriter::CHAIN_ID_HEADER, Mockery::type('string')); // https://github.com/padraic/mockery/issues/497

        $writer = new DefaultWriter($this->logDir);
        $path1 = $writer->write($request, $requestMessage = "request1\n");
        $path2 = $writer->write($response, $responseMessage = "response1\n");

        Assert::same($path1, $path2);
        Assert::true(is_file($path1));

        $logContents = file_get_contents($path1);
        Assert::contains($requestMessage, $logContents);
        Assert::contains($responseMessage, $logContents);
    }

    protected function tearDown() {
        parent::tearDown();
        Mockery::close();
        $this->cleanLogDir();
    }

    private function cleanLogDir() {
        FileSystem::delete($this->logDir);
    }

}

(new DefaultWriterTest)->run();
