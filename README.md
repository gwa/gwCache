gwCache
=======

Simple PHP cache

[![Quality Score](https://img.shields.io/scrutinizer/g/gwa/gwCache.svg?style=flat-square)](https://scrutinizer-ci.com/g/gwa/gwCache/code-structure/master)  [![Build Status](https://api.travis-ci.org/gwa/gwCache.svg?branch=master)](https://travis-ci.org/gwa/gwCache)

## Usage

### Installation

Install [package](https://packagist.org/packages/gwa/gw-cache) via composer.

```php
composer require gwa/gw-cache
```

### Using the cache

```php
use Gwa\Cache\Cache;

// Cache directory should be writable.
// Cache will try to create it if it does not exist.
$cachedir = __DIR__ . '/cachestore';

// Set cache validity in minutes
$cacheminutes = 60 * 12;

// create a cache instance
$cache = new Cache('myidentifier', $cachedir, $cacheminutes);

$iscached = $cache->isCached(); // false

// write a value to the cache
$bytes = $cache->set('foo');

// new object, same directory and identifier
$cache2 = new Cache('myidentifier', $cachedir, $cacheminutes);
$iscached = $cache2->isCached(); // true
$value = $cache2->get(); // 'foo'

// clear the cache
$cache2->clear();
$iscached = $cache2->isCached(); // false
```

## Contributing

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a feature branch, and send us a pull request.

To ensure a consistent code base, you should make sure the code follows
the [Coding Standards](http://www.php-fig.org/psr/psr-2/)
which we borrowed from PSR-2.

The easiest way to do make sure you're following the coding standard is to run `vendor/bin/php-cs-fixer fix` before committing.
