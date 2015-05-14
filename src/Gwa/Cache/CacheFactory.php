<?php
namespace Gwa\Cache;

class CacheFactory
{
	/**
     * @var $persistance CachePersistenceInterface
     */
    protected $persistence;

    /**
     * @var $caches array
     */
    protected $caches;

    /**
     * @param $persistance CachePersistenceInterface
     */
    public function __construct(CachePersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        $this->caches = array();
    }

    /**
     * @param string $identifier
     * @param string $group
     * @param int $cacheminutes
     * @param string $type
     *
     * @return Cache
     */
    public function create($identifier, $group = '', $cacheminutes = 60, $type = Cache::TYPE_FLAT)
    {
    	$key = $group ? $identifier.'-'.$group : $identifier;

    	if (array_key_exists($key, $this->caches)) {
    		return $this->caches[$key];
    	}

    	$cache = new Cache($identifier, $group, $cacheminutes, $type);
    	$cache->setPersistance($this->persistence);
    	$this->caches[$key] = $cache;

    	return $cache;
    }
}
