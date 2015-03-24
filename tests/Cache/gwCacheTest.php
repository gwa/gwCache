<?php
use Gwa\Cache\gwCache;
use Gwa\Filesystem\gwDirectory;

class gwCacheTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir);
        $this->assertInstanceOf('Gwa\Cache\gwCache', $cache);
    }

    public function testIsCachedFalse()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir);
        $this->assertFalse($cache->isCached());
        $this->assertFalse($cache->get());
    }

    public function testSet()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir);
        $bytes = $cache->set('foo');
        $this->assertEquals(3, $bytes);
        $this->assertTrue($cache->isCached());
    }

    public function testCacheInfinite()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir, -1);
        $this->assertTrue($cache->isCached());
    }

    public function testGet()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir);
        $this->assertEquals('foo', $cache->get());
        $this->assertEquals('foo', $cache->get()); // test cached in persistance instance
    }

    public function testClear()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('foo', $cachedir);
        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testFileNotExist()
    {
        // not exist exception should be caught internally
        $cachedir = __DIR__.'/notexist';
        $cache = new gwCache('foo', $cachedir);
        $data = $cache->clear();
        $this->assertFalse($cache->isCached());
    }

    public function testCacheObject()
    {
        $cachedir = __DIR__.'/../temp';
        $cache = new gwCache('obj', $cachedir, 30, gwCache::TYPE_OBJECT);
        $obj = new \stdClass;
        $obj->foo = 'bar';
        $cache->set($obj);

        $cache2 = new gwCache('obj', $cachedir, 30, gwCache::TYPE_OBJECT);
        $this->assertTrue($cache2->isCached());
        $obj2 = $cache2->get();
        $this->assertEquals('bar', $obj2->foo);

        $cache2->clear();
    }
}
