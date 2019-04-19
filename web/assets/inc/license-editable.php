<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		print_r($_POST);
	}
}
mysqli_close($link);
?>