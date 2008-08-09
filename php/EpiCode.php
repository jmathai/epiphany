<?php
/**
 * EpiCode master file
 *
 * This contains the EpiCode class as wel as the EpiException abstract class
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @version 1.0  
 * @package EpiCode
 */

// EPICODE_BASE is the full path to the directory that contains your core files (default :: ../)
if(!defined('EPICODE_BASE'))
{
  define('EPICODE_BASE', dirname(dirname(__FILE__)));
}

// EPICODE_VIEWS is the full path to the directory that contains the views (default :: ./views)
if(!defined('EPICODE_VIEWS'))
{
  define('EPICODE_VIEWS', EPICODE_BASE . '/views');
}



/**
 * This is the main EpiCode class.
 * @name    EpiCode
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @final
 */
class EpiCode
{
  /**
   * EpiCode::display('/path/to/template.php', $array);
   * @name  display
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $template
   * @param array $vars
   * @method display
   * @static method
   */
  public static function display($template = null, $vars = null)
  {
    $templateInclude = EPICODE_VIEWS . '/' . $template;
    if(is_file($templateInclude))
    {
      if(is_array($vars))
      {
        extract($vars);
      }
      
      include $templateInclude;
    }
    else
    {
      throw new EpiException("Could not load template: {$templateInclude}", EpiException::EPI_EXCEPTION_TEMPLATE);
    }
  }
  
  /**
   * EpiCode::get('/path/to/template.php', $array);
   * @name  get
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $template
   * @param array $vars
   * @method get
   * @static method
   */
  public static function get($template = null, $vars = null)
  {
    $templateInclude = EPICODE_VIEWS . '/' . $template;
    if(is_file($templateInclude))
    {
      if(is_array($vars))
      {
        extract($vars);
      }
      ob_start();
      include $templateInclude;
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
    else
    {
      throw new EpiException("Could not load template: {$templateInclude}", EpiException::EPI_EXCEPTION_TEMPLATE);
    }
  }

  /**
   * EpiCode::getRoute($_GET['__route__'], $_['routes']); 
   * @name  getRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param array $routes
   * @method getRoute
   * @static method
   */
  public static function getRoute($route = null, $routes = null)
  { 
    if($route != '.' && is_array($routes))
    {
      $route = preg_replace('/(\/|\?.*)$/', '', $route);
      
      if(isset($routes[$route]))
      {
        $arg1  = $routes[$route][0];
        $arg2  = $routes[$route][1];

        if(method_exists($arg1, $arg2))
        {
          call_user_func(array($arg1, $arg2));
        }
        else
        {
          throw new EpiException("Could not call {$arg1}::{$arg2}", EpiException::EPI_EXCEPTION_METHOD);
        }
      }
      else
      {
        if(dirname($route) != '')
        {
          return self::getRoute(dirname($route), $routes);
        }
      }
    }
    else
    {
      throw new EpiException("Could not find route {$route} from {$_SERVER['REQUEST_URI']}", EpiException::EPI_EXCEPTION_ROUTE);
    }
  }
  
  /**
   * EpiCode::insert('/path/to/template.php'); 
   * This method is experimental and undocumented
   * @name  insert
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $template
   * @method insert
   * @static method
   * @ignore
   */
  public static function insert($template = null)
  {
    if(file_exists($template))
    {
      include $template;
    }
    else
    {
      throw new EpiException("Could not insert {$template}", EpiException::EPI_EXCEPTION_INSERT);
    }
  }
  
  /**
   * EpiCode::json($variable); 
   * @name  json
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param mixed $data
   * @return string
   * @method json
   * @static method
   */
  public static function json($data)
  {
    if($retval = json_encode($data))
    {
      return $retval;
    }
    else
    {
      throw new EpiException("JSON encode failed", EpiException::EPI_EXCEPTION_JSON);
    }
  }
  
  /**
   * EpiCode::jsonResponse($variable); 
   * This method echo's JSON data in the header and to the screen and returns.
   * @name  jsonResponse
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param mixed $data
   * @method jsonResponse
   * @static method
   */
  public static function jsonResponse($data)
  {
    $json = self::json($data);
    header('X-JSON: (' . json_encode($data) . ')');
    header('Content-type: application/x-json');
    echo $json;
  }
  
  /**
   * EpiCode::redirect($url); 
   * @name  redirect
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $url
   * @method redirect
   * @static method
   */
  public static function redirect($url = null)
  {
    if($url != '')
    {
      header('Location: ' . $url);
      die();
    }
    else
    {
      throw new EpiException(EpiException::EPI_EXCEPTION_REDIRECT, "Redirect to {$url} failed");
    }
  }
  
  /**
   * EpiCode::write($contents); 
   * This method is experimental and undocumented.
   * @name  write
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $content
   * @method write
   * @static method
   * @ignore
   */
  public static function write($contents = null)
  {
    echo $contents;
    return true;
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

  const EPI_EXCEPTION_DB_CONNECTION = 100;
  const EPI_EXCEPTION_DB_QUERY = 101;

  public function __construct($message = '', $code = 0)
  {
    if(function_exists('EpiExceptionHandler'))
    {
      EpiExceptionHandler($message, $code);
    }
    else
    {
      parent::__construct($message, $code);
    }
  }
}
?>
