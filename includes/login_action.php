<?php
		require "dbConnect.php";
		require "classes.php";
		$session = new Session($conn);
		$success = $session->login($_POST['email'],$_POST['password']);
		if($success===true){
			header("Location: ../index.php");
		} else {
			header("Location: ../login.php?error&message=".urlencode($success));
		}