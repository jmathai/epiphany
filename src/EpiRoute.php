<?php
/**
 * EpiRoute master file
 *
 * This contains the EpiRoute class as wel as the EpiException abstract class
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @version 1.0  
 * @package EpiRoute
 */

/**
 * This is the EpiRoute class.
 * @name    EpiRoute
 * @author  Jaisen Mathai <jaisen@jmathai.com>
 * @final
 */
class EpiRoute
{
  private $routes = array();
  private $regexes= array();

  /**
   * addRoute('GET', '/', 'function');
   * @name  addRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param array $routes
   * @method run
   * @static method
   */
  public function addRoute($method, $path, $callback)
  {
    $this->routes[] = array('httpMethod' => $method, 'path' => $path, 'callback' => $callback);
    $this->regexes[]= "#^{$path}\$#";
  }

  /**
   * EpiRoute::run($_GET['__route__'], $_['routes']); 
   * @name  run
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $route
   * @param array $routes
   * @method run
   * @static method
   */
  public function run($key = '__route__')
  {
    $route = isset($_GET[$key]) ? $_GET[$key] : '/';
    foreach($this->regexes as $ind => $regex)
    {
      if(preg_match($regex, $route, $arguments))
      {
        array_shift($arguments);
        $def = $this->routes[$ind];
        if(is_array($def['callback']) && method_exists($def['callback'][0], $def['callback'][1]))
        {
          return call_user_func_array($def['callback'], $arguments);
        }
        else if(function_exists($def['callback']))
        {
          return call_user_func_array($def['callback'], $arguments);
        }

        throw new EpiException('Could not call ' . json_encode($def) . " for route {$regex}", EpiException::EPI_EXCEPTION_METHOD);
      }
    }
    throw new EpiException("Could not find route {$route} from {$_SERVER['REQUEST_URI']}", EpiException::EPI_EXCEPTION_ROUTE);
  }

  /**
   * EpiRoute::redirect($url); 
   * @name  redirect
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $url
   * @method redirect
   * @static method
   */
  public function redirect($url, $code = null)
  {
    if($url != '')
    {
      if($code != null && (int)$code == $code)
        header("Status: {$code}");
      header("Location: {$url}");
      die();
    }
    else
    {
      throw new EpiException(EpiException::EPI_EXCEPTION_REDIRECT, "Redirect to {$url} failed");
    }
  }
}
