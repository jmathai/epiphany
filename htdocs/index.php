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
                  ''                => array(':function:', 'Home'), // yoursite.com
                  'sample'          => array(':function:', 'MyFunction'), // yoursite.com/sample
                  'sample/method'   => array('MyClass', 'MyMethod'), // yoursite.com/sample/method
                  'function/error'  => array(':function:', 'UndefinedFunction'), // yoursite.com/function/error
                  'source'          => array(':function:', 'ViewSource'), // yoursite.com/source
                  'sample/redirect' => array(':redirect:', 'http://www.google.com') // yoursite.com/sample/redirect
                );
  include_once '../php/EpiCode.php';
  
  
  if(EpiCode::getRoute($_GET['__route__'], $_['routes']) === false)
  {
    echo 'Unhandled route on line ' . __LINE__ . ' of ' . __FILE__;
  }

  
  /*
   * ******************************************************************************************
   * Define functions and classes which are executed by EpiCode based on the $_['routes'] array
   * ******************************************************************************************
   */
  function Home()
  {
    echo '<h1>Welcome to the home page.  Check out these other pages.</h1>
          <ul>
            <li><a href="/sample">Call a function</a></li>
            <li><a href="/sample/method">Call a method</a></li>
            <li><a href="/function/error">Call a function that does not exist</a></li>
            <li><a href="/source">View the source of this page</a></li>
            <li><a href="/sample/redirect">Redirect to Google</a></li>
          </ul>';
  }

  function MyFunction()
  {
    echo '<h1>You called a page which executed MyFunction()</h1>';
  }

  function ViewSource()
  {
    highlight_file(__FILE__);
  }

  class MyClass
  {
    static public function MyMethod()
    {
      echo '<h1>You called MyClass::MyMethod</h1>';
    }
  }
?>
