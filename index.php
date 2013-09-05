<!doctype html>
<html>
<head>
	<title>Setting off</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<?php


/**
 * Simple example of extending the SQLite3 class and changing the __construct
 * parameters, then using the open method to initialize the DB.
 */
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('mysqlitedb.db');
    }
}

$db = new MyDB();

$db->exec('CREATE TABLE foo (bar STRING)');
$db->exec("INSERT INTO foo (bar) VALUES ('This is a test')");

$result = $db->query('SELECT bar FROM foo');
var_dump($result->fetchArray());

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

<?php

// display partners details here

?>

</body>
</html>
