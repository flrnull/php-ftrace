<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Viewer;

class Console {

    const TEXT_CLEAR   = "clear";
    const TEXT_BOLD    = "bold";

    const COLOR_BLACK   = "black";
    const COLOR_RED     = "red";
    const COLOR_GREEN   = "green";
    const COLOR_YELLOW  = "yellow";
    const COLOR_BLUE    = "blue";
    const COLOR_MAGENTA = "magenta";
    const COLOR_CYAN    = "cyan";
    const COLOR_WHITE   = "white";

    private $_colorsMap = array(
        self::COLOR_BLACK   => "\033[30m",
        self::COLOR_RED     => "\033[31m",
        self::COLOR_GREEN   => "\033[32m",
        self::COLOR_YELLOW  => "\033[33m",
        self::COLOR_BLUE    => "\033[34m",
        self::COLOR_MAGENTA => "\033[35m",
        self::COLOR_CYAN    => "\033[36m",
        self::COLOR_WHITE   => "\033[37m",
    );

    private $_textOptions = array(
        self::TEXT_CLEAR   => "\033[0m",
        self::TEXT_BOLD    => "\033[1m",
    );

    /**
     * @param string $message
     * @return string
     */
    public function read ($message) {
        return readline($message);
    }

    /**
     * @param string $message
     * @param string $color
     */
    public function out ($message, $color = null) {
        $colorText = is_null($color) ? "" : $this->_colorsMap[$color];
        $clearText = $this->_textOptions[self::TEXT_CLEAR];
        echo "{$colorText}{$message}{$clearText}";
    }

    public function newLine () {
        echo "\n";
    }

    /**
     * @param $colorName
     */
    public function color ($colorName) {
        return $this->_colorsMap[$colorName];
    }

    /**
     * @param $optionName
     */
    public function textOption ($optionName) {
        return $this->_textOptions[$optionName];
    }

}