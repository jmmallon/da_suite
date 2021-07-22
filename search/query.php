<?php
  include("DB.inc");
?>
<html><head>
<title>MP3 search</title>
<link rel="stylesheet" href="jquery-ui.css" />
<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
<script src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script src="DAsearch.js"></script>
<SCRIPT LANGUAGE="JavaScript">
var plusImg = new Image();
        plusImg.src = "plusbl.gif"
var minusImg = new Image();
        minusImg.src = "minusbl.gif"

function showLevel( _levelId, _imgId ) {
        var thisLevel = document.getElementById( _levelId );
        var thisImg = document.getElementById( _imgId );
        if ( thisLevel.style.display == "none") {
                thisLevel.style.display = "";
                thisImg.src = minusImg.src;
                }
        else {
                thisLevel.style.display = "none";
                thisImg.src = plusImg.src;
                }
}

$(document).ready(function(){
  	$("#intro").change(function(){
		var item = $(this);
		// alert(item.val())
           	$("#shortcut").load('shortcut.php?intro=' + encodeURIComponent(item.val()));
       });
});
</SCRIPT>
</head>
<body bgcolor="#FFFFFF">
<form name=query32a method=post action=action.php target="playlist">
<input type=hidden name=action value="needs">
<input type=submit name="submit" value="DA shorts"> &nbsp; 
<input type=submit name="submit" value="VOs"> &nbsp; 
<input type=submit name="submit" value="Donation"> &nbsp; 
<input type=submit name="submit" value="Random Promo"><p>
Intros <select id=intro name=intro>
<option>
<?php
list($select, $list) = option_query("song", "artist = 'Delicious Agony' and song like '%intro'", '');
print $select;
?>
</select> &nbsp; <input type=submit name="submit" value="Intro">
<br>
Promos <select name=promo>
<?php
list($select, $list) = option_query("song", "length < 120 and song like '%promo%' and song not like '%donation%'", '');
print $select;
?>
</select> &nbsp; <input type=submit name="submit" value="Promo">
<br>
Donation Promos <select name=donation>
<?php
list($select, $list) = option_query("song", "length < 120 and song like '%donation promo%'", 'donation_sort');
print $select;
?>
</select> &nbsp; <input type=submit name="submit" value="Donation Promo">
</form><p>
<form action="playlist.php" method=post target="playlist">
<input type=hidden name="clear" value="yes">
<input type=submit value="Clear playlist">
</form>
<div id="shortcut">
</div>
<hr>
<form name=query1 method=post action=action.php target="search_results">
<input type=hidden name=action value="artist">
Artist <select name=artist>
<?php
list($select, $list) = option_query("artist", "filename not like '%promo%' and station_id != 1 and filename not like '%interviews%' and length > 0", '');
print $select;
?>
</select> &nbsp;<input type=submit value="Search">
</form>
<form name=query3 method=post action=action.php target="search_results">
<input type=hidden name=action value="title">
Song title
<select name=songtype>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text name="song"> &nbsp;<input type=submit name=submit value="Song">
<br>
Artist name
<select name=artisttype>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text id="artist" name="artist"> &nbsp;<input type=submit name=submit value="Artist">
<br>
Album name
<select name=albumtype>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text id="album" name="album"> &nbsp;<input type=submit name=submit size=60 value="Album">
</form>
<form name=query22 method=post action=action.php target="search_results">
<input type=hidden name=action value="date">
<!--
<input type=submit name="submit" value="Holiday">
<br>
-->
What's new <select name=new>
<option> today
<option> this week
<option> this month
</select>&nbsp;<input type=submit name="submit" value="New">
<br>
Newer than <select name=days>
<option value=1> 1
<option value=2> 2
<option value=3> 3
<option value=4> 4
<option value=5> 5
<option value=6> 6
<option value=7> 7
<option value=14> 14
<option value=21> 21
<option value=31> 31
<option value=60> 60
<option value=90> 90
<option value=120> 120
<option value=180> 180
<option value=365> 365
</select> days old &nbsp;<input type=submit name="submit" value="Days">
<br>
Year <select name=year>
<?php
list($select, $list) = option_query("year", "year > 0 and year < 10000", '');
$year_select = $select;
print $select;
?>
</select> &nbsp;<input type=submit name="submit" value="Year">
<br>
Decade <select name=decade>
<?php
$decade_select = "<option value=1960> 1960s
<option value=1970> 1970s
<option value=1980> 1980s
<option value=1990> 1990s
<option value=2000> 2000s
<option value=2010> 2010s
<option value=2020> 2020s
";
print $decade_select;
?>
</select> &nbsp;<input type=submit name="submit" value="Decade">
</form>
<form name=query321 method=post action=action.php target="search_results">
<input type=hidden name=action value="station_id">
Station ID
<input type=text id="id" name="station_id"> &nbsp;<input type=submit name="submit" value="Search">
 &nbsp; <input type=submit name="submit" value="IDs for current playlist">
</form>
<a href="javascript:showLevel('JoesShows','imgJoesShows');"><img border=0 id=imgJoesShows class=subImage src=plusbl.gif></a> <a href="javascript:showLevel('JoesShows','imgJoesShows');"><b>Joe's shows</b></a>
<br>
<div id=JoesShows style='display:none'>
<form name=query1b method=post action=action.php target="search_results">
<input type=hidden name=action value="show">
<table>
<tr><td>
Interviews:
</td><td width=5></td><td>
<input type=submit name="submit" value="Interviews">
</td></tr>

<?php
foreach ($sections as $abbreviation => $section) {
	list($title, $file) = array($section['title'], $section['file']);
	list($select, $list) = option_file($file);
	print "
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
<input type=submit name='submit' value='${abbreviation} Live'>
</td></tr>
";
}
?>

<tr><td>
The Golden Age:
</td><td width=5></td><td>
<select name=TGA>
<?php
list($select, $list) = option_query("artist", "year > 0 and year < 1981 and filename not like '%promo%' and filename not like '%IDs' and length > 0 and artist != 'Emerson, Lake & Palmer' and artist != 'Genesis' and artist != 'Jethro Tull' and artist != 'Pink Floyd' and artist != 'Rush' and artist != 'Yes'", '');
print $select;
?>
</select>
</td><td width=5></td><td>
<input type=submit name="submit" value="TGA">
</td><td width=5></td><td>
<input type=hidden name="TGAlist" value="<?php print "$list"; ?>">
<input type=submit name="submit" value="TGA IDs">
</td><td width=5></td><td>
<input type=text name="TGAminutes" size=2>
min. &nbsp;
<input type=text name="TGAseconds" size=2>
sec. &nbsp;
<input type=submit name="submit" value="TGA Len">
</td><td width=5></td><td>
<select name=TGAdays>
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
</select> days old &nbsp;<input type=submit name="submit" value="TGA Age">
</td><td width=5></td><td>
<select name=TGAyear>
<option value=1951> 1951
<option value=1952> 1952
<option value=1955> 1955
<option value=1956> 1956
<option value=1957> 1957
<option value=1958> 1958
<option value=1959> 1959
<option value=1960> 1960
<option value=1961> 1961
<option value=1962> 1962
<option value=1963> 1963
<option value=1964> 1964
<option value=1965> 1965
<option value=1966> 1966
<option value=1967> 1967
<option value=1968> 1968
<option value=1969> 1969
<option value=1970> 1970
<option value=1971> 1971
<option value=1972> 1972
<option value=1973> 1973
<option value=1974> 1974
<option value=1975> 1975
<option value=1976> 1976
<option value=1977> 1977
<option value=1978> 1978
<option value=1979> 1979
<option value=1980> 1980

</select> &nbsp;<input type=submit name="submit" value="TGA Year">
</td><td width=5></td><td>
Decade <select name=TGAdecade>
<option value=1960> 1960s
<option value=1970> 1970s
<option value=1980> 1980s
</select> &nbsp;<input type=submit name="submit" value="TGA Decade">
</td><td width=5></td><td>
<input type=text name="TGAtext" size=20>
<input type=submit name="submit" value="TGA Song">
<input type=submit name="submit" value="TGA Live">
</td></tr>

</table>
</form>
</div>
<p>
<form name=query4 method=post action=action.php target="search_results">
<input type=hidden name=action value="time">
Length &nbsp;
<select name=type>
<option value="xt"> exactly
<option value="lt"> shorter than
<option value="gt"> longer than
</select> &nbsp;
<input type=text name=minutes size=2> min. 
<input type=text name="seconds" size=4>
sec. &nbsp; <input type=submit name="submit" value="Length">
<br>
Between &nbsp;
<input type=text name=minutes1 size=2> min. 
<select name=seconds1>
<option value=0> 00
<option value=5> 05
<option value=10> 10
<option value=15> 15
<option value=20> 20
<option value=25> 25
<option value=30> 30
<option value=35> 35
<option value=40> 40
<option value=45> 45
<option value=50> 50
<option value=55> 55

</select> sec. &nbsp;and &nbsp;
<input type=text name=minutes2 size=2> min. 
<select name=seconds2>
<option value=0> 00
<option value=5> 05
<option value=10> 10
<option value=15> 15
<option value=20> 20
<option value=25> 25
<option value=30> 30
<option value=35> 35
<option value=40> 40
<option value=45> 45
<option value=50> 50
<option value=55> 55

</select> sec. &nbsp; <input type=submit name="submit" value="Between">
</form>
<form name=query321 method=post action=action.php target="search_results">
<input type=hidden name=action value="bitrate">
Bitrate &nbsp;
<select name=type>
<option value="lt"> less than
<option value="xt"> exactly
<option value="gt"> greater than
</select> &nbsp;
<select name=bitrate>
<option value=64> 64
<option value=96> 96
<option value=128> 128
<option value=160> 160
<option value=192> 192
<option value=224> 224
<option value=256> 256
<option value=320> 320
</select> kbps
 &nbsp; <input type=submit name="submit" value="Bitrate">
</form>
<form name=query321 method=post action=action.php target="search_results">
<input type=hidden name=action value="lastquery">
<input type=submit name="submit" value="Last Query">
</form>
<hr>
<form name=query32 method=post action=action.php target="search_results">
<input type=hidden name=action value="filename">
File name
<select name=type>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text name="string"> &nbsp;<input type=submit value="Search">
</form>
<form name=query33 method=post action=action.php target="search_results">
<input type=hidden name=action value="id">
ID <input type=text name="id"> &nbsp;<input type=submit value="Search">
</form>
<script>
$(function(){
        $('#artist').autocomplete({ source: "dbget.php?type=artist", minLength: 2});
        $('#album').autocomplete({ source: "dbget.php?type=album", minLength: 2});
        $('#id').autocomplete({ source: "dbget.php?type=id", minLength: 2});
});
</SCRIPT>
</body></html>
