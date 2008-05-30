<?php
  sleep($_GET['secs']);
  echo 'timeout of ' . $_GET['secs'] . ' in ' . __FILE__;
?>
