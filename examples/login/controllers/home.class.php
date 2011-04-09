<?php
class HomeController
{
  static public function display()
  {
    $params = array();
    $params['body'] = 'home.php';
    $params['title'] = 'Test landing page';
    $params['message'] = 'Hello everyone';

    getTemplate()->display('baseplate.php', $params);
  }
}
?>
