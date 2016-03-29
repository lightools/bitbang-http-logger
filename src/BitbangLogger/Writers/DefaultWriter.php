<?php

namespace Lightools\BitbangLogger\Writers;

use Bitbang\Http\Message;
use Bitbang\Http\Request;

/**
 * @author Jan Nedbal
 */
class DefaultWriter implements IWriter {

    /**
     * @var string
     */
    const CHAIN_ID_HEADER = 'X-Chain-Id';

    /**
     * @var string
     */
    private $chainId;

    /**
     * @var string
     */
    private $logDir;

    /**
     * @param string $logDir
     */
    public function __construct($logDir) {
        $this->logDir = $logDir;
    }

    /**
     * @param Message $message
     * @param string $contents
     * @return string Path to written log file
     */
    public function write(Message $message, $contents) {

        $time = $this->getCurrentTime();

        if ($message instanceof Request) {
            $this->setupChainId($message, $time);
        }

        $file = "$this->logDir/$this->chainId.txt";
        $contents = "($time)\n" . $contents;

        @mkdir(dirname($file), 0777, TRUE); // @ - dir may exist
        file_put_contents($file, $contents, FILE_APPEND);

        return $file;
    }

    /**
     * @param Request $request
     * @param string $currentTime
     */
    private function setupChainId(Request $request, $currentTime) {

        if (!$request->hasHeader(self::CHAIN_ID_HEADER)) {
            $requestId = date('Y-m-d') . '/' . $currentTime . '_' . uniqid();
            $request->setHeader(self::CHAIN_ID_HEADER, $requestId);
            $this->chainId = $requestId;
        } else {
            $this->chainId = $request->getHeader(self::CHAIN_ID_HEADER);
        }
    }

    /**
     * @return string
     */
    private function getCurrentTime() {
        list($microseconds) = explode(' ', microtime());
        return date('H-i-s-') . substr($microseconds, 2);
    }

}
