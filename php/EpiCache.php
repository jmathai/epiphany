<?php
class EpiCache
{
  const MEMCACHED = 'EpiCache_Memcached';
  const APC = 'EpiCache_Apc';
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
    if(!file_exists($file = dirname(__FILE__) . "/{$type}.php"))
      throw new EpiCacheTypeDoesNotExistException("EpiCache type does not exist: ({$type}).  Tried loading {$file}", 404);

    require_once $file;
    self::$instances[$hash] = new $type($params);
    self::$instances[$hash]->hash = $hash;
    return self::$instances[$hash];
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

  protected function getByKey()
  {
    $params = func_get_args();
    return $this->get(implode('.', $params));
  }
 
  protected function setByKey()
  {
    $params = func_get_args();
    $value = array_pop($params);
    return $this->set(implode('.', $params), $value);
  }
  
}

if(!class_exists('EpiException')){
  class EpiException extends Exception{}
}

class EpiCacheException extends EpiException{}
class EpiCacheTypeDoesNotExistException extends EpiCacheException{}
?>
