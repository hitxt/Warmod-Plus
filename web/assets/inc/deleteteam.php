<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$team = $_POST['data1'];
		
		// send notify
		$result=mysqli_query($link, "
		INSERT INTO ".$invite_table." (`key_id`, `send`, `team`, `status`, `type`, `receive`) 
		SELECT NULL, '".$steamprofile['steamid']."', '".$team."', '', 'deleteteam', steam_id_64 FROM ".$player_table." WHERE team = ".$team." AND steam_id_64 <> '".$steamprofile['steamid']."'");

		// delete team in player table
		$result=mysqli_query($link, "UPDATE ".$player_table." SET `team`='' WHERE `team` = '".$team."'");
		
		// delete team in team table
		$result=mysqli_query($link, "
		UPDATE ".$player_table." SET `status`='delete', 
		SET ".$team_table.".fb = '', ".$team_table.".twitter = '',  ".$team_table.".youtube = '', ".$team_table.".twitch = '', ".$team_table.".steam_url = '',
		".$team_table.".logo = '', ".$team_table.".logo_game = ''
		WHERE `id` = '".$team."'");
		
		// clean invite notify
		$result=mysqli_query($link, "
		UPDATE ".$invite_table." SET `status`='delete' WHERE `team` = '".$team."' AND type = 'invite'");
	}
}
mysqli_close($link);
?>