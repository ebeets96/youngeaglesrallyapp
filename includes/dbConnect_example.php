<?php
	$servername = "localhost";
	$db_name = "";
	$username = "";
	$password = "";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password,$db_name);
	
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
