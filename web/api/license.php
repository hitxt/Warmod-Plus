<?php	
	require_once("../config.php");		
	function checkVariable(&$var, $attrib) 
	{		
		if(isset($_POST[$attrib])) 		
		{			
			$var = $_POST[$attrib];		
		} 		
		else if(isset($_GET[$attrib])) 		
		{			
			$var = $_GET[$attrib];		
		} 		
		else 		
		{			
			return false;		
		}		
		return true;	
	}		
	if(checkVariable($token, 't') && checkVariable($version, 'v'))	
	{		
		$result=mysqli_query($link, "		
		SELECT * FROM ".$license_table." WHERE token = '".$token."'");		
		mysqli_set_charset ($link , "utf-8");		
		$numb=mysqli_num_rows($result); 		
		if (!empty($numb)) 		
		{			
			while ($row = mysqli_fetch_array($result))			
			{				
				$time_exp = $row['time_exp'];				
				$ftp_a = $row['ftp_a'];				
				$ftp_p = $row['ftp_p'];				
				$steam = $row['steam_id_64'];			
			}						
			$tempDate = date("Y-m-d");			
			echo '"license" { "license" { "time_exp" "'.$time_exp.'" "time_now" "'.$tempDate.'" "ftp_a" "'.$ftp_a.'" "ftp_p" "'.$ftp_p.'" "version" "'.$plugin_version.'" } }';		
		}	
	}	
	mysqli_close($link);
?>