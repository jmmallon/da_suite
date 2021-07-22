<?php
include("DB.inc");
$intro = $_GET['intro'];

$title = preg_replace("/ *[0-9]* Intro$/", "", $intro);

if (array_key_exists($title, $shortcut_sections)) {
  	$section = $shortcut_sections[$title];
  	$abbreviation = $section['abbreviation'];
  	$yearlimit = (array_key_exists('yearlimit', $section)) ? $section['yearlimit'] : date('Y');

  	list($select, $list) = option_query("year", "year > 0 and year <= $yearlimit", '');
  	$year_select = $select;
	$decade_select = "";
  	for ($year = 1960; $year <= $yearlimit; $year += 10) {
 		$decade_select .= "<option value=$year> ${year}s\n";
  	}

  	if (array_key_exists('file', $section)) {
  		list($select, $list) = option_file($section['file']);
  	}
  	if (array_key_exists('query', $section)) {
  		list($select, $list) = option_query('artist', $section['query'], '');
  	}

  	print "
<form name=query1b method=post action=action.php target='search_results'>
<input type=hidden name=action value='show'>
<table>
<tr><td>
$title:
</td><td width=5></td><td>
<select name=${abbreviation}>
$select
</select>
</td><td width=5></td><td>
<input type=submit name='submit' value='${abbreviation}'>
</td><td width=5></td><td>
<input type=hidden name='${abbreviation}list' value='$list'>
<input type=submit name='submit' value='${abbreviation} IDs'>
</td><td width=5></td><td>
<input type=text name='${abbreviation}minutes' size=2>
min. &nbsp;
<input type=text name='${abbreviation}seconds' size=2>
sec. &nbsp;
<input type=submit name='submit' value='${abbreviation} Len'>
</td><td width=5></td><td>
<select name=${abbreviation}days>
<option value=1> 1
<option value=2> 2
<option value=3> 3
<option value=4> 4
<option value=5> 5
<option value=6> 6
<option value=7> 7
<option value=14> 14
<option value=21> 21
<option value=28> 28
<option value=60> 60
<option value=90> 90
<option value=120> 120
<option value=180> 180
<option value=365> 365
</select> days old &nbsp;<input type=submit name='submit' value='${abbreviation} Age'>
</td><td width=5></td><td>
<select name=${abbreviation}year>
$year_select
</select> &nbsp;<input type=submit name='submit' value='${abbreviation} Year'>
</td><td width=5></td><td>
Decade <select name=${abbreviation}decade>
$decade_select
</select> &nbsp;<input type=submit name='submit' value='${abbreviation} Decade'>
</td><td width=5></td><td>
<input type=text name='${abbreviation}text' size=20>
<input type=submit name='submit' value='${abbreviation} Song'>
</td></tr>
</table>
</form>
<form name=query321 method=post action=action.php target='search_results'>
<input type=hidden name=action value='station_id'>
<input type=submit name='submit' value='IDs for current playlist'>
</form>
";
} else {
	print "";
}
?>
