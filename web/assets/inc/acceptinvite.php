<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$team = $_POST['data1'];
		$id = $_POST['data2'];
		
		// set notify status
		$result=mysqli_query($link, "
		UPDATE ".$invite_table." SET `status`='accepted' WHERE key_id = '".$id."'");

		// send join notify to leader
		$result=mysqli_query($link, "
		INSERT INTO ".$invite_table." (`key_id`, `send`, `team`, `status`, `type`, `receive`) 
		SELECT NULL, '".$steamprofile['steamid']."', '".$team."', '', 'joinedteam', leader FROM ".$team_table." WHERE id = '".$team."'");
		
		// get old info, if have
		$result=mysqli_query($link, "
		SELECT ".$player_table.".team, ".$team_table.".leader 
		FROM ".$player_table.", ".$team_table." WHERE ".$team_table.".id = ".$player_table.".team AND ".$player_table.".steam_id_64 = '".$steamprofile['steamid']."'");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{ 
			while ($row = mysqli_fetch_array($result))
			{
				$old_team = $row['team'];
				$old_leader = $row['leader'];
			}
			
			// member leave
			if($old_leader != $steamprofile['steamid'])
			{
				// send leave notify to old team leader
				$result=mysqli_query($link, "
				INSERT INTO ".$invite_table." (`key_id`, `send`, `team`, `status`, `type`, `receive`) 
				SELECT NULL, '".$steamprofile['steamid']."', '".$old_team."', '', 'leave', leader FROM ".$team_table." WHERE id = '".$old_team."'");
			}
			// leader leave
			else
			{
				// select new leader
				$result=mysqli_query($link, "SELECT steam_id_64 FROM ".$player_table." WHERE team = '".$team."' AND steam_id_64 <> ".$steamprofile['steamid']." ORDER BY RAND() LIMIT 1");
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
					fb = '', twitter = '',  youtube = '', twitch = '', steam_url = '', logo = '', logo_game = ''
					WHERE `id` = '".$team."'");
					
					// clean invite notify
					$result=mysqli_query($link, "
					UPDATE ".$invite_table." SET `status`='delete' WHERE `team` = '".$team."' AND type = 'invite'");
				}
			}
		}
		
		// update player info
		$result="
		UPDATE ".$player_table." SET `team` = '".$team."' WHERE steam_id_64 = '".$steamprofile['steamid']."'";
		mysqli_set_charset ($link , "utf-8");
		// https://www.w3schools.com/php/php_mysql_update.asp
		// https://stackoverflow.com/questions/16250596/successful-fail-message-pop-up-box-after-submit
		if(!mysqli_query($link, $result))
		{
			$result="
			INSERT INTO ".$player_table."(`key_id`, `steam_id_64`, `last_ip`, `rws`, `team`, `fb`, `twitter`, `twitch`, `youtube`) VALUES 
			(NULL, '".$steamprofile['steamid']."', '0.0.0.0', '0.0000', '".$team."', '', '', '', '')";
			mysqli_set_charset ($link , "utf-8");
		}
	}
}
mysqli_close($link);
?>