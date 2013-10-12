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

    /**
     * @var int
     */
    private $_currentBlockPointer;

    /**
     * @var int
     */
    private $_depth;

    /**
     * @var Block[]
     */
    private $_blocks;

    public function __construct () {
        $this->_blocks = array();
    }

    /**
     * @param Trace $obTrace
     */
    public function pushCode (Trace $obTrace) {
        $unit = new Unit($obTrace);

        if ($this->_firstBlock() || $this->_noOpenBlocks($unit)) {
            $this->_initBlock($unit);
        } else {
            $this->_pushToCurrentBlock($unit);
        }
    }

    /**
     * @return Block[]
     */
    public function getBlocks () {
        return $this->_blocks;
    }

    /**
     * @param Unit $unit
     */
    private function _pushToCurrentBlock (Unit $unit) {
        $this->_getCurrentBlock()->addUnit($unit);
    }

    /**
     * @param Unit $unit
     */
    private function _initBlock (Unit $unit) {
        if ($this->_firstBlock()) {
            $this->_depth = $unit->getDepth();
        }

        if ($this->_depth > $unit->getDepth()) {
            return; // don't trace higher code levels
        }

        $block = new Block($unit, array(), $this->_depth);
        $this->_addBlock($block);
    }

    /**
     * @param Block $block
     */
    private function _addBlock (Block $block) {
        $this->_blocks[] = $block;
        $this->_stackPointerInc();
    }

    /**
     * @param Unit $unit
     * @return bool
     */
    private function _noOpenBlocks (Unit $unit) {
        $openBlocks = $this->_getOpenBlocks($unit);
        if (count($openBlocks) === 0) {
            return true;
        }
        return false;
    }

    /**
     * @param Unit $Unit
     * @return Block[]
     */
    private function _getOpenBlocks (Unit $Unit) {
        $openBlocks = array();

        $stackPointNum = $this->_currentBlockPointer;
        if (is_null($stackPointNum))
            return $openBlocks;

        while($stackPointNum >= 0) {
            if ($this->_blocks[$stackPointNum]->isOpen($Unit)) {
                $openBlocks[] = $this->_blocks[$stackPointNum];
            }
            $stackPointNum--;
        }

        return $openBlocks;
    }

    /**
     * @return bool
     */
    private function _firstBlock () {
        return is_null($this->_currentBlockPointer);
    }

    /**
     * @return Block
     */
    private function _getCurrentBlock () {
        return $this->_blocks[$this->_currentBlockPointer];
    }

    private function _stackPointerInc () {
        if (is_null($this->_currentBlockPointer)) {
            $this->_currentBlockPointer = 0;
        } else {
            $this->_currentBlockPointer++;
        }
    }

}