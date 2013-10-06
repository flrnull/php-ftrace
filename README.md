php-forward-backtrace
============

_Under construction_

debug_backtrace() has big majority problem: you should know where to put it.
Also you should put it again and again when you debug some forked logic.
Unlike it forward-backtrace style allows you not to know where to put trace call.
You put debug call once and move from the beginning to the end and watch what php code is doing.

Usage
-----

```php
<?php
declare(ticks = 1);
use ForwardTrace\Profiler;

Profiler::start();
// Some code
$result = Profiler::stop();

var_export($result);
```