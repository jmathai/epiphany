<?php
class EpiCache_Memcached extends EpiCache
{
  private $memcached = null;
  private $host = null;
  private $port = null;
  private $compress = null;
  private $expiry   = null;
  public function __construct($params = array())
  {
    $this->host = !empty($params[0] ? $params[0] : 'localhost';
    $this->port = !empty($params[1] ? $params[1] : 11211;;
    $this->compress = !empty($params[2] ? $params[2] : 0;;
    $this->expiry   = !empty($params[3] ? $params[3] : 3600;
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
