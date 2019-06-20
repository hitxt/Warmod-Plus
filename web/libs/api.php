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
			
			/*******************************************************************************************************/

			case "team-create":
				$error = "";
				$webfile = "";
				try{
					if(!empty($_SESSION['steamid']))
					{
						$input = array(
							":id" => $_SESSION['steamid'],
						);
						$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
						$sth = $pdo->prepare($sql);
						$sth->execute($input);
						$result = $sth->fetchAll();
						if(!empty($result[0]["team"]))	throw new Exception("You can't create a team when you're in a team.");

						if($_FILES['weblogo']['error'] !== UPLOAD_ERR_NO_FILE)
						{
							if($_FILES['weblogo']['error'] === UPLOAD_ERR_OK)
							{
								if($_FILES["weblogo"]["size"] < "1048576")
								{
									$webfile = md5_file($_FILES['weblogo']['tmp_name']).".".pathinfo($_FILES['weblogo']['name'], PATHINFO_EXTENSION);
									$move = move_uploaded_file($_FILES['weblogo']['tmp_name'], "../assets/img/teams/".$webfile);
									if($move === false) throw new Exception('Error occurred when uploading the file.');
								}
								else throw new Exception('Your web logo is over 1MB.');
							}
							else throw new Exception('Error occurred when uploading the web logo.');
						}
						
						if($_FILES['teamlogo']['error'] !== UPLOAD_ERR_NO_FILE)
						{
							if($_FILES['teamlogo']['error'] === UPLOAD_ERR_OK)
							{
								if($_FILES["teamlogo"]["size"] < "10240")
								{
									// load svg
									$doc = new DOMDocument();
									$load = $doc->load($_FILES["teamlogo"]["tmp_name"]);
									if(!$load)	 throw new Exception('Error occurred when reading uploaded in-game logo.');

									// check it is svg format or not
									$svg = $doc->getElementsByTagName('svg');
									if($svg->length == 1)
									{
										// make sure it doesn't have image or style element
										$image = $doc->getElementsByTagName('image');
										$style = $doc->getElementsByTagName('style');
										$text = $doc->getElementsByTagName('text');
										if($style->length == 0 && $image->length == 0 && $text->length == 0){
											// resize and save it
											$svg[0]->setAttribute("width", "64px");
											$svg[0]->setAttribute("height", "64px");
										}
										else  throw new Exception('Your SVG file is not supported in CSGO.');
									}
									else	 throw new Exception('Your SVG file is not supported in CSGO.');
								}
								else throw new Exception('Your web logo is over 10KB.');
							}
							else throw new Exception('Error occurred when uploading in-game logo.');
						}

						$input = array(
							":name" => $_POST["name"],
							":logo" => $webfile,
							":leader" => $_SESSION['steamid'],
							":steamurl" => $_POST["steamurl"],
							":facebook" => $_POST["facebook"],
							":twitter" => $_POST["twitter"],
							":twitch" => $_POST["twitch"],
							":youtube" => $_POST["youtube"],
						);

						$sql = "INSERT INTO ".$team_table." VALUES (null, :name, :logo, :leader, :steamurl, :facebook, :twitter, :youtube, :twitch, '')";
						$sth = $pdo->prepare($sql);
						$stmt = $sth->execute($input);

						$teamid = $pdo->lastInsertId(); 
						$input = array(
							":id" => $teamid,
						);

						if(!empty($doc)){
							$sql = "INSERT INTO ".$game_logo_table." VALUES(null, :id)";
							$sth = $pdo->prepare($sql);
							$stmt = $sth->execute($input);
							$id = $pdo->lastInsertId(); 
							$save = $doc->save("../assets/img/teams_game/wm".$id.".svg");
							if(!$symlink)	$save = $doc->save($fastdl_path."materials/panorama/images/tournaments/teams/wm".$id.".svg");
						}

						$input = array(
							":id" => $_SESSION['steamid'],
						);
						$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
						$sth = $pdo->prepare($sql);
						$sth->execute($input);
						$result = $sth->fetchAll();
						if(!empty($result)){
							$input = array(
								":team" => $teamid,
								":id" => $_SESSION['steamid'],
							);
							$sql = "UPDATE ".$player_table." SET team = :team WHERE steam_id_64 = :id";
							$sth = $pdo->prepare($sql);
							$sth->execute($input);
						}
						else{
							$input = array(
								":id" => $_SESSION['steamid'],
								":team" => $teamid,
							);
							$sql = "INSERT INTO ".$player_table." VALUES(null, :id, '0.0.0.0', '0', :team, '', '', '', '')";
							$sth = $pdo->prepare($sql);
							$sth->execute($input);
						}

						if(!empty($_POST["steam"])){
							foreach($_POST["steam"] as $steam){
								if(!empty($steam)){
									$input = array(
										":send" => $_SESSION['steamid'],
										":receive" => $steam,
										":team" => $teamid,
										":type" => "invite"
									);
									$sql = "INSERT INTO ".$notify_table." VALUES(null, :send, :receive, :team, '', :type)";
									$sth = $pdo->prepare($sql);
									$stmt = $sth->execute($input);
								}
							}
						}
					}
					else throw new Exception('You are not logged in.');
				}
				catch (Exception $e){
					$error = $e->getMessage();
				}
				
				$json = array(
					"error" => $error,
				);
				echo json_encode($json, true);
				break;
			
			/*******************************************************************************************************/

			case "team-save":
				$success = true;

				$input = array(
					":steamid" => $_SESSION['steamid']
				);

				$sql = "SELECT * FROM ".$team_table." WHERE leader = :steamid";
				$sth = $pdo->prepare($sql);
				$stmt = $sth->execute($input);
				$result = $sth->fetchAll();
				if(count($result) > 0){
					$input = array(
						":facebook" => $_POST["facebook"],
						":twitter" => $_POST["twitter"],
						":twitch" => $_POST["twitch"],
						":youtube" => $_POST["youtube"],
						":id" => $result[0]["id"]
					);
					$sql = "UPDATE ".$team_table." SET fb = :facebook, twitter = :twitter, twitch = :twitch, youtube = :youtube WHERE id = :id";
					$sth = $pdo->prepare($sql);
					$stmt = $sth->execute($input);
					if(!$stmt)	$success = false;
				}
				else	$success = false;

				$json = array(
					"responce" => $success,
				);
					
				echo json_encode($json, true);
				break;

			/*******************************************************************************************************/

			case "team-logo":
				$error = "";
				$webfile = "";
				try{
					if(!empty($_SESSION['steamid']))
					{
						$input = array(
							":id" => $_SESSION['steamid'],
						);
						$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
						$sth = $pdo->prepare($sql);
						$sth->execute($input);
						$result = $sth->fetchAll();
						$teamid = $result[0]["team"];
						if(empty($teamid))	throw new Exception("You are not in any team.");

						$input = array(
							":id" => $teamid,
						);
						$sql = "SELECT * FROM ".$team_table." WHERE id = :id";
						$sth = $pdo->prepare($sql);
						$sth->execute($input);
						$result = $sth->fetchAll();
						if($result[0]["leader"] != $_SESSION['steamid'])	throw new Exception("You are not the leader.");

						if($_FILES['weblogo']['error'] !== UPLOAD_ERR_NO_FILE)
						{
							if($_FILES['weblogo']['error'] === UPLOAD_ERR_OK)
							{
								if($_FILES["weblogo"]["size"] < "1048576")
								{
									$webfile = md5_file($_FILES['weblogo']['tmp_name']).".".pathinfo($_FILES['weblogo']['name'], PATHINFO_EXTENSION);
									$move = move_uploaded_file($_FILES['weblogo']['tmp_name'], "../assets/img/teams/".$webfile);
									if($move === false) throw new Exception('Error occurred when uploading the file.');
								}
								else throw new Exception('Your web logo is over 1MB.');
							}
							else throw new Exception('Error occurred when uploading the web logo.');
						}
						
						if($_FILES['teamlogo']['error'] !== UPLOAD_ERR_NO_FILE)
						{
							if($_FILES['teamlogo']['error'] === UPLOAD_ERR_OK)
							{
								if($_FILES["teamlogo"]["size"] < "10240")
								{
									// load svg
									$doc = new DOMDocument();
									$load = $doc->load($_FILES["teamlogo"]["tmp_name"]);
									if(!$load)	 throw new Exception('Error occurred when reading uploaded in-game logo.');

									// check it is svg format or not
									$svg = $doc->getElementsByTagName('svg');
									if($svg->length == 1)
									{
										// make sure it doesn't have image or style element
										$image = $doc->getElementsByTagName('image');
										$style = $doc->getElementsByTagName('style');
										$text = $doc->getElementsByTagName('text');
										if($style->length == 0 && $image->length == 0 && $text->length == 0){
											// resize and save it
											$svg[0]->setAttribute("width", "64px");
											$svg[0]->setAttribute("height", "64px");
										}
										else  throw new Exception('Your SVG file is not supported in CSGO.');
									}
									else	 throw new Exception('Your SVG file is not supported in CSGO.');
								}
								else throw new Exception('Your web logo is over 10KB.');
							}
							else throw new Exception('Error occurred when uploading in-game logo.');
						}

						if(!empty($webfile)){
							$input = array(
								":logo" => $webfile,
								":id" => $teamid
							);
	
							$sql = "UPDATE ".$team_table." SET logo = :logo WHERE id = :id";
							$sth = $pdo->prepare($sql);
							$stmt = $sth->execute($input);
						}
						
						if(!empty($doc)){
							$input = array(
								":id" => $teamid
							);
							$sql = "INSERT INTO ".$game_logo_table." VALUES(null, :id)";
							$sth = $pdo->prepare($sql);
							$stmt = $sth->execute($input);
							$id = $pdo->lastInsertId(); 
							$save = $doc->save("../assets/img/teams_game/wm".$id.".svg");
							if(!$symlink)	$save = $doc->save($fastdl_path."materials/panorama/images/tournaments/teams/wm".$id.".svg");
						}
					}
				}
				catch (Exception $e){
					$error = $e->getMessage();
				}
				$json = array(
					"error" => $error,
				);
				echo json_encode($json, true);
				break;
		}
	}

	$pdo = null;
?>