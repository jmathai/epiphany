<?php
define('IS_UNIT_TEST', 1);
define('SRC_DIR', dirname(dirname(__DIR__)));
date_default_timezone_set('America/Los_Angeles');

require SRC_DIR . '/Epi.php';

// if using the phar of phpunit we don't need the pear files
//  see http://phpunit.de/getting-started.html
// else load it from pear
if(file_exists('PHPUnit/Autoload.php'))
  require_once 'PHPUnit/Autoload.php';
