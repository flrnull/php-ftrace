<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

include_once __DIR__ . '/Profiler.php';
include_once __DIR__ . '/Viewer/HTML.php';

use FTrace\Profiler;
use FTrace\Viewer\HTML;

function ftrace ($depthLimit = null) {
    clearOpcodeCache();
    Profiler::start($depthLimit);
}

function ftrace_stop () {
    clearOpcodeCache();
    return Profiler::stop();
}

function ftrace_print () {
    $result = ftrace_stop();

    if (php_sapi_name() == 'cli') {
        $viewer = new HTML($result);
    } else {
        $viewer = new HTML($result);
    }
    
    $viewer->view();
}

function clearOpcodeCache () {
    if (function_exists('apc_clear_cache')) {
        @apc_clear_cache('opcode');
    }
    if (function_exists('accelerator_reset')) {
        @accelerator_reset();
    }
    if (function_exists('xcache_clear_cache') && defined(XC_TYPE_PHP)) {
        @xcache_clear_cache(XC_TYPE_PHP, 0);
    }
}