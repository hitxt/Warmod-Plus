<?php
require_once("../../config.php");require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$data = $_POST['data2'];
		$steam = substr($data, 29, -10);
		//echo $steam;
		
		$result=mysqli_query($link, "UPDATE ".$invite_table." SET `status`='canceled' WHERE receive = '".$steam."'");
	}
}
mysqli_close($link);
?>