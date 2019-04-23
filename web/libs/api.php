<?php
	require_once("../configs/configs.php");
	require_once("./sql.php");
	require_once("./steam/SteamID.php");

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
		}
	}

	$pdo = null;
?>