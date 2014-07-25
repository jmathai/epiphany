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
  private static $properties = array('exceptions-setting' => false);
  private static $manifest = array(
    '*' => array('api', 'base','route','template','cache','session','database'),
    'api' => array('base', 'EpiApi.php', 'route'),
    'base' => array(),
    'debug' => array('base', 'EpiDebug.php'),
    'route'  => array('base', 'EpiRoute.php'),
    'template' => array('base', 'EpiTemplate.php'),
    'cache' => array('base', 'EpiCache.php', 'cache-apc', 'cache-file', 'cache-memcached'),
    'cache-apc' => array('base', 'EpiCache.php', 'EpiCache_Apc.php'),
    'cache-file' => array('base', 'EpiCache.php', 'EpiCache_File.php'),
    'cache-memcached' => array('base', 'EpiCache.php', 'EpiCache_Memcached.php'),
    'config' => array('base', 'EpiConfig.php', 'config-file', 'config-mysql'),
    'config-file' => array('base', 'EpiConfig.php', 'EpiConfig_File.php'),
    'config-mysql' => array('base', 'database', 'EpiConfig.php', 'EpiConfig_MySql.php'),
    'logger' => array('base', 'EpiLogger.php'),
    'session' => array('base', 'EpiSession.php', 'session-php', 'session-apc', 'session-memcached'),
    'session-php' => array('base', 'EpiSession.php', 'EpiSession_Php.php'),
    'session-apc' => array('base', 'EpiSession.php', 'EpiSession_Apc.php'),
    'session-memcached' => array('base', 'EpiSession.php', 'EpiSession_Memcached.php'),
    'database' => array('base', 'EpiDatabase.php')
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

  public static function setSetting($name, $value)
  {
    self::$properties["{$name}-setting"] = $value;
  }

  public static function getSetting($name)
  {
    return isset(self::$properties["{$name}-setting"]) ? self::$properties["{$name}-setting"] : false;
  }

  private static function loadDependency($dep)
  {
    $value = isset(self::$manifest[$dep]) ? self::$manifest[$dep] : $dep;
    if(!is_array($value))
    {
      if(!isset(self::$included[$value]))
      {
          $status = @include(self::getPath('base') . "/{$value}");
          if(!$status)
            throw new EpiDependencyException(sprintf('Could not load %s from %s', $value, self::getPath('base')));
      }
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
  public static function raise($exception)
  {
    $useExceptions = Epi::getSetting('exceptions');
    if($useExceptions)
    {
      throw new $exception($exception->getMessage(), $exception->getCode());
    }
    else
    {
      echo sprintf("An error occurred and you have <strong>exceptions</strong> disabled so we're displaying the information.
                    To turn exceptions on you should call: <em>Epi::setSetting('exceptions', true);</em>.
                    <ul><li>File: %s</li><li>Line: %s</li><li>Message: %s</li><li>Stack trace: %s</li></ul>",
                    $exception->getFile(), $exception->getLine(), $exception->getMessage(), nl2br($exception->getTraceAsString()));
    }
  }
}
class EpiDependencyException extends EpiException {}
class EpiCacheException extends EpiException{}
class EpiCacheTypeDoesNotExistException extends EpiCacheException{}
class EpiCacheMemcacheClientDneException extends EpiCacheException{}
class EpiCacheMemcacheConnectException extends EpiCacheException{}
class EpiDatabaseException extends EpiException{}
class EpiDatabaseConnectionException extends EpiDatabaseException{}
class EpiDatabaseQueryException extends EpiDatabaseException{}
class EpiSessionException extends EpiException{}

