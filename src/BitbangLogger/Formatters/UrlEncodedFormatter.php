<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\ArrayDumper;

/**
 * @author Jan Nedbal
 */
class UrlEncodedFormatter implements IFormatter {

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
        $contentType = $message->getHeader('content-type');
        return !is_array($body) && strpos($contentType, 'application/x-www-form-urlencoded') !== FALSE;
    }

    /**
     * @param string $body
     * @return string
     */
    public function format($body) {
        $data = [];
        parse_str($body, $data);
        return $this->arrayDumper->toString($data);
    }

}
