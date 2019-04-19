<?php
require_once "../../config.php";
require_once "./SteamID.php";

if(!empty($_GET["id"]))
{
	$id=$_GET["id"];

	$result=mysqli_query($link, "select * from ".$result_table." where match_id = ".$id."");
	$num=mysqli_num_rows($result);

	if($num<1)
	{
		try
		{
			$steam_id = new SteamID( $id );
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
				mysqli_close($link);
				header("Location: ../../showplayer.php?id=".$id."");
				exit;
			}
			else
			{
				mysqli_close($link);
				echo "<script type='text/javascript'>
				alert('Invalid Match ID or Steam64 ID');
				window.history.back();
				</script>";
				exit;
			}	
		}
	}
	else
	{
		mysqli_close($link);
		header("Location: ../../showmatch.php?id=".$id.""); 
		exit;
	}
}
else
{
	mysqli_close($link);
	echo "<script type='text/javascript'>
	alert('Invalid Match ID or Steam64 ID');
	window.history.back();
	</script>";
	exit;
}	
?>