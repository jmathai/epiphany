<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('base','session-php');

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
    $session = EpiSession::getInstance(EpiSession::PHP);
    $counter = (int)$session->get('counter');
    $counter++;
    $session->set('counter', $counter);
    echo '<h1>You have clicked ' . $session->get('counter') . ' times <a href="">Reload</a></h1>';
  }
}
