<?php
include_once '../epiphany/src/Epi.php';
include_once 'controllers/home.class.php';
include_once 'controllers/login.class.php';
include_once 'controllers/dashboard.class.php';
include_once 'controllers/logout.class.php';
include_once 'lib/constants.class.php';

Epi::setSetting('exceptions', true);
Epi::setPath('base', '../epiphany/src');
Epi::setPath('view', './views');
Epi::init('route','template','session');

$router = new EpiRoute();
$router->get('/', array('HomeController', 'display'));
$router->get('/login', array('LoginController', 'display'));
$router->post('/login', array('LoginController', 'processLogin'));
$router->get('/dashboard', array('DashboardController', 'display'));
$router->get('/logout', array('LogoutController', 'processLogout'));
$router->run();
?>
