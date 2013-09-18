<?php

$todayTime = date("H:i");
$todayDate = date("j F Y");

include "functions.php";

$error = "uh oh";
try
{
  //create or open the database
  $db = new SQLite3('myDatabase.db', 0666, $error);
}
catch(Exception $e)
{
  die($error);
}

$db->exec('
	CREATE TABLE if not exists users (
	id INT PRIMARY KEY NOT NULL,
 	email TEXT NOT NULL,
 	name TEXT NOT NULL,
 	password CHAR(32),
 	todayTime TEXT NOT NULL,
 	todayDate TEXT NOT NULL,
 	lastTimeTime TEXT NOT NULL,
 	home TEXT NOT NULL
 	)');
 	
$db->exec('
	CREATE TABLE if not exists connection (
	email KEY NOT NULL,
 	connectionNo TEXT NOT NULL,
 	dateCreated TEXT NOT NULL
 	)');


if (isset($_POST['name']))
	$name = $_POST['name'];
if (isset($_POST['passone'])){	
	if (strlen($_POST['passone']) < 6) // password validation
		getBackToPage("notenoughchars");
	else{
		if (!preg_match('/[A-Za-z0-9]/', $_POST['passone']))
			getBackToPage("lettersandnumberspass");
		else
			$passone = md5($_POST['passone']);
	}		
}
if (isset($_POST['passtwo']))
	$passtwo = md5($_POST['passtwo']);
if (isset($_POST['email']))		
	$email = $_POST['email'];

if(isset($_POST['register'])){
	$id = rand(1111111,9999999);
	//$result = $db->exec("SELECT id FROM users WHERE id = '$id'");
	$result = $db->query("SELECT COUNT(id) as count FROM users WHERE id = '$id'");
	$row = $result->fetchArray();
	
	// validation
	while($row['count'] == 0){
		$emailQuery = $db->query("SELECT COUNT(email) as count FROM users WHERE email = '$email'");
		$emailRow = $emailQuery->fetchArray();
		if ($emailRow['count'] != 0)
			getBackToPage("emailalreadyregistered");	
		if (!preg_match('/[A-Za-z0-9]/', $name))
			getBackToPage("lettersandnumbersname");
	  	if ($passone !== $passtwo)
			getBackToPage("passnotmatch");
		$query = "INSERT INTO users 
	  		(id,    email,   name,   password,  todayTime,   todayDate,   lastTimeTime, home) VALUES 
	  		('$id','$email','$name','$passone','','','', '')";
	  	$db->exec($query);
		$row['count'] = 1;
		
		
	}
	$id = "";
	getBackToPageOK("thanks");
}

if(isset($_POST['login'])){
	$result = $db->query("SELECT COUNT(email) as count FROM users WHERE email = '$email'");
	$row = $result->fetchArray();
	if($row['count'] == 1){
		$checkPassword = $db->query("SELECT * FROM users WHERE email = '$email'");
		while ($rowPass = $checkPassword->fetchArray()) {
			if ($rowPass['password'] == $passone){
				//echo "start session";
				session_start();
				$_SESSION['name'] = $rowPass['name'];
				$_SESSION['id'] = $rowPass['id'];
				$_SESSION['email'] = $rowPass['email'];
				getBackToPageOK("login");
			}else
				getBackToPage("passnotright");
		}
			
		
	}else
		getBackToPage("noemailregistered");	


}else
	$_POST['login'] = "";


if(isset($_POST['connect'])){
	$id = $_GET['id'];
	$name = $_GET['name'];
	$getEmail = $_GET['email'];
	
	$connectId = rand(111111111,9999999999);
	$connectId = $connectId.md5($todayDate).$id;
	$result = $db->query("SELECT COUNT(connectionNo) as count FROM connection WHERE connectionNo = '$connectId'");
	$row = $result->fetchArray();
	
	while($row['count'] == 0){
		$connectIdb = $connectId."b";
		$query = "INSERT INTO connection 
	  		(email,    connectionNo,   dateCreated) VALUES 
	  		('$email','$connectIdb','$todayDate')";
	  	$db->exec($query);
	  	$connectIda = $connectId."a";
		$query = "INSERT INTO connection 
	  		(email,    connectionNo,   dateCreated) VALUES 
	  		('$getEmail','$connectIda','$todayDate')";
	  	$db->exec($query);
		$row['count'] = 1;
		
		// Send email
		$to = $email;
	   	$subject = $name." has invited you to GoingHo.me";
	  	$message = $name." has invited you to GoingHo.me.\n\nClick the link below to join\n\nhttp://localhost:8888/settingoff/settingoff/index.php?request=".$connectId;
	    $header = "From:GoingHo.me\r\n";
  	    $retval = mail($to,$subject,$message,$header);
  	    $connectId = "";
	    if( $retval == true )  {
			getBackToPageOK("emailok");
	   	} else {
			getBackToPage("emailbad");
	   	}
		
	}
}else
	$_POST['connect'] = "";


if(isset($_GET['action'])){
	if($_GET['action'] == "setoff"){
		$setOffTime = $_GET['setOffTime'];
		$setOffDate = $_GET['setOffDate'];
		$myid = $_GET['id'];
		$myconnection = $_GET['connection'];
	
		$result = $db->exec("UPDATE users SET todayDate='$setOffDate',todayTime='$setOffTime' WHERE id = '$myid'");
		 getBackToPageID("setoff",$myconnection);
	
	}
}else
	$_GET['action'] = '';


$db->close();
?>