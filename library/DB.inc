<?php
session_start();

$dbname = "deliciou_suite";	# your username (= login name  = account name )
$host = "127.0.0.1";    # = "localhost", the server your are on.
$user = "deliciou_jm";		# your Database name is the same as your account name.
$pwd = "2cwhptqj9p7y";	# Your account password
$table = "songlist";

$mysql = mysqli_connect($host, $user, $pwd, $dbname);
if (mysqli_connect_errno($mysql)){
      error("Failed to connect to MySQL: " . mysqli_connect_error());
}

?>
