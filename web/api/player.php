<?php
	require_once("../config.php");
	
	function checkVariable(&$var, $attrib) 
	{
		if(isset($_POST[$attrib])) 
		{
			$var = $_POST[$attrib];
		} 
		else if(isset($_GET[$attrib])) 
		{
			$var = $_GET[$attrib];
		} 
		else 
		{
			return false;
		}
		return true;
	}
	
	if(checkVariable($token, 't') && checkVariable($steam, 'i') && checkVariable($ip, 'ip'))
	{
		$rws = "0.00";
		$round = "0";
		$team_name = "";
		$team_logo = "";
		$leader = "0";
		$team_id = "";
		
		// check license
		$result=mysqli_query($link, "SELECT * FROM ".$license_table." WHERE token = '".$token."'");
		mysqli_set_charset ($link , "utf-8");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{
			// check player in db or not
			$result=mysqli_query($link, "SELECT * FROM ".$player_table." WHERE steam_id_64 = '".$steam."'");
			mysqli_set_charset ($link , "utf-8");
			$numb=mysqli_num_rows($result); 
			// in
			if (!empty($numb)) 
			{
				while ($row = mysqli_fetch_array($result))
				{
					$rws = $row['rws'];
					$team_id = $row['team'];
					$dbip = $row['last_ip'];
				}
				
				// check ip, if diff, update
				if($dbip != $ip)
					$result=mysqli_query($link, "UPDATE ".$player_table." SET `last_ip` = '".$ip."' WHERE `steam_id_64` = '".$steam."'");
				
				$result=mysqli_query($link, "SELECT * FROM ".$team_table." WHERE id = '".$team_id."'");
				mysqli_set_charset ($link , "utf-8");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{
					while ($row = mysqli_fetch_array($result))
					{
						$team_name = $row['name'];
						$team_leader = $row['leader'];
					}

					if($team_leader == $steam)	$leader = "1";
				}
				
				$result=mysqli_query($link, "SELECT * FROM ".$game_logo_table." WHERE team = '".$team_id."'");
				mysqli_set_charset ($link , "utf-8");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{
					while ($row = mysqli_fetch_array($result))
					{	
						$team_logo = $row['id'];
					}
				}
				
				$result=mysqli_query($link, "SELECT SUM(rounds_played) AS 'rounds' FROM ".$stats_table." WHERE steam_id_64 = '".$team_id."'");
				mysqli_set_charset ($link , "utf-8");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{
					while ($row = mysqli_fetch_array($result))
					{
						$round = $row['rounds'];
					}
				}	
			}
			else
			{
				$result=mysqli_query($link, "INSERT INTO ".$player_table." (`key_id`, `steam_id_64`, `last_ip`, `rws`) VALUES (NULL,'".$steam."','".$ip."', '0.00')");
				mysqli_set_charset ($link , "utf-8");
			}
			
			
			echo '"player" { "player" {"rws" "'.$rws.'" "team_name" "'.$team_name.'" "team_logo" "'.$team_logo.'" "round" "'.$round.'" "team_leader" "'.$leader.'" "team_id" "'.$team_id.'"} }';
		}
	}
	mysqli_close($link);
?>