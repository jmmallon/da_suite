<html><head><title>DA Playlist</title>
<META HTTP-EQUIV="refresh" CONTENT="600; URL=<?php print $_SERVER['PHP_SELF'] ?>"> 
</head><body>
<table border=0>
<tr>
<th>Time</th>
<th width=10></th>
<th>Listeners</th>
<th width=10></th>
<th>Song</th>
</tr>
<?php
	include("DB.inc");
    	$result_id = mysqli_query($mysql, "select * from $table order by `id` asc");
        while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
        	$time = $row['time'];
        	$listeners = $row['listeners'];
        	$listeners = ($listeners) ? $listeners : '';
        	$listeners .= ($row['server']) ? ' ' . $row['server'] : '';
        	$song = $row['song'];
		print "<tr><td>$time</td><td></td>";
		print "<td align=center>$listeners</td><td></td>";
		print "<td>$song</td></tr>\n";
	}
?>
</table></body></html>
