<?php
use Gwa\Cache\Cache;
use Gwa\Cache\CacheFactory;
use Gwa\Cache\CacheDirectoryPersistance;

class CacheFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $persistance = new CacheDirectoryPersistance(__DIR__.'/../temp');
        $factory = new CacheFactory($persistance);
        $cache = $factory->create('foo', 'bar', 10, Cache::TYPE_OBJECT);

        $this->assertInstanceOf('Gwa\Cache\Cache', $cache);
        $this->assertEquals('foo', $cache->getIdentifier());
        $this->assertEquals('bar', $cache->getGroup());
        $this->assertEquals(10, $cache->getCacheMinutes());
        $this->assertEquals(Cache::TYPE_OBJECT, $cache->getType());

        // test same instance returned
        $cache2 = $factory->create('foo', 'bar', 10, Cache::TYPE_OBJECT);

        $this->assertSame($cache, $cache2);
    }
}
