<?php
use Gwa\Cache\Cache;
use Gwa\Cache\CacheDirectoryPersistance;
use Gwa\Filesystem\gwDirectory;

class CacheTest extends PHPUnit_Framework_TestCase
{
    public static function tearDownAfterClass()
    {
        $path = __DIR__.'/../temp/subfolder';
        if (is_dir($path)) {
            $dir = new gwDirectory($path);
            $dir->delete();
        }
    }

    public function testConstructor()
    {
        $cache = new Cache('foo', 'bar');
        $this->assertInstanceOf('Gwa\Cache\Cache', $cache);
    }

    public function testGetPersistance()
    {
        $cache = new Cache('foo', 'bar');
        $this->assertNull($cache->getPersistance());

        // change persistance
        $persistance = new CacheDirectoryPersistance(__DIR__.'/../temp');
        $cache->setPersistance($persistance);
        $this->assertSame($persistance, $cache->getPersistance());
    }

    public function testIsCachedFalse()
    {
        $cache = new Cache('foo');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $this->assertFalse($cache->isCached());
        $this->assertFalse($cache->get());
    }

    public function testSet()
    {
        $cache = new Cache('foo');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $bytes = $cache->set('foo');
        $this->assertEquals(3, $bytes);
        $this->assertTrue($cache->isCached());
    }

    public function testSetInNonExistingGroup()
    {
        $cache = new Cache('foo', 'group');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $bytes = $cache->set('foo');
        $this->assertEquals(3, $bytes);
        $this->assertTrue($cache->isCached());
    }

    public function testCacheInfinite()
    {
        $cache = new Cache('foo', '', -1);
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));
        $this->assertTrue($cache->isCached());
    }

    public function testGet()
    {
        $cache = new Cache('foo');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $this->assertEquals('foo', $cache->get());
        $this->assertEquals('foo', $cache->get()); // test cached in persistance instance
    }

    public function testClear()
    {
        $cache = new Cache('foo');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testFileNotExist()
    {
        $cache = new Cache('foo');
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../notexist'));

        // not exist exception should be caught internally
        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testCacheObject()
    {
        $cache = new Cache('obj', '', 30, Cache::TYPE_OBJECT);
        $cache->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $obj = new \stdClass;
        $obj->foo = 'bar';
        $cache->set($obj);

        $cache2 = new Cache('obj', '', 30, Cache::TYPE_OBJECT);
        $cache2->setPersistance(new CacheDirectoryPersistance(__DIR__.'/../temp'));

        $this->assertTrue($cache2->isCached());
        $obj2 = $cache2->get();
        $this->assertEquals('bar', $obj2->foo);

        $cache2->clear();
    }
}
