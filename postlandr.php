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
//$db->exec("INSERT INTO foo (bar) VALUES ('This is a test')");

//$result = $db->query('SELECT bar FROM foo');
//var_dump($result->fetchArray());


if (isset($_POST['name']))
	$name = $_POST['name'];
if (isset($_POST['passone']))	
$passone = md5($_POST['passone']);
if (isset($_POST['passtwo']))
	$passtwo = md5($_POST['passtwo']);
if (isset($_POST['email']))		
	$email = $_POST['email'];

$id = rand(11111,99999);
//$result = $db->exec("SELECT id FROM users WHERE id = '$id'");
$result = $db->query("SELECT COUNT(id) as count FROM users WHERE id = '$id'");
$row = $result->fetchArray();
$numRows = $row['count'];
echo $numRows;


if(isset($_POST['login'])){
	//$result = $db->exec("INSERT INTO users VALUES ('This is a test')");
	//var_dump($result->fetchArray());
}else
	$_POST['login'] = "";


$db->close();
?>