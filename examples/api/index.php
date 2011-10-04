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
getRoute()->get('/users/javascript', 'showUsersJavaScript');
getRoute()->get('/params', 'showParams');
getApi()->get('/version.json', 'apiVersion', EpiApi::external);
getApi()->get('/users.json', 'apiUsers', EpiApi::external);
getApi()->get('/params.json', 'apiParams', EpiApi::external);
getApi()->get('/params-internal.json', 'apiParams', EpiApi::internal);
getApi()->get('/params-internal-from-external.json', 'apiParamsFromExternal', EpiApi::external);
getRoute()->run();

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
 * ******************************************************************************************
 */

function showEndpoints()
{
  echo '<ul>
          <li><a href="/api">/</a> -> (home)</li>
          <li><a href="/api/version">/version</a> -> (print the version of the api)</li>
          <li><a href="/api/users">/users</a> -> (print each user)</li>
          <li><a href="/api/users/javascript">/users/javascript</a> -> (make an ajax call to the users.json api)</li>
          <li><a href="/api/params">/params</a> -> (simulate get/post params to the api call)</li>
          <li><a href="/api/version.json">/version.json</a> -> (api endpoint for version.json)</li>
          <li><a href="/api/users.json">/users.json</a> -> (api endpoint for users.json)</li>
          <li><a href="/api/params-internal-from-external.json">/params-internal-from-external.json</a> -> (private api should be accessible from external)</li>
          <li><a href="/api/params-internal.json">/params-internal.json</a> -> (private api should not be accessible)</li>
        </ul>';
}

function showUsers()
{
  $users = getApi()->invoke('/users.json', EpiRoute::httpGet, array('_GET' => array('hello' => 'world')));
  echo '<ul>';
  foreach($users as $user)
  {
    echo "<li>{$user['username']}</li>";
  }
  echo '</ul>';
}

function showUsersJavaScript()
{
  echo <<<MKP
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <a href="/api/users.json">Click to be alerted of users via ajax</a>
    <script>
      $("a").click(function(ev) { 
        var a = ev.target;
        $.get(a.href, {}, function(users) {
          var msg = 'Users are: ';
          for(i in users) {
            msg += users[i].username + " - ";
          }
          alert(msg);
        }, 'json');
        return false;
      });
    </script>
MKP;
}

function showVersion()
{
  echo 'The version of this api is: ' . getApi()->invoke('/version.json');
}

function showParams()
{
  $apiParams = getApi()->invoke('/params.json', EpiRoute::httpGet, array('_GET' => array('caller' => 'api')));

  $apiParams_json = json_encode($apiParams);
  $httpParams = json_encode($_GET);
  echo <<<MKP
    <h3>The _GET params are:</h3>
    <pre>
      {$httpParams}
    </pre>
    <h3>The API params are:</h3>
    <pre>
      {$apiParams_json}
    </pre>
MKP;
}

function apiVersion()
{
  return '1.0';
}

function apiUsers()
{
  return array(
    array('username' => 'jmathai'),
    array('username' => 'stevejobs'),
    array('username' => 'billgates')
  );
}

function apiParams()
{
  return $_GET;
}

function apiParamsFromExternal()
{
  $res = getApi()->invoke('/params-internal.json', EpiRoute::httpGet);
  return $res;
}
