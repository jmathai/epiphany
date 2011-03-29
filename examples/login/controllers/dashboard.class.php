<?php
class DashboardController
{
  static public function display()
  {
    $params = array();
    $params['body'] = 'dashboard.php';
    $params['title'] = 'Dashboard page';
    $params['message'] = 'Details to show the user about their projects';

    getTemplate()->display('baseplate.php', $params);
  }
}
?>
