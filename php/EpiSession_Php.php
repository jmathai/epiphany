<?php
class EpiSession_Php implements EpiSessionInterface
{
  public function end()
  {
    $_SESSION = array();

    if (isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time()-42000, '/');
    }

    session_destroy();
  }

  public function get($key = null)
  {
    if(empty($key))
      return false;

    $this->init();
    return $_SESSION[$key];
  }

  public function set($key = null, $value = null)
  {
    if(empty($key))
      return false;
    
    $this->init();
    $_SESSION[$key] = $value;
    return true;
  }

  public function __construct()
  {
    session_start();
  }
}
