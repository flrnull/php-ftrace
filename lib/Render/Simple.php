<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Render;

use FTrace\Code\Unit;

class Simple {

    public function render (Unit $unit) {
        $tabCount = $unit->getDepth();
        $codeLine = $unit->getLineView();

        if (trim($codeLine) == '}')
            return;

        $tabs = str_repeat('———', $tabCount);

        if ($unit->isCall()) {
            $args = " # (" . implode(", ", $unit->getArgs()) . ")";
        } else {
            $args = "";
        }

        echo "<div>{$tabs}> {$codeLine}{$args}<div>";

        if ($unit->isCall()) {
            $call = $unit->getCall();
            foreach($call->getUnits() as $innerUnit) {
                $this->render($innerUnit);
            }
        }

        if ($tabCount == 1) {
            echo "<br>";
        }
    }

}