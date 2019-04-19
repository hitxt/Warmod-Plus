<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	//form
	if($_POST)
	{
		$fb = $_POST['facebook'];
		$twitter = $_POST['twitter'];
		$youtube = $_POST['youtube'];
		$twitch = $_POST['twitch'];
		$steam = $_POST['steam'];
		$name = $_POST['name'];
		
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
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{
			while ($row = mysqli_fetch_array($result))
			{
				$result_leader = $row['leader'];
			}
			
			if($result_leader == $steamprofile['steamid'])
			{
				$result=mysqli_query($link, "
				SELECT ".$player_table.".team FROM ".$player_table." WHERE ".$player_table.".steam_id_64='".$steamprofile['steamid']."'");
				$numb=mysqli_num_rows($result); 
				if (!empty($numb)) 
				{ 
					while ($row = mysqli_fetch_array($result))
					{
						$team_id = $row['team'];
					}
				}
				
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
								$result=mysqli_query($link, "
								UPDATE ".$team_table." 
								SET ".$team_table.".name = '".$name."', 
								".$team_table.".fb = '".$fb."', 
								".$team_table.".twitter = '".$twitter."',  
								".$team_table.".youtube = '".$youtube."', 
								".$team_table.".twitch = '".$twitch."', 
								".$team_table.".steam_url = '".$steam."' ,
								".$team_table.".logo = '".$fileName.".".$ext."'
								WHERE ".$team_table.".id='".$team_id."'");
								
								$json = array(
									"responce" => "success",
								);
									
								echo json_encode($json, true);
								mysqli_close ($link);
								exit;
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
				else
				{
					$result=mysqli_query($link, "
					UPDATE ".$team_table." 
					SET ".$team_table.".name = '".$name."', 
					".$team_table.".fb = '".$fb."', 
					".$team_table.".twitter = '".$twitter."',  
					".$team_table.".youtube = '".$youtube."', 
					".$team_table.".twitch = '".$twitch."', 
					".$team_table.".steam_url = '".$steam."' 
					WHERE ".$team_table.".id='".$team_id."'");
					
					$json = array(
						"responce" => "success",
					);
						
					echo json_encode($json, true);
					mysqli_close ($link);
					exit;
				}
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
		else
		{
			$result=mysqli_query($link, "
			SELECT ".$player_table.".team FROM ".$player_table." WHERE ".$player_table.".steam_id_64='".$steamprofile['steamid']."'");
			$numb=mysqli_num_rows($result); 
			if (!empty($numb)) 
			{ 
				while ($row = mysqli_fetch_array($result))
				{
					$team_id = $row['team'];
				}
			}
			
			if(isset($_FILES["file"]["name"])) 
			{
				if ($_FILES['file']['error'] === UPLOAD_ERR_OK)
				{
					if($_FILES["file"]["size"] <= "1048576")
					{
						$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
						$fileName = md5_file($_FILES['file']['tmp_name']);
						$image = new Imagick($_FILES['file']['tmp_name']);
						$image->resizeImage("512", "512", Imagick::FILTER_LANCZOS, 1, TRUE);
						$img_result = $image->writeImages($upload_dir . $fileName . "." . $ext , true);
						
						if($img_result)
						{
							$result=mysqli_query($link, "
							UPDATE ".$team_table." 
							SET ".$team_table.".name = '".$name."', 
							".$team_table.".fb = '".$fb."', 
							".$team_table.".twitter = '".$twitter."',  
							".$team_table.".youtube = '".$youtube."', 
							".$team_table.".twitch = '".$twitch."', 
							".$team_table.".steam_url = '".$steam."' ,
							".$team_table.".logo = '".$fileName.".".$ext."'
							WHERE ".$team_table.".id='".$team_id."'");
							
							$json = array(
								"responce" => "success",
							);
								
							echo json_encode($json, true);
							mysqli_close ($link);
							exit;
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
			else
			{
				$result=mysqli_query($link, "
				UPDATE ".$team_table." 
				SET ".$team_table.".name = '".$name."', 
				".$team_table.".fb = '".$fb."', 
				".$team_table.".twitter = '".$twitter."',  
				".$team_table.".youtube = '".$youtube."', 
				".$team_table.".twitch = '".$twitch."', 
				".$team_table.".steam_url = '".$steam."' 
				WHERE ".$team_table.".id='".$team_id."'");
				
				$json = array(
					"responce" => "success",
				);
					
				echo json_encode($json, true);
				mysqli_close ($link);
				exit;
			}
		}
	}
	//image only
	else
	{
		if(isset($_FILES["file"]["name"])) 
		{
			if ($_FILES['file']['error'] === UPLOAD_ERR_OK)
			{
				if($_FILES["file"]["size"] <= "1048576")
				{
					$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
					$fileName = md5_file($_FILES['file']['tmp_name']);
					$image = new Imagick($_FILES['file']['tmp_name']);
					$image->resizeImage("512", "512", Imagick::FILTER_LANCZOS, 1, TRUE);
					$img_result = $image->writeImages($upload_dir . $fileName . "." . $ext , true);
					
					if($img_result)
					{
						$result=mysqli_query($link, "
						UPDATE ".$team_table." 
						SET ".$team_table.".name = '".$name."', 
						".$team_table.".fb = '".$fb."', 
						".$team_table.".twitter = '".$twitter."',  
						".$team_table.".youtube = '".$youtube."', 
						".$team_table.".twitch = '".$twitch."', 
						".$team_table.".steam_url = '".$steam."' ,
						".$team_table.".logo = './assets/img/teams/".$fileName.".".$ext."'
						WHERE ".$team_table.".id='".$team_id."'");
						
						$json = array(
							"responce" => "success",
						);
							
						echo json_encode($json, true);
						mysqli_close ($link);
						exit;
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
	}
}
mysqli_close($link);
?>