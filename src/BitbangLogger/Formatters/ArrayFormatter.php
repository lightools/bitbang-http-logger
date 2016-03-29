<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use CURLFile;
use Lightools\BitbangLogger\ArrayDumper;

/**
 * @author Jan Nedbal
 */
class ArrayFormatter implements IFormatter {

    /**
     * @var ArrayDumper
     */
    private $arrayDumper;

    public function __construct(ArrayDumper $arrayDumper) {
        $this->arrayDumper = $arrayDumper;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function canFormat(Message $message) {
        $body = $message->getBody();
        return is_array($body);
    }

    /**
     * @param array $body
     * @return string
     */
    public function format($body) {
        array_walk_recursive($body, function (& $value) {
            if ($value instanceof CURLFile) {
                $value = file_get_contents($value->getFilename());
            }
        });
        return $this->arrayDumper->toString($body);
    }

}
