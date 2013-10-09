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
        if ($lastBlock->isOpen()) {
            $lastBlock->addUnit($unit);
        } else {
            $block = new Block($unit);
            $this->addBlock($block);
            $this->_calcCallIsOpen($block);
        }
    }

    /**
     * @return bool
     */
    public function isOpen () {
        return $this->_isOpen;
    }

    /**
     * @param Block $block
     */
    private function _calcCallIsOpen (Block $block) {
        throw new \Exception("Not imp");
    }

}