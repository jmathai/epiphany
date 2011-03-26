<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('api');

/*
 * We create 3 normal routes (think of these are user viewable pages).
 * We also create 2 api routes (this of these as data methods).
 *  The beauty of the api routes are they can be accessed natively from PHP
 *    or remotely via HTTP.
 *  When accessed over HTTP the response is json.
 *  When accessed natively it's a php array/string/boolean/etc.
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
