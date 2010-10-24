<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::setPath('view', '../src');
Epi::init('base','cache');
// Epi::init('base','cache','session');
// Epi::init('base','cache-apc','session-apc');
// Epi::init('base','cache-memcached','session-apc');

/*
 * This is a sample page whch uses EpiCode.
 * There is a .htaccess file which uses mod_rewrite to redirect all requests to index.php while preserving GET parameters.
 * The $_['routes'] array defines all uris which are handled by EpiCode.
 * EpiCode traverses back along the path until it finds a matching page.
 *  i.e. If the uri is /foo/bar and only 'foo' is defined then it will execute that route's action.
 * It is highly recommended to define a default route of '' for the home page or root of the site (yoursite.com/).
 */
$_['routes'] = array(
                ''                => array('MyClass', 'MyMethod'), // yoursite.com
                'sample'          => array('MyClass', 'MyOtherMethod'), // yoursite.com/sample
                'anypath/source'  => array('MyClass', 'ViewSource'), // yoursite.com/sample
              );

$route = isset($_GET['__route__']) ? $_GET['__route__'] : '';
EpiCode::getRoute($route, $_['routes']); 


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
            <li><a href="/simple-site">Call MyClass::MyMethod</a></li>
            <li><a href="/simple-site/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/simple-site/anypath/source">View the source of this page</a></li>
            </ul>
            <p><img src="http://c.asstatic.com/images/Octavio-11464-Funny-images-music-Entertainment-ppt-powerpoint-118_88.jpg"></p>';
  }

  static public function MyOtherMethod()
  {
    echo '<h1>You are looking at the output from MyClass::MyOtherMethod</h1>
          <ul>
            <li><a href="/simple-site">Call MyClass::MyMethod</a></li>
            <li><a href="/simple-site/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/simple-site/anypath/source">View the source of this page</a></li>
          </ul>
          <p><img src="http://zef.me/wp-content/uploads/2008/02/funny-cat.jpg"></p>';
  }

  static public function ViewSource()
  {
    echo '<h1>You are looking at the output from MyClass::ViewSource</h1>
          <ul>
            <li><a href="/simple-site">Call MyClass::MyMethod</a></li>
            <li><a href="/simple-site/sample">Call MyClass::MyOtherMethod</a></li>
            <li><a href="/simple-site/anypath/source">View the source of this page</a></li>
          </ul>';
    highlight_file(__FILE__);
  }
}
