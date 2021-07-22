<?php
  include("DB.inc");
  include("ids.php");
  include_once("../../scripts/links.php");
  if ($_POST['clear'] == "yes") {
    $_SESSION['playlist'] = "";
    print "
<html>
<head><title>Playlist</title>
<script>
window.focus();
</script>
</head>
<body>
Playlist cleared.
</body></html>
";
    exit;
  }
  $songs = explode(",",substr($_SESSION['playlist'],1));
  // print_r($songs);
  if ($songs[0]) {
   foreach ($songs as $songid) {
    $query_string = "select * from $table where id='$songid'";
    $result_id = mysqli_query($mysql, $query_string);
    while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
      $length = intval($row['length']/60) . ":" . sprintf("%02d",$row['length'] % 60);
      $total_length += $row['length'];
      $db = array("`","<",">");
      $display = array("'","(",")");
      $artist = str_replace($db,$display,$row['artist']);
      $link = find_band_link($artist);
      if (! strpos($link, "http")) {
      	$link = "&nbsp;";
      }
      if (strlen($artist) > 25) {
	$shortartist = substr($artist, 0, 25) . "...";
        $link = str_replace(">$artist<",">$shortartist<",$link);
      }
      if (strlen($artist) > 35) {
	$artist = substr($artist, 0, 35) . "...";
      }
      $song = str_replace($db,$display,$row['song']);
      if (strlen($song) > 35) {
	$song = substr($song, 0, 35) . "...";
      }
      $album = str_replace($db,$display,$row['album']);
      if (strlen($album) > 30) {
	$album = substr($album, 0, 30) . "...";
      }
      $song = ($song) ? $song : '-';
      $album = ($album) ? $album : '-';
      $hasid = "";
      if (array_key_exists($artist,$ids)) { $hasid = "*"; }
      $filename = str_replace($db,$display,str_replace("%%","\\",str_replace("J:","",$row['filename'])));
      $year = ($row['year'] == 0) ? "-" : $row['year'];
//<a href='#' title='delete' class='itemDelete'>x</a> &nbsp; $artist - $song ($album) - $year - $length
      $print_html .= "<li id='$songid'>
<div style='float:left; width:6%;'>
<a href='#' title='delete' class='itemDelete'>x</a>
</div>

<div style='float:left; width:20%;'>
$artist
</div>

<div style='float:left; width:20%;'>
$song
</div>

<div style='float:left; width:20%'>
$album
</div>

<div style='float:left; width:4%'>
$year
</div>

<div style='float:left; width:12%'>
$link
</div>

<div style='float:left; text-align:right; width:4%'>
$length
</div>

<div style='float:left; text-align:center; width:6%'>
$hasid
</div>
\n";
    }
   }
  }
  $total_length_print = sprintf("%02d",intval($total_length/3600)) . ":" . sprintf("%02d",intval(($total_length % 3600)/60)) . ":" . sprintf("%02d",$total_length % 60);
  $left = 0;
  while ($left < $total_length) {
	$left += 3600;
  }
$left = $left - $total_length;
  $left_print = sprintf("%02d",intval($left/3600)) . ":" . sprintf("%02d",intval(($left % 3600)/60)) . ":" . sprintf("%02d",$left % 60);
?>
<html>
<head><title>Playlist</title>
<script>
window.focus();
</script>
<META HTTP-EQUIV="refresh" CONTENT="600; URL=<?php print $PHP_SELF; ?>"> 
</head>
<body>
<h2>Playlist</h2>
Drag songs to move to a new position in the playlist.  When you're done adding songs, click the <b>Confirm</b> to see the finished playlist.<p>
Total length: <?php print $total_length_print; ?><p>
	<style>
		.sortable {
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			padding: 0;
			list-style-type: none;
		}
		.sortable.grid {
			overflow: hidden;
		}
		.sortable li {
			list-style: none;
			//border: 1px solid #CCC;
			margin: 5px;
			padding: 5px;
			height: 12px;
			cursor: move;
		}
		.sortable.grid li {
			line-height: 80px;
			float: left;
			width: 80px;
			height: 80px;
			text-align: center;
		}
		.sortable2 {
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			padding: 0;
			list-style-type: none;
		}
		.sortable2.grid {
			overflow: hidden;
		}
		.sortable2 li {
			list-style: none;
			margin: 5px;
			padding: 5px;
			height: 12px;
		}
		.sortable2.grid li {
			line-height: 80px;
			float: left;
			width: 80px;
			height: 80px;
			text-align: center;
		}
		.itemDelete {
			border: 1px solid #CCC;
			background: none;
			text-decoration:none;
			margin: 2px;
			padding: 2px;
			color: #000000;
		}
		.sortable.connected {
			width: 200px;
			min-height: 100px;
			float: left;
		}
		li.disabled {
			opacity: 0.5;
		}
		li.highlight {
			background: #FEE25F;
		}
		li.sortable-placeholder {
			border: 1px dashed #CCC;
			background: none;
		}
	</style>
<ul class="sortable2">
<li>
<div style='float:left; width:6%;'>
<b>Delete</b>
</div>

<div style='float:left; width:20%;'>
<b>Artist</b>
</div>

<div style='float:left; width:20%;'>
<b>Song</b>
</div>

<div style='float:left; width:20%'>
<b>Album</b>
</div>

<div style='float:left; width:4%'>
<b>Year</b>
</div>

<div style='float:left; width:12%'>
<b>Link</b>
</div>

<div style='float:left; text-align:right; width:4%'>
<b>Length</b>
</div>

<div style='float:left; text-align:right; width:6%'>
<b>Has ID?</b>
</div>
</ul>
<ul class="sortable">
<?php print $print_html; ?>
</ul>
	<script src="./jquery-1.7.1.min.js"></script>
	<script src="./jquery.sortable.min.js"></script>
	<script>
function playlistUpdate (obj) {
	var idstring = [];
	for (var i=1; i< obj.childNodes.length; i++) {
		idstring.push(obj.childNodes[i].id);
        }
$.ajax({
    data: 'playlist=,' + idstring.join(','),
    url: 'order.php',
    method: 'GET',
});
}

$('.sortable').sortable();
$('.sortable').sortable().bind('sortupdate', function() {
        var obj = this;
	playlistUpdate(obj);
});

$('.itemDelete').live('click', function() {
    var obj = $(this).closest('ul');
    $(this).closest('li').remove();
    playlistUpdate(obj[0]);
//    document.location.reload(true);
});
	</script>
<p>
Total length: <?php print $total_length_print; ?><p>
Left: <?php print $left_print; ?><p>
<a href="<?php print $PHP_SELF; ?>">Reload</a><p>
<table>
<tr>
<td>
<form action=confirm.php method=post>
<input type=submit value="Confirm">
</form>
</td>
<td>
<form action=undo.php method=post>
<input type=submit value="Undo">
</form>
</td>
<td>
<form action="<?php print $PHP_SELF; ?>" method=post>
<input type=hidden name="clear" value="yes">
<input type=submit value="Clear playlist">
</form>
</td>
</tr>
<tr>
<td colspan=3>
<form name=query32a method=post action=action.php>
<input type=hidden name=action value="needs">
<input type=submit name="submit" value="DA shorts"> &nbsp; 
<input type=submit name="submit" value="VOs"> &nbsp; 
<input type=submit name="submit" value="Donation"> &nbsp;
<input type=submit name="submit" value="Random Promo">
</form>
</td>
</tr>
</table>
</body></html>
