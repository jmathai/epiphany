<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::setSetting('debug', true);
Epi::init('route','debug');

getRoute()->get('/', array('MyClass', 'MyMethod'));
getRoute()->get('/sample', array('MyClass', 'MyOtherMethod'));
getRoute()->get('/somepath/source', array('MyClass', 'ViewSource'));
getRoute()->run(); 

echo '<pre>';
echo getDebug()->renderAscii();
echo '</pre>';
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
            <li><a href="/debug">Call MyClass::MyMethod</a></li>
            <li><a href="/debug/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/debug/somepath/source">View the source of this page</a></li>
            </ul>
            <p><img src="https://github.com/images/modules/header/logov3-hover.png"></p>';
  }

  static public function MyOtherMethod()
  {
    echo '<h1>You are looking at the output from MyClass::MyOtherMethod</h1>
          <ul>
            <li><a href="/debug">Call MyClass::MyMethod</a></li>
            <li><a href="/debug/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/debug/somepath/source">View the source of this page</a></li>
          </ul>
          <p><img src="http://www.google.com/images/logos/ps_logo2.png"></p>';
  }

  static public function ViewSource()
  {
    echo '<h1>You are looking at the output from MyClass::ViewSource</h1>
          <ul>
            <li><a href="/debug">Call MyClass::MyMethod</a></li>
            <li><a href="/debug/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/debug/somepath/source">View the source of this page</a></li>
          </ul>';
    highlight_file(__FILE__);
  }
}
