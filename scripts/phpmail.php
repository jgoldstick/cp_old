<?php
// This file is used by subscribe.php to add subscribers to the mailing list
// It sends a confirmation email to the subscriber, an email to "inquiries@<sitename>"
// announcing the new subscriber, and it (will!) add the subscriber name and email to the database
//
// data base usage:  tables: customer, subscribers
// FirstName, LastName and Email are added to the customer table
// The customer.ID for each added name is added to the subscribers table along with the listID 
// listID is to identify which mailing list the customer is subscribed to (1 = subscribers, 2 = restaurants, ...)
// Process:
//	1. Name is added to customer table, ID is automatically generated
//	2. customer ID is read from customer table and added to subscribers table


$FName = $_POST[FirstName_required_alphabetic];
$LName = $_POST[LastName_required_alphabetic];
$Email = $_POST[Email_required_email];
$List = 1; // Restaurant list id is 1;
$List = $_POST[ListNumber];


//////////////////////////////////////////////////////////////
function AddSubscriber($FName, $LName, $Email, $List){

echo "<br>AddSubscriber entered with $FName, $LName, $Email, $List<br>";
// add to Customer List
// Read ID from just added Customer record
// Add CustomerID to subscribers table

// jcg 1/18/2006 problem with primary key not being email.  This allows multiple entries
// for same email which isn't good.  One solution is to check for email in customer table.
// if in table, check if subscriber table has a relation with customer ID for this email
// If both true, we are already done
// if 

	db_start(true);
	$sql = "INSERT into customer"
			. " SET FirstName = '$FName',"
			. "  LastName = '$LName',"
			. "  Email = '$Email'";
	$result = mysql_query("$sql");
	if (!$result) {
		db_rollback(true);
		echo "$sql";
		die(" query failed: " . mysql_error());	
	}
//echo "<br>$sql<br>";
	
// Get customer.ID and add it to subscriber table

	$sql = "SELECT ID from customer "
			. " WHERE Email = '$Email'";
	
	$result = mysql_query("$sql");
	if (!$result) {
		db_rollback(true);
		echo "$sql";
		die(" query failed: " . mysql_error());	
	}
//echo "<br>$sql<br>";
	
	while ($row = mysql_fetch_array($result,  MYSQL_ASSOC)){
		$Id = $row[ID];
	}
	$sql = "INSERT into subscriber"
			. " SET customerId = '$Id',"
			. "  ListId = '$List'";
	$result = mysql_query("$sql");
	if (!$result) {
		db_rollback(true);
		echo "$sql";
		die(" query failed: " . mysql_error());	
	}
	db_commit(true);
//echo "<br>$sql<br>";
	
	// jcg more here to do 5/11/2005
}


/////////////////////////////////////////////
function CreateBody(){
	$s = "";
	foreach ($_POST as $key => $value) {
		$s = $s . $key . ": " . $value . "\n";
//		$s = $s . $value . "\n";
	}
	return $s;
}



// MAIN code starts here
//echo "Createbody returns: <br>" . CreateBody() . "<br>";
	if (isset($_POST['submit'])){
//echo "submit is:" . isset($_POST['Subscribe']);
		if ($_POST[SendCopy] == "Yes"){
			$SendCopy = ", " . $Email;
		}
		else {
			$SendCopy = "";
		}
		
		
		$MailData = CreateBody();
//echo $MailData;
	$ToList = "inquiries@jamesdmacdonald.org" . $SendCopy;
//echo "---$ToList---";
		AddSubscriber($FName, $LName, $Email, $List);
/*
echo "<br>mailing parameters: $ToList, $_POST[Subject], $_POST[EmailAddress],
					$MailData, $_POST[fromName], $_POST[EmailAddress]<br>";
*/					
		if(mail($ToList, 
				"$_POST[Subject]", 
				"$MailData", 
				"From: $FName $LName <$Email>")
			){
			echo "<p>Thank you $FName $LName for subscribing!</p>";
		}
		else {
			echo "<p>For some reason, your email was not sent.  Please try again later</p>";
		}
		
	}
?>