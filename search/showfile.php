<?php
  include("DB.inc");
  $id = $_GET['id'];
    $query_string = "select filename from $table where id=$id";
    $result_id = mysqli_query($mysql, $query_string);
    while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
      $db = array("`","<",">");
      $display = array("'","(",")");
      $filename = str_replace($db,$display,str_replace("%%","\\",substr($row['filename'], 2)));
?>
<html>
<head><title>File Name</title>
<script>
window.focus();
</script>
</head>
<body>
<h2>File name</h2>
<pre>
<?php print $filename; ?><p>
</pre>
</body></html>
<?php
}
?>
