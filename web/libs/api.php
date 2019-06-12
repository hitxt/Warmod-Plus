<?php
	require_once("../configs/configs.php");
	require_once("./sql.php");
	require_once("./steamauth/steamauth.php");
	require_once("./steamauth/userInfo.php");
	require_once("./steam/SteamID.php");
	require_once("./class/player.php");
	require_once("./steam/SourceQuery.php");

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

			/*******************************************************************************************************/

			case "valid-steam":
				$valid = false;
				try
				{
					$steam_id = new SteamID( $_POST["steam"] );
					if( !$steam_id->IsValid() )
					{
						throw new InvalidArgumentException( "invalid" );
					}
					else {throw new InvalidArgumentException("exist");}
				}
				catch( InvalidArgumentException $e )
				{
					if( $e->getMessage() == "exist" )	$valid = true;
				}

				echo json_encode($valid);
				break;

			/*******************************************************************************************************/
				
			case "token-del":
				$success = false;
				// check is admin or not because to prevent ajax hack.
				if(in_array($_SESSION['steamid'], $admins)){
					$input = array(
						":id" => $_POST["id"]
					);
					
					$sql = "DELETE FROM ".$license_table." WHERE id = :id";
					$sth = $pdo->prepare($sql);
					$stmt = $sth->execute($input);
					if($stmt)	$success = true;
				}

				$json = array(
					"success" => $success
				);
				echo json_encode($json, true);
				break;

			/*******************************************************************************************************/

			case "token-edit":
				$success = false;
				$input = array(
					":id" => $_POST["id"],
					":steam" => $_POST["steam"],
					":token" => $_POST["token"],
					":ftpu" => $_POST["ftpu"],
					":ftpp" => $_POST["ftpp"],
					":exp" => $_POST["exp"]
				);
				
				$sql = "UPDATE ".$license_table." SET steam_id_64 = :steam, token = :token, time_exp = :exp, ftp_a = :ftpu, ftp_p = :ftpp WHERE id = :id";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				if($stmt)	$success = true;
				$steamids[] = $_POST["steam"];
				$steam = SteamData::GetData($SteamAPI_Key, $steamids);
				$json = array(
					"success" => $success,
					"name" => $steam["name"][$_POST["steam"]],
					"id" => $_POST["id"]
				);
				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/

			case "token-new":
				$success = false;
				$input = array(
					":steam" => $_POST["steam"],
					":token" => $_POST["token"],
					":ftpu" => $_POST["ftpu"],
					":ftpp" => $_POST["ftpp"],
					":exp" => $_POST["exp"]
				);
				
				$sql = "INSERT INTO ".$license_table." VALUES(null, :steam, :token, :exp, :ftpu, :ftpp)";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				if($stmt)	$success = true;
				$steamids[] = $_POST["steam"];
				$steam = SteamData::GetData($SteamAPI_Key, $steamids);
				$id = $pdo->lastInsertId();
				$json = array(
					"success" => $success,
					"name" => $steam["name"][$_POST["steam"]],
					"id" => $id
				);
				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/

			case "server-del":
				$success = false;
				// check is admin or not because to prevent ajax hack.
				if(in_array($_SESSION['steamid'], $admins)){
					$input = array(
						":id" => $_POST["id"]
					);
					
					$sql = "DELETE FROM ".$server_table." WHERE id = :id";
					$sth = $pdo->prepare($sql);
					$stmt = $sth->execute($input);
					if($stmt)	$success = true;
				}

				$json = array(
					"success" => $success
				);

				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/

			case "server-edit":
				if(empty($_POST["enable"]))	$_POST["enable"] = 0;
				$success = false;
				$input = array(
					":id" => $_POST["id"],
					":ip" => $_POST["ip"],
					":port" => $_POST["port"],
					":enable" => $_POST["enable"],
				);
				
				$sql = "UPDATE ".$server_table." SET ip = :ip, port = :port, enabled = :enable WHERE id = :id";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				if($stmt)	$success = true;

				try
				{
					$server = new SourceQuery($_POST['ip'],$_POST['port']);
					$infos  = $server->getInfos();
					if(is_null($infos["mod"]))	throw new InvalidArgumentException( "invalid" );
					else	throw new InvalidArgumentException( "valid" );
				}
				catch( InvalidArgumentException $e )
				{
					if( $e->getMessage() == "invalid" )
					{
						$infos['name'] = "Cannot Connect To Server.";
						$infos["map"] = "-";
						$infos["players"] = "-";
						$infos["places"] = "-";
					}	
				}

				$json = array(
					"success" => $success,
					"name" => $infos['name'],
					"map" => $infos["map"],
					"players" => $infos["players"],
					"places" => $infos["places"]
				);
				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/

			case "server-new":
				if(empty($_POST["enable"]))	$_POST["enable"] = 0;
				$success = false;
				$input = array(
					":ip" => $_POST["ip"],
					":port" => $_POST["port"],
					":enable" => $_POST["enable"],
				);
				
				$sql = "INSERT INTO ".$server_table." VALUES (null, :ip, :port, :enable)";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				if($stmt)	$success = true;

				try
				{
					$server = new SourceQuery($_POST['ip'],$_POST['port']);
					$infos  = $server->getInfos();
					if(is_null($infos["mod"]))	throw new InvalidArgumentException( "invalid" );
					else	throw new InvalidArgumentException( "valid" );
				}
				catch( InvalidArgumentException $e )
				{
					if( $e->getMessage() == "invalid" )
					{
						$infos['name'] = "Cannot Connect To Server.";
						$infos["map"] = "-";
						$infos["players"] = "-";
						$infos["places"] = "-";
					}	
				}

				$json = array(
					"success" => $success,
					"name" => $infos['name'],
					"map" => $infos["map"],
					"players" => $infos["players"],
					"places" => $infos["places"],
					"id" => $pdo->lastInsertId()
				);
				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/
			
			case "server-enable":
				$success = false;
				$enable = !$_POST["enable"];
				$input = array(
					":id" => $_POST["id"],
					":enable" => $enable,
				);
				
				$sql = "UPDATE ".$server_table." SET enabled = :enable WHERE id = :id";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				if($stmt)	$success = true;

				$json = array(
					"success" => $success,
				);
				echo json_encode($json, true);
				break;
		}
	}

	$pdo = null;
?>