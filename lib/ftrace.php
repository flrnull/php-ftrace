<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */
declare(ticks = 1);

include_once __DIR__ . '/../lib/Profiler.php';

use FTrace\Profiler;

function ftrace ($depthLimit = null) {
    clearOpcodeCache();
    Profiler::start($depthLimit);
}

function ftrace_stop () {
    return Profiler::stop();
}

function ftrace_print () {
    $result = ftrace_stop();

    if (php_sapi_name() == 'cli') {
        $render = new \FTrace\Render\Cli();
    } else {
        echo "<pre>";
        $render = new \FTrace\Render\Simple();
    }
    foreach($result['code']->getUnits() as $unit) {
        $render->render($unit);
    }
}

function clearOpcodeCache () {
    if (function_exists('apc_clear_cache')) {
        apc_clear_cache('opcode');
    }
    if (function_exists('accelerator_reset')) {
        accelerator_reset();
    }
    if (function_exists('xcache_clear_cache') && defined(XC_TYPE_PHP)) {
        xcache_clear_cache(XC_TYPE_PHP, 0);
    }
}