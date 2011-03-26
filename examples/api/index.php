<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('api');

/*
 * This is a sample page whch uses EpiCode.
 * There is a .htaccess file which uses mod_rewrite to redirect all requests to index.php while preserving GET parameters.
 * The $_['routes'] array defines all uris which are handled by EpiCode.
 * EpiCode traverses back along the path until it finds a matching page.
 *  i.e. If the uri is /foo/bar and only 'foo' is defined then it will execute that route's action.
 * It is highly recommended to define a default route of '' for the home page or root of the site (yoursite.com/).
 */
getRoute()->get('/', 'showEndpoints');
getRoute()->get('/version', 'showVersion');
getRoute()->get('/users', 'showUsers');
getApi()->get('/version.json', 'apiVersion', EpiApi::external);
getApi()->get('/users.json', 'apiUsers', EpiApi::external);
getRoute()->run();

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */

function showEndpoints()
{
  echo '<ul>
          <li><a href="/api">/</a></li>
          <li><a href="/api/version">/version</a></li>
          <li><a href="/api/users">/users</a></li>
          <li><a href="/api/version.json">/version.json</a></li>
          <li><a href="/api/users.json">/users.json</a></li>
        </ul>';
}

function showUsers()
{
  $users = getApi()->invoke('/users.json');
  echo '<ul>';
  foreach($users as $user)
  {
    echo "<li>{$user['username']}</li>";
  }
  echo '</ul>';
}

function showVersion()
{
  echo 'The version of this api is: ' . getApi()->invoke('/version.json');
}

function apiVersion()
{
  return '1.0';
}

function apiUsers()
{
  return array(
    array('username' => 'jmathai'),
    array('username' => 'billgates')
  );
}
