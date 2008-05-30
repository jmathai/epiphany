<?php
  ob_start();
  include '../php/EpiCurl.php';
  $mc = EpiCurl::getInstance();
  $ch1 = curl_init('http://mac.episuite.com/timeout.php?secs=0');
  curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
  $curl1 = $mc->addCurl($ch1);
  echo 'I started 1 at ' . date('h:i:s', time()) . '<br>';
  $ch2 = curl_init('http://mac.episuite.com/timeout.php?secs=2');
  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
  $curl2 = $mc->addCurl($ch2);
  echo 'I started 2 at ' . date('h:i:s', time()) . '<br>';
  $ch3 = curl_init('http://mac.episuite.com/timeout.php?secs=8');
  curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
  $curl3 = $mc->addCurl($ch3);
  echo 'I started 3 at ' . date('h:i:s', time()) . '<br>';
  echo 'final: ' . date('h:i:s', time()) . '<br>';

  echo 'Code: ' . $curl1->code . '<br/>Data: ' . $curl1->data . '<br/>';
  echo 'received above at: ' . date('h:i:s', time()) . '<br/>';
  echo 'Code: ' . $curl2->code . '<br/>Data: ' . $curl2->data . '<br/>';
  echo 'received above at: ' . date('h:i:s', time());
?>
