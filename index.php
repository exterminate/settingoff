

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
	}
	
	echo "<div class='error-warning'><p>$errorMessage</p></div>";

}
?>
<h1>Setting off?</h1>
<h2>Need to let a loved one know what time you've set of to meet them? You can now, with this handy app.</h2>
<p>Sign up, login, share the link and save the page to your smart phone's home screen.</p>

<?php 
// if logged in show options, set off
//if($session){
        if(!isset($_GET['id'])) {
                $_GET['id'] = "";
                
                // have you forgot your unique id? Log in
?>
<div class='register-form'>				
	<form action="postlandr.php" method="POST">
		<label>Name:</label><br><input type="text" name="name">
		<!-- validate for 2 or more letters --><br>
		<label>E-mail:</label><br><input type="email" name="email"><br>
		<label>Enter Password:</label><br><input type="password" name="passone">
		<!-- validate for 6 or more letters --><br>
		<label>Enter password again:</label><br><input type="password" name="passtwo">
		<!-- validate matching passwords --><br>
		<input type="submit" name="register" value="Register">
	</form>
	<button id="toggle-register">Login</button>
</div>
<div class='login-form'>				
	<form action="postlandr.php" method="POST">		
		<label>E-mail:</label><br><input type="email" name="email"><br>
		<label>Enter Password:</label><br><input type="password" name="passone"><br>
		<input type="submit" name="login" value="Login">
	</form>
	<button id="toggle-login">Register</button>
</div>
<?php                
                // need to register?
                
        }else{
                $id = $_GET['id'];
                //find saved information that from database that matches id.
                $sql = "SELECT * FROM data WHERE id = '$id'";
                
                // to create a new "set off" or edit an old one, log in
                
                echo "<h3>Log in to modify your settings.</h3>";
                // form
      
                
        
//}

?>


<button id="set-off">Set off</button>

<?php
		}

// display partners details here


class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('myDatabase.db');
    }
}

$db = new MyDB();
$db->exec('
	CREATE TABLE if not exists users (
	id INT PRIMARY KEY NOT NULL,
 	email TEXT NOT NULL,
 	name TEXT NOT NULL,
 	password CHAR(32),
 	todayTime TEXT NOT NULL,
 	todayDate TEXT NOT NULL,
 	lastTimeTime TEXT NOT NULL
 	)');
$query = 'SELECT * FROM users'; 	
//$result = $db->query($query);

if($result = $db->query($query))
{
  while($row = $result->fetchArray())
  {
    print("Email: {$row['email']} <br />" .
          "Name: {$row['name']} <br />".
          "Name: {$row['0']} <br />".
          "Time: {$row['todayTime']} <br /><br />");
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
	
});

</script>
</body>
</html>
