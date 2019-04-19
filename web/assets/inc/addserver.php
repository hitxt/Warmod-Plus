<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
require_once "./SteamID.php";
include_once "./steam.php";
include_once "./inc.php";
include_once "./SourceQuery.php";
print_r($_SESSION, TRUE);
if(isset($_SESSION['steamid']))
{
	$ip =  $_POST['ip'];
	$port =  $_POST['port'];
	$type =  $_POST['type'];
	$enable = $_POST['enable'];
	
	$result=mysqli_query($link, "SELECT * FROM ".$server_table." WHERE ip = '".$ip."' AND port = '".$port."'");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		$json = array(
			"responce" => "already",
		);
		
		echo json_encode($json, true);
		mysqli_close ($link);
		exit;
	}
	
	$result=mysqli_query($link, "INSERT INTO ".$server_table." VALUES 
	(NULL, '".$ip."', '".$port."', '".$enable."', '".$type."')");
	
	try
	{
		$server = new SourceQuery($ip,$port);
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
	
	if($enable == "1")	$enable = "Enable";
	else	$enable = "Disable";
	
	if($type == "1")	$type = "Official";
	elseif($type == "2")	$type = "Partner";
	else	$type = "";
														
	$json = array(
		"responce" => "success",
		"responce2" => $enable,
		"responce3" => $ip,
		"responce4" => $port,
		"responce5" => $type,
		"responce6" => $infos['name'],
		"responce7" => $infos['map'],
		"responce8" => $infos["players"]."/".$infos["places"],
		"responce9" => "<a href ='steam://connect/".$ip.":".$port."'>CONNECT</a>",
	);
		
	echo json_encode($json, true);
	mysqli_close ($link);
	exit;
}
?>