<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * @author Jan Nedbal
 */
class JsonFormatter implements IFormatter {

    /**
     * @param Message $message
     * @return bool
     */
    public function canFormat(Message $message) {
        $body = $message->getBody();
        $contentType = $message->getHeader('content-type');
        return !is_array($body) && strpos($contentType, 'json') !== FALSE;
    }

    /**
     * @param string $body
     * @return string
     */
    public function format($body) {
        try {
            return Json::encode(Json::decode($body), Json::PRETTY);
        } catch (JsonException $e) {
            return $body;
        }
    }

}
