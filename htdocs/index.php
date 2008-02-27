<?php
  /*
   * This is a sample page whch uses EpiCode.
   * There is a .htaccess file which uses mod_rewrite to redirect all requests to index.php while preserving GET parameters.
   * The $_['routes'] array defines all uris which are handled by EpiCode.
   * EpiCode traverses back along the path until it finds a matching page.
   *  i.e. If the uri is /foo/bar and only 'foo' is defined then it will execute that route's action.
   * It is highly recommended to define a default route of '' for the home page or root of the site (yoursite.com/).
   */
  $_['routes'] = array(
                  ''                => array('MyClass', 'MyMethod'), // yoursite.com
                  'sample'          => array('MyClass', 'MyOtherMethod'), // yoursite.com/sample
                  'anypath/source'  => array('MyClass', 'ViewSource'), // yoursite.com/sample
                );
  include_once '../php/EpiCode.php';
  
  EpiCode::getRoute($_GET['__route__'], $_['routes']); 


  /*
   * ******************************************************************************************
   * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
   * ******************************************************************************************
   */
  class MyClass
  {
    static public function MyMethod()
    {
      echo '<h1>You are looking at the output from MyClass::MyMethod</h1>
            <ul>
              <li><a href="/">Call MyClass::MyMethod</a></li>
              <li><a href="/sample">Call MyClass::MyOtherMethod</a></li>
              <li><a href="/anypath/source">View the source of this page</a></li>
            </ul>';
    }

    static public function MyOtherMethod()
    {
      echo '<h1>You are looking at the output from MyClass::MyOtherMethod</h1>
            <ul>
              <li><a href="/">Call MyClass::MyMethod</a></li>
              <li><a href="/sample">Call MyClass::MyOtherMethod</a></li>
              <li><a href="/anypath/source">View the source of this page</a></li>
            </ul>';
    }

    static public function ViewSource()
    {
      echo '<h1>You are looking at the output from MyClass::ViewSource</h1>
            <ul>
              <li><a href="/">Call MyClass::MyMethod</a></li>
              <li><a href="/sample">Call MyClass::MyOtherMethod</a></li>
              <li><a href="/anypath/source">View the source of this page</a></li>
            </ul>';
      highlight_file(__FILE__);
    }
  }
?>
