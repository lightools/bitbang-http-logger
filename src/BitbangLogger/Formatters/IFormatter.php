<?php

namespace Lightools\BitbangLogger\Formatters;

use Bitbang\Http\Message;

/**
 * @author Jan Nedbal
 */
interface IFormatter {

    /**
     * @param Message $message HTTP request or response
     * @return bool
     */
    public function canFormat(Message $message);

    /**
     * @param string|array $body HTTP body
     * @return string
     */
    public function format($body);

}
