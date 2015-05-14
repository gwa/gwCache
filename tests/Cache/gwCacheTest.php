<?php
use Gwa\Cache\Cache;
use Gwa\Cache\CacheFile;
use Gwa\Filesystem\gwDirectory;

class gwCacheTest extends PHPUnit_Framework_TestCase
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
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $this->assertInstanceOf('Gwa\Cache\Cache', $cache);
    }

    public function testGetPersistance()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $this->assertInstanceOf('Gwa\Cache\CachePersistenceInterface', $cache->getPersistance());

        // change persistance
        $cachefile = new CacheFile('bar', $cachedir);
        $this->assertNotSame($cachefile, $cache->getPersistance());
        $cache->setPersistance($cachefile);
        $this->assertSame($cachefile, $cache->getPersistance());
    }

    public function testIsCachedFalse()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $this->assertFalse($cache->isCached());
        $this->assertFalse($cache->get());
    }

    public function testSet()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $bytes = $cache->set('foo');
        $this->assertEquals(3, $bytes);
        $this->assertTrue($cache->isCached());
    }

    public function testSetInNonExistingSubfolder()
    {
        $cachedir = __DIR__.'/../temp/subfolder';
        $cache = new Cache('foo', $cachedir);
        $bytes = $cache->set('foo');
        $this->assertEquals(3, $bytes);
        $this->assertTrue($cache->isCached());
    }

    public function testCacheInfinite()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir, -1);
        $this->assertTrue($cache->isCached());
    }

    public function testGet()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $this->assertEquals('foo', $cache->get());
        $this->assertEquals('foo', $cache->get()); // test cached in persistance instance
    }

    public function testClear()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('foo', $cachedir);
        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testFileNotExist()
    {
        // not exist exception should be caught internally
        $cachedir = __DIR__.'/notexist';
        $cache = new Cache('foo', $cachedir);
        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testCacheObject()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new Cache('obj', $cachedir, 30, Cache::TYPE_OBJECT);
        $obj = new \stdClass;
        $obj->foo = 'bar';
        $cache->set($obj);

        $cache2 = new Cache('obj', $cachedir, 30, Cache::TYPE_OBJECT);
        $this->assertTrue($cache2->isCached());
        $obj2 = $cache2->get();
        $this->assertEquals('bar', $obj2->foo);

        $cache2->clear();
    }
}
