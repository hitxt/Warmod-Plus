<?php
	try{
		$pdo = new PDO("mysql:host=$db_host;dbname=$db_name",$db_user,$db_password);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	catch(PDOException $e){
		die( $e->getMessage() ); 
	}

	date_default_timezone_get($timezone);
	// session_start();
?>