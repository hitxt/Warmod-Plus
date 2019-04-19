<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{	
	// leader	
	if($_POST)	
	{		
		$type = $_POST['data1'];
		
		if($type == "leader")
		{
			$team = $_POST['data2'];
			
			// leave team
			$result = mysqli_query($link, "UPDATE ".$player_table." SET `team`='' WHERE steam_id_64 = '".$steamprofile['steamid']."'");
			// select new leader
			$result=mysqli_query($link, "SELECT steam_id_64 FROM ".$player_table." WHERE team = '".$team."' ORDER BY RAND() LIMIT 1");
			$numb=mysqli_num_rows($result); 
			if (!empty($numb))
			{
				while ($row = mysqli_fetch_array($result))
				{
					$new_leader = $row['steam_id_64'];
				}
			}
			// have new leader
			if(isset($new_leader))
			{
				$result=mysqli_query($link, "UPDATE ".$team_table." SET `leader` = '".$new_leader."' WHERE id = '".$team."'");
				
				// send notify to new leader
				$result=mysqli_query($link, "
				INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) 	
				VALUES (NULL, '".$steamprofile['steamid']."', '".$new_leader."', '".$team."', '', 'newleader')");
			}
			// delete team
			else
			{	
				$result=mysqli_query($link, "
				UPDATE ".$team_table." SET `status`='delete', `leader` = '',
				fb = '', twitter = '',  youtube = '', twitch = '', steam_url = ''
				WHERE `id` = '".$team."'");	
				// clean invite notify
				$result=mysqli_query($link, "
				UPDATE ".$invite_table." SET `status`='delete' WHERE `team` = '".$team."' AND type = 'invite'");
			}
		}
		elseif($type == "member")
		{
			$team = $_POST['data2'];
			$result=mysqli_query($link, "SELECT leader FROM ".$team_table." WHERE id = '".$team."'");
			$numb=mysqli_num_rows($result); 
			if (!empty($numb)) 
			{ 
				while ($row = mysqli_fetch_array($result))
				{
					$leader = $row['leader'];
				}
				// send leave notify to leader
				$result=mysqli_query($link, "
				INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) 
				VALUES (NULL, '".$steamprofile['steamid']."', '".$leader ."', '".$team."', '', 'leave')");
			}
			$result=mysqli_query($link, "UPDATE ".$player_table." SET `team`='' WHERE steam_id_64 = '".$steamprofile['steamid']."'");
		}
	}
}
mysqli_close($link);
?>