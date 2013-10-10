<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

/**
 * Class Call
 * @package FTrace\Utils\Code
 */
class Call {

    /**
     * @var Unit[]
     */
    private $_blocksList;

    /**
     * @var bool
     */
    private $_isOpen;

    /**
     * @var Unit
     */
    private $_previousUnit;

    public function __construct (Block $block) {
        $this->_blocksList = array($block);
        $this->_isOpen = true;
    }

    /**
     * @return array
     */
    public function getBlocks () {
        return $this->_blocksList;
    }

    /**
     * @return Block
     */
    public function getLastBlock () {
        $blockCount = count($this->_blocksList);
        return $this->_blocksList[--$blockCount];
    }

    /**
     * @param Block $block
     */
    public function addBlock (Block $block) {
        $this->_blocksList[] = $block;
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        $lastBlock = $this->getLastBlock();
        if ($lastBlock->blockIsOpen($unit)) {
            $lastBlock->addUnit($unit);
        } else {
            $block = new Block($unit);
            $this->addBlock($block);
            $this->_tryToCloseCurrentCall($block);
        }
        $this->_previousUnit = $unit;
    }

    /**
     * @return bool
     */
    public function isOpen () {
        return $this->_isOpen;
    }

    private function _closeCurrentCall () {
        $this->_isOpen = false;
    }

    /**
     * @param Block $block
     */
    private function _tryToCloseCurrentCall (Block $block) {
        if (is_null($this->_previousUnit))
            return;

        $prevUnit = $this->_previousUnit;
        $currentUnit = $block->getLastUnit();
        if ($prevUnit->getDepth() > $currentUnit->getDepth())
            $this->_closeCurrentCall();
    }

}