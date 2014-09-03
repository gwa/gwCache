<?php
namespace Gwa\Cache;

use Gwa\Filesystem\gwFile;
use Gwa\Filesystem\gwDirectory;
use Gwa\Exception\gwFilesystemException;

class gwCacheFile implements gwiCachePersistance
{
    protected $_identifier;
    protected $_dirpath;
    protected $_fullpath;
    protected $_cachetime;
    protected $_data;

    public function __construct( $identifier, $dirpath, $cacheminutes=60 )
    {
        $this->_identifier = md5($identifier);
        $this->_dirpath = $dirpath;
        $this->_fullpath = realpath($dirpath) . '/' . $this->_identifier;
        $this->_cachetime = $cacheminutes;
    }

    /**
     * @return boolean
     */
    public function isCached()
    {
        // does file exist?
        if (!file_exists($this->_fullpath)) {
            return false;
        }

        // infinite cache
        if ($this->_cachetime == -1) {
            return true;
        }

        // has cache expired
        $filemtime = filemtime($this->_fullpath);
        $elapsedmins = floor((time() - $filemtime) / 60);

        return $elapsedmins < $this->_cachetime;
    }

    /**
     * Clears the cached file.
     * @return boolean
     */
    public function clear()
    {
        $file = new gwFile($this->_fullpath);
        try {
            $file->delete();
        } catch (\Exception $e) {
            if ($e->getMessage() != gwFilesystemException::ERR_FILE_NOT_EXIST) {
                // @codeCoverageIgnoreStart
                throw($e);
                // @codeCoverageIgnoreEnd
            }
        }
    }

    /**
     * @param string $content
     * @returns int bytes written
     * @throws gwFilesystemException
     */
    public function set( $content )
    {
        gwDirectory::makeDirectoryRecursive($this->_dirpath);
        $file = new gwFile($this->_fullpath);
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

        if (isset($this->_data)) {
            return $this->_data;
        }

        $file = new gwFile($this->_fullpath);
        return $this->_data = $file->getContent();
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->_fullpath;
    }
}
