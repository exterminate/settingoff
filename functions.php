<?php

function loginOrRegister(){
	echo '
	<div class="register-form">				
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
	<div class="login-form">				
		<form action="postlandr.php" method="POST">		
			<label>E-mail:</label><br><input type="email" name="email"><br>
			<label>Enter Password:</label><br><input type="password" name="passone"><br>
			<input type="submit" name="login" value="Login">
		</form>
		<button id="toggle-login">Register</button>
	</div>';
}

function getBackToPage($errormsg) {
	header("Location: index.php?error=$errormsg"); /* Redirect browser */
	exit;
}


function getBackToPageOK($goodmsg) {
	if($goodmsg != "login")
		$msgValue = "?msg=".$goodmsg;
	else
		$msgValue = "";
	header("Location: index.php$msgValue"); /* Redirect browser */
	exit;
}	

function getBackToPageID($goodmsg,$theID) {
	
	$msgValue = "?msg=".$goodmsg."&id=".$theID;

	header("Location: index.php$msgValue"); /* Redirect browser */
	exit;
}

function makeConnect($id,$logInName,$email){
	echo '
	<div class="connect-form">	
		<form action="postlandr.php?id='.$id.'&name='.$logInName.'&email='.$email.'" method="POST">
			<label>E-mail:</label><br><input type="email" name="email">
			<input type="submit" name="connect" value="Connect">
		</form>
		<button id="connecthide">Hide</button>
	</div>';
}


function showConnection($id,$db,$todayDate,$todayTime){
	
	$nowTime = date("F d, Y ");
	
	echo "<hr>";	echo "<hr>";	
	$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$id'");
	while($rowCon = $connectionResult->fetchArray()){
		$email = $rowCon['email'];
		$rowConDate = $rowCon['dateCreated'];
		
		// get user info from their id
		$getPersonEmail = $db->query("SELECT * FROM users WHERE email = '$email'");
		while($rowPersonEmail = $getPersonEmail->fetchArray()){
			$myName = $rowPersonEmail['name'];
			$myDate = $rowPersonEmail['todayDate'];
			$myTime = $rowPersonEmail['todayTime'];
			$personID = $rowPersonEmail['id'];
			
			// have you set off?
			if($myDate == $todayDate) { // yes
				
				echo "<p>You set off at ".$myTime."</p><p><span id='time-left'>".$nowTime.$myTime.":00</span> minutes ago.</p>";
			}else{                      // no
				echo '<a href="postlandr.php?action=setoff&setOffTime='.$todayTime.'&setOffDate='.$todayDate.'&id='.$personID.'&connection='.$id.'" id="set-off">Set off</a>';
			}

		}
	}
	
	
	
	echo "<hr>";	
	
	if(substr($id,-1) == "a") {
		$id = substr($id,0,-1)."b";
	}elseif(substr($id,-1) == "b") {
		$id = substr($id,0,-1)."a";
	}
	
	// the other guy's info
	
	$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$id'");
	while($rowCon = $connectionResult->fetchArray()){
		$email = $rowCon['email'];
		$rowConDate = $rowCon['dateCreated'];
		
		
		// get user info from their id
		$getPersonEmail = $db->query("SELECT * FROM users WHERE email = '$email'");
		while($rowPersonEmail = $getPersonEmail->fetchArray()){
			$myName = $rowPersonEmail['name'];
			$myDate = $rowPersonEmail['todayDate'];
			$myTime = $rowPersonEmail['todayTime'];
			$personID = $rowPersonEmail['id'];
			
			// have you set off?
			if($myDate == $todayDate) { // yes
				echo "<p>".$myName." set off at ".$myTime.".</p><p><span id='time-left'>".$nowTime.$myTime.":00</span> minutes ago.</p>";
			}else{                      // no
				echo "<p>".$myName." has not set off yet.</p>";
			}

		}
		echo "<p>Connection created: ".$rowConDate."</p>";
	}
	
	
	echo "<hr>";	
/*	
	//echo $id."<br>";
	
	$personID = substr($id,-8,7);
	//echo $personID;
	
	// get creator's email
	$getPersonEmail = $db->query("SELECT * FROM users WHERE id = '$personID'");
	while($rowPersonEmail = $getPersonEmail->fetchArray()){
		//$email = $rowPersonEmail['email'];
		$myDate = $rowPersonEmail['todayDate'];
		$myTime = $rowPersonEmail['todayTime'];
		//echo $email."@";
	}	

	echo "<hr>";
	
	// show connection
	
	$person1 = substr($id,0,-1)."a";
	$person2 = substr($id,0,-1)."b";
	if($person1 == $id) {
		$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$person1'");
		while($rowCon = $connectionResult->fetchArray()){
			$otherUserEmail = $rowCon['email'];
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
		// have you set off?
		if($myDate == $todayDate) { // yes
			echo "<p>You set of at ".$myTime."</p>";
		}else{                      // no
			echo '<a href="postlandr.php?action=setoff&setOffTime='.$todayTime.'&setOffDate='.$todayDate.'&id='.$personID.'&connection='.$id.'" id="set-off">Set off</a>';
		}

	}
	
	
	echo "<hr>";

	
	// get creator's email
	$getSecondEmail = $db->query("SELECT * FROM users WHERE id = '$id'");
	while($rowPersonEmail = $getPersonEmail->fetchArray()){
		//$email = $rowPersonEmail['email'];
		$myDate = $rowPersonEmail['todayDate'];
		$myTime = $rowPersonEmail['todayTime'];
		//echo $email."@";
	}	


	if($person2 == $id) {
		$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$person2'");
		while($rowCon = $connectionResult->fetchArray()){
			$otherUserEmail = $rowCon['email'];
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
		// have you set off?
		if($myDate == $todayDate) { // yes
			echo "<p>You set of at ".$myTime."</p>";
		}else{                      // no
			echo '<a href="postlandr.php?action=setoff&setOffTime='.$todayTime.'&setOffDate='.$todayDate.'&id='.$personID.'&connection='.$id.'" id="set-off">Set off</a>';
		}

	}
	echo "<hr>";


/*	
	$connectionResult = $db->query("SELECT * FROM connection WHERE connectionNo = '$person1'");
	while($rowCon = $connectionResult->fetchArray()){
		$otherUserEmail = $rowCon['email'];
	
		//if($rowCon['email'] == $email) 
		//	continue;
	
	
		$connectionName = $db->query("SELECT * FROM users WHERE email = '$otherUserEmail'");
		while($rowConName = $connectionName->fetchArray()) {
			echo "<p class='name'>".$rowConName['name']."</p>";
			$conName = $rowConName['name'];
			$conTime = $rowConName['todayTime'];
			$conDate = $rowConName['todayDate'];
		}
	

	
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
	*/
	
}


?>