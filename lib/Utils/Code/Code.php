<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

use FTrace\Utils\Code\Unit;
use FTrace\Utils\Code\Call;
use FTrace\Utils\Trace;

class Code {

    /**
     * @var Call
     */
    private $_call;

    public function __construct () {
        $this->_call = new Call(true);
    }

    /**
     * @param Trace $obTrace
     */
    public function pushCode (Trace $obTrace) {
        // Code has root call
        // Each unit could have call
        // Each call has array of units
        /*
         Code:
            0. Init root call in constructor
            1. Create unit
            2. Push unit to call
         Call:
            1. If we have no units, simply add it as first and calc call depth
            2. If we already have units
                2.1 If unit has deeper depth
                    2.1.1 If last unit is mock, push this new unit to last call
                    2.1.2 If we have no open call, we create unit mock with call, and put unit as first unit of this call
                2.2 If unit has the same depth
                    2.2.1 If last unit is mock, insert data from new unit into mock, remove new unit (unit closing call)
                    2.2.2 If last unit not mock, simply add new unit to array
                2.2 If unit has smaller depth, we remove it and skip step (we are out of scope)
        */

        $unit = new Unit($obTrace);
        echo "\n\n -> Code: create unit <strong>" . $unit->getLineView() . "</strong>\n\n";

        echo "\n\n -> Code: add unit to root call\n\n";
        $this->_call->addUnit($unit);
    }

    /**
     * @return Call
     */
    public function getData () {
        return $this->_call;
    }

}