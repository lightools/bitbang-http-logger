<?php

namespace Lightools\BitbangLogger;

/**
 * @author Jan Nedbal
 */
class ArrayDumper {

    /**
     * @var string
     */
    private $indent;

    /**
     * @param string $indent
     */
    public function __construct($indent = '    ') {
        $this->indent = $indent;
    }

    /**
     * @param array $data
     * @param int $indent Level of current dump
     * @return string
     */
    public function toString(array $data, $indent = 0) {
        $content = '';
        $indentString = str_repeat($this->indent, $indent);

        foreach ($data as $key => $value) {
            $prefix = $indentString . $key . ' => ';

            if (is_array($value)) {
                $content .= $prefix . "\n" . $this->toString($value, $indent + 1);
            } else {
                $content .= $prefix . $value . "\n";
            }
        }

        return $content;
    }

}
