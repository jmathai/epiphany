<?php
chdir('..');
include_once '../src/Epi.php';
Epi::setPath('base', '../src');
Epi::init('route');

/*
 * This file is an example of using regular expressions in a route.
 * You can use subpatterns which are passed to the function as parameters.
 */
getRoute()->get('/', 'home');
getRoute()->get('/(\w+)/(\w+)', 'greeting');
getRoute()->run(); 

/*
 * ******************************************************************************************
 * Define functions and classes which are executed by EpiRoute
 * ******************************************************************************************
 */
function home()
{
  echo '<h1>Home page. <a href="jaisen/mathai">Click for greeting</a></h1>';
}

function greeting($firstName, $lastName)
{
  echo "<h1>Welcome, {$firstName} {$lastName}</h1>";
}
