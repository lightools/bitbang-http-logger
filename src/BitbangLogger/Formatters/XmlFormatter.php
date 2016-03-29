<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;
use Lightools\Xml\XmlException;
use Lightools\Xml\XmlLoader;

/**
 * @author Jan Nedbal
 */
class XmlFormatter implements IFormatter {

    /**
     * @var XmlLoader
     */
    private $xmlLoader;

    public function __construct(XmlLoader $xmlLoader) {
        $this->xmlLoader = $xmlLoader;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function canFormat(Message $message) {
        $body = $message->getBody();
        $contentType = $message->getHeader('content-type');
        return !is_array($body) && strpos($contentType, 'xml') !== FALSE;
    }

    /**
     * @param string $body
     * @return string
     */
    public function format($body) {

        try {
            $dom = $this->xmlLoader->loadXml($body);
            $dom->formatOutput = TRUE;
            return $dom->saveXML();

        } catch (XmlException $e) {
            return $body;
        }
    }

}
