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
	<link href="http://fonts.googleapis.com/css?family=Lilita+One" rel="stylesheet" type="text/css">
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
		case "alreadyshared":				
			$errorMessage = "You are already sharing with this person.";
			break;	
		case "cantconnectwithself":				
			$errorMessage = "You can't connect with yourself, silly.";
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
		case "sethome":				
			$goodMessage = "Your home location has been set";
			break;							
	}
	
	echo "<div class='good-msg paddingTen'><p>$goodMessage <span id='hide' class='pointer' alt='hide' title='hide'>[X]</span></p></div>";	
}				


if(isset($_GET['request'])){
		echo "<div class='good-msg paddingTen'><p>You have a request. Log in or register to join and share a connection <span id='hide' class='pointer' alt='hide' title='hide'>[X]</span></p></div>";	
}
?>
<h1>Setting off?</h1>
<h2>Need to let a loved one know what time you've set off to meet them? You can now, with this handy app.</h2>
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
			<a href='#' alt='make home'>Set home</a>
			<a href='index.php?id=".$id."' alt='refresh'>Refresh</a>
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
				
				// Show connection from ID
				showConnection($id,$db,$todayDate,$todayTime);
				
			} 
			
			echo "<p><a href='index.php'>Show all connections</a></p>";
		}	
	}else{
		
		// Show connection from ID
		showConnection($id,$db,$todayDate,$todayTime);
		loginOrRegister();
	} 

}else{                             

	if(isset($_SESSION['name'])){
		$logInName = $_SESSION['name'];
		echo "
		<h3>Hello, $logInName</h3>
		<nav class='nav'>
			<a alt='make home'>Set home</a>
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
			echo "<hr>";
			
			$connectionNumber = $row['connectionNo'];
			// get complementary connectionNO
			if(substr($connectionNumber,-1) == "a")
				$tempNo = substr($connectionNumber,0,-1)."b";
			else	
				$tempNo = substr($connectionNumber,0,-1)."a";
								
			$getOtherUser = $db->query("SELECT * FROM connection WHERE connectionNo = '$tempNo'");
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
					}else // does this work?
						echo "<p>Your connection has not been confirmed yet.</p>";		
				}
			}
			echo "<p>Connection created: ".$row['dateCreated']."</p>";
			

			// set home location
			$varHome = returnCoords($db,$email);
			
			echo "<p id='jqd'> </p>";// NOT FINISHED WITH THIS
			if(!empty($varHome)){
				$coordArray = explode(",",$varHome);
				$latHome = $coordArray[0];
				$lngHome = $coordArray[1];				
			// FINISH THIS
				echo "<p id='linkToSetHome'></p>";
				
			}else{
				echo "<p id='linkToSetHome'></p>";
			}
			
			echo "<hr>";
		}
		
	}else{
		loginOrRegister();
	}	
       
}

?>


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
    print("ID: {$row['id']} <br />" .
		  "Email: {$row['email']} <br />" .
          "Name: {$row['name']} <br />".
          "Date: {$row['todayDate']} <br />".
          "Home: {$row['home']} <br />".
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

if(!isset($latHome)){
$latHome = 52.23567979454229;
$lngHome = 0.14059868454934;
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
	
	
	// START -- calculate minutes since set off
	var nowDate = $('#time-left').html(); 
	
	var startDate = new Date(nowDate);
	setInterval(function(){getTime(startDate);}, 1000);

	$('#time-left').html(startDate); 

  
	function getTime(startDate){   
		var date = new Date();
	  	var seconds = (date - startDate)/1000;
	  	$('#time-left').html(toHHMMSS(seconds));
  
	}

	function toHHMMSS(sec) {
		var sec_num = parseInt(sec, 10);
		var hours   = Math.floor(sec_num / 3600);
		var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
		var seconds = sec_num - (hours * 3600) - (minutes * 60);

		if (hours   < 10) {hours   = "0"+hours;}
		if (minutes < 10) {minutes = minutes;}
		if (seconds < 10) {seconds = "0"+seconds;}
	  	//  var time    = hours+':'+minutes+':'+seconds;
  		if(hours > 0) { var time = hours * 60 + minutes; }
  		if(hours == 0) { var time = minutes; }
	  	
		return time;
	}});
	// END -- calculate minutes since set off
	
	
	// START -- calculate distance
	
	var y = document.getElementById("jqd");
	function getLocation() {
	  	if (navigator.geolocation) {
			navigator.geolocation.watchPosition(showPosition);
		}else{y.innerHTML="Geolocation is not supported by this browser.";}
	}
	
	//duplicate for first time position
	var z = document.getElementById("FTP");
	function getLocation() {
	  	if (navigator.geolocation) {
			navigator.geolocation.watchPosition(showPosition);
		}else{z.innerHTML="Geolocation is not supported by this browser.";}
	}
  
	function distance(lat1, lng1, lat2, lng2) {
		var miles = true;
		var pi80 = Math.PI / 180;
		var lat1 = lat1 * pi80;
		var lng1 = lng1 * pi80;
		var lat2 = lat2 * pi80;
		var lng2 = lng2 * pi80;
	
 
		var r = 6372.797; // mean radius of Earth in km
		console.log("r:"+r);
		var dlat = lat2 - lat1;
		var dlng = lng2 - lng1;
		var a = Math.sin(dlat / 2) * Math.sin(dlat / 2) + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dlng / 2) * Math.sin(dlng / 2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
		var km = r * c;
 
		var dist = (miles ? (km * 0.621371192) : km);
		return dist.toFixed(1);
	}
  
  
	function showPosition(position) {
	  	y.innerHTML="Latitude: " + position.coords.latitude + 
	  	"<br>Longitude: " + position.coords.longitude;	
  
	  	var lati = position.coords.latitude;
	  	var lng = position.coords.longitude;
	  	
	  	var setHome = "<a id='setHomeLink' href='postlandr.php?action=set-home&lat=" + lati + "&lng=" + lng + "'>Set home</a>";
  		$("#linkToSetHome").html(setHome);
  		
	  	var stuff = distance("<?php echo $latHome; ?>","<?php echo $lngHome; ?>",lati,lng);
	  	$("#jqd").text(stuff);
	}
	
    $("#locationNow").click(function(){
        $("#demo").addClass("blue");
        getLocation();
    });	
    
    $('#jqd').append(getLocation());
	
	$(document).on("click","#setHomeLink",function(){
		var r = confirm("Reset home?");
		if (r==true) {
			x="You pressed OK!";
		} else {
			x="You pressed Cancel!";
			return false;
		}
		alert(x);
		
	});
	
	// END -- calculate distance
  	
</script>
</body>
</html>
