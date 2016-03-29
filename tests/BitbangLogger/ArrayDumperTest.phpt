<?php

namespace Lightools\Tests;

use Lightools\BitbangLogger\ArrayDumper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 * @author Jan Nedbal
 */
class ArrayDumperTest extends TestCase {

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
        $dumper = new ArrayDumper($indent);
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

(new ArrayDumperTest)->run();
