php-profiler
============

Under construction

Usage
_____

```php
<?php
declare(ticks = 1);
use Profiler\Profiler;

Profiler::start();
// Some code
$result = Profiler::stop();

var_export($result);
```