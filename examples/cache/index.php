<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route','cache-apc');
// If you'd like to use Memcached for cache then init the 'cache' or 'cache-memcached' module and call EpiCache::employ()
// EpiCache::employ(EpiCache::MEMCACHED);

/*
 * This is a sample page which uses native php sessions
 * It's easy to switch the session backend by passing a different value to getInstance.
 *  For example, EpiSession::getInstance(EpiSession::Memcached);
 */
getRoute()->get('/', array('MyClass', 'MyMethod'));
getRoute()->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiRoute
 * ******************************************************************************************
 */
class MyClass
{
  static public function MyMethod()
  {
    if(isset($_GET['name']))
      getCache()->set('name', $_GET['name']);

    $name = getCache()->get('name');
    if(empty($name))
      $name = '[Enter your name]';
    echo '<h1>Hello '. $name . '</h1><p><form><input type="text" size="30" name="name"><br><input type="submit" value="Enter your name"></form></p>';
  }
}
