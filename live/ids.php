<?php
	$ids = array();
	$idfiles = array();
	foreach (array('idents', 'shows') as $file) {
		$myfile = fopen("$file.ini", "r") or die("Unable to open $file file!");
		while (($line = fgets($myfile)) !== false) {
			$line = rtrim($line);
			if (!$line) {
				continue;
			}
			if (preg_match('/^\[([^\]]*)/', $line, $match)) {
				$band = $match[1];
				if (! isset($ids[$band])) {
					$ids[$band] = array();
				}
				continue;
			}
			array_push($ids[$band], $line);
			if (! array_key_exists($line, $idfiles)) {
				$idfiles[$line] = array();
			}
			array_push($idfiles[$line], $band);
		}
		fclose($myfile);
	}

function id_string ($array) {
	global $ids;
	global $idfiles;
      	$display = array("'","(",")", "\\");
      	$db = array("`","<",">","%%");
	$files = array();
	foreach ($array as $band) {
      		$band = str_replace($db,$display,$band);
		if ($ids[$band]) {
			foreach ($ids[$band] as $file) {
      				$file = str_replace($display,$db,$file);
				$file = "filename like '%$file'";
				array_push($files, $file);
			}
		}
	}

	return($files);
}
?>
