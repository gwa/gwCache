gwCache
=======

A simple but flexible PHP cache

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

// Create a persistence layer for the cache
// Cache directory should be writable.
// (Cache will try to create it if it does not exist.)
$cachedir = __DIR__ . '/cachestore';
$persistence = new CacheDirectoryPersistence($cachedir);



// Set an optional group for the cache
$group = '';

// Set cache validity in minutes
$cacheminutes = 60 * 12;

// create a cache instance using an identifier unique to the group
$cache = new Cache('myidentifier', $group, $cacheminutes);
$cache->setPersistence($persistence);



$iscached = $cache->isCached(); // false

// write a value to the cache
$cache->set('foo');

// new object, same group and identifier
$cache2 = new Cache('myidentifier', $group, $cacheminutes);
$cache2->setPersistence($persistence);
$iscached = $cache2->isCached(); // true
$value = $cache2->get(); // 'foo'

// clear the cache
$cache2->clear();
$iscached = $cache2->isCached(); // false
```

### Using a factory

Instead of always passing in the persistence layer, a factory can be used.

The factory could be set up as a service in your app (using, for example, [Pimple](http://pimple.sensiolabs.org/)). Creating a cache instance is then not dependent on the persistence layer.

```php
$factory = new CacheFactory(new CacheDirectoryPersistence($cachedir));

$cache = $factory->create('myidentifier', $group, $cacheminutes);
```

### Caching serialized data

To cache complex PHP types (ie. objects), set the cache type argument when creating the cache instance.

```php
$cache = new Cache('myidentifier', $group, $cacheminutes, Cache::TYPE_OBJECT);

// with a factory
$cache = $factory->create('myidentifier', $group, $cacheminutes, Cache::TYPE_OBJECT);
```

### Custom persistence layers

You can roll your own persistence layer (MySQL, memcache, redis) by creating a class that implements the `CachePersistenceInterface` interface.

If you are using a factory-as-a-service architecture, you can use different persistence layers in different environments without changing the code that uses the service.

## Contributing

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a feature branch, and send us a pull request.

To ensure a consistent code base, you should make sure the code follows
the [Coding Standards](http://www.php-fig.org/psr/psr-2/)
which we borrowed from PSR-2.

The easiest way to do make sure you're following the coding standard is to run `vendor/bin/php-cs-fixer fix` before committing.
