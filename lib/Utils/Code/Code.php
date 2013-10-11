<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

use FTrace\Utils\Code\Unit;
use FTrace\Utils\Code\Call;
use FTrace\Utils\Code\Block;
use FTrace\Utils\Trace;

class Code {

    private $_stackPointer;

    /**
     * @var Call[]
     */
    private $_callStack;

    public function __construct () {
        $this->_callStack = array();
    }

    public function pushCode (Trace $obTrace) {
        // Block - блок строчек кода (массив строчек Unit)
        // Unit - единица кода (файл, номер строки, содержание строки, изначальный трейс массив, опционально может содержать в себе новый Call
        // Call - метод/функция (Состоит из блоков кода Block)

        $unit = new Unit($obTrace);

        if ($this->_firstCall() || $this->_noOpenCalls()) {
            $this->_initCall($unit);
        } else {
            $this->_pushToCurrentCall($unit);
        }
    }

    /**
     * @return array
     */
    public function getCalls () {
        return $this->_callStack;
    }

    private function _pushToCurrentCall (Unit $unit) {
        $this->_getCurrentCall()->addUnit($unit);
    }

    private function _initCall (Unit $unit) {
        $call = Call::create($unit);
        $this->_addCallToStack($call);
    }

    private function _addCallToStack (Call $call) {
        $this->_callStack[] = $call;
        $this->_stackPointerInc();
    }

    private function _noOpenCalls () {
        $openCalls = $this->_getOpenCalls();
        if (count($openCalls) === 0) {
            return true;
        }
        return false;
    }

    private function _getOpenCalls () {
        $openCalls = array();

        $stackPointNum = $this->_stackPointer;
        if (is_null($stackPointNum))
            return $openCalls;

        while($stackPointNum >= 0) {
            if ($this->_callStack[$stackPointNum]->isOpen())
                $openCalls[] = $this->_callStack[$stackPointNum];
            $stackPointNum--;
        }

        return $openCalls;
    }

    private function _firstCall () {
        return is_null($this->_stackPointer);
    }

    /**
     * @return Call
     */
    private function _getCurrentCall () {
        return $this->_callStack[$this->_stackPointer];
    }

    private function _stackPointerInc () {
        if (is_null($this->_stackPointer)) {
            $this->_stackPointer = 0;
        } else {
            $this->_stackPointer++;
        }
    }

}