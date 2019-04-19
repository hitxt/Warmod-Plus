<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');

if(isset($_SESSION['steamid']))
{
	$logo_result = false;
	$logo_game_result = false;
	//form
	if($_POST)
	{
		$fb = $_POST['facebook'];
		$twitter = $_POST['twitter'];
		$youtube = $_POST['youtube'];
		$twitch = $_POST['twitch'];
		$steam = $_POST['steam'];
		$name = $_POST['name'];
		$member = $_POST['member'];
		
		$member_count = count($member);
		
		if(empty($name))
		{
			$json = array(
				"responce" => "empty",
			);
				
			echo json_encode($json, true);
			mysqli_close ($link);
			exit;
		}
		
		$result=mysqli_query($link, "
		SELECT ".$team_table.".* FROM ".$team_table." WHERE ".$team_table.".name='".$name."'");
		mysqli_set_charset ($link , "utf-8");
		$numb=mysqli_num_rows($result); 
		if (empty($numb)) 
		{
			$fileName = "";
			$ext = "";
			$fileName2 = "";
			
			if(isset($_FILES["file"]["name"])) 
			{
				if ($_FILES['file']['error'] === UPLOAD_ERR_OK)
				{
					if($_FILES["file"]["size"] < "1048576")
					{
						$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
						$fileName = md5_file($_FILES['file']['tmp_name']);
						$image = new Imagick($_FILES['file']['tmp_name']);
						$image->resizeImage("512", "512", Imagick::FILTER_LANCZOS, 1, TRUE);
						$img_result = $image->writeImages($upload_dir . $fileName . "." . $ext , true);
						
						if($img_result)
						{
							$logo_result = true;
						}
					}
					else
					{
						$json = array(
							"responce" => "large",
						);
							
						echo json_encode($json, true);
						mysqli_close ($link);
						exit;
					}
				}
				else 
				{
					$json = array(
						"responce" => "upload-logo-error",
						"responce2" => $_FILES['file']['error']
					);
						
					echo json_encode($json, true);
					mysqli_close ($link);
					exit;
				}
			}	
			
			// gaem logo
			if(isset($_FILES["file2"]["name"])) 
			{
				if ($_FILES['file2']['error'] === UPLOAD_ERR_OK)
				{
					if($_FILES["file2"]["size"] <= "1048576")
					{
						/*
						list($width, $height) = getimagesize($_FILES['file2']['tmp_name']);
						
						if($width == "64" && $height == "64")
						{
						*/
							$ext = pathinfo($_FILES['file2']['name'], PATHINFO_EXTENSION);
							if ($ext != "svg") 
							{ 
								$json = array(
									"responce" => "svg-only",
								);
								echo json_encode($json, true);
								mysqli_close ($link);
								exit;
							}
							
							$result=mysqli_query($link, "SELECT logo_game FROM ".$game_logo_table." ORDER BY id DESC LIMIT 1");
							$numb=mysqli_num_rows($result); 
							if (!empty($numb)) 
							{ 
								while ($row = mysqli_fetch_array($result))
								{
									$logo_max = $row['logo_game'];
									if(is_null($logo_max) || empty($logo_max))	$logo_max = "0";
								}
							}
							else $logo_max = "0";
							$fileName2 = $logo_max+="1";
							
							//$image = new Imagick($_FILES['file2']['tmp_name']);
							//$image->resizeImage("64", "64", Imagick::FILTER_LANCZOS, 1, TRUE);
							//$img_result = $image->writeImages($upload_dir2 . $fileName2 . "." . $ext , true);
							copy($_FILES['file2']['tmp_name'], $upload_dir2 . $fileName2 . "." . $ext);
							copy($upload_dir2.$insert_id.".".$ext, $upload_dir3.$insert_id.".".$ext);
							
							if($img_result)
							{
								$logo_game_result = true;
							}
						/*
						}
						else
						{
							$json = array(
								"responce" => "size-large",
							);
								
							echo json_encode($json, true);
							mysqli_close ($link);
							exit;
						}
						*/
					}
					else
					{
						$json = array(
							"responce" => "file-large",
						);
							
						echo json_encode($json, true);
						mysqli_close ($link);
						exit;
					}
				}
				else 
				{
					$json = array(
						"responce" => "upload-logo-error",
						"responce2" => $_FILES['file2']['error']
					);
						
					echo json_encode($json, true);
					mysqli_close ($link);
					exit;
				}
			}
			
			// save team
			if($logo_result)
				$result=mysqli_query($link, "INSERT INTO ".$team_table." VALUES 
			(NULL, '".$name."', '".$fileName.".".$ext."', '".$steam."', '".$steamprofile['steamid']."', '".$fb."', '".$twitter."', '".$youtube."', '".$twitch."', '')");
			else
				$result=mysqli_query($link, "INSERT INTO ".$team_table." VALUES 
			(NULL, '".$name."', '', '".$steam."', '".$steamprofile['steamid']."', '".$fb."', '".$twitter."', '".$youtube."', '".$twitch."', '')");
			
			$team = mysqli_insert_id($link);
			
			// save game logo
			if($logo_game_result)	$result=mysqli_query($link, "INSERT INTO ".$game_logo_table." VALUES (NULL, '".$team."')");
			
			// add player into team
			$result=mysqli_query($link, "UPDATE ".$player_table." SET team = '".$team."' WHERE steam_id_64 = '".$_SESSION['steamid']."'");
			
			if(mysqli_affected_rows($link) < 1)
				$result=mysqli_query($link, "
			INSERT INTO ".$player_table."(`key_id`, `steam_id_64`, `last_ip`, `rws`, `team`, `fb`, `twitter`, `twitch`, `youtube`) VALUES 
			(NULL, '".$steamprofile['steamid']."', '0.0.0.0', '0.0000', '".$team."', '', '', '', '')");
			
			// build invite query
			$sql = "INSERT INTO ".$invite_table." (`key_id`, `send`, `receive`, `team`, `status`, `type`) VALUES";
			for($i=0;$i<$member_count;$i++) 
			{
				$sql .= " (NULL, '".$steamprofile['steamid']."', '".$member[$i]."', '".$team."', '', 'invite')";
				if($i != ($member_count-1))	$sql .= ",";
			}
			
			$result=mysqli_query($link, $sql);
			$json = array(
				"responce" => "success",
			);
				
			echo json_encode($json, true);
			mysqli_close ($link);
			exit;
		}
		else
		{
			$json = array(
				"responce" => "name-duplicate",
			);
				
			echo json_encode($json, true);
			mysqli_close ($link);
			exit;
		}
	}
}
mysqli_close($link);
?>