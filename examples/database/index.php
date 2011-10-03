<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route','database');
EpiDatabase::employ('mysql','mysql','localhost','root',''); // type = mysql, database = mysql, host = localhost, user = root, password = [empty]

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
getRoute()->get('/', 'dbhandler');
getRoute()->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */
function dbhandler()
{
  $users = getDatabase()->all('SELECT * FROM user');
  echo "<h2>All users</h2><ol>";
  foreach($users as $key => $user)
  {
    echo "<li>User {$key} - select privilege = {$user['Select_priv']}</li>";
  }
  echo "</ol>";

  $user = getDatabase()->one('SELECT * FROM user WHERE Host=:Localhost', array(':Localhost' => 'localhost'));
  echo "<h2>First localhost users</h2><ol>";
  echo "<li>First - select privilege = {$user['Select_priv']}</li>";
  echo "</ol>";
}
