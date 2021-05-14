<?php
  include("DB.inc");
  $order = array();
  foreach ($_POST as $song => $ord) {
    if ($ord == "D") {
      continue;
    }
    $order[$ord] = $song;
    $max = ($max < $ord) ? $ord : $max;
  }
  for ($i = 1; $i <= $max; $i++) {
    if ($order[$i] == "") {
      continue;
    }
    list($idno, $tmp) = explode("-", $order[$i]);
    $list .= ",$idno";
    $query_string = "select * from $table where id=$idno";
    $result_id = mysqli_query($mysql, $query_string);
    while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
      $length = intval($row['length']/60) . ":" . sprintf("%02d",$row['length'] % 60);
      $total_length += $row['length'];
      $db = array("`","<",">");
      $display = array("'","(",")");
      $artist = str_replace($db,$display,$row['artist']);
      $song = str_replace($db,$display,$row['song']);
      $album = str_replace($db,$display,$row['album']);
      $year = ($row['year'] == 0) ? "" : $row['year'];
      $id = $row['id'];
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
<td align=right>
$length
</td>
</tr>
";
    }
  }
  $total_length = sprintf("%02d",intval($total_length/3600)) . ":" . sprintf("%02d",intval(($total_length % 3600)/60)) . ":" . sprintf("%02d",$total_length % 60);
  $_SESSION['playlist'] = $list;
?>
<html>
<head><title>Playlist</title>
<script>
window.focus();
</script>
</head>
<body>
<h2>Playlist</h2>
Confirm the order and click the <b>Confirm</b> button.  If you need to re-order, click the <b>Re-order</b> button<p>
Total length: <?php print $total_length; ?><p>
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
<th>Length</th>
</tr>
<?php print $print_html; ?>
</table>
<br>Total length: <?php print $total_length; ?><p>
<a href=playlist.php>Playlist page</a><p>
<table>
<tr>
<td>
<form action=confirm.php method=post>
<input type=submit value="Confirm">
</form>
</td> <td width=15></td>
<td>
<form action=order.php method=post>
<input type=submit value="Re-order">
</form>
</td>
</td> <td width=15></td>
<td>
<form name=query32a method=post action=action.php>
<input type=hidden name=action value="needs">
<input type=submit name="submit" value="DA shorts"> &nbsp; 
<input type=submit name="submit" value="VOs">
</form>
</td>
</tr>
</table>
</body></html>
