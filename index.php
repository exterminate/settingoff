<?php
class MyDB extends SQLite3 {
    function __construct() {
        $this->open('myDatabase.db');
    }
}
    $db = new MyDB();
    $db->exec('CREATE TABLE if not exists goinghome 
    (ID TEXT PRIMARY KEY NOT NULL,
    date TEXT NOT NULL,
    time TEXT NOT NULL,
    home TEXT NOT NULL,
    assoc1 TEXT NOT NULL,
    assoc2 TEXT NOT NULL,
    assoc3 TEXT NOT NULL,
    assoc4 TEXT NOT NULL
    )');

//today's date and time need to go here
$todayDate = date("F j Y");
$todayTime = date("H:i:00");

/* Random functions */

function redirect($id,$msg) {
	header("Location: index.php?id=$id&msg=$msg"); /* Redirect browser */
    exit;
}

if(isset($_GET['msg'])) {
	switch($_GET['msg']) {
		case "usernoexist":
			$msg = "The user you tried to add does not exist";
			break;
	}

	echo "<div class='message'><p>".$msg."</p></div>";
}


/* Collect submitted data */


if(isset($_POST['submit'])) {
    if (preg_match('/[A-Za-z0-9]/', $_POST['id']))
		$id = $_POST['id']; // clean potential attacks
    // insert id into database   
	$sql = "INSERT INTO goinghome (ID,date,time,home,assoc1,assoc2,assoc3,assoc4) VALUES ('$id','','','','','','','')";
	$db->exec($sql);
}

if(isset($_POST['addAssoc'])) {
	$assoc = trim($_POST['assoc']);
	$name = trim($_POST['name']);
	if (preg_match('/[A-Za-z0-9]/', $_GET['id']))
		$id = $_GET['id'];
	//check assoc user exists
	$result = $db->query("SELECT count(ID) as count FROM goinghome WHERE ID='$assoc'");
	while($row = $result->fetchArray()) {
		if($row['count'] == 0)
			redirect($id,"usernoexist");
	}
	
	// join assoc
	
	$assoc = $assoc.$name;
	
	// update
	$result = $db->query("SELECT * FROM goinghome WHERE ID='$id'");
	while($row = $result->fetchArray()) {
		if(empty($row['assoc1'])){
			$db->query("UPDATE goinghome SET assoc1 = '$assoc' WHERE ID='$id'");
			break;
		}elseif(empty($row['assoc2'])){
			$db->query("UPDATE goinghome SET assoc2 = '$assoc' WHERE ID='$id'");
			break;
		}elseif(empty($row['assoc3'])){
			$db->query("UPDATE goinghome SET assoc3 = '$assoc' WHERE ID='$id'");
			break;
		}elseif(empty($row['assoc4'])){
			$db->query("UPDATE goinghome SET assoc4 = '$assoc' WHERE ID='$id'");
			break;
		}
	}
}


if(isset($_GET['set'])) {

	if($_GET['set'] == "home") {
		if(isset($_GET['lat']))
			$lat = $_GET['lat'];
		else
			$lat="";
		if(isset($_GET['lon']))		
			$lon = $_GET['lon'];
		else
			$lon='';	
		$home = $lat.";".$lon;
		if (preg_match('/[A-Za-z0-9]/', $_GET['id']))
			$id = $_GET['id'];
		$db->query("UPDATE goinghome SET home = '$home' WHERE ID='$id'");
	}elseif($_GET['set'] == "resethome") {
		$lat = $_GET['lat'];
		$lon = $_GET['lon'];
		$home = $lat.";".$lon;
		if (preg_match('/[A-Za-z0-9]/', $_GET['id']))
			$id = $_GET['id'];
		$db->query("UPDATE goinghome SET home = '$home' WHERE ID='$id'");
	}elseif($_GET['set'] == "off"){
        if (preg_match('/[A-Za-z0-9]/', $_GET['id']))
			$id = $_GET['id'];
		$db->exec("UPDATE goinghome SET time='$todayTime',date='$todayDate' WHERE ID = '$id'");
	}elseif($_GET['set'] == "off-again"){
        if (preg_match('/[A-Za-z0-9]/', $_GET['id']))
			$id = $_GET['id'];
		$db->exec("UPDATE goinghome SET time='',date='' WHERE ID = '$id'");
	}	
}
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<style>
		#home {
			display:none
		}	
	</style>
</head>
<body>

<?php

class Person {
    function __construct($id){
        $this->id = $id;
    }    
    
    function getStatus($db){ //may need id
        $rows = $db->query("SELECT * FROM goinghome WHERE ID='$this->id'");         
        while ($row = $rows->fetchArray()) {
            if(empty($row['home'])){
                return "<div class=''><p class='info'>Set your home location</p><a id='setHome' href='index.php?id=".$this->id."&set=home'>Set home</a></div>";
            }else{
				// reset home
				return "<div class=''><p class='info'>Set your home location</p><a id='setHome' href='index.php?id=".$this->id."&set=resethome'>Reset home</a></div>";
			}
        }
    }
    
    function setOff($db,$todayDate) {
        $rows = $db->query("SELECT date,time FROM goinghome WHERE ID='$this->id'");         
        while ($row = $rows->fetchArray()) {
            if($row['date'] != $todayDate){
                return "<div class=''><p>Set off home</p><a href='index.php?id=".$this->id."&set=off'>Set off</a></div>";
            }else{
				/*
				
				@@@ - Matt here's where you need the JS to calculate time
				
				*/
                return "
				<div class=''>
					<p id='time-left'>".$row['date']." ".$row['time'].":00</p>
				</div>
				<div class=''>
					<p>Going somewhere else today?</p>
					<a href='index.php?id=".$this->id."&set=off-again'>Reset</a>
				</div>";
            }
        }
    }
	
	
	/*
	
	@@@ - Matt, this method has all the bits for the friends (or assocs) 
	You can have four friends hence the repetative code
	
	*/
	function showAssoc($db) {
		$result = $db->query("SELECT * FROM goinghome WHERE ID='$this->id'");
		$arbNum = 1;
		$returnThis = "";
		while($row = $result->fetchArray()) {
			
			if(!empty($row['assoc1'])) {
				$getID = substr($row['assoc1'],0,8);
				$getAssoc = $db->query("SELECT * FROM goinghome WHERE ID='$getID'"); 
				while($row1 = $getAssoc->fetchArray()) {	
					$returnThis .= "<div class='connection' id='".$arbNum."'><p class='name'>".substr($row['assoc1'],8)."</p><p id='time-left'>".$row1['date']." ".$row1['time']."</p><p class='homeCoords'>".$row1['home']."</p></div>"; 
				}	
				$arbNum++;
			}
			
			if(!empty($row['assoc2'])) {
				$getID = substr($row['assoc2'],0,8);
				$getAssoc = $db->query("SELECT * FROM goinghome WHERE ID='$getID'"); 
				while($row1 = $getAssoc->fetchArray()) {				
					$returnThis .= "<div class='connection' id='".$arbNum."'><p class='name'>".substr($row['assoc2'],8)."</p><p id='time-left'>".$row1['date']." ".$row1['time']."</p><p class='homeCoords'>".$row1['home']."</p></div>"; 
				}	
				$arbNum++;
			}
			
			if(!empty($row['assoc3'])) {
				$getID = substr($row['assoc3'],0,8);
				$getAssoc = $db->query("SELECT * FROM goinghome WHERE ID='$getID'"); 
				while($row1 = $getAssoc->fetchArray()) {				
					$returnThis .= "<div class='connection' id='".$arbNum."'><p class='name'>".substr($row['assoc3'],8)."</p><p id='time-left'>".$row1['date']." ".$row1['time']."</p><p class='homeCoords'>".$row1['home']."</p></div>"; 
				}	
				$arbNum++;
			}
			
			if(!empty($row['assoc4'])) {
				$getID = substr($row['assoc4'],0,8);
				$getAssoc = $db->query("SELECT * FROM goinghome WHERE ID='$getID'"); 
				while($row1 = $getAssoc->fetchArray()) {				
					$returnThis .= "<div class='connection' id='".$arbNum."'><p class='name'>".substr($row['assoc4'],8)."</p><p id='time-left'>".$row1['date']." ".$row1['time']."</p><p class='homeCoords'>".$row1['home']."</p></div>"; 
				}	
				$arbNum++;
			}
			
		}
		
		return $returnThis;
		
	}
	
	function addAssoc($db) {
		$rows = $db->query("SELECT * FROM goinghome WHERE ID='$this->id'");         
		$free = 0;
        while ($row = $rows->fetchArray()) {
			if(empty($row['assoc1']))
				$free++;
			if(empty($row['assoc2']))
				$free++;
			if(empty($row['assoc3']))
				$free++;
			if(empty($row['assoc4']))
				$free++;				
		}
		if($free != 0){
			return "
				<div class=''>
					<p>Add another user's ID</p>
					<form action='index.php?id=".$this->id."' method='POST'>
						<label>ID</label><br>
						<input type='text' name='assoc'><br>
						<label>Name</label><br>
						<input type='text' name='name'><br>
						<input type='submit' name='addAssoc' value='Add'>
					</form>
					<p>You can add $free more people.</p>
				</div>";
		}		
	}
}
?> 

<div class='main'>

<?php

if(!isset($_GET['id'])){
    $good = 0;
    while($good == 0){
	
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomID = '';
		for ($i = 0; $i < 8; $i++) {
			$randomID .= $characters[rand(0, strlen($characters) - 1)];
		}
	
        $rows = $db->query("SELECT count(ID) as count FROM goinghome WHERE ID='$randomID'");         
        $row = $rows->fetchArray();
        $numRows = $row['count'];
        if($numRows == 0)
            $good = 1;
    }
    echo "<p>Below is your unique ID, hit Go! to start.</p>";
    echo "
    <form method='POST' action='index.php?id=".$randomID."'>
    <label>ID</label>
    <input type='text' name='id' value='".$randomID."' readonly='readonly'>
    <input type='submit' value='Go!' name='submit'>
    </form>
    ";
}else{
    echo "<h2>Welcome!</h2>";
	
    if(isset($_POST['id']))
        echo "<p class='info'>Bookmark this page so you don't lose your connection</p>";
    $id = $_GET['id'];
    $user = new Person($id);
    echo $user->getStatus($db);
    echo $user->setOff($db,$todayDate);
	echo $user->addAssoc($db);
	
	// show users that you are connected with, delete <hr> later
	echo "<hr>";
	echo $user->showAssoc($db);
	echo "<hr>";
	
	
	// this is filled by the JS
	echo "<div id='home'>*</div>";
    
    
    
    /* v - view table - delete later */
	$display = $db->query("SELECT * FROM goinghome");
	while($row = $display->fetchArray()) {
		echo $row['ID']." * ".$row['home']." * ".$row['date']." * ".$row['time']." * ".$row['assoc1']."<br>";
	}
	/* ^ - Delete later */
}    
    
?>
<!--<button onclick='getLocation()'>Try It</button>-->
</div>

<script>
var x=document.getElementById("home");
function getLocation()
  {
  if (navigator.geolocation)
    {
    navigator.geolocation.getCurrentPosition(showPosition);
    }
  else{x.innerHTML="Geolocation is not supported by this browser.";}
  }
function showPosition(position)
  {
  //x.innerHTML="Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude; 
  x.innerHTML= "&lat=" + position.coords.latitude + "&lon=" + position.coords.longitude;
  var coords = "&lat=" + position.coords.latitude + "&lon=" + position.coords.longitude;
  var replaceAttr = $('#setHome').attr('href');
  var newAction = replaceAttr + coords;
  $('#setHome').get(0).setAttribute('href', newAction); //this works
  }

	$(document).ready(function(){
		$("#home").text(getLocation());
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
	
</script>
<style>
.connection {
	border: 1px solid black;
}
</style>
</body>
</html>