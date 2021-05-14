<?php
  include("../DB.inc");
  if ($_POST['filelist']) {
    $input = explode("\n",$_POST['filelist']);
    foreach ($input as $line) {
      $db = array("`","<",">");
      $display = array("'","(",")");
      $filename = str_replace($display,$db,str_replace("\\","%%",trim($line)));
      $filename = str_replace("%%","%",$filename);
// print "$filename<br>\n";
      $query_string = "select id from $table where filename='$filename'";
// print "$query_string<br>\n";
      $result_id = mysql_query($query_string);
// $num=mysql_numrows($result_id);
// print "results = $num<br>\n";
      while ($row = mysql_fetch_array($result_id)) {
      // print $row['id'] . "<br>\n";
        $HTTP_SESSION_VARS['playlist'] .= "," . $row['id'];
      }
    }
  }
  header("Location: http://" . $_SERVER['HTTP_HOST'] . str_replace("admin","playlist.php",dirname($_SERVER['PHP_SELF'])));
?>
