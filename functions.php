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


?>