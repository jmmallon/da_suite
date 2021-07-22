<?php
session_start();

$dbname = "deliciou_suite";	# your username (= login name  = account name )
$user = "deliciou_jm";		# your Database name is the same as your account name.
$host = "127.0.0.1";    # = "localhost", the server your are on.
$pwd = "2cwhptqj9p7y";	# Your account password
$table = "songlist";
# $table = "songlist_test";

$now = time();
$day = 60 * 60 * 24;

$mysql = mysqli_connect($host, $user, $pwd, $dbname);
if (mysqli_connect_errno($mysql)){
      error("Failed to connect to MySQL: " . mysqli_connect_error());
}

$shortcut_sections = array(
	'Electronic Realizations' => array('abbreviation' => 'ER', 'file' => 'Electronic.txt'),
	'The Freak Out Hour' => array('abbreviation' => 'FOH', 'file' => 'Freakout.txt'),
	'Stainless Steel Prog' => array('abbreviation' => 'SSP', 'file' => 'Stainless.txt'),
	'Spectrum' => array('abbreviation' => 'SP', 'file' => 'Spectrum.txt'),
	'Unearthed Treasures' => array('abbreviation' => 'UT', 'file' => 'Unearthed.txt'),
	'Superstars Of Prog' => array('abbreviation' => 'SOP', 'file' => 'Superstars.txt'),
	'The Golden Age' => array('abbreviation' => 'TGA', 'query' => "year > 0 and year < 1981 and filename not like '%promo%' and filename not like '%IDs' and length > 0 and artist != 'Emerson, Lake & Palmer' and artist != 'Genesis' and artist != 'Jethro Tull' and artist != 'Pink Floyd' and artist != 'Rush' and artist != 'Yes'", 'yearlimit' => '1980')
);

$sections = array(
	'ER' => array('title' => 'Electronic Realizations', 'file' => 'Electronic.txt'),
	'FOH' => array('title' => 'The Freak Out Hour', 'file' => 'Freakout.txt'),
	'SSP' => array('title' => 'Stainless Steel Prog', 'file' => 'Stainless.txt'),
	'SP' => array('title' => 'Spectrum', 'file' => 'Spectrum.txt'),
	'UT' => array('title' => 'Unearthed Treasures', 'file' => 'Unearthed.txt'),
	'SOP' => array('title' => 'Superstars of Prog', 'file' => 'Superstars.txt')
);

function error ($string) {
	die("<html><head><title>Oops!</title></head><body><font color=red size=6>Oops!</font><p>$string</body></html>");
}

function my_parse_ini_file ($f)
{

    // if cannot open file, return false
    if (!is_file($f))
        return false;

    $ini = file($f);

    // to hold the categories, and within them the entries
    $array = array();

    $count = 0;
    foreach ($ini as $i) {
        if (@preg_match('/\[(.+)\]/', $i, $matches)) {
            $header = $matches[1];
        } elseif (@preg_match('/(.+)/', $i, $matches)) {
            $array[$header][$count++] = $matches[1];
        } else {
	    $count = 0;
	}
    }

    return $array;

}

$ids = my_parse_ini_file('idents.ini');

function translate_string ($string) {
      $db = array("`","<",">","%%");
      $display = array("'","(",")", "\\");
      $translated_string = str_replace($db,$display,$string);

      return $translated_string;
}

function pr ($object) {
	print "<pre>\n";
	print_r($object);
	print "</pre>\n";
}

function prepare_string ($string) {
      $db = array("`","<",">","%%");
      $display = array("'","(",")", "\\");
      $prepared_string = str_replace($display,$db,$string);

      return $prepared_string;
}

function option_query ($field, $where, $sort) {
	global $table, $mysql;

	$select = [];
	$list = [];

	$query = "select distinct $field from $table where $where order by $field";
// 	print "$query<br>";
	$result_id = mysqli_query($mysql, $query);
	while ($row = mysqli_fetch_array($result_id, MYSQLI_ASSOC)) {
		$value = prepare_string($row[$field]);
		$select[] = "<option value='$value'> $value";
		$list[] = "$value";
	}
	if ($sort) {
		usort($select, $sort);
	}
	return(array(implode("\n", $select), implode("|", $list)));
}

function option_file ($file) {
	$select = [];
	$list = [];

	$input = file_get_contents($file);
	$bands = explode("\n", $input);
	$five = array_slice($bands, 0, 5);
	$rest = array_slice($bands, 5, -1);
	sort($rest);
	foreach (array_merge($five, $rest) as $band) {
		$value = prepare_string($band);
		$select[] = "<option value='$value'> $value";
		$list[] = "$value";
	}
	return(array(implode("\n", $select), implode("|", $list)));
}

function donation_sort ($a, $b) {
	list($trash, $anum) = explode("#", $a);
	list($trash, $bnum) = explode("#", $b);

	if ((int)$anum == (int)$bnum) {
        	return 0;
	}
	return ((int)$anum < (int)$bnum) ? -1 : 1;
}

?>
