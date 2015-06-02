<?php
function db_start($debug){
	$sql = "START TRANSACTION";
	$result = mysql_query("$sql");
	if ($debug) echo "START TRANSACTION: $result";
}
function db_rollback($debug){
	$sql = "ROLLBACK";
	$result = mysql_query("$sql");
	if ($debug) echo "ROLLBACK: $result";
}
function db_commit($debug){
	$sql = "COMMIT";
	$result = mysql_query("$sql");
	if ($debug) echo "COMMIT: $result";
}
class db_transaction{
	var $errno;
	var $error;

	function f($sql){
		$this->errno = 0;
		$this->errinfo = "";
		$result = mysql_query($sql);
		$this->errno = mysql_errno();
		$this->error = mysql_error();
	}
	function start(){
		db_transaction::f("START TRANSACTION");
	}
	function rollback(){
		db_transaction::f("ROLLBACK");
	}
	function commit(){
		db_transaction::f("COMMIT");
	}
}
	
