<?php
namespace Gwa\Cache;

interface gwiCachePersistance
{
    /**
     * @return boolean
     */
    public function isCached();
    /**
     * @return false|string
     */
    public function get();
    /**
     * @return integer
     */
    public function set($content);
    /**
     * @return boolean|null
     */
    public function clear();
}
