<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$token = $_POST['data2'];
		
		$result=mysqli_query($link, "DELETE FROM ".$license_table." WHERE token = '".$token."'");
	}
}
mysqli_close($link);
?>