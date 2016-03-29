<?php

namespace Lightools\Tests;

use Lightools\BitbangLogger\PostDataDumper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class PostDataDumperTest extends TestCase {

    public function testDump() {
        $data = [
            'key1' => 'value',
            'key2' => [
                'deepkey1' => 'deepvalue',
                'deepkey2' => [
                    'deepdeepvalue'
                ]
            ],
        ];

        $indent = '  ';
        $dumper = new PostDataDumper($indent);
        $result = $dumper->toString($data);

        $expected = <<<EOS
key1 => value
key2 => 
  deepkey1 => deepvalue
  deepkey2 => 
    0 => deepdeepvalue

EOS;
        Assert::same($expected, $result);
    }

}

(new PostDataDumperTest)->run();
