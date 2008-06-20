<?php
  include '../php/EpiForm.php';
  
  if(count($_POST) > 0)
  {
    $val = new EpiFormServer();
    if($val->checkFields() > 0)
    {
      header('Location: ' . $_SERVER['PHP_SELF'] . '?__epi__=' . EpiFormServer::encode($_POST));
      die();
    }
  }
  $form = EpiForm::addForm('f');
  //$form->debug(true);
  $form->addField('i')->addType('maxChars', 5)->addEvent('keyup')->addMessage('Cannot be more then 5 chars');
  //$form->addField('i')->addType('maxChars', 5)->addMessage('Cannot be more then 5 chars');
  $form->overloadFail('function(aDef){ YAHOO.util.Dom.setStyle(aDef.el+"-div", "background-color", "red"); YAHOO.util.Dom.get(aDef.el+"-msg").innerHTML=aDef.msg; }');
  $form->overloadPass('function(aDef){ YAHOO.util.Dom.setStyle(aDef.el+"-div", "background-color", ""); YAHOO.util.Dom.get(aDef.el+"-msg").innerHTML=""; }');
?>
<html>
  <head>
    <title>Yui</title>
    <!-- css --> 
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.5.1/build/logger/assets/skins/sam/logger.css"> 
    <!-- js --> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/yahoo/yahoo-debug.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/dom/dom-debug.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/event/event-debug.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/element/element-beta-debug.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/json/json-debug.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.5.1/build/logger/logger-debug.js"></script> 
    <script type="text/javascript" src="/javascript/yui/EpiForm.js"></script> 
    <script>
    </script>
  </head>
<body>
  <?php
    if(isset($_GET['__epi__'])){ echo '<div style="padding:10; margin:10; background:pink; text-align:center;">Error with your form</div>'; }
  ?>
  <form id="f" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div id="i-div" style="padding:10px; border:solid 1px #ccc;">
      <input type="text" name="i" value="test" />
      <div id="i-msg"></div>
    </div>
    <input type="submit" value="submit" id="s" />
    <?php echo $form->prepareForServer(); ?>
  </form>
  <script>
    <?php echo $form->validateJS(); ?>
    <?php
      if(isset($_GET['__epi__']))
      {
        echo $form->repopulate($_GET['__epi__']);
      }
    ?>
  </script>
</body>
</html>
