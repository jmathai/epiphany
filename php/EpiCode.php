<?php
/*
* Class:        EpiCode
* Description:  Minimalistic framework for PHP
* Authors:      Jaisen Mathai <jaisen@jmathai.com>
*               John Mcfarlane <john.mcfarlane@rockfloat.com>
*/

/*
* EpiCode defines constants if they do not yet exist
* Define these constants prior to including this file if you have a custom directory structure
*/

// EPICODE_VIEWS is the full path to the directory that contains the views (default :: ./views)
if(!defined('EPICODE_VIEWS'))
{
  define('EPICODE_VIEWS', dirname(__FILE__) . '/views');
}

// EPICODE_BASE is the full path to the directory that contains your core files (default :: ../)
if(!defined('EPICODE_BASE'))
{
  define('EPICODE_BASE', dirname(dirname(__FILE__)));
}

final class EpiCode
{
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
      // raise an error?
      echo 'Could not find ' . $templateInclude;
    }
  }
  
  public static function getRoute($route = null, $routes = null)
  { 
    if($route != '.' && is_array($routes))
    {
      $route = preg_replace('/\/$/', '', $route);
      
      if(isset($routes[$route]))
      {
        $class = $routes[$route][0];
        $method= $routes[$route][1];
        include_once PATH_MODEL . '/' . $class . '.php';
        
        if(class_exists($class) && method_exists($class, $method))
        {
          call_user_func(array($class, $method));
        }
        else
        {
          // raise an error?
          echo 'Could not call ' . $class . '::' . $method;
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
      return false;
    }
  }
  
  public static function insert($template = null)
  {
    if(strncmp(EPICODE_BASE, $template, strlen(EPICODE_BASE)) == 0)
    {
      include $template;
    }
  }
  
  public static function json($data)
  {
    return json_encode($data);
  }
  
  public static function jsonResponse($data)
  {
    header('X-JSON: (' . json_encode($data) . ')');
    header('Content-type: application/x-json');
    echo json_encode($data);
    die();
  }
  
  public static function redirect($url = null)
  {
    if($url !== null)
    {
      header('Location: ' . $url);
      die();
    }
  }
  
  public static function write($contents = null)
  {
    echo $contents;
    return true;
  }
}
?>