<?php
class LoginController
{
  static public function display()
  {
    // Check if we're already logged in, although we SHOULD never get here
    if (getSession()->get(Constants::LOGGED_IN) == true)
    {
        getRoute()->redirect('/dashboard');
    }

    $params = array();
    $params['title'] = 'Login page';
    $params['rid_email'] = 'Email';
    $params['email'] = '';
    $params['rid_pwd'] = 'Password';
    $params['rid_login'] = 'Login';

    getTemplate()->display('login.php', $params);
  }

  static public function processLogin()
  {
    // Confirm the password is correct

    // * Assume it's all good for the time being * //

    // Redirect to the logged in home page
    getSession()->set(Constants::LOGGED_IN, true);

    getRoute()->redirect('/dashboard');
  }
}
?>
