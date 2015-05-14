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

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $group;

    /**
     * @var int
     */
    protected $cacheminutes;

    /**
     * @var CachePersistenceInterface
     */
    protected $persistence;

    /**
     * constructor.
     *
     * @param string $identifier   unique (to the group) identifier for this file
     * @param string $group        group
     * @param int    $cacheminutes cache time in minutes. Set to CACHEMINUTES_INFINITE for infinite caching
     * @param string $type         type of cache
     */
    public function __construct($identifier, $group = '', $cacheminutes = 60, $type = self::TYPE_FLAT)
    {
        $this->identifier = $identifier;
        $this->group = $group;
        $this->cacheminutes = $cacheminutes;
        $this->type = $type;
    }

    /**
     * gets the persistence instance.
     *
     * @return  CachePersistenceInterface
     */
    public function getPersistance()
    {
        return $this->persistence;
    }

    /**
     * sets the persistence instance.
     *
     * @param  CachePersistenceInterface $persisance
     * @return  Cache
     */
    public function setPersistance(CachePersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return int
     */
    public function getCacheMinutes()
    {
        return $this->cacheminutes;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * checks if the file is cached.
     *
     * @return boolean
     */
    public function isCached()
    {
        return $this->persistence->isCached($this);
    }

    /**
     * clears the cached file.
     *
     * @return Cache
     *
     * @uses gwFile::delete()
     */
    public function clear()
    {
        $this->persistence->clear($this);
        return $this;
    }

    /**
     * sets the cache.
     *
     * @param mixed $content
     *
     * @return Cache
     *
     * @throws gwFilesystemException
     */
    public function set($content)
    {
        if ($this->type === self::TYPE_OBJECT) {
            $content = serialize($content);
        }

        $this->persistence->set($this, $content);
        return $this;
    }

    /**
     * get the cache.
     *
     * @return mixed string or false if not cached
     */
    public function get()
    {
        if ($this->type === self::TYPE_OBJECT) {
            return unserialize($this->persistence->get($this));
        }

        return $this->persistence->get($this);
    }
}
