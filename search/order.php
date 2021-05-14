<?php
session_start();
  if ($_GET['playlist'] != "") {
    $temp = $_SESSION['playlist'];
    $_SESSION['playlist'] = $_GET['playlist'];
    $_SESSION['old_playlist'] = $temp;
  }
  echo $_SESSION['playlist'];
?>
