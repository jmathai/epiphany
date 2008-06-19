<?php
  include '../php/EpiForm.php';
  $form = EpiForm::addForm('f');
  //$form->debug(true);
  $form->addField('i')->addType('maxChars', 5)->addEvent('keyup')->addEvent('mouseup')->addMessage('Cannot be more then 5 chars');
  $form->overloadPass('function(aDef){ alert(aDef.toSource()); }');
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
  <form id="f">
    <input type="text" id="i" value="test" />
    <input type="submit" value="submit" id="s" />
    <?php echo $form->prepareForServer(); ?>
  </form>
  <script>
    <?php echo $form->validateJS(); ?>
  </script>
</body>
</html>
