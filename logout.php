<?php
	//Start session
	session_start();
	//Unset the variables stored in session
	unset($_SESSION["current_rally_id"]);
	unset($_SESSION["current_admin_id"]);
	header("Location:index.php");
?>