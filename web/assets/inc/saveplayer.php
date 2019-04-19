<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$fb = $_POST['facebook'];
		$twitter = $_POST['twitter'];
		$youtube = $_POST['youtube'];
		$twitch = $_POST['twitch'];
		
		$result=mysqli_query($link, "
		UPDATE ".$player_table." SET `fb`='".$fb."',`twitter`='".$twitter."',`twitch`='".$twitch."',`youtube`='".$youtube."' WHERE steam_id_64 = '".$steamprofile['steamid']."'");
		if(mysqli_affected_rows($link) < 1)
		{
			$result=mysqli_query($link, "
			INSERT INTO ".$player_table."(`key_id`, `steam_id_64`, `last_ip`, `rws`, `team`, `fb`, `twitter`, `twitch`, `youtube`) VALUES 
			(NULL, '".$steamprofile['steamid']."', '0.0.0.0', '0.0000', '', '".$fb."', '".$twitter."', '".$twitch."', '".$youtube."')");
		}
	}
}
mysqli_close($link);
?>