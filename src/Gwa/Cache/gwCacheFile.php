<?php
namespace Gwa\Cache;

use Gwa\Filesystem\gwFile;
use Gwa\Filesystem\gwDirectory;
use Gwa\Exception\gwFilesystemException;

class gwCacheFile implements gwiCachePersistance
{
    protected $identifier;
    protected $dirpath;
    protected $fullpath;
    protected $cacheTime;
    protected $data;

    public function __construct($identifier, $dirpath, $cacheminutes = 60)
    {
        $this->identifier = md5($identifier);
        $this->dirpath    = $dirpath;
        $this->fullpath   = realpath($dirpath).'/'.$this->identifier;
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
     * @return boolean
     */
    public function clear()
    {
        $file = new gwFile($this->fullpath);

        try {
            $file->delete();
        } catch (\Exception $e) {
            if ($e->getMessage() !== gwFilesystemException::ERR_FILE_NOT_EXIST) {
                // @codeCoverageIgnoreStart
                throw($e);
                // @codeCoverageIgnoreEnd
            }
        }
    }

    /**
     * @param  string                $content
     * @returns int bytes written
     * @throws gwFilesystemException
     */
    public function set($content)
    {
        gwDirectory::makeDirectoryRecursive($this->dirpath);
        $file = new gwFile($this->fullpath);

        return $file->replaceContent($content);
    }

    /**
     * get the cache
     * @return mixed string or false if not cached
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
