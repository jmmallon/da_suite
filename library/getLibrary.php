<?php
include_once("DB.inc");
include_once("../../scripts/links.php");
$day = 60 * 24 * 60;
$db = array("`","<",">");
$display = array("'","(",")");

$string = trim($_POST['submit']);
$nodates = $_GET['nodates'];
$thisyear = $_GET['thisyear'];
$thisyearquery = '';
if ($thisyear) {
	$thisyearquery = "and year = " .  date("Y");
}

$dates = $_GET['date'];
$days_array = explode(',', $dates);
sort($days_array);
$albums = array();

if ($string) {
	$nodates = 'Yes';
	$days = time() - ($_POST['days'] * $day);
	$op = ">";
}

foreach ($days_array as $date) {
	if (! $string) {
		$datetime1 = strtotime($date);
		$datetime2 = strtotime($date) + $day;
		$op = ">= $datetime1 and date < $datetime2";
	}
    	$query = "select distinct album,artist,year,date from songlist where date $op $days $thisyearquery  and year > 0 order by date,artist,year";
    	$result_id = mysqli_query($mysql, $query);
	$max = 0;
	$string = '';
        while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
		$artist = str_replace($db,$display,$row['artist']);
		$album = str_replace($db,$display,$row['album']);
        	$year = $row['year'];
		$albums[$artist][] = "$year - $album";
	}
	if (!$nodates) {
?>
<html><head><title>DA Library</title>
</head><body>
Added to library on <?php print $date; ?>
<p>
<?php
		foreach ($albums as $artist => $list) {
			print find_band_link($artist). "<br>\n";
			sort($list);
			$printed = array();
			foreach ($list as $album) {
				if (! array_key_exists($album, $printed)) {
					print "$album<br>\n";
					$printed[$album]++;
				}
			}
?>
<p>
<?php
		}
		$albums = array();
?>
<hr>
<?php
	}
}
if ($nodates) {
	ksort($albums);
	foreach ($albums as $artist => $list) {
		print find_band_link($artist). "<br>\n";
		sort($list);
		$printed = array();
		foreach ($list as $album) {
			if (! array_key_exists($album, $printed)) {
				print "$album<br>\n";
				$printed[$album]++;
			}
		}
?>
<p>
<?php
	}
}
?>
</body></html>
