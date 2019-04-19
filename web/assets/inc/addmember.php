<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
require_once "./SteamID.php";
include_once "./steam.php";
include_once "./inc.php";

if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$team = $_POST['data1'];
		$steam = $_POST['val'];
		// echo "steam:".$steam.", data:".$data."";
		
		// check steamid is valid or not
		try
		{
			$steam_id = new SteamID( $steam );
			if( !$steam_id->IsValid() )
			{
				throw new InvalidArgumentException( "invalid" );
			}
			else {throw new InvalidArgumentException("exist");}
		}
		catch( InvalidArgumentException $e )
		{
			if( $e->getMessage() == "invalid" )
			{
				$json = array(
				"responce" => "invalid"
				);
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}	
		}
		
		$name = getName($steam);
		
		// invite self
		if($steam == $steamprofile['steamid'])
		{
			$json = array(
			"responce" => "self"
			);
			
			echo json_encode($json, true);
			mysqli_close($link);
			exit;
		}
		
		// player already have team
		$result=mysqli_query($link, "SELECT team FROM ".$player_table." WHERE steam_id_64 = '".$steam."'");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{ 
			while ($row = mysqli_fetch_array($result))
			{
				$result_team = $row['team'];
			}
			
			if($result_team == $team)
			{
				$json = array(
				"responce" => "already-in"
				);
			
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}
			/*elseif($result_team != $team && !empty($result_team))
			{
				$json = array(
				"responce" => "already-have"
				);
			
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}*/
			elseif(empty($result_team))
			{
				$result=mysqli_query($link, "SELECT * FROM ".$invite_table." WHERE receive = '".$steam."' AND status = '' AND team = '".$team."'");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{ 
					$json = array(
					"responce" => "already-invite"
					);
				
					echo json_encode($json, true);
					mysqli_close($link);
					exit;
				}
				
				// send invite notify
				$result=mysqli_query($link, "
				INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) 
				VALUES (NULL, '".$steamprofile['steamid']."', '".$steam."', '".$team."', '', 'invite')");
				
				// get stats for datatables new row
				$result=mysqli_query($link, "
				SELECT
				   rank,
				   steam_id_64,
				   rws,
				   kills,
				   deaths,
				   hits,
				   shots 
				FROM
				   (
					  SELECT
						 a.rank AS 'Rank',
						 a.steam_id_64,
						 a.rws,
						 a.kills,
						 a.deaths,
						 a.hits,
						 a.shots 
					  FROM
						 (
							SELECT
							   stats.steam_id_64,
							   stats.rws,
							   stats.kills,
							   stats.deaths,
							   stats.hits,
							   stats.shots,
							   @prev := @curr,
							   @curr := stats.rws,
							   @rank := IF(@prev = @curr, @rank, @rank + 1) AS rank 
							FROM
							   (
								  SELECT
									 @curr := NULL,
									 @prev := NULL,
									 @rank := 0 
							   )
							   s,
							   (
								  SELECT
									 ".$player_table.".steam_id_64,
									 ".$player_table.".rws,
									 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".kills, 0 ) ) AS 'kills',
									 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".deaths, 0 ) ) AS 'deaths',
									 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".hits, 0 ) ) AS 'hits',
									 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".shots, 0 ) ) AS 'shots' 
								  FROM
									 `".$stats_table."`,
									 `".$player_table."` 
								  WHERE
									 ".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR' 
								  GROUP BY
									 ".$player_table.".steam_id_64 
								  ORDER BY
									 ".$player_table.".rws DESC 
							   )
							   stats 
							ORDER BY
							   rws DESC 
						 )
						 a 
				   )
				   b
				WHERE
				   steam_id_64 = '".$steam."'
				ORDER BY
				   rws DESC");
				
				mysqli_set_charset ($link , "utf-8");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{
					while ($row = mysqli_fetch_array($result))
					{
						$rws = $row['rws'];
						if($rws != 0.00 && $rws != NULL)	$rws = floor_dec($rws,2);
						else $rws = 0.00;
							
						$kills = $row['kills'];
						$deaths = $row['deaths'];
						$hits = $row['hits'];
						$shots = $row['shots'];
									
						if($shots == 0) $ac = 0;
						else	$ac = round($hits/$shots, 2);
							
						if($deaths == 0)	$kdr = round($kills/1, 2);
						else	$kdr = round($kills/$deaths, 2);

						if($rws > 0)	$rank = $row['rank'];
						else $rank = "Unranked";
					}
					
					$json = array(
					"responce" => "added",
					"name" => $name,
					"rank" => $rank,
					"rws" => $rws,
					"k" => $kills,
					"d" => $deaths,
					"kdr" => $kdr,
					"ac" => $ac,
					"profile" => $steam,
					);
					
					echo json_encode($json, true);
					mysqli_close($link);
					exit;
				}
			}
		}
		// player not in db
		else
		{
			$result=mysqli_query($link, "SELECT * FROM ".$invite_table." WHERE receive = '".$steam."' AND status = '' AND team = '".$team."'");
			$numb=mysqli_num_rows($result); 
			if (!empty($numb)) 
			{ 
				$json = array(
				"responce" => "already-invite"
				);
			
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}
			
			// send invite notify
			$result=mysqli_query($link, "
			INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) 
			VALUES (NULL, '".$steamprofile['steamid']."', '".$steam."', '".$team."', '', 'invite')");
			
			// get stats for datatables new row
			$json = array(
			"responce" => "added",
			"name" => $name,
			"rank" => "Unranked",
			"rws" => "0",
			"k" => "0",
			"d" => "0",
			"kdr" => "0",
			"ac" => "0",
			"profile" => $steam
			);
			
			echo json_encode($json, true);
			mysqli_close($link);
			exit;
		}
	}
}
mysqli_close($link);
?>