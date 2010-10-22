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
  public static function init()
  {
    include self::getPath('base') . '/EpiCode.php';
  }

  public static function setPath($name, $path)
  {
    self::$properties["{$name}-path"] = $path;
  }

  public static function getPath($name)
  {
    return isset(self::$properties["{$name}-path"]) ? self::$properties["{$name}-path"] : null;
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
