<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use Lightools\BitbangLogger\PostDataDumper;

/**
 * @author Jan Nedbal
 */
class ArrayFormatter implements IFormatter {

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
        return is_array($body);
    }

    /**
     * @param array $body
     * @return string
     */
    public function format($body) {
        return $this->postDataDumper->toString($body);
    }

}
