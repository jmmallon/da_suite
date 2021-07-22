<?php
  include("DB.inc");
  include("ids.php");
  include_once("../../scripts/links.php");
  function print_results ($argument, $showid = null) {
    global $now, $day, $ids, $idfiles, $mysql;
    $revised_argument = preg_replace("/ order by.*/", "", $argument);
    $_SESSION['query'] = "$revised_argument";
    $_SESSION['lastquery'] = $_SESSION['thisquery'];
    $_SESSION['thisquery'] = "$argument";
    $result_id = mysqli_query($mysql, $argument);
    $row_num = mysqli_num_rows($result_id);
    if (!$row_num) {
      print "No songs returned";
    } else {
      while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
        $length = intval($row['length']/60) . ":" . sprintf("%02d",$row['length'] % 60);
        $totallength += $row['length'];
        $id = $row['id'];
        $date = $row['date'];
	$days =  "$now - $date : " . ($now - $date);
	$days = intval(($now - $date) / $day) + 1;
        $db = array("`","<",">");
        $display = array("'","(",")");
        $artist = str_replace($db,$display,$row['artist']);
	// if (array_key_exists($artist,$ids)) { $artist .= " *"; }
        $song = str_replace($db,$display,$row['song']);
        $album = str_replace($db,$display,$row['album']);
        $id = $row['id'];
        $urlalbum = urlencode($row['album']);
        $urlartist = urlencode($row['artist']);
        $year = ($row['year'] == 0) ? "" : $row['year'];
        $live = ($row['live'] == 1) ? "*" : "";
        $track = ($row['track'] == 0) ? "" : $row['track'];
        $bitrate = $row['bitrate'] . " kbps";
	$link = find_band_link($artist);
	$hasid = "";
	if (array_key_exists($artist,$ids)) {
		$hasid = "<a style='text-decoration: none' href=\"action.php?action=station_id&station_id=$artist\">*</a>";
	}
      	if ($showid) {
        	$fullfilename = str_replace($db,$display,$row['filename']);
		$names = explode("/", $fullfilename);
		$filename = array_pop($names);
		$idnames = $idfiles[$filename];
		asort($idnames);
		$idartists = [];
		foreach ($idnames as $idartist) {
			array_push($idartists, "<a href=\"action.php?action=artist&artist=$idartist\">$idartist</a>");
		}
		$idlist = implode(", ", $idartists);
      	}
        $html .= "
<tr>
<td valign=top>
<input type=checkbox name=\"songs[]\" value=\"$id\">
</td>
<td></td>
<td valign=top>
<a href=\"action.php?action=artist&artist=$urlartist\">$artist</a>
</td>
      ";
      if (! $showid) {
        $html .= "
<td></td>
<td align=center valign=top>
$hasid
</td>
<td></td>
<td align=center valign=top>
$live
</td>
";
      }	
        $html .= "
<td></td>
<td valign=top>
<a target=showfile href=showfile.php?id=$id>$song</a>
</td>
";
      	if (! $showid) {
        	$html .= "
<td></td>
<td valign=top>
$track
</td>
<td></td>
<td valign=top>
<a href=\"action.php?action=album&album=$urlartist%23%23%23$urlalbum\">$album</a>
</td>
<td></td>
<td valign=top align=right>
$year
</td>
";
	}
        $html .= "
<td></td>
<td valign=top align=right>
$length
</td>
<td></td>
<td valign=top align=right>
$bitrate
</td>
<td></td>
<td valign=top align=right>
$days
</td>
";
      	if ($showid) {
        	$html .= "
<td></td>
<td valign=top>
$idlist
</td>
";
      	} else {
        	$html .= "
<td></td>
<td valign=top>
$link
</td>
";
	}
        $html .= "
</tr>
";
      }
      $totallength = sprintf("%02d",intval($totallength/3600)) . ":" . sprintf("%02d",intval(($totallength % 3600)/60)) . ":" . sprintf("%02d",$totallength % 60);
      list ($type, $desc) = explode(":",$_SESSION['sort']);
      $fields = array('artist', 'song', 'track', 'album', 'year', 'length', 'bitrate', 'date','live');
      $second = array();
      $second['artist'] = '';
      $second['song'] = 'year';
      $second['track'] = 'album';
      $second['album'] = 'artist';
      $second['length'] = 'year,artist,album';
      $second['bitrate'] = 'artist,year,album';
      $second['live'] = 'artist,year,album,track';
      $second['year'] = 'artist,album';
      $second['date'] = 'artist,album';
      foreach ($fields as $field) {
        $order[$field] = (($type == $field) and ($desc == 'asc')) ? "type=$field&second=" . $second[$field] . "&order=desc" : "type=$field&second=" . $second[$field] . "&order=asc";
      }
      print <<<ENDLINE
$row_num songs returned.  Total length: $totallength.  Check the songs you want to add & click the button.
<form method=post name="playlist" action=addcart.php target="playlist">
<table>
<tr>
<td></td>
<td width=10></td>
ENDLINE;
      print '<th><a href="action.php?action=reorder&' . $order['artist'] . '">Artist</a></th><td width=10></td>' . "\n";
      if (! $showid) {
        print '<th>Has ID?</th><td width=10></td>';
        print '<th><a href="action.php?action=reorder&' . $order['live'] . '">Live</a></th> <td width=10></td>' . "\n";
      }
      print '<th><a href="action.php?action=reorder&' . $order['song'] . '">Song</a></th> <td width=10></td>' . "\n";
      if (! $showid) {
      	print '<th><a href="action.php?action=reorder&' . $order['track'] . '">Track</a></th> <td width=10></td>' . "\n";
      	print '<th><a href="action.php?action=reorder&' . $order['album'] . '">Album</a></th> <td width=10></td>' . "\n";
      	print '<th><a href="action.php?action=reorder&' . $order['year'] . '">Year</a></th> <td width=10></td>' . "\n";
      }
      print '<th><a href="action.php?action=reorder&' . $order['length'] . '">Length</a></th> <td width=10></td>' . "\n";
      print '<th><a href="action.php?action=reorder&' . $order['bitrate'] . '">Bitrate</a></th> <td width=10></td>' . "\n";
      print '<th><a href="action.php?action=reorder&' . $order['date'] . '">Age</a></th>';
      if ($showid) {
      	print '<td width=10></td><th width="50%">ID For</th>';
      } else {
      	print '<td width=10></td><th>More Info</th>';
      }
      print <<<ENDLINE
</tr>
$html
</table>
<a href="javascript:CheckAll();">Check All</a> - <a href="javascript:ClearAll();">Clear All</a>
<p>
<input type=submit value="Add song(s) to playlist">
</form>
</body></html>
ENDLINE;
    }
  }
  $action = (trim($_GET['action'])) ? trim($_GET['action']) : trim($_POST['action']);
  if ($action != 'reorder') {
    $_SESSION['sort'] = "";
  }
  if ($action != 'needs')
  {
?>
<html>
<head>
<script type="text/javascript">
    function CheckAll()
    {
	var ml = document.playlist;
	var len = ml.elements.length;
	for (var i = 0; i < len; i++) {
	    var e = ml.elements[i];
	    e.checked = true;
	}
    }

    function ClearAll()
    {
	var ml = document.playlist;
	var len = ml.elements.length;
	for (var i = 0; i < len; i++) {
	    var e = ml.elements[i];
	    e.checked = false;
	}
    }
</script>
</head>
<body>
<?php
  }

  # switch (trim($_POST['action'])) {
  switch ($action) {
    case 'album':
      $getalbum = (trim($_GET['album'])) ? trim($_GET['album']) : trim($_POST['album']);
      list ($artist, $album) = explode("###",$getalbum);
      print_results("select * from $table where artist='$artist' and album='$album' order by track,song");
      break;
    case 'reorder':
      $type = trim($_GET['type']);
      $second = trim($_GET['second']);
      $sortorder = trim($_GET['order']);
      $_SESSION['sort'] = "$type:$sortorder";
      $type .= " $sortorder";
      $query = $_SESSION['query'];
      if ($second) {
      	$type .= ",$second";
      }
      print_results("$query order by $type");
      break;
    case 'artist':
      $artist = (trim($_GET['artist'])) ? trim($_GET['artist']) : trim($_POST['artist']);
      print_results("select * from $table where artist='$artist' order by year,album,track,song");
      break;
    case 'show':
      $show = trim($_POST['submit']);
      $yearquery = "";
      switch($show) {
    	case 'Interviews':
      	    print_results("select * from $table where album='Delicious Agony Interviews' order by year,album,track,song");
      	    break;
    	case 'SOP':
      	    $artist = (trim($_GET['SOP'])) ? trim($_GET['SOP']) : trim($_POST['SOP']);
      	    break;
    	case 'SOP IDs':
	    $words = explode('|', trim($_POST['SOPlist']));
      	    break;
    	case 'SOP Len':
            $length = (trim($_POST['SOPminutes']) * 60) + trim($_POST['SOPseconds']);
	    $words = explode('|', trim($_POST['SOPlist']));
            break;
    	case 'SOP Age':
            $days = trim($_POST['SOPdays']);
	    $words = explode('|', trim($_POST['SOPlist']));
      	    break;
    	case 'SOP Year':
            $year = $_POST['SOPyear'];
	    $words = explode('|', trim($_POST['SOPlist']));
            break;
    	case 'SOP Decade':
            $first_year = trim($_POST['SOPdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['SOPlist']));
            break;
    	case 'SOP Song':
            $title = $_POST['SOPtext'];
	    $words = explode('|', trim($_POST['SOPlist']));
            break;
    	case 'SOP Live':
	    $words = explode('|', trim($_POST['SOPlist']));
            break;
    	case 'SSP':
      	    $artist = (trim($_GET['SSP'])) ? trim($_GET['SSP']) : trim($_POST['SSP']);
      	    break;
    	case 'SSP IDs':
	    $words = explode('|', trim($_POST['SSPlist']));
      	    break;
    	case 'SSP Decade':
            $first_year = trim($_POST['SSPdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['SSPlist']));
            break;
    	case 'SSP Year':
            $year = $_POST['SSPyear'];
	    $words = explode('|', trim($_POST['SSPlist']));
            break;
    	case 'SSP Len':
            $length = (trim($_POST['SSPminutes']) * 60) + trim($_POST['SSPseconds']);
	    $words = explode('|', trim($_POST['SSPlist']));
            break;
    	case 'SSP Age':
            $days = trim($_POST['SSPdays']);
	    $words = explode('|', trim($_POST['SSPlist']));
      	    break;
    	case 'SSP Song':
            $title = $_POST['SSPtext'];
	    $words = explode('|', trim($_POST['SSPlist']));
            break;
    	case 'SSP Live':
	    $words = explode('|', trim($_POST['SSPlist']));
            break;
    	case 'UT':
      	    $artist = (trim($_GET['UT'])) ? trim($_GET['UT']) : trim($_POST['UT']);
      	    break;
    	case 'UT IDs':
	    $words = explode('|', trim($_POST['UTlist']));
      	    break;
    	case 'UT Len':
            $length = (trim($_POST['UTminutes']) * 60) + trim($_POST['UTseconds']);
	    $words = explode('|', trim($_POST['UTlist']));
            break;
    	case 'UT Age':
            $days = trim($_POST['UTdays']);
	    $words = explode('|', trim($_POST['UTlist']));
      	    break;
    	case 'UT Year':
            $year = $_POST['UTyear'];
	    $words = explode('|', trim($_POST['UTlist']));
            break;
    	case 'UT Decade':
            $first_year = trim($_POST['UTdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['UTlist']));
            break;
    	case 'UT Song':
            $title = $_POST['UTtext'];
	    $words = explode('|', trim($_POST['UTlist']));
            break;
    	case 'UT Live':
	    $words = explode('|', trim($_POST['UTlist']));
            break;
    	case 'TGA':
      	    $artist = (trim($_GET['TGA'])) ? trim($_GET['TGA']) : trim($_POST['TGA']);
	    $yearquery = "year < 1981 and";
      	    break;
    	case 'TGA IDs':
	    $words = explode('|', trim($_POST['TGAlist']));
      	    break;
    	case 'TGA Len':
            $length = (trim($_POST['TGAminutes']) * 60) + trim($_POST['TGAseconds']);
	    $words = explode('|', trim($_POST['TGAlist']));
	    $yearquery = "year < 1980 and";
            break;
    	case 'TGA Age':
            $days = trim($_POST['TGAdays']);
	    $words = explode('|', trim($_POST['TGAlist']));
	    $yearquery = "year < 1980 and";
      	    break;
    	case 'TGA Year':
            $year = $_POST['TGAyear'];
	    $words = explode('|', trim($_POST['TGAlist']));
            break;
    	case 'TGA Decade':
            $first_year = trim($_POST['TGAdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['TGAlist']));
            break;
    	case 'TGA Song':
            $title = $_POST['TGAtext'];
	    $words = explode('|', trim($_POST['TGAlist']));
            break;
    	case 'TGA Live':
	    $words = explode('|', trim($_POST['TGAlist']));
            break;
    	case 'FOH':
      	    $artist = (trim($_GET['FOH'])) ? trim($_GET['FOH']) : trim($_POST['FOH']);
      	    break;
    	case 'FOH IDs':
	    $words = explode('|', trim($_POST['FOHlist']));
      	    break;
    	case 'FOH Len':
            $length = (trim($_POST['FOHminutes']) * 60) + trim($_POST['FOHseconds']);
	    $words = explode('|', trim($_POST['FOHlist']));
            break;
    	case 'FOH Age':
            $days = trim($_POST['FOHdays']);
	    $words = explode('|', trim($_POST['FOHlist']));
      	    break;
    	case 'FOH Year':
            $year = $_POST['FOHyear'];
	    $words = explode('|', trim($_POST['FOHlist']));
            break;
    	case 'FOH Decade':
            $first_year = trim($_POST['FOHdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['FOHlist']));
            break;
    	case 'FOH Song':
            $title = $_POST['FOHtext'];
	    $words = explode('|', trim($_POST['FOHlist']));
            break;
    	case 'FOH Live':
	    $words = explode('|', trim($_POST['FOHlist']));
            break;
    	case 'SP':
      	    $artist = (trim($_GET['SP'])) ? trim($_GET['SP']) : trim($_POST['SP']);
      	    break;
    	case 'SP IDs':
	    $words = explode('|', trim($_POST['SPlist']));
      	    break;
    	case 'SP Len':
            $length = (trim($_POST['SPminutes']) * 60) + trim($_POST['SPseconds']);
	    $words = explode('|', trim($_POST['SPlist']));
            break;
    	case 'SP Age':
            $days = trim($_POST['SPdays']);
	    $words = explode('|', trim($_POST['SPlist']));
      	    break;
    	case 'SP Year':
            $year = $_POST['SPyear'];
	    $words = explode('|', trim($_POST['SPlist']));
            break;
    	case 'SP Decade':
            $first_year = trim($_POST['SPdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['SPlist']));
            break;
    	case 'SP Song':
            $title = $_POST['SPtext'];
	    $words = explode('|', trim($_POST['SPlist']));
            break;
    	case 'SP Live':
	    $words = explode('|', trim($_POST['SPlist']));
            break;
    	case 'ER':
      	    $artist = (trim($_GET['ER'])) ? trim($_GET['ER']) : trim($_POST['ER']);
      	    break;
    	case 'ER Len':
            $length = (trim($_POST['ERminutes']) * 60) + trim($_POST['ERseconds']);
	    $words = explode('|', trim($_POST['ERlist']));
            break;
    	case 'ER Age':
            $days = trim($_POST['ERdays']);
	    $words = explode('|', trim($_POST['ERlist']));
      	    break;
    	case 'ER IDs':
	    $words = explode('|', trim($_POST['ERlist']));
      	    break;
    	case 'ER Year':
            $year = $_POST['ERyear'];
	    $words = explode('|', trim($_POST['ERlist']));
            break;
    	case 'ER Decade':
            $first_year = trim($_POST['ERdecade']) - 1;
            $last_year = $first_year + 11;
	    $words = explode('|', trim($_POST['ERlist']));
            break;
    	case 'ER Song':
            $title = $_POST['ERtext'];
	    $words = explode('|', trim($_POST['ERlist']));
            break;
    	case 'ER Live':
	    $words = explode('|', trim($_POST['ERlist']));
            break;
      }
      if (preg_match('/ IDs/', $show)) {
        $string = array();
	$string = id_string($words);
	$like_string = implode(' or ', $string);
        print_results("select * from $table where station_id = 1 and ($like_string) order by artist,song", 1);
      }
      elseif (preg_match('/ Age/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
	$days = $now - ($days * $day);
        print_results("select *, ROUND((UNIX_TIMESTAMP(NOW()) - date) / $day) + 1 as days from $table where $yearquery date > $days and ($like_string) order by days,artist,album,track");
      }
      elseif (preg_match('/ Year/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
        print_results("select * from $table where year = $year and ($like_string) order by artist,album,track");
      }
      elseif (preg_match('/ Decade/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
        print_results("select * from $table where ((year > $first_year) and (year < $last_year)) and ($like_string) order by artist,album,track");
      }
      elseif (preg_match('/ Len/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
        print_results("select * from $table where $yearquery length = $length and ($like_string) order by artist,album,track");
      }
      elseif (preg_match('/ Song/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
        print_results("select * from $table where $yearquery (song like '%$title%' or album like '%$title%') and ($like_string) order by artist,album,track");
      }
      elseif (preg_match('/ Live/', $show)) {
        $string = array();
  	foreach ($words as $word) {
      	    array_push($string,"artist like '$word%'");
	}
	$like_string = implode(' or ', $string);
        print_results("select * from $table where live = 1 and ($like_string) order by artist,album,track");
      }
      elseif ($show != 'Interviews' && ! preg_match('/ IDs/', $show)) {
      	print_results("select * from $table where $yearquery (artist like '$artist%' or artist like 'the $artist%') order by artist,year,album,track,song");
      }
      break;
    case 'station_id':
      $submit = (trim($_GET['submit'])) ? trim($_GET['submit']) : trim($_POST['submit']);
      if ($submit == "IDs" || $submit == "IDs for current playlist") {
  	$songs = explode(",",substr($_SESSION['playlist'],1));
	$string = array();
	$artists = array();
  	foreach ($songs as $song) {
    	    $query_string = "select artist from $table where id='$song'";
    	    $result_id = mysqli_query($mysql, $query_string);
    	    $row_num = mysqli_num_rows($result_id);
    	    if ($row_num) {
              while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
		// $words = explode(' ', $row['artist']);
		// $word = array_pop($words);
      		// array_push($string,"artist like '%$word%'");
      		array_push($artists, $row['artist']);
	      }
	    }
	}
	$string = id_string($artists);
	if ($string[0]) {
	  $like_string = implode(' or ', $string);
          print_results("select * from $table where station_id = 1 and ($like_string) order by artist,song", 1);
        } else {
          print "No songs returned.";
        }
      } else {
        $idstring = (trim($_GET['station_id'])) ? trim($_GET['station_id']) : trim($_POST['station_id']);
	$artists = array();
	foreach ($ids as $band => $list) {
		if (stripos($band, $idstring) !== false) {
			array_push($artists, $band);
		}
	}
	/*foreach ($idfiles as $band => $list) {
		if (stripos($band, $idstring) !== false) {
	print "$band - ";
	print_r($list);
			foreach ($idfiles[$band] as $item1) {
				array_push($artists, $item1);
			}
		}
	} */
	$string = id_string($artists);
	if ($string[0]) {
	  $like_string = implode(' or ', $string);
          print_results("select * from $table where station_id = 1 and ($like_string) order by artist,song", 1);
        } else {
          print "No songs returned.";
        }
      }
      break;
    case 'filename':
      $string = trim($_POST['string']);
      switch (trim($_POST['type'])) {
        case 'bg':
          print_results("select * from $table where filename like '$string%' order by artist,year,album,track,song");
          break;
        case 'ct':
          print_results("select * from $table where filename like '%$string%' order by artist,year,album,track,song");
          break;
        case 'ed':
          print_results("select * from $table where filename like '%$string' order by artist,year,album,track,song");
          break;
      }
      break;
    case 'needs':
      $need = trim($_POST['submit']);
      switch($need) {
        case 'VOs':
    	    $result_id = mysqli_query($mysql, "select id from $table where ((length = 0) and (artist like '%voiceo%')) order by song");
    	    $row_num = mysqli_num_rows($result_id);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                $id = $row['id'];
        	    break;
            }
        break;
        case 'Donation':
            $result_id = mysqli_query($mysql, "select * from $table where song like 'Donation Promo%' and length < 48 and song not like '%Rick%' order by song");
            $row_num = mysqli_num_rows($result_id);
            $count = rand(1, $row_num);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                $rowcount++;
                if ($rowcount == $count) {
                   $id = $row['id'];
        	       break;
                }
            }
        break;
        case 'Random Promo':
            $result_id = mysqli_query($mysql, "select * from $table where song not like '%donation%' and song not like '%msk%' and song like '%promo%' and length < 120 order by song");
            $row_num = mysqli_num_rows($result_id);
            $count = rand(1, $row_num);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                $rowcount++;
                if ($rowcount == $count) {
                   $id = $row['id'];
        	       break;
                }
            }
        break;
        case 'DA shorts':
            $result_id = mysqli_query($mysql, "select * from $table where song like 'Delicious Agony ID%' order by song");
            $row_num = mysqli_num_rows($result_id);
            $count = rand(1, $row_num);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                $rowcount++;
                if ($rowcount == $count) {
                   $id = $row['id'];
        	       break;
                }
            }
        break;
        case 'Intro':
            $result_id = mysqli_query($mysql, "select id from $table where filename like '%DA sh%' order by song");
            $row_num = mysqli_num_rows($result_id);
            $count = rand(1, $row_num);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                $rowcount++;
                if ($rowcount == $count) {
                   $id = $row['id'];
        	       break;
                }
            }
            $intro = trim($_POST['intro']);
            $result_id = mysqli_query($mysql, "select id from $table where song = '$intro'");
            $row_num = mysqli_num_rows($result_id);
            $count = rand(1, $row_num);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
               $id .= "," . $row['id'];
        	   break;
            }
            if ((preg_match("/spectrum/i", "$intro") == 0) and (preg_match("/freak out/i", "$intro") == 0) and (preg_match("/^electron/i", "$intro") == 0) and (preg_match("/^prog/i", "$intro") == 0) and (preg_match("/garden/i", "$intro") == 0) and (preg_match("/^the spirit/i", "$intro") == 0) and (preg_match("/^welcome/i", "$intro") == 0) and (preg_match("/^south/i", "$intro") == 0) and (preg_match("/^uneart/i", "$intro") == 0) and (preg_match("/^stain/i", "$intro") == 0)) {
              $result_id = mysqli_query($mysql, "select id from $table where ((length = 0) and (artist like '%voiceo%')) order by song");
              while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
                 $id .= "," . $row['id'];
        	     break;
              }
            }
          break;
        case 'Promo':
            $promo = trim($_POST['promo']);
            $result_id = mysqli_query($mysql, "select id from $table where song = '$promo'");
            $row_num = mysqli_num_rows($result_id);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
               $id = $row['id'];
        	   break;
            }
          break;
        case 'Donation Promo':
            $donation = trim($_POST['donation']);
            $result_id = mysqli_query($mysql, "select id from $table where song = '$donation'");
            $row_num = mysqli_num_rows($result_id);
            while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
               $id = $row['id'];
        	   break;
            }
      }
      header("Location: https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "addcart.php?song=$id");
      break;
    case 'date':
      $string = trim($_POST['submit']);
      switch($string) {
    	case 'Holiday':
      	    print_results("select * from $table where filename like '%Holiday Music%' order by artist,album,track,song");
      	    break;
        case 'New':
          $new = trim($_POST['new']);
	  switch($new) {
	    case 'today':
	      $newdays = 1;
	      break;
	    case 'this week':
	      $newdays = 7;
	      break;
	    case 'this month':
	      $newdays = date('d');
	      break;
	  }
	  $newdays = $now - ($newdays * $day);
          print_results("select *, (ROUND((UNIX_TIMESTAMP(NOW()) - date) / $day) + 1) as days from $table where date > $newdays order by days,artist,album,track,song");
          break;
        case 'Days':
          $days = trim($_POST['days']);
	  $days = $now - ($days * $day);
          print_results("select *, (ROUND((UNIX_TIMESTAMP(NOW()) - date) / $day) + 1) as days from $table where date > $days order by days,artist,album,track,song");
          break;
        case 'Year':
          $year = trim($_POST['year']);
          print_results("select * from $table where year = $year order by artist,album,track,song");
          break;
        case 'Decade':
          $first_year = trim($_POST['decade']) - 1;
          $last_year = $first_year + 11;
          print_results("select * from $table where ((year > $first_year) and (year < $last_year)) order by year,artist,album,track,song");
          break;
      }
      break;
    case 'lastquery':
      $argument = $_SESSION['lastquery'];
      $_SESSION['lastquery'] = $_SESSION['thisquery'];
      print_results("$argument");
      break;
    case 'id':
      $id = trim($_POST['id']);
      print_results("select * from $table where id = '$id'");
      break;
    case 'title':
      $string = trim($_POST['submit']);
      switch($string) {
        case 'Song':
          $song = trim($_POST['song']);
          switch (trim($_POST['songtype'])) {
            case 'bg':
              print_results("select * from $table where song like '$song%' order by song");
              break;
            case 'ct':
              print_results("select * from $table where song like '%$song%' order by song");
              break;
            case 'ed':
              print_results("select * from $table where song like '%$song' order by song");
              break;
          }
          break;
        case 'Artist':
          $artist = trim($_POST['artist']);
          switch (trim($_POST['artisttype'])) {
            case 'bg':
              print_results("select * from $table where (artist like '$artist%' or artist like 'the $artist%') order by artist,year,album,track,song");
              break;
            case 'ct':
              print_results("select * from $table where artist like '%$artist%' order by artist,year,album,track,song");
              break;
            case 'ed':
              print_results("select * from $table where artist like '%$artist' order by artist,year,album,track,song");
              break;
          }
          break;
        case 'Album':
          $album = trim($_POST['album']);
          switch (trim($_POST['albumtype'])) {
            case 'bg':
              print_results("select * from $table where album like '$album%' order by artist,track,song");
              break;
            case 'ct':
              print_results("select * from $table where album like '%$album%' order by artist,track,song");
              break;
            case 'ed':
              print_results("select * from $table where album like '%$album' order by artist,track,song");
              break;
          }
          break;
      }
      break;
    case 'autos':
      $type = trim($_POST['submit']);
      if ($type == 'Intros') {
        print_results("select * from $table where artist = 'Delicious Agony' and song like '%intro' order by song");
        # print_results("select * from $table where song like '%intro' and song not like 'That year%'  and song != 'Intro' order by song");
      }
      break;
    case 'bitrate':
      $bitrate = trim($_POST['bitrate']);
      switch (trim($_POST['type'])) {
        case 'lt':
          print_results("select * from $table where bitrate < $bitrate and filename not like '%idents%' order by bitrate desc,artist");
          break;
        case 'xt':
          print_results("select * from $table where bitrate = $bitrate and filename not like '%idents%' order by bitrate,artist");
          break;
        case 'gt':
          print_results("select * from $table where bitrate > $bitrate and filename not like '%idents%' order by bitrate,artist");
          break;
      }
      break;
    case 'time':
      $string = trim($_POST['submit']);
      switch($string) {
        case 'Length':
          $order = "";
          $length = (trim($_POST['minutes']) * 60) + trim($_POST['seconds']);
          switch (trim($_POST['type'])) {
            case 'lt':
              print_results("select * from $table where length < $length and album not like '%Delicious Agony Interviews%' order by length desc");
              break;
            case 'xt':
              print_results("select * from $table where length = $length and album not like '%Delicious Agony Interviews%' order by length,artist");
              break;
            case 'gt':
              print_results("select * from $table where length > $length and album not like '%Delicious Agony Interviews%' order by length");
              break;
          }
          break;
        case 'Between':
          $order = "";
          $len1 = (trim($_POST['minutes1']) * 60) + trim($_POST['seconds1']);
          $len2 = (trim($_POST['minutes2']) * 60) + trim($_POST['seconds2']);
          if ($len2 < $len1) {
            print_results("select * from $table where (length >= $len2 and length <= $len1)  order by length");
          } else {
            print_results("select * from $table where (length >= $len1 and length <= $len2)  order by length");
          }
          break;
    }
    break;
  }
?>

