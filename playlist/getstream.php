<?php
include_once("DB.inc");
ini_set("allow_url_fopen", 1);

$nowdate = date("Y-m-d H:i:s");

$result_id = mysqli_query($mysql, "select max(playedat) as playedat from $table");
$row = mysqli_fetch_array($result_id, MYSQLI_ASSOC);
$lastplayedat = $row['playedat'];

$playlistURL = "https://admin:efHMLz9qfh@deliciousagony.streamguys1.com/admin/showlog.xsl?log=playlistlog";

$content = file_get_contents($playlistURL);
preg_match('/<pre>(.*)<\/pre>/sim', $content, $list);
$items = explode("\n", trim($list[1]));

$arr_length = count($items) - 1;

for ($i = $arr_length; $i >= 0; $i--) {
	List($date, $mount, $listeners, $title) = explode('|', $items[$i]);
	if ($listeners == 0) {
		continue;
	}
	$playedat = date('U', strtotime($date));
	if ($playedat > $lastplayedat) {
		$title = mysqli_real_escape_string($mysql, $title);
		$query = "insert into $table (`playedat`, `song`, `listeners`) values ('$playedat', '$title', $listeners)";
		# print "$query\n";
		file_put_contents('playlist.txt', "$query\n", FILE_APPEND);
    		if (! mysqli_query($mysql, $query)) {
			$error = mysqli_error($mysql);
			file_put_contents('playlist.txt', "$error\n", FILE_APPEND);
		}
	}
}
?>
