<?php
declare(strict_types=1);

namespace Search\Analysis\Analyzer\Common;

use Search\Analysis;


class Text extends AbstractCommon
{
    /**
     * Current position in a stream
     *
     * @var integer
     */
    private $_position;

    /**
     * Reset token stream
     */
    public function reset()
    {
        $this->_position = 0;

        if ($this->_input === null) {
            return;
        }

        // convert input into ascii
        if (PHP_OS != 'AIX') {
            //$this->_input = iconv($this->_encoding, 'ASCII//TRANSLIT', $this->_input);
            $this->input = mb_convert_encoding($str, "ASCII", "UTF-8");
        }
        $this->_encoding = 'ASCII';
    }

    /**
     * Tokenization stream API
     * Get next token
     * Returns null at the end of stream
     *
     * @return Search\Analysis\Token|null
     */
    public function nextToken()
    {
        if ($this->_input === null) {
            return null;
        }

        do {
            if (! preg_match('/[a-zA-Z]+/', $this->_input, $match, PREG_OFFSET_CAPTURE, $this->_position)) {
                // It covers both cases a) there are no matches (preg_match(...) === 0)
                // b) error occured (preg_match(...) === FALSE)
                return null;
            }

            $str = $match[0][0];
            $pos = $match[0][1];
            $endpos = $pos + strlen($str);

            $this->_position = $endpos;

            $token = $this->normalize(new Analysis\Token($str, $pos, $endpos));
        } while ($token === null); // try again if token is skipped

        return $token;
    }
}
