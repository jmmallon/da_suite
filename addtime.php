<html><head><title>Count Time</title></head>
<body>
<?php
  if ($_POST['time']) {
    print "<small>\n";
    $input = explode("\n",$_POST['time']);
    foreach ($input as $line) {
      print "$line<br>\n";
      list ($min, $sec) = explode(":", end(preg_split("/\s+/",trim($line))));
      $timeall += ($hour * 3600) + ($min * 60) + $sec;
    }
    print "</small>\n";
    printf("<p>Total time: %02d:%02d:%02d<p>", intval($timeall/3600), intval(($timeall % 3600)/60), $timeall % 60);
  }
?>
<form action=addtime.php method=post>
<textarea rows=25 name=time cols=150></textarea><p>
<input type=submit value="Total time">
</form>
</body></html>
