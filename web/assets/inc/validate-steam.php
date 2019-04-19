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
		$steam = $_POST['steam'];
		
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
				$valid = false;
				echo json_encode($valid);
			}
			else
			{
				if($steam == $steamprofile['steamid'])
				{
					$valid = false;
					echo json_encode($valid);
				}
				else
				{
					$valid = true;
					echo json_encode($valid);
				}
			}
		}
	}
}
mysqli_close($link);
?>