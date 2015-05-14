<?php
namespace Gwa\Cache;

/**
 * Provides simple caching functions.
 */
class Cache
{
    const CACHEMINUTES_INFINITE = -1;

    /**
     * Flat text type.
     *
     * @var string
     */
    const TYPE_FLAT = 'Cache::type_flat';

    /**
     * PHP object type.
     *
     * @var string
     */
    const TYPE_OBJECT = 'Cache::type_object';

    protected $type;

    protected $persistance;

    /**
     * constructor.
     *
     * @param string $identifier   unique identifier for this file
     * @param string $directory    absolute path to writable cache directory
     * @param int    $cacheminutes cache time in minutes. Set to CACHEMINUTES_INFINITE for infinite caching
     * @param string $type         type of cache
     */
    public function __construct($identifier, $directory, $cacheminutes = 60, $type = self::TYPE_FLAT, $persistanceclass = 'Gwa\Cache\CacheFile')
    {
        $this->type = $type;
        $this->setPersistance(new $persistanceclass($identifier, $directory, $cacheminutes));
    }

    /**
     * gets the persistance instance.
     *
     * @return  CachePersistanceInterface $persisance
     */
    public function getPersistance()
    {
        return $this->persistance;
    }

    /**
     * sets the persistance instance.
     *
     * @param  CachePersistanceInterface $persisance
     */
    public function setPersistance(CachePersistanceInterface $persistance)
    {
        $this->persistance = $persistance;
    }

    /**
     * checks if the file is cached.
     *
     * @return boolean
     */
    public function isCached()
    {
        return $this->persistance->isCached();
    }

    /**
     * clears the cached file.
     *
     * @return boolean|null
     *
     * @uses gwFile::delete()
     */
    public function clear()
    {
        $this->persistance->clear();
    }

    /**
     * sets the cache.
     *
     * @param mixed $content
     *
     * @return int bytes written
     *
     * @throws gwFilesystemException
     */
    public function set($content)
    {
        if ($this->type === self::TYPE_OBJECT) {
            $content = serialize($content);
        }

        return $this->persistance->set($content);
    }

    /**
     * get the cache.
     *
     * @return mixed string or false if not cached
     */
    public function get()
    {
        if ($this->type === self::TYPE_OBJECT) {
            return unserialize($this->persistance->get());
        }

        return $this->persistance->get();
    }
}
