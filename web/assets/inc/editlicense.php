<?php
require_once("../../config.php");
require_once ('../../steamauth/steamauth.php');
include_once ('../../steamauth/userInfo.php');
if(isset($_SESSION['steamid']))
{
	if($_POST)
	{
		$data = $_POST['data1'];
		
		$steam = substr($data[0], 29, 17);
		
		$result = mysqli_query($link, "UPDATE ".$license_table." SET 
		token = '".$data[1]."',
		time_exp = '".$data[2]."',
		ftp_a = '".$data[3]."', 
		ftp_p = '".$data[4]."'
		WHERE steam_id_64 = '".$steam."'");
	}
}
mysqli_close($link);
?>