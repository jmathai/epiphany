<?php
$_['routes'] = array(
                  ''        => array(':function:', 'simplex'),
                  'simple'  => array(':function:', 'simple')
                );
  include_once '../php/EpiCode.php';
  
  
  if(EpiCode::getRoute($_GET['__route__'], $_['routes']) === false)
  {
    header('Location: /error');
  }

  function simple()
  {
    echo 'hello world';
  }
?>
