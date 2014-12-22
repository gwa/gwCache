<?php
namespace Gwa\Cache;

/**
 * Provides caching functions.
 * @ingroup data
 */
class gwCache
{
    const CACHEMINUTES_INFINITE = -1;

    /**
     * Flat text type
     * @var string
     */
    const TYPE_FLAT = 'gwCache::type_flat';

    /**
     * PHP variable type
     * @var string
     */
    const TYPE_VARIABLE = 'gwCache::type_variable';

    /**
     * PHP object type
     * @var string
     */
    const TYPE_OBJECT = 'gwCache::type_object';

    /**
     * PHP object type
     * @var string
     */
    const TYPE_DATABASE = 'gwCache::type_database';

    protected $type;

    protected $persistance;

    /**
     * constructor
     *
     * @param string $identifier   unique identifier for this file
     * @param string $directory    absolute path to writable cache directory
     * @param int    $cacheminutes cache time in minutes. Set to CACHEMINUTES_INFINITE for infinite caching
     * @param string $type         type of cache
     */
    public function __construct($identifier, $directory, $cacheminutes = 60, $type = self::TYPE_FLAT)
    {
        $this->type = $type;
        switch ($type) {
            case self::TYPE_FLAT:
            case self::TYPE_VARIABLE:
            case self::TYPE_OBJECT:
                $this->persistance = new gwCacheFile($identifier, $directory, $cacheminutes);
                break;
        }
    }

    /**
     * checks if the file is cached
     *
     * @return boolean
     */
    public function isCached()
    {
        return $this->persistance->isCached();
    }

    /**
     * clears the cached file
     *
     * @return boolean|null
     * @uses gwFile::delete()
     */
    public function clear()
    {
        $this->persistance->clear();
    }

    /**
     * sets the cache
     *
     * @param  mixed                 $content
     * @returns int bytes written
     * @throws gwFilesystemException
     */
    public function set($content)
    {
        switch ($this->type) {
            case self::TYPE_VARIABLE:
                $content = '<?php return '.var_export($content, true).';';
                break;
            case self::TYPE_OBJECT:
                $content = serialize($content);
                break;

        }

        return $this->persistance->set($content);
    }

    /**
     * get the cache
     *
     * @return mixed string or false if not cached
     */
    public function get()
    {
        switch ($this->type) {
            case self::TYPE_VARIABLE:
                return include $this->persistance->getFullPath();
            case self::TYPE_OBJECT:
                return unserialize($this->persistance->get());

        }

        return $this->persistance->get();
    }
}
