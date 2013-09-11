<?php 

	session_start();
	include "functions.php";
	if(isset($_SESSION['email']))
		$email = $_SESSION['email'];
		
	$todayTime = date("H:i");
	$todayDate = date("j F Y");	
	
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('myDatabase.db');
    }
}
	
	$db = new MyDB();
?>

<!doctype html>
<html>
<head>
	<title>Setting off</title>
	<link rel="stylesheet" href="style.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<?php

//include "postlandr.php";


//maybe look at this http://www.tutorialspoint.com/sqlite/sqlite_php.htm

/*
Fields

email
name
password
id
today_time
today_date
lasttime_time

*/

?>

<?php
if ( isset($_GET['error']) ) {
	$error = $_GET['error'];
	switch($error){
		case "emailalreadyregistered":
			$errorMessage = "An account has been registered with this email address. Reset password or try again.";
			break;
		case "lettersandnumbersname":
			$errorMessage = "Please only use English letters and numbers for your name.";	
			break;
		case "lettersandnumberspass":
			$errorMessage = "Please only use English letters and numbers for your password.";	
			break;	
		case "notenoughchars":	
			$errorMessage = "Your password must be more than 5 characters.";
			break;		
		case "passnotmatch":	
			$errorMessage = "Your passwords do not match. Please go back and try again.";
			break;
		case "passnotright":	
			$errorMessage = "Your password does not match the password on out system. Reset password or try again.";
			break;			
		case "noemailregistered":				
			$errorMessage = "Your email address is not in our system. Why not try registering?";
			break;
		case "nomatchingid":				
			$errorMessage = "This link .";
			break;	
		case "emailbad":				
			$errorMessage = "Oops, something went wrong there.";
			break;						

	}
	
	echo "<div class='error-warning paddingTen'><p>$errorMessage <span id='hide' class='pointer' alt='hide' title='hide'>[X]</span></p></div>";	

}

if ( isset($_GET['msg']) ) {
	$good = $_GET['msg'];
	switch($good){
		case "thanks":				
			$goodMessage = "Thank you for registering. Please log in to add some settings.";
			break;
		case "emailok":				
			$goodMessage = "We have emailed your connection. We will email you when your contact has accepted.";
			break;
		case "setoff":				
			$goodMessage = "You have set off!";
			break;					
	}
	
	echo "<div class='good-msg paddingTen'><p>$goodMessage <span id='hide' class='pointer' alt='hide' title='hide'>[X]</span></p></div>";	
}				


if(isset($_GET['request'])){
		echo "<div class='good-msg paddingTen'><p>You have a request. Log in or register to join and share a connection <span id='hide' class='pointer' alt='hide' title='hide'>[X]</span></p></div>";	
}
?>
<h1>Setting off?</h1>
<h2>Need to let a loved one know what time you've set of to meet them? You can now, with this handy app.</h2>
<p>Sign up, login, share the link and save the page to your smart phone's home screen.</p>

<?php 
// if logged in show options, set off



if(isset($_GET['id'])){
	$id = $_GET['id'];
	if(isset($_SESSION['name'])){
		$logInName = $_SESSION['name'];
		echo "
		<h3>Hello, $logInName</h3>
		<nav class='nav'>
			<a href='#' alt='make home'>Add home</a>
			<a href='index.php' alt='refresh'>Refresh</a>
			<button id='connect' alt='Connect'>Connect</button>
			<a href='#' alt='delete'>Delete</a>
			<a href='logout.php' alt='logout'>Logout</a>
		</nav>
		";
		makeConnect($id,$logInName,$email);
		
		// look for pre-existing links
		$result = $db->query("SELECT COUNT(email) as count FROM connection WHERE connectionNo = '$id'");
		
		while ($row = $result->fetchArray()) {
			if ($row['count'] == 0){
				echo "<p>This is an invalid connection.</p>";
			}else{
				// show connection
				
				$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$id'");
				while($rowCon = $connectionResult->fetchArray()){
					$otherUserEmail = $rowCon['email'];
					
					if($rowCon['email'] == $email) 
						continue;
					
					
					$connectionName = $db->query("SELECT * FROM users WHERE email = '$otherUserEmail'");
					while($rowConName = $connectionName->fetchArray()) {
						echo "<p class='name'>".$rowConName['name']."</p>";
						$conName = $rowConName['name'];
						$conTime = $rowConName['todayTime'];
						$conDate = $rowConName['todayDate'];
					}
					
					$rowConDate = $rowCon['dateCreated'];
					echo "<p>Connection created: ".$rowConDate."</p>";
					
				}
				
			// has your connected friend set off?
			if($conDate == $todayDate) { 	//yes
				echo "<p>". $conName." set off today at ".$conTime."</p>";
			}else{ 							//no
				echo "<p>". $conName." has not set off today</p>";
			}
			
			
			$myDetails = $db->query("SELECT * FROM users WHERE email = '$email'");
			while($rowConMe = $myDetails->fetchArray()) {
				
				$myDate = $rowConMe['todayDate'];
				$myTime = $rowConMe['todayTime'];	
	
				// have you set off?
				if($myDate == $todayDate) { // yes
					echo "<p>You set of at ".$myTime."</p>";
				}else{                      // no
					echo '<a href="postlandr.php?action=setoff&setOffTime='.$todayTime.'&setOffDate='.$todayDate.'&id='.$rowConMe['id'].'&connection='.$id.'" id="set-off">Set off</a>';
				}
			
			}
				
			} 
			
			echo "<p><a href='index.php'>Show all connections</a></p>";
		}	
	}else{
		loginOrRegister();
	} 

}else{                             

	if(isset($_SESSION['name'])){
		$logInName = $_SESSION['name'];
		echo "
		<h3>Hello, $logInName</h3>
		<nav class='nav'>
			<a href='#' alt='make home'>Add home</a>
			<a href='index.php' alt='refresh'>Refresh</a>
			<button id='connect' alt='Connect'>Connect</button>
			<a href='#' alt='delete'>Delete</a>
			<a href='logout.php' alt='logout'>Logout</a>
		</nav>";
		$id = $_SESSION['id'];
		makeConnect($id,$logInName,$email);

		// display existing connections
		$result = $db->query("SELECT * FROM connection WHERE email = '$email'");
		
		while($row = $result->fetchArray()) {
			
			
			$connectionNumber = $row['connectionNo'];
			$getOtherUser = $db->query("SELECT * FROM connection WHERE connectionNo = '$connectionNumber'");
			while($rowConnection = $getOtherUser->fetchArray()) {
				$otherUserEmail = $rowConnection['email'];
				
				// remove self
				if($rowConnection['email'] == $email)
					continue;
				
				// has user other user registered yet?
				$userRegistered = $db->query("SELECT COUNT(email) as count FROM users WHERE email = '$otherUserEmail'");
				while ($rowCount = $userRegistered->fetchArray()) {
					if ($rowCount['count'] > 0){
				
						// yes, let's show them
						$UsersName = $db->query("SELECT * FROM users WHERE email = '$otherUserEmail'");
						while($rowOtherUser = $UsersName->fetchArray()) {
							echo "<p>Connection with: <a href='index.php?id=".$connectionNumber."'><span class='name'>" .$rowOtherUser['name']."</span></a></p>";
						}
					}else
						echo "<p>Your connection has not been confirmed yet.</p>";		
				}
			}
			echo "<p>Connection created: ".$row['dateCreated']."</p>";
			

		}
		
	}else{
		loginOrRegister();
	}	
       
}

?>


<button id="set-off">Set off</button>
<hr>
<?php


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

// DELETE LATER

$query = 'SELECT * FROM users'; 	
//$result = $db->query($query);

if($result = $db->query($query))
{
  while($row = $result->fetchArray())
  {
    print("Email: {$row['email']} <br />" .
          "Name: {$row['name']} <br />".
          "Date: {$row['todayDate']} <br />".
          "Time: {$row['todayTime']} <br /><br />");
  }
}
else
{
  die($error);
}


$query = 'SELECT * FROM connection'; 	
//$result = $db->query($query);

if($result = $db->query($query))
{
  while($row = $result->fetchArray())
  {
    print("Email: {$row['email']} <br />" .
          "Name: {$row['connectionNo']} <br />"
          );
  }
}
else
{
  die($error);
}

?>
<script>

$(document).ready(function(){
	$("#toggle-login").click(function(){
		$(this).parent().hide();
		$(".register-form").show();	
	});
	$("#toggle-register").click(function(){
		$(this).parent().hide();	
		$(".login-form").show();
	});
	// hide parent of anything with id=hide 
	$("#hide").click(function(){
		$(this).parent().parent().slideUp('fast');
	});
	// show id=connect 
	$("#connect").click(function(){
		$(".connect-form").show();		
	});
	// hide parent of anything with id=hide 
	$("#connecthide").click(function(){
		$(this).parent().slideUp('fast');
	});

});

</script>
</body>
</html>
