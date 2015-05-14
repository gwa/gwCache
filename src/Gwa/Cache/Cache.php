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
    protected $persistance;

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
     * gets the persistance instance.
     *
     * @return  CachePersistenceInterface $persisance
     */
    public function getPersistance()
    {
        return $this->persistance;
    }

    /**
     * sets the persistance instance.
     *
     * @param  CachePersistenceInterface $persisance
     */
    public function setPersistance(CachePersistenceInterface $persistance)
    {
        $this->persistance = $persistance;
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
        return $this->persistance->isCached($this);
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
        $this->persistance->clear($this);
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

        return $this->persistance->set($this, $content);
    }

    /**
     * get the cache.
     *
     * @return mixed string or false if not cached
     */
    public function get()
    {
        if ($this->type === self::TYPE_OBJECT) {
            return unserialize($this->persistance->get($this));
        }

        return $this->persistance->get($this);
    }
}
