<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$data = $_POST['data2'];
		$steam = substr($data, 29, -10);
		
		$team = $_POST['data3'];
		
		$result=mysqli_query($link, "UPDATE ".$player_table." SET `team`='' WHERE steam_id_64 = '".$steam."'");
		
		// send notify
		$result=mysqli_query($link, "
		INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) 
		VALUES (NULL, '".$steamprofile['steamid']."', '".$steam."', '".$team."', '', 'kickteam')");
	}
}
mysqli_close($link);
?>