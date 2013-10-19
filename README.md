php-ftrace
============

_Under construction_

Usage
-----

```php
<?php
declare(ticks = 1); // should be in the beginning of file
ftrace();

$test = new test();
$test->func1();

ftrace_print();
```

Docs
----

* Depth limit

```php
ftrace(2); // will not trace deeper than 2 levels
```

* Return trace result

```php
ftrace();
// ...
$traceData = ftrace_stop();
```

License
-------
MIT