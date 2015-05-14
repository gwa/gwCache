<?php
namespace Gwa\Cache;

interface CachePersistenceInterface
{
    /**
     * @return boolean
     */
    public function isCached(Cache $cache);
    /**
     * @return false|string
     */
    public function get(Cache $cache);
    /**
     * @return integer
     */
    public function set(Cache $cache, $content);
    /**
     * @return boolean|null
     */
    public function clear(Cache $cache);
}
