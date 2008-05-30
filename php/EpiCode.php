<?php
/**
 * EpiCode master file
 *
 * This contains the EpiCode class as wel as the EpiError abstract class
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @version 1.0  
 * @package EpiCode
 */

/**
 * Require included EpiError class which extends EpiAbstract
 */
require_once dirname(__FILE__) . '/EpiError.php';

/**
 * This is the main EpiCode class.
 * @name    EpiCode
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @final
 */
final class EpiCode
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
    try
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
        throw new EpiError(EpiError::EPI_ERROR_TEMPLATE);
      }
    }
    catch(EpiError $e)
    {
      $e->handler();
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
      $route = preg_replace('/\/$/', '', $route);
      
      if(isset($routes[$route]))
      {
        $arg1  = $routes[$route][0];
        $arg2  = $routes[$route][1];

        try
        {
          if(method_exists($arg1, $arg2))
          {
            call_user_func(array($arg1, $arg2));
          }
          else
          {
            throw new EpiError(EpiError::EPI_ERROR_METHOD);
          }
        }
        catch(EpiError $e)
        {
          $e->handler();
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
      try
      {
        throw new EpiError(EpiError::EPI_ERROR_ROUTE);
      }
      catch(EpiError $e)
      {
        $e->handler();
      }
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
    try
    {
      if(file_exists($template))
      {
        include $template;
      }
      else
      {
        throw new EpiError(EpiError::EPI_ERROR_INSERT);
      }
    }
    catch(EpiError $e)
    {
      $e->handler();
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
    try
    {
      if($retval = json_encode($data))
      {
        return $retval;
      }
      else
      {
        throw new EpiError(EpiError::EPI_ERROR_JSON);
      }
    }
    catch(EpiError $e)
    {
      $e->handler();
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
    header('X-JSON: (' . json_encode($data) . ')');
    header('Content-type: application/x-json');
    echo self::json($data);
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
    try
    {
      if($url != '')
      {
        header('Location: ' . $url);
        die();
      }
      else
      {
        throw new EpiError(EpiError::EPI_ERROR_REDIRECT);
      }
    }
    catch(EpiError $e)
    {
      $e->handler();
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
 * This is an abstract class which is extended in EpiError.php
 * @name EpiAbstract
 * @author Jaisen Mathai <jaisen@jmathai.com>
 * @uses Exception
 * @abstract class
 */
abstract class EpiAbstract extends Exception
{
  /**
   * @usedby EpiCode::getRoute
   */
  const EPI_ERROR_ROUTE     = 1;
  
  /**
   * @usedby EpiCode::display
   */
  const EPI_ERROR_TEMPLATE  = 2;

  /**
   * @usedby EpiCode::getRoute
   */
  const EPI_ERROR_METHOD    = 3;

  /**
   * @usedby EpiCode::getRoute
   */
  const EPI_ERROR_FUNCTION  = 4;

  /**
   * @usedby EpiCode::getRoute
   */
  const EPI_ERROR_FILE      = 5;

  /**
   * @usedby EpiCode::getRoute
   * @ignore
   */
  const EPI_ERROR_INSERT    = 6;

  /**
   * @usedby EpiCode::json
   * @usedby EpiCode::jsonRequest
   */
  const EPI_ERROR_JSON      = 7;

  /**
   * @usedby EpiCode::redirect
   */
  const EPI_ERROR_REDIRECT  = 8;
  
  /**
   * @ignore
   */
  public function __construct($code, $message = '')
  {
    parent::__construct($message, $code);
  }

  /**
   * @ignore
   */
  public function handler()
  {
    switch($this->getCode())
    {
      case self::EPI_ERROR_ROUTE:
        $method = '_route';
        break;
      case self::EPI_ERROR_TEMPLATE:
        $method = '_template';
        break;
      case self::EPI_ERROR_METHOD:
        $method = '_method';
        break;
      case self::EPI_ERROR_FUNCTION:
        $method = '_function';
        break;
      case self::EPI_ERROR_FILE:
        $method = '_file';
        break;
      case self::EPI_ERROR_JSON:
        $method = '_json';
        break;
      case self::EPI_ERROR_REDIRECT:
        $method = '_json';
        break;
    }

    if(isset($method))
    {
      return call_user_func(array($this, $method));
    }
  }
  
  /**
   * Implement this method by defining an EpiError class in EpiError.php.
   * @name _route
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _route
   */
  abstract protected function _route();

  /**
   * Implement this method in EpiError.
   * @name _template
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _template
   */
  abstract protected function _template();

  /**
   * Implement this method in EpiError.
   * @name _method
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _method
   */
  abstract protected function _method();

  /**
   * Implement this method in EpiError.
   * @name _function
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _function
   */
  abstract protected function _function();

  /**
   * Implement this method in EpiError.
   * @name _file
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _file
   */
  abstract protected function _file();

  /**
   * Implement this method in EpiError.
   * @name _json
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _json
   */
  abstract protected function _json();

  /**
   * Implement this method in EpiError.
   * @name _redirect
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @method _redirect
   */
  abstract protected function _redirect();
}
?>
