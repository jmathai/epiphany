<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route');
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
getRoute()->get('/', 'home');
// catchall
getRoute()->get('.*', 'error404');
getRoute()->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */

function home() {
  echo "<h1>My Page Header</h1>";
  echo nav();
}

function error404() {
  echo "<h1>404 Page Does Not Exist</h1>";
  echo nav();
}

function nav() {
  return <<<MKP
    <ul>
      <li><a href="./">home</a></li>
      <li><a href="./blog/article.html">blog</a></li>
      <li><a href="./does-not-exist">page which does not exist</a></li>
    </ul>
MKP;
}
