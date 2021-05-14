<?php
  include("DB.inc");
  $temp = $_SESSION['playlist'];
  $_SESSION['playlist'] = $_SESSION['old_playlist'];
  $_SESSION['old_playlist'] = $temp;
  header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/playlist.php");
?>
