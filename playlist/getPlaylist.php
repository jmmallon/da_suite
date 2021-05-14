<?php
	include_once("DB.inc");
	include_once("../../scripts/links.php");
	if ($_GET['date']) {
		$date = $_GET['date'] . " 00:00:00";
		$timezone = $_GET['tz'];
	} else {
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$date = "$year-$month-$day 00:00:00";
	}
	$dateObject = new DateTime($date);
	$seconds = $dateObject->format('U');
	$secondsend = $seconds + (24 * 60 * 60);
    	$query = "select * from $table where playedat < $secondsend and playedat >= $seconds order by playedat";
    	$result_id = mysqli_query($mysql, $query);
    	if (! $result_id) {
		$error = mysqli_error($mysql);
		print "$error\n";
	}
	$max = 0;
	$string = '';
        while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
        	$time = $row['playedat'] - ($timezone * 60);
		$adjusted_time = new DateTime("@$time");
		$print_time = $adjusted_time->format('Y-m-d H:i:s');
        	$song = $row['song'];
        	$song = makeitalink($song);
        	$listeners = $row['listeners'];
        	$listeners = ($listeners) ? $listeners : '';
		if (($max < $listeners) && (! strpos($song, "Station ID")) && (! preg_match('/ Promo$/', $song)) && (! preg_match('/^Delicious Agony \-/', $song)) && (! preg_match('/Voiceover/i', $song))) {
			$max = $listeners;
			$maxtime = $print_time;
			$maxsong = $song;
		}
		$string .= "<tr><td width='10%'>$print_time</td><td></td>";
		$string .= "<td width='10%' align=center>$listeners</td><td></td>";
		$string .= "<td width='65%'>$song</td></tr>\n";
	}
?>
<html><head><title>DA Playlist</title>
</head><body>
<?php print "Max listeners: $max<br>Max listeners time: $maxtime<br>Max listeners song: $maxsong<p>";?>
<table border=0>
<tr>
<th width="10%">Time</th>
<th width=10></th>
<th width="10%">Listeners</th>
<th width=10></th>
<th width="65%">Song</th>
</tr>
<?php print $string;?>
</table>
</body></html>
