<?php

namespace Lightools\BitbangLogger;

use CURLFile;

/**
 * @author Jan Nedbal
 */
class PostDataDumper {

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
     * Dump classic POST data as array (CURLFile handling included)
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

            } elseif ($value instanceof CURLFile) {
                $content .= $prefix . file_get_contents($value->getFilename()) . "\n";

            } else {
                $content .= $prefix . $value . "\n";
            }
        }

        return $content;
    }

}
