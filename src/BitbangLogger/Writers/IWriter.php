<?php

namespace Lightools\BitbangLogger\Writers;

use Bitbang\Http\Message;

/**
 * @author Jan Nedbal
 */
interface IWriter {

    /**
     * @param Message $message
     * @param string $contents
     */
    public function write(Message $message, $contents);

}
