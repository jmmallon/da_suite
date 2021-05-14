<?php

include("DB.inc");
include("ids.php");
require_once("JSON.php");

$item = $_GET['term'];
$type = $_GET['type'];
$data = array();
if ($type == 'id') {
	foreach ($ids as $band => $list) {
		if (stripos($band, $item) !== false) {
			array_push($data, $band);
		}
	}
	foreach ($idfiles as $band => $list) {
		if (stripos($band, $item) !== false) {
			foreach ($idfiles[$band] as $item1) {
				array_push($data, $item1);
			}
		}
	}
	$data = array_unique($data);
	sort($data);
} else {
    $query = "select distinct $type from $table where $type LIKE 'The $item%' or $type LIKE '$item%' order by $type";
    $rs = mysqli_query($mysql, $query);
    if ( $rs && mysqli_num_rows($rs) )
    {
        while( $row = mysqli_fetch_array($rs, MYSQLI_ASSOC) )
        {
	    $keep['value'] = $row[$type];
            $data[] = $keep;
        }
    }
}

$json = new Services_JSON();
echo $json->encode($data);
flush();
?>
