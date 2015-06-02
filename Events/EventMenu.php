<?php
$path = "$_SERVER[DOCUMENT_ROOT]/db";
if (!strstr(get_include_path(), $path)){
  set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}
//echo get_include_path();
/* comment next for localhost testing */
//include "../db/event_connect.php";
include "event_connect.php";

$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!mysql_select_db(DB_NAME, $dbc)){
		die ("Can't connect to DB " . DB_NAME);
}	
$query = "SELECT event.*, date_format(event.start_date, '%M %d, %Y') as 'sd', date_format(event.end_date, '%M %d, %Y') as 'ed' FROM event where event.start_date > curdate() ORDER BY start_date asc";
//echo "$query";

$result = mysql_query("$query");

if (!$result) {
   die("$query query failed: " . mysql_error());
}
	$eventListNotEmpty = false;
	if (mysql_num_rows($result)){
		echo "\n<ul id=\"EventList\">";
		$eventListNotEmpty = true;
		}
	$UpcomingPage = '/Events/Upcoming.php';

	while ($row = mysql_fetch_array($result,  MYSQL_ASSOC)) {
		$ndx = "eventNdx" . $row[ndx];
		echo ("\n<li><a href = '$UpcomingPage#$ndx'>$row[conference_name]:  ");
		echo ("\n$row[sd] - $row[ed]</a></li>");

		}
	if ($eventListNotEmpty){
		echo "\n</ul>";
		}
	else {
		echo "\n<p>No Events scheduled</p>\n";
		}
?>


