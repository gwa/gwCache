<?php
namespace Gwa\Cache;

interface gwiCachePersistance
{
    public function isCached();
    public function get();
    public function set($content);
    public function clear();
}
