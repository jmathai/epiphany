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
  private static $instance;
  private $routes = array();
  private $regexes= array();
  private $route = null;
  const routeKey= '__route__';
  const httpGet = 'GET';
  const httpPost= 'POST';

  /**
   * get('/', 'function');
   * @name  get
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   */
  public function get($path, $callback)
  {
    $this->addRoute($path, $callback, self::httpGet);
  }

  /**
   * post('/', 'function');
   * @name  post
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   */
  public function post($path, $callback)
  {
    $this->addRoute($path, $callback, self::httpPost);
  }
  
  /**
   * NOT YET IMPLEMENTED
   * request('/', 'function', array(EpiRoute::httpGet, EpiRoute::httpPost));
   * @name  request
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   */
  /*public function request($path, $callback, $httpMethods = array(self::httpGet, self::httpPost))
  {
  }*/

  /**
   * load('/path/to/file');
   * @name  load
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $file
   */
  public function load($file)
  {
    $file = Epi::getPath('config') . "/{$file}";
    if(!file_exists($file))
    {
      EpiException::raise(new EpiException("Config file ({$file}) does not exist"));
      break; // need to simulate same behavior if exceptions are turned off
    }

    $parsed_array = parse_ini_file($file, true);
    foreach($parsed_array as $route)
    {
      $method = strtolower($route['method']);
      if(isset($route['class']) && isset($route['function']))
        $this->$method($route['path'], array($route['class'], $route['function']));
      elseif(isset($route['function']))
        $this->$method($route['path'], $route['function']);
    }
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
  public function run($route = false)
  {
    if($route)
      $this->route = $route;
    else
      $this->route = isset($_GET[self::routeKey]) ? $_GET[self::routeKey] : '/';
    foreach($this->regexes as $ind => $regex)
    {
      if(preg_match($regex, $this->route, $arguments))
      {
        array_shift($arguments);
        $def = $this->routes[$ind];
        if($_SERVER['REQUEST_METHOD'] != $def['httpMethod'])
        {
          continue;
        }
        else if(is_array($def['callback']) && method_exists($def['callback'][0], $def['callback'][1]))
        {
          return call_user_func_array($def['callback'], $arguments);
        }
        else if(function_exists($def['callback']))
        {
          return call_user_func_array($def['callback'], $arguments);
        }

        EpiException::raise(new EpiException('Could not call ' . json_encode($def) . " for route {$regex}"));
      }
    }
    EpiException::raise(new EpiException("Could not find route {$this->route} from {$_SERVER['REQUEST_URI']}"));
  }

  /**
   * EpiRoute::redirect($url); 
   * @name  redirect
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $url
   * @method redirect
   * @static method
   */
  public function redirect($url, $code = null, $offDomain = false)
  {
    $continue = !empty($url);
    if($offDomain === false && preg_match('#^https?://#', $url))
      $continue = false;

    if($continue)
    {
      if($code != null && (int)$code == $code)
        header("Status: {$code}");
      header("Location: {$url}");
      die();
    }
    EpiException::raise(new EpiException("Redirect to {$url} failed"));
  }

  public function route()
  {
    return $this->route;
  }

  /*
   * EpiRoute::getInstance
   */
  public static function getInstance()
  {
    if(self::$instance)
      return self::$instance;

    self::$instance = new EpiRoute;
    return self::$instance;
  }

  /**
   * addRoute('/', 'function', 'GET');
   * @name  addRoute
   * @author  Jaisen Mathai <jaisen@jmathai.com>
   * @param string $path
   * @param mixed $callback
   * @param mixed $method
   */
  private function addRoute($path, $callback, $method)
  {
    $this->routes[] = array('httpMethod' => $method, 'path' => $path, 'callback' => $callback);
    $this->regexes[]= "#^{$path}\$#";
  }
}

function getRoute()
{
  return EpiRoute::getInstance();
}
