<?php
//include_once "../scripts/TransactionControl.php";
define ('DB_HOST', 'mysql.jamesdmacdonald.org');
$DB_NAME = getenv('DB_NAME');
$DB_PASSWORD = getenv('DB_PASSWORD');
$DB_USER  = getenv('DB_USER');

$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!mysql_select_db(DB_NAME, $dbc)){
		die ("Can't connect to DB " . DB_NAME);
}
?>
