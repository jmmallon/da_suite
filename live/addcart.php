<?php
  include("DB.inc");
  $_SESSION['old_playlist'] = $_SESSION['playlist'];
  if ($_POST['songs']) {
    foreach ($_POST['songs'] as $song) {
      $_SESSION['playlist'] .= ",$song";
    }
  }
  elseif ($_GET['song']) {
      $_SESSION['playlist'] .= "," . $_GET['song'];
  }
//    print $_SESSION['playlist'];
  header("Location: https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "playlist.php");
?>
