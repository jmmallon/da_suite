<?php
  include("DB.inc");
  $ids = "'" . implode("','", $_POST['songs']) . "'";
    $query_string = "update $table set `live` = 1 where id in ($ids)";
    $result_id = mysqli_query($mysql, $query_string);
    if ($result_id) {
	$result = "Updates complete.";
} else {
      $result = "Failed to update: " . mysqli_connect_error();
}
    print "
<html>
<head><title>Playlist</title>
</head>
<body>
$result
</body></html>
";
