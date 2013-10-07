php-ftrace
============

_Under construction_

Usage
-----

```php
<?php
declare(ticks = 1);
use FTrace\Profiler;

Profiler::start();
// Some code
$result = Profiler::stop();

var_export($result);
```
