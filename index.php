<!doctype html>
<html>
<head>
	<title>Setting off</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<?php
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
<h1>Setting off?</h1>
<h2>Need to let a loved one know what time you've set of to meet them? You can now, with this handy app.</h2>
<p>Sign up, login, share the link and save the page to your smart phone's home screen.</p>

<?php 
// if logged in show options, set off
if($session()){
        if(!isset($_GET['id']) {
                $_GET['id'] = "";
                
                // have you forgot your unique id? Log in
                
                // need to register?
                
        }else{
                $id = $_GET['id'];
                //find saved information that from database that matches id.
                $sql = "SELECT * FROM data WHERE id = '$id'";
                
                // to create a new "set off" or edit an old one, log in
                
                echo "<h3>Log in to modify your settings.</h3>";
                // form
      
                
        }
}

?>


<button id="set-off">Set off</button>

</body>
</html>
