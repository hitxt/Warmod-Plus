<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$id = json_decode($_POST['value1'], true);
		
		$result=mysqli_query($link, "
		UPDATE ".$invite_table." SET `status`='ignored' WHERE key_id = '".$id."'");
	}
}
mysqli_close($link);
?>