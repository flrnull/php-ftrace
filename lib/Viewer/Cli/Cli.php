<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Viewer;

include_once __DIR__ . '/Console.php';
include_once __DIR__ . '/RawViewer.php';

use FTrace\Code\Call;

class Cli {

    /**
     * @var array
     */
    private $_data;

    /**
     * @param array $traceData
     */
    public function __construct (array $traceData) {
        $this->_data = $traceData;
    }

    public function view () {
        $codeTime = $this->_data['code_time'];
        $codeCount = $this->_data['counter'];
        $profileTime = $this->_data['profiler_time'];
        $rootCall = $this->_data['code'];
        $console = new Console();
        $rawViewer = new RawViewer($rootCall);
        $rawViewer->showNextPage();
        while($console->read("Press Enter to view next") == "" && $rawViewer->nextPageExists()) {
            $rawViewer->showNextPage();
        }
    }

}