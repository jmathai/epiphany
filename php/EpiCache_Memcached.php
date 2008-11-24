<?php
class EpiCache_Memcached extends EpiCache
{
  private $memcached = null;
  private $host = null;
  private $port = null;
  private $compress = null;
  private $expiry   = null;
  public function __construct($host, $port, $compress = 0, $expiry = 3600)
  {
    $this->host = $host;
    $this->port = $port;
    $this->compress = $compress;
    $this->expiry   = $expiry;
  }

  public function get($key)
  {
    if(!$this->connect() || empty($key))
    {
      return null;
    }
    else if($getEpiCache = $this->getEpiCache($key))
    {
      return $getEpiCache;
    }
    else
    {
      $value = $this->memcached->get($key);
      $this->setEpiCache($key, $value);
      return $value;
    }
  }

  public function set($key = null, $value = null, $ttl = null)
  {
    if(!$this->connect() || empty($key) || $value === null)
      return false;

    $expiry = $ttl === null ? $this->expiry : $ttl;
    $this->memcached->set($key, $value, $this->compress, $expiry);
    $this->setEpiCache($key, $value);
    return true;
  }

  private function connect()
  {
    if(class_exists('Memcache'))
    {
      $this->memcached = new Memcache;
      if($this->memcached->connect($this->host, $this->port))
        return true;
    }

    return false;
  }
}
?>
