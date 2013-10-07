<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

use FTrace\Utils\Code\Unit;

class Code {

    private $_currentCall;
    private $_callStack;

    public function __construct () {
        $this->_callStack = array();
    }

    public function pushCode (TraceObject $obTrace) {
        // Block - Набор строчек кода (массив строчек Unit)
        // Unit - единица вызова/кода (файл, номер строки, содержание строки, изначальный трейс массив, опционально может содержать в себе новый Call
        // Call - абстрактный стек. (уровень вложенности в стеке, массив блоков Block)

        $unit = new Unit($obTrace);

        if ($this->_firstCall() || $this->_noOpenCalls()) {
            $this->_initCall();
        }

        $this->_pushToCurrentCall($unit);
    }

    /**
     * @return array
     */
    public function getCalls () {
        return $this->_callStack;
    }

    private function _pushToCurrentCall (Unit $unit) {
        throw new \Exception('Not implemented');
    }

    private function _initCall () {
        throw new \Exception('Not implemented');
    }

    private function _noOpenCalls () {
        throw new \Exception('Not implemented');
    }

    private function _firstCall () {
        return is_null($this->_currentCall);
    }

}