<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\PostDataDumper;

/**
 * @author Jan Nedbal
 */
class UrlEncodedFormatter implements IFormatter {

    /**
     * @var PostDataDumper
     */
    private $postDataDumper;

    public function __construct(PostDataDumper $arrayDumper) {
        $this->postDataDumper = $arrayDumper;
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
        return $this->postDataDumper->toString($data);
    }

}
