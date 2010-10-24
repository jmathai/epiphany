<?php
/**
 * EpiCode master file
 *
 * This contains the EpiCode class as wel as the EpiException abstract class
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @version 1.0  
 * @package EpiCode
 */
class Epi
{
  private static $properties;
  private static $manifest = array(
    'base'  => 'EpiCode.php',
    'cache' => array('EpiCache.php', 'cache-apc', 'cache-memcached'),
    'cache-apc' => array('EpiCache.php', 'EpiCache_Apc.php'),
    'cache-memcached' => array('EpiCache.php', 'EpiCache_Memcached.php'),
    'session' => array('EpiSession.php', 'session-apc', 'session-memcached'),
    'session-apc' => array('EpiSession.php', 'EpiSession_Apc.php'),
    'session-memcached' => array('EpiSession.php', 'EpiSession_Memcached.php'),
    'database' => 'EpiDatabase.php'
  );
  private static $included = array();
  public static function init()
  {
    $args = func_get_args();
    if(!empty($args))
    {
      foreach($args as $arg)
        self::loadDependency($arg);
    }
  }

  public static function setPath($name, $path)
  {
    self::$properties["{$name}-path"] = $path;
  }

  public static function getPath($name)
  {
    return isset(self::$properties["{$name}-path"]) ? self::$properties["{$name}-path"] : null;
  }

  private static function loadDependency($dep)
  {
    $value = isset(self::$manifest[$dep]) ? self::$manifest[$dep] : $dep;
    if(!is_array($value))
    {
      if(!isset(self::$included[$value]))
        include(self::getPath('base') . "/{$value}");
      self::$included[$value] = 1;
    }
    else
    {
      foreach($value as $d)
        self::loadDependency($d);
    }
  }
}

/**
 * @author Jaisen Mathai <jaisen@jmathai.com>
 * @uses Exception
 */
class EpiException extends Exception
{
  const EPI_EXCEPTION_ROUTE     = 1;
  const EPI_EXCEPTION_TEMPLATE  = 2;
  const EPI_EXCEPTION_METHOD    = 3;
  const EPI_EXCEPTION_FUNCTION  = 4;
  const EPI_EXCEPTION_FILE      = 5;
  const EPI_EXCEPTION_INSERT    = 6;
  const EPI_EXCEPTION_JSON      = 7;
  const EPI_EXCEPTION_REDIRECT  = 8;

  public function __construct($message = '', $code = 0)
  {
    EpiExceptionHandler($message, $code);
  }
}


if(!function_exists('EpiExceptionHandler'))
{
  function EpiExceptionHandler($message, $code)
  {
    echo "<h1>An EpiException was thrown</h1>
          <ul><li>Code: {$code}</li><li>Message: {$message}</li></ul>
          <p>For more information on handling and catching exceptions visit this page.";

  }
}
