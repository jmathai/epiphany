<?php
class LogoutController
{
  static public function processLogout()
  {
    // Redirect to the logged in home page
    getSession()->set(Constants::LOGGED_IN, false);

    getRoute()->redirect('/');
  }
}
?>
