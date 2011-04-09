<html>
<head>
<title><?php echo $title; ?></title>
</head>
<body>
<?php
  if (getSession()->get(Constants::LOGGED_IN) == true) {
?>
  <div>
    <span><a href='/dashboard'>Home</a></span>
    <span><a href='/logout'>Logout</a></span>
  </div>
<?php
  } else {
?>
  <div>
    <span><a href='/'>Home</a></span>
    <span><a href='/login'>Login</a></span>
  </div>
<?php
  }
?>
