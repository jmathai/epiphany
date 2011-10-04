<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route','session-php');
// If you'd like to use Memcached for sessions then init the 'session' or 'session-memcached' module and call EpiSession::employ()
// EpiSession::employ(EpiSession::MEMCACHED);

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
    $counter = (int)getSession()->get('counter');
    $counter++;
    getSession()->set('counter', $counter);
    echo '<h1>You have clicked ' . getSession()->get('counter') . ' times <a href="">Reload</a></h1>';
  }
}
