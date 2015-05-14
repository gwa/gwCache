<?php
namespace Gwa\Cache;

use Gwa\Exception\gwFilesystemException;
use Gwa\Filesystem\gwDirectory;
use Gwa\Filesystem\gwFile;

class CacheDirectoryPersistence implements CachePersistenceInterface
{
    protected $dirpath;

    /**
     * @param string $dirpath
     */
    public function __construct($dirpath)
    {
        $this->dirpath = $dirpath;
    }

    /**
     * @param Cache $cache
     * @return boolean
     */
    public function isCached(Cache $cache)
    {
        $filepath = $this->getFilePath($cache);
        $cachetime = $cache->getCacheMinutes();

        // does file exist?
        if (!file_exists($filepath)) {
            return false;
        }

        // infinite cache
        if ($cachetime === Cache::CACHEMINUTES_INFINITE) {
            return true;
        }

        // has cache expired
        $filemtime   = filemtime($filepath);
        $elapsedmins = floor((time() - $filemtime) / 60);

        return $elapsedmins < $cachetime;
    }

    /**
     * Clears the cached file.
     *
     * @return boolean|null
     */
    public function clear(Cache $cache)
    {
        $filepath = $this->getFilePath($cache);

        if (!file_exists($filepath)) {
            return;
        }

        $file = new gwFile($filepath);
        $file->delete();
    }

    /**
     * @param Cache $cache
     * @param string $content
     *
     * @return int bytes written
     *
     * @throws gwFilesystemException
     */
    public function set(Cache $cache, $content)
    {
        $dir = $this->getDirPath($cache);
        gwDirectory::makeDirectoryRecursive($dir);

        $file = new gwFile($this->getFilePath($cache));
        return $file->replaceContent($content);
    }

    /**
     * get the cache.
     *
     * @param Cache $cache
     *
     * @return false|string string or false if not cached
     */
    public function get(Cache $cache)
    {
        if (!$this->isCached($cache)) {
            return false;
        }

        $file = new gwFile($this->getFilePath($cache));

        return $file->getContent();
    }

    /**
     * @param Cache $cache
     *
     * @return string
     */
    public function getDirPath(Cache $cache)
    {
        return $this->dirpath.'/'.basename($cache->getGroup());
    }

    /**
     * @param Cache $cache
     *
     * @return string
     */
    public function getFilePath(Cache $cache)
    {
        return $this->getDirPath($cache).'/'.$cache->getIdentifier();
    }
}
