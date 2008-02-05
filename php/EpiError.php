<?php
/*
 * Class:        EpiError
 * Description:  Minimalistic framework for PHP
 * Authors:      Jaisen Mathai <jaisen@jmathai.com>
 */

class EpiError extends EpiAbstract
{
  public function _route()
  {
    echo 'Route error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _template()
  {
    echo 'Template error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _method()
  {
    echo 'Method error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _function()
  {
    echo 'Function error handled by EpiError.  
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _file()
  {
    echo 'File error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _json()
  {
    echo 'Json error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }

  public function _redirect()
  {
    echo 'Redirect error handled by EpiError.  Possible reasons:
          Error ' . $this->getCode() . ' occured on line ' . $this->getLine() . ' of ' . $this->getFile() . '.  
          <ul>
            <li>' . $this->getMessage() . '</li>
          </ul>
          You can customize this message or handle it differently by editing EpiError.php';
  }
}
?>
