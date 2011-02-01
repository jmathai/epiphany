<html>
<head>
  <title>EpiCode examples</title>
</head>
<body>
  <ul>
  <?php
    $dir = dir('.');
    while($entry = $dir->read())
    {
      if(!is_dir($entry) || $entry[0] == '.')
        continue;
      echo '<li><a href="'.$entry.'/">'.$entry.'</li>';
    }
  ?>
  </ul>
</body>
</html>
