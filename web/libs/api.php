<?php
	require_once("../configs/configs.php");
	require_once("./sql.php");
	require_once("./steamauth/steamauth.php");
	require_once("./steamauth/userInfo.php");
	require_once("./steam/SteamID.php");
	require_once("./class/player.php");

	if(!empty($_GET["action"]))
	{
		switch($_GET["action"])
		{
			case "search":
				$input = array(
					":id" => $_POST["id"],
				);
				$sql = "SELECT * from ".$result_table." WHERE id = :id";
				$sth = $pdo->prepare($sql);
				$sth->execute($input);
				$result = $sth->fetchAll();
				if(count($result) < 1){
					try
					{
						$steam_id = new SteamID( $_POST["id"] );
						if( !$steam_id->IsValid() )
						{
							throw new InvalidArgumentException( "exit" );
						}
						else {throw new InvalidArgumentException("redirect");}
					}
					catch( InvalidArgumentException $e )
					{
						if( $e->getMessage() == "redirect" )
						{
							header("Location: ../showplayer.php?id=".$_POST["id"]."");
						}
						else
						{
							echo "<script type='text/javascript'>
							alert('Invalid Match ID or Steam64 ID');
							window.history.back();
							</script>";
						}	
					}
				}
				else{
					header("Location: ../showmatch.php?id=".$_POST["id"].""); 
				}
				break;

			/*******************************************************************************************************/
			
			case "profile-save":
				$input = array(
					":steamid" => $_SESSION['steamid']
				);

				$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :steamid";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				$result = $sth->fetchAll();

				$input = array(
					":facebook" => $_POST["facebook"],
					":twitter" => $_POST["twitter"],
					":twitch" => $_POST["twitch"],
					":youtube" => $_POST["youtube"],
					":steamid" => $_SESSION['steamid']
				);

				if(count($result) > 0){
					$sql = "UPDATE ".$player_table." SET fb = :facebook, twitter = :twitter, twitch = :twitch, youtube = :youtube WHERE steam_id_64 = :steamid";
					$sth = $pdo->prepare($sql);
					$stmt = $sth->execute($input);
				}
				else{
					$sql = "INSERT INTO ".$player_table." VALUES (null, :steamid, '0.0.0.0', 0, 0, :facebook, :twitter, :twitch, :youtube)";
					echo $sql;
					$sth = $pdo->prepare($sql);
					$stmt = $sth->execute($input);
				}

				$success = true;
				if(!$stmt){
					$success = false;
				}

				$json = array(
					"responce" => $success,
				);
					
				echo json_encode($json, true);
				break;
		}
	}

	$pdo = null;
?>