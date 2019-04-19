<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$date2 = $_POST['date2'];
		$token = $_POST['token'];
		$ftpa = $_POST['ftpa'];
		$ftpp = $_POST['ftpp'];

		
		$result = mysqli_query($link, "UPDATE ".$license_table." SET 
		time_exp = '".$date2."',
		ftp_a = '".$ftpa."', 
		ftp_p = '".$ftpp."'
		WHERE token = '".$token."'");
		
		echo  "UPDATE ".$license_table." SET 
		time_exp = '".$date2."',
		ftp_a = '".$ftpa."', 
		ftp_p = '".$ftpp."'
		WHERE token = '".$token."'";
	}
}
mysqli_close($link);
?>