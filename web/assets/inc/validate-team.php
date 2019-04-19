<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
require_once "./SteamID.php";
include_once "./steam.php";
include_once "./inc.php";

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$request = $_POST['createname'];

		$result=mysqli_query($link, "SELECT * FROM ".$team_table." WHERE name ='".$request."'");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb))	$valid = false;
		else	$valid = true;
		
		echo json_encode($valid);
	}
}
mysqli_close($link);
?>