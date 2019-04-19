<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$ip = $_POST['data2'];
		$port = $_POST['data3'];
		
		$result=mysqli_query($link, "DELETE FROM ".$server_table." WHERE ip = '".$ip."' AND port = '".$port."'");
	}
}
mysqli_close($link);
?>