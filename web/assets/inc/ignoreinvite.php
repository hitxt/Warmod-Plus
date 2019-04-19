<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$team = json_decode($_POST['data1'], true);
		
		// send notify to leader
		$result=mysqli_query($link, "
		INSERT INTO ".$invite_table." (`key_id`, `send`, `team`, `status`, `type`, `receive`) 
		SELECT NULL, '".$steamprofile['steamid']."', '".$team."', '', 'ignoreinvite', leader FROM ".$team_table." WHERE id = '".$team."'");
		
		// clean notify
		$result=mysqli_query($link, "
		UPDATE ".$invite_table." SET `status`='ignored' WHERE `receive` = '".$steamprofile['steamid']."' AND type = 'invite' AND `team` = '".$team."'");
	}
}
mysqli_close($link);
?>