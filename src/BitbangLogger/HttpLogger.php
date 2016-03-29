<?php

namespace Lightools\BitbangLogger;

use Bitbang\Http\Message;
use Bitbang\Http\Request;
use Bitbang\Http\Response;
use Lightools\BitbangLogger\Formatters\IFormatter;
use Lightools\BitbangLogger\Writers\IWriter;

/**
 * @author Jan Nedbal
 */
class HttpLogger {

    /**
     * @var IWriter
     */
    private $writer;

    /**
     * @var IFormatter[]
     */
    private $formatters = [];

    /**
     * @param IWriter $writer
     */
    public function __construct(IWriter $writer) {
        $this->writer = $writer;
    }

    /**
     * @param IFormatter $formatter
     */
    public function registerFormatter(IFormatter $formatter) {
        $this->formatters[] = $formatter;
    }

    /**
     * @param Request
     */
    public function onRequest(Request $request) {
        $http = $request->getMethod() . ' ' . $request->getUrl() . ' HTTP/1.1';
        $content = $this->getFormattedMessage($http, $request);

        $this->writer->write($request, $content);
    }

    /**
     * @param Response $response
     */
    public function onResponse(Response $response) {
        $http = 'HTTP/1.1 ' . $response->getCode();
        $content = $this->getFormattedMessage($http, $response);

        $this->writer->write($response, $content);
    }

    /**
     * @param string $http First line of HTTP message
     * @param Message $message
     * @return string
     */
    private function getFormattedMessage($http, Message $message) {
        $head = $this->getFormattedHeaders($message->getMultiHeaders());
        $body = $this->getFormattedBody($message);
        return $http . "\n" . $head . "\n\n" . $body . "\n\n";
    }

    /**
     * @param array $multiHeaders
     * @return string
     */
    private function getFormattedHeaders(array $multiHeaders) {
        $data = [];
        foreach ($multiHeaders as $name => $headers) {
            foreach ($headers as $header) {
                $data[] = "$name: $header";
            }
        }

        return implode("\n", $data);
    }

    /**
     * @param Message $message
     * @return string
     */
    private function getFormattedBody(Message $message) {
        $body = $message->getBody();

        if (!$body) {
            return '';
        }

        foreach ($this->formatters as $formatter) {
            if ($formatter->canFormat($message)) {
                return $formatter->format($body);
            }
        }

        return is_array($body) ? json_encode($body, JSON_PRETTY_PRINT) : $body;
    }

}
