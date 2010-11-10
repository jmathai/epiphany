<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route','session-php');

/*
 * This is a sample page which uses native php sessions
 * It's easy to switch the session backend by passing a different value to getInstance.
 *  For example, EpiSession::getInstance(EpiSession::Memcached);
 */
$router = new EpiRoute();
$router->get('/', array('MyClass', 'MyMethod'));
$router->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiRoute
 * ******************************************************************************************
 */
class MyClass
{
  static public function MyMethod()
  {
    $session = EpiSession::getInstance(EpiSession::PHP);
    $counter = (int)$session->get('counter');
    $counter++;
    $session->set('counter', $counter);
    echo '<h1>You have clicked ' . $session->get('counter') . ' times <a href="">Reload</a></h1>';
  }
}
