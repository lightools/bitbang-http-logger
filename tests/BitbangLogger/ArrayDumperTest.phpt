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

        $expected = "key1 => value\n";
        $expected .= "key2 => \n";
        $expected .= "  deepkey1 => deepvalue\n";
        $expected .= "  deepkey2 => \n";
        $expected .= "    0 => deepdeepvalue\n";

        Assert::same($expected, $result);
    }

}

(new ArrayDumperTest)->run();
