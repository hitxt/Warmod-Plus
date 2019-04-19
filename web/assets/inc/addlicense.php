<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
require_once "./SteamID.php";
include_once "./steam.php";
include_once "./inc.php";
print_r($_SESSION, TRUE);
if(isset($_SESSION['steamid']))
{
	$steam =  $_POST['steam'];
	$date_e =  $_POST['date2'];
	$ftpa =  $_POST['ftpa'];
	$ftpp =  $_POST['ftpp'];
	
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
				"responce" => "invalid",
			);
				
			echo json_encode($json, true);
			mysqli_close ($link);
			exit;
		}	
	}
	
	$name = getName($steam);
	
	$result=mysqli_query($link, "SELECT * FROM ".$license_table." WHERE steam_id_64 = '".$steam."'");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		$json = array(
			"responce" => "already",
			"responce2" => $name,
		);
		
		echo json_encode($json, true);
		mysqli_close ($link);
		exit;
	}
	
	$token = random_password(10);
	
	$result=mysqli_query($link, "INSERT INTO ".$license_table." VALUES 
	(NULL, '".$steam."', '".$token."', '".$date_e."', '".$ftpa."', '".$ftpp."')");
	
	$id = mysqli_insert_id($link);
	
	$json = array(
		"responce" => "success",
		"responce2" => "<a href='./showplayer.php?id=".$steam."'>".$name."</a>",
		"responce3" => $token,
		"responce4" => $date_e,
		"responce5" => $ftpa,
		"responce6" => $ftpp,
		"responce7" => $id,
	);
		
	echo json_encode($json, true);
	mysqli_close ($link);
	exit;
}
?>