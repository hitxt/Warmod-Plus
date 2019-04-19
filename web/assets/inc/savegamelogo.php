<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if(isset($_FILES["file"]["name"])) 
	{
		if ($_FILES['file']['error'] === UPLOAD_ERR_OK)
		{
			if($_FILES["file"]["size"] <= "1048576")
			{
				/*
				list($width, $height) = getimagesize($_FILES['file']['tmp_name']);
				
				if($width == "64" && $height == "64")
				{
				*/
					$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
					if ($ext != "svg") 
					{ 
						$json = array(
							"responce" => "svg-only",
						);
						echo json_encode($json, true);
						mysqli_close ($link);
						exit;
					}
					
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
					
					$result=mysqli_query($link, "DELETE FROM ".$game_logo_table." WHERE team = '".$team_id."'");
					mysqli_set_charset ($link , "utf-8");
					
					$result=mysqli_query($link, "INSERT INTO ".$game_logo_table." (`id`, `team`) VALUES (NULL, '".$team_id."')");
					mysqli_set_charset ($link , "utf-8");
					$insert_id = mysqli_insert_id($link);
					
					//$image = new Imagick($_FILES['file']['tmp_name']);
					//$image->resizeImage("64", "64", Imagick::FILTER_LANCZOS, 1, TRUE);
					//$img_result = $image->writeImages($upload_dir2 . $insert_id . "." . $ext , true);
					copy($_FILES['file']['tmp_name'], $upload_dir2 . $fileName2 . "." . $ext);
					copy($upload_dir2.$insert_id.".".$ext, $upload_dir3.$insert_id.".".$ext);
					
					if($img_result)
					{
							$json = array(
								"responce" => "success",
							);
								
							echo json_encode($json, true);
							mysqli_close ($link);
							exit;
					}
					else
					{
						$result=mysqli_query($link, "DELETE FROM ".$game_logo_table." WHERE id = '".$insert_id."'");
						mysqli_set_charset ($link , "utf-8");
						
						$json = array(
							"responce" => "error",
						);
							
						echo json_encode($json, true);
						mysqli_close ($link);
						exit;
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
				"responce2" => $_FILES['file']['error']
			);
				
			echo json_encode($json, true);
			mysqli_close ($link);
			exit;
		}
	}
}
mysqli_close($link);
?>