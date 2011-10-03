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

getRoute()->get('/', array('HomeController', 'display'));
getRoute()->get('/login', array('LoginController', 'display'));
getRoute()->post('/login', array('LoginController', 'processLogin'));
getRoute()->get('/dashboard', array('DashboardController', 'display'));
getRoute()->get('/logout', array('LogoutController', 'processLogout'));
getRoute()->run();
?>
