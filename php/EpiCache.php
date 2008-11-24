<?php
class EpiCache
{
  const EPICACHE_MEMCACHED = 'EpiCache_Memcached';
  const EPICACHE_APC = 'EpiCache_Apc';
  private static $instances;
  private $cached;
  private $hash;
  private function __construct(){}
  
  /*
   * @param  type  required
   * @params optional
   */
  public static function getInstance()
  {
    $params = func_get_args();
    $hash   = md5(implode('.', $params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    $type = array_shift($params);
    switch($type)
    {
      case self::EPICACHE_MEMCACHED:
        require_once PATH_MODEL . '/' . self::EPICACHE_MEMCACHED . '.php';
        self::$instances[$hash] = new EpiCache_Memcached($params[0], $params[1]);
        self::$instances[$hash]->hash = $hash;
        return self::$instances[$hash];
      case self::EPICACHE_APC:
        require_once PATH_MODEL . '/' . self::EPICACHE_APC . '.php';
        self::$instances[$hash] = new EpiCache_Apc();
        self::$instances[$hash]->hash = $hash;
        return self::$instances[$hash];
    }
  }

  protected function getEpiCache($key)
  {
    if(isset($this->cached[$this->hash][$key]))
      return $this->cached[$this->hash][$key];
    else
      return false;
  }

  protected function setEpiCache($key, $value)
  {
    $this->cached[$this->hash][$key] = $value;
  }
}
?>
