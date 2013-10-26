<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Viewer;

use FTrace\Code\Call;
use FTrace\Code\Unit;

class RawViewer {

    const LINES_PER_PAGE = 10;

    /**
     * @var Unit[]
     */
    private $_units;

    /**
     * @var Console
     */
    private $_console;

    /**
     * @var int
     */
    private $_currentUnitNum;

    /**
     * @param Call $rootCall
     */
    public function __construct (Call $rootCall) {
        $this->_units = $this->_extractAllUnits($rootCall->getUnits());
        $this->_console = new Console();
    }

    public function showNextPage () {
        if (is_null($this->_currentUnitNum)) {
            $this->_currentUnitNum = 0;
        } else {
            $this->_currentUnitNum += self::LINES_PER_PAGE;
        }

        $this->_showCurrentPage();
    }

    /**
     * @return bool
     */
    public function nextPageExists () {
        if (is_null($this->_currentUnitNum)) {
            return true;
        }
        return isset($this->_units[$this->_currentUnitNum+1]);
    }

    /**
     * @param Unit[] $units
     * @return Unit[]
     */
    private function _extractAllUnits (array $units) {
        $rawUnits = array();

        foreach($units as $unit) {
            $rawUnits[] = $unit;
            if ($unit->isCall()) {
                $rawUnits = array_merge($rawUnits, $this->_extractAllUnits($unit->getCall()->getUnits()));
            }
        }

        return $rawUnits;
    }

    private function _showCurrentPage () {
        $unitNum = $this->_currentUnitNum;
        $unitCount = count($this->_units);
        $loop = 1;
        for($i = $unitNum; $i < $unitCount; $i++) {
            $this->_printUnit($this->_units[$i]);
            if ($loop++ >= self::LINES_PER_PAGE) {
                break;
            }
        }
    }

    /**
     * @param Unit $unit
     */
    private function _printUnit (Unit $unit) {
        $level = $unit->getDepth();
        $this->_console->newLine();
        $this->_console->out(str_repeat("    ", $level));
        $this->_console->out($unit->getTrace()->getFile(), Console::COLOR_GREEN);
        $this->_console->out(" : ");
        $this->_console->out($unit->getLineNumber());
        $this->_console->newLine();
        $this->_console->out(str_repeat("    ", $level));
        $this->_console->out($this->_highlightUnitLine($unit->getLineView()));
        if ($unit->isCall()) {
            $this->_console->out(" # (");
            $this->_console->out((count($unit->getArgs()) ? implode(", ", $unit->getArgs()) : ""));
            $this->_console->out(")");
        }
        $this->_console->newLine();
    }

    /**
     * @param string $unitLine
     * @return string
     */
    private function _highlightUnitLine ($unitLine) {
        $patterns = array();
        $replacements = array();

        $patterns[0] = '#(new|private|public|protected|function|class|abstract|interface|echo|print)#';
        $replacements[0] = $this->_console->color(Console::COLOR_CYAN) . '$1' . $this->_console->textOption(Console::TEXT_CLEAR);
        $patterns[1] = '#(\$[a-zA-Z0-9_]+)#';
        $replacements[1] = $this->_console->color(Console::COLOR_YELLOW) . '$1' . $this->_console->textOption(Console::TEXT_CLEAR);
        $patterns[2] = '#(\'[^\']*\')#';
        $replacements[2] = $this->_console->color(Console::COLOR_RED) . '$1' . $this->_console->textOption(Console::TEXT_CLEAR);
        $patterns[3] = '#("[^\"]*")#';
        $replacements[3] = $this->_console->color(Console::COLOR_RED) . '$1' . $this->_console->textOption(Console::TEXT_CLEAR);

        $unitLine = preg_replace($patterns, $replacements, $unitLine);
        return $unitLine;
    }

}