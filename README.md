Sclable Array Functions
=======================

An array wrapper to normalize php array functions, give them
an ObjectOriented and functional programming approach.

Installation
------------

With [Composer](https://getcomposer.org):
```
composer require sclable/array-functions
```

Usage
-----

```php
echo ArrayWrap::range(0, 10)
    ->filter(function ($nr) { return $nr < 3; })
    ->map(function ($nr) { return "Number: $nr\n"; })
    ->reverse();

// echoes
// Number: 2
// Number: 1
// Number: 0
```

Contribute
----------

Clone/fork the repository as you like. To get started, run `composer install`.

- PHPUnit Tests preferred
- PSR-2 Coding style must apply

License
-------

see [LICENSE](LICENSE).