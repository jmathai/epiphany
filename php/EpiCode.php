<?php
/*
 * Class:        EpiCode
 * Description:  Minimalistic framework for PHP
 * Authors:      Jaisen Mathai <jaisen@jmathai.com>
 *               John Mcfarlane <john.mcfarlane@rockfloat.com>
 */

// Require the error handling class
require_once dirname(__FILE__) . '/EpiError.php';

final class EpiCode
{
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
  
  public static function getRoute($route = null, $routes = null)
  { 
    if($route != '.' && is_array($routes))
    {
      $route = preg_replace('/\/$/', '', $route);
      
      if(isset($routes[$route]))
      {
        $arg1  = $routes[$route][0];
        $arg2  = $routes[$route][1];

        switch($arg1)
        {
          case ':function:':
            try
            {
              if(function_exists($arg2))
              {
                call_user_func($arg2);
              }
              else
              {
                throw new EpiError(EpiError::EPI_ERROR_FUNCTION);
              }
            }
            catch(EpiError $e)
            {
              $e->handler();
            }
            break;
          case ':json:':
            try
            {
              if(isset($arg2))
              {
                self::jsonResponse($arg2);
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
            break;
          case ':redirect:':
            try
            {
              if($arg2 != '')
              {
                self::redirect($arg2);
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
            break;
          default:
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
            break;
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
  
  public static function jsonResponse($data)
  {
    header('X-JSON: (' . json_encode($data) . ')');
    header('Content-type: application/x-json');
    echo self::json($data);
    die();
  }
  
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
  
  public static function write($contents = null)
  {
    echo $contents;
    return true;
  }
}

abstract class EpiAbstract extends Exception
{
  const EPI_ERROR_ROUTE     = 1;
  const EPI_ERROR_TEMPLATE  = 2;
  const EPI_ERROR_METHOD    = 3;
  const EPI_ERROR_FUNCTION  = 4;
  const EPI_ERROR_FILE      = 5;
  const EPI_ERROR_INSERT    = 6;
  const EPI_ERROR_JSON      = 7;
  const EPI_ERROR_REDIRECT  = 8;
  
  public function __construct($code, $message = '')
  {
    parent::__construct($message, $code);
  }

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
  
  /*
   * Abstract methods to be defined in EpiError class
   */
  abstract protected function _route();
  abstract protected function _template();
  abstract protected function _method();
  abstract protected function _function();
  abstract protected function _file();
  abstract protected function _json();
  abstract protected function _redirect();
}
?>
