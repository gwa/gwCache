<?php
namespace Gwa\Cache;

use Gwa\Filesystem\gwFile;
use Gwa\Filesystem\gwDirectory;
use Gwa\Exception\gwFilesystemException;

class CacheFile implements CachePersistanceInterface
{
    protected $identifier;
    protected $dirpath;
    protected $fullpath;
    protected $cacheTime;
    protected $data;

    /**
     * @param string $identifier
     * @param string $dirpath
     */
    public function __construct($identifier, $dirpath, $cacheminutes = 60)
    {
        $this->identifier = md5($identifier);
        $this->dirpath    = $dirpath;
        $this->fullpath   = $dirpath . '/' . $this->identifier;
        $this->cacheTime  = $cacheminutes;
    }

    /**
     * @return boolean
     */
    public function isCached()
    {
        // does file exist?
        if (!file_exists($this->fullpath)) {
            return false;
        }

        // infinite cache
        if ($this->cacheTime === -1) {
            return true;
        }

        // has cache expired
        $filemtime   = filemtime($this->fullpath);
        $elapsedmins = floor((time() - $filemtime) / 60);

        return $elapsedmins < $this->cacheTime;
    }

    /**
     * Clears the cached file.
     *
     * @return boolean|null
     */
    public function clear()
    {
        if (!file_exists($this->fullpath)) {
            return;
        }
        $file = new gwFile($this->fullpath);
        $file->delete();
    }

    /**
     * @param string $content
     *
     * @return int bytes written
     *
     * @throws gwFilesystemException
     */
    public function set($content)
    {
        gwDirectory::makeDirectoryRecursive($this->dirpath);
        $file = new gwFile($this->fullpath);

        return $file->replaceContent($content);
    }

    /**
     * get the cache.
     *
     * @return string string or false if not cached
     */
    public function get()
    {
        if (!$this->isCached()) {
            return false;
        }

        if (isset($this->data)) {
            return $this->data;
        }

        $file = new gwFile($this->fullpath);

        return $this->data = $file->getContent();
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullpath;
    }
}
