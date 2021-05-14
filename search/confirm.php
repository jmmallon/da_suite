<?php
  include("DB.inc");
  include("ids.php");
  include_once("../../scripts/links.php");
  $songs = explode(",",substr($_SESSION['playlist'],1));
  $print_html = "
<table>
<tr>
<th>Artist</th>
<td width=10></td>
<th>Song</th>
<td width=10></td>
<th>Album</th>
<td width=10></td>
<th>Year</th>
<td width=10></td>
<th>More Info</th>
<td width=10></td>
<th>Length</th>
</tr>
";
  $print_pre = "<pre>";
  $print_list = "<pre>";
  foreach ($songs as $id) {
    $query_string = "select * from $table where id=$id";
    $result_id = mysqli_query($mysql, $query_string);
    while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
      $row_count++;
      $length = intval($row['length']/60) . ":" . sprintf("%02d",$row['length'] % 60);
      $total_length += $row['length'];
      $db = array("`","<",">");
      $display = array("'","(",")");
      $artist = str_replace($db,$display,$row['artist']);
      $song = str_replace($db,$display,$row['song']);
      $album = str_replace($db,$display,$row['album']);
      $year = ($row['year'] == 0) ? "" : $row['year'];
      $filename = str_replace($db,$display,str_replace("%%","\\",substr($row['filename'], 2)));
      $link = find_band_link($artist);
      if (! strpos($link, "http")) {
      	$link = "";
      }
      list($artist, $temp) = explode("- ", $artist);
      list($song_name, $temp) = explode(", from the", $song);
      list($song_name, $temp) = explode(" from the", $song_name);
      list($song_name, $temp) = explode(", from '", $song_name);
      list($song_name, $temp) = explode(", from \"", $song_name);
      $album = preg_replace('/ \([^\(\)]*remaster[^\(\)]*\)/i', '', $album);
      $album = preg_replace('/ \(Dis[ck] [^\(\)]*\)/i', '', $album);
      if (($row['length'] > 70 || strstr($song_name, 'Intro')) && ! preg_match('/ Promo$/', $song_name)) {
         if ($row_count == 2 && strstr($song_name, 'Intro')) {
            $song_name = preg_replace('/ Intro.*/i', '', $song_name);
            $print_list .= "$artist - $song_name\n";
         }
         elseif ($row_count == 3) {
            $print_list .= "\n$artist - $song_name ($album)\n";
         } else {
            $print_list .= "$artist - $song_name ($album)\n";
         }
      } elseif ($row['length'] == 0) {
         $print_list .= "\n";
      } else {
         $print_list .= "\n";
      }
      $print_html .= "
<tr>
<td>
$artist
</td>
<td></td>
<td>
$song
</td>
<td></td>
<td>
$album
</td>
<td></td>
<td>
$year
</td>
<td></td>
<td>
$link
</td>
<td></td>
<td align=right>
$length
</td>
</tr>
";
      $print_pre .= "$filename\n";
    }
  }
  $total_length = sprintf("%02d",intval($total_length/3600)) . ":" . sprintf("%02d",intval(($total_length % 3600)/60)) . ":" . sprintf("%02d",$total_length % 60);
  $print_html .= "
</table>
<br>Total length: $total_length<p>
";
  $print_pre .= "</pre>\n";
  $print_list .= "</pre>\n";
  $print_list = preg_replace('/^\s*/', '', $print_list);
  $print_list = preg_replace('/\s*$/', '', $print_list);
  $print_list .= "\n";
?>
<html>
<head><title>Playlist</title>
<script>
window.focus();
function myFunction() { var copyText = document.getElementById("field"); copyText.select(); document.execCommand("copy"); }
</script>
</head>
<body>
<h2>Playlist</h2>
<form action=reorder.php method=post>
<?php print $print_html; ?>
Copy this section and upload it to the FTP - it will work as a playlist.
<?php print $print_pre; ?>
<p>
Here's the song list:
<?php print $print_list; ?>
<p>
<a href=playlist.php>Playlist page</a><p>
<form action="playlist.php" method=post>
<input type=hidden name="clear" value="yes">
<input type=submit value="Clear playlist">
</form>
<button onclick="myFunction()">Copy text</button><p>
<textarea id="field" cols=150 rows=13>
<html>
<head><title>Playlist</title>
</head>
<body>
<?php
  $print = $print_html . $print_pre . $print_list;
  print $print;
?>
</body></html>
</textarea><br>
</body></html>
