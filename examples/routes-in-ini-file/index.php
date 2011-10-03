<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::setPath('config', dirname(__FILE__));
Epi::init('route');

/*
 * ******************************************************************************************
 * Load the routes from routes.ini then call run()
 * ******************************************************************************************
 */
getRoute()->load('routes.ini');
getRoute()->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */
class MyClass
{
  static public function MyMethod()
  {
    echo '<h1>You are looking at the output from MyClass::MyMethod</h1>
          <ul>
            <li><a href="/routes-in-ini-file">Call MyClass::MyMethod</a></li>
            <li><a href="/routes-in-ini-file/anotherpage">Call MyClass::MyOtherMethod</a></li>
            </ul>
            <p><img src="https://github.com/images/modules/header/logov3-hover.png"></p>';
  }

  static public function MyOtherMethod()
  {
    echo '<h1>You are looking at the output from MyClass::MyOtherMethod</h1>
          <ul>
            <li><a href="/routes-in-ini-file">Call MyClass::MyMethod</a></li>
            <li><a href="/routes-in-ini-file/anotherpage">Call MyClass::MyOtherMethod</a></li>
          </ul>
          <p><img src="http://www.google.com/images/logos/ps_logo2.png"></p>';
  }
}
