<?php namespace Pehape\Traits;

use Pehape\Helpers\Objects;
use Pehape\Models\Option;

trait HasCache
{

    /**
     * Cache container
     * @var mixed
     */
    protected $cache;

    /**
     * Cache directory path
     * @var string
     */
    protected $cache_dir;

    /**
     * Load cache file
     *
     * @return self
     */
    protected function LoadCache()
    {
        $this->cache = false;

        /** Check if cache file is present **/
        $cache = $this->cache_dir . '.cache';
        if ($this->cache_dir && file_exists($cache)) {
          $this->cache = file_get_contents($cache);
        }

        /** Make sure the cache content written as a valid JSON string **/
        $this->cache = Objects::ToList($this->cache, true);
        if (is_array($this->cache) && !Objects::IsSequentialIndexed($this->cache)) {
          /** Construct cache array as Option object **/
          $this->cache = new Option($this->cache);
        }
        return $this;
    }

    /**
     * Save cache to file
     *
     * @return self
     */
    protected function SaveCache()
    {
        $cache = $this->cache_dir . '.cache';
        /** Save content to file **/
        if ($this->cache_dir) {
          $content = is_array($this->cache) || is_object($this->cache) ? json_encode($this->cache) : $this->cache;
          @file_put_contents($cache, $content);
        }
        return $this;
    }

}
