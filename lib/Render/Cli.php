<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Render;

use FTrace\Code\Unit;

class Cli {

    public function render (Unit $unit) {
        $tabCount = $unit->getDepth();
        $codeLine = $unit->getLineView();
        $tabs = str_repeat('    ', $tabCount);

        echo "\n";
        echo "{$tabs}> {$codeLine}\n";
        if ($unit->isCall()) {
            $call = $unit->getCall();
            foreach($call->getUnits() as $innerUnit) {
                $this->render($innerUnit);
            }
        }

        if ($tabCount == 1) {
            echo "\n\n";
        }
    }

}