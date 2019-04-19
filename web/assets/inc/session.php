<?php
	if(isset($_SESSION['steamid']))
	{
		require_once "steamauth/userInfo.php";
		
		$result=mysqli_query($link, "
		select ".$player_table.".*
			from ".$player_table." WHERE ".$player_table.".steam_id_64='".$steamprofile['steamid']."'");
		mysqli_set_charset ($link , "utf-8");
		$numb=mysqli_num_rows($result); 
		
		if (!empty($numb)) 
		{ 
			while ($row = mysqli_fetch_array($result))
			{
				$session_team_id = $row['team'];
				
				$fb = $row['fb'];
				$twitter = $row['twitter'];
				$youtube = $row['youtube'];
				$twitch = $row['twitch'];
			}
			
			$result=mysqli_query($link, "
			select ".$team_table.".*
				from ".$team_table." WHERE ".$team_table.".id='".$session_team_id."'");
			mysqli_set_charset ($link , "utf-8");
			$numb=mysqli_num_rows($result); 
			if (!empty($numb)) 
			{ 
				while ($row = mysqli_fetch_array($result))
				{
					$session_team_name = $row['name'];
					$session_team_leader = $row['leader'];
					$session_team_fb = $row['fb'];
					$session_team_twitter = $row['twitter'];
					$session_team_youtube = $row['youtube'];
					$session_team_twitch = $row['twitch'];
					$session_team_steam = $row['steam_url'];
					$session_team_logo = "./assets/img/teams/".$row['logo']."";

					if(empty($row['logo']) || !file_exists($session_team_logo))	$session_team_logo = "./assets/img/teams/unknown.png";
				}
			}
			else 
			{
				
			}
			
			$result=mysqli_query($link, "
			select * from ".$game_logo_table." WHERE ".$game_logo_table.".team='".$session_team_id."'");
			mysqli_set_charset ($link , "utf-8");
			$numb=mysqli_num_rows($result); 
			
			if (!empty($numb)) 
				$session_team_logo_game = "./assets/img/teams_game/".mysqli_fetch_array($result)['id'].".svg";						
			
			if(!isset($session_team_logo_game) || empty($session_team_logo_game) || !file_exists($session_team_logo))	
				$session_team_logo_game = "./assets/img/teams/unknown.png";		
		}
		else
		{
			$fb = "";
			$twitter = "";
			$youtube = "";
			$twitch = "";
		}
	}
	
	if(!isset($_SESSION['view_mode'])) $_SESSION['view_mode']= "module";
?>