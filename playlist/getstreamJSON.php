<?php
// Feeds playlist information to DA mobile apps

include_once("DB.inc");
ini_set("allow_url_fopen", 1);

$scheduleURL = "https://www.deliciousagony.com/schedule.php?json=1";

$json = file_get_contents($scheduleURL);
$result = json_decode($json);

date_default_timezone_set('America/New_York');
$day = date('w');
# $day = date('w') + 3;
$daylist = $result[$day]->{'events'};
$hour = date('H:m');
# $hour = "20:00";
$show = "";
foreach ($daylist as $item) {
	$start = $item->{'show_time'};
	$end = $item->{'show_time_end'};
	$title = $item->{'show_title'};
	if ($hour >= $start && $hour <= $end) {
		$show = $title;
		if (strpos($show, "/") !== false) {
			$shows = explode("/", $show);
			$shows[] = $shows[1];
			$time = time();
			# $time = date('U', strtotime("2020-05-01"));
			$dates = getdate($time);
			# print_r($dates);
			if ($dates['mday'] == 1 && $dates['wday'] == 5) {
				# print "Woo\n";
				$dates = getdate($time - (3600 * 24));
			}
			$day = $dates['mday'];
			$week = floor(($day - 1) / 7);
			$show = $shows[$week];
		}

		break;
	}
}

$playlistURL = "https://admin:efHMLz9qfh@deliciousagony.streamguys1.com/admin/showlog.xsl?log=playlistlog";

$content = file_get_contents($playlistURL);
preg_match('/<pre>(.*)<\/pre>/sim', $content, $list);
//print_r($list);
$items = explode("\n", trim($list[1]));

$arr_length = count($items) - 1;
List($date, $mount, $listeners, $title) = explode('|', $items[$arr_length]);
$id = date('U', strtotime($date));

$title = str_replace("`", "'", $title);
$json = "[
{
                   \"show_id\":   \"$id\",
                   \"show_time\":   \"00:00\",
                   \"show_time_end\":   \"00:01\",
		   \"show_title\":   \"$title\"
}";
if ($show) {
	$json .= ",
{
                   \"show_id\":   \"1$id\",
                   \"show_time\":   \"00:01\",
                   \"show_time_end\":   \"00:02\",
		   \"show_title\":   \"$show\"
}";
}

$json .= "
]";

print "$json";
