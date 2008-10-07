<?php
class EpiCache
{
  const MEMCACHED = 'EpiCache_Memcached';
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
      case self::MEMCACHED:
        require_once PATH_MODEL . '/' . self::MEMCACHED . '.php';
        self::$instances[$hash] = new EpiCache_Memcached($params[0], $params[1]);
        self::$instances[$hash]->hash = $hash;
        return self::$instances[$hash];
    }
  }

  public function getByKey()
  {
    $params = func_get_args();
    return $this->get(implode('.', $params));
  }

  public function setByKey()
  {
    $params = func_get_args();
    $value  = array_pop($params);
    return $this->set(implode('.', $params), $value);
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
