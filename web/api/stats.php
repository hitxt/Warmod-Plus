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
	
	if(checkVariable($token, 't') && 	
		checkVariable($team, 'team') &&
		checkVariable($aid, 'aid') && 		
		checkVariable($mid, 'mid') && 		
		checkVariable($rws, 'rws') && 		
		checkVariable($round, 'round') && 		
		checkVariable($k, 'k') && 		
		checkVariable($d, 'd') && 		
		checkVariable($a, 'a') && 		
		checkVariable($hs, 'hs') && 		
		checkVariable($tk, 'tk') && 		
		checkVariable($ata, 'ata') && 		
		checkVariable($dmg, 'dmg') && 		
		checkVariable($hit, 'hit') && 		
		checkVariable($shot, 'shot') && 		
		checkVariable($last, 'last') && 		
		checkVariable($won, 'won') && 		
		checkVariable($k1, 'k1') && 		
		checkVariable($k2, 'k2') && 		
		checkVariable($k3, 'k3') && 		
		checkVariable($k4, 'k4') && 		
		checkVariable($k5, 'k5') && 		
		checkVariable($mvp, 'mvp') &&
		checkVariable($rws2, 'rws2') &&
		checkVariable($knife, 'knife') &&
		checkVariable($glock, 'glock') &&
		checkVariable($hkp2000, 'hkp2000') &&
		checkVariable($usp_silencer, 'usp_silencer') &&
		checkVariable($p250, 'p250') &&
		checkVariable($deagle, 'deagle') &&
		checkVariable($elite, 'elite') &&
		checkVariable($fiveseven, 'fiveseven') &&
		checkVariable($tec9, 'tec9') &&
		checkVariable($cz75a, 'cz75a') &&
		checkVariable($revolver, 'revolver') &&
		checkVariable($nova, 'nova') &&
		checkVariable($xm1014, 'xm1014') &&
		checkVariable($mag7, 'mag7') &&
		checkVariable($sawedoff, 'sawedoff') &&
		checkVariable($bizon, 'bizon') &&
		checkVariable($mac10, 'mac10') &&
		checkVariable($mp9, 'mp9') &&
		checkVariable($mp7, 'mp7') &&
		checkVariable($mp5sd, 'mp5sd') &&
		checkVariable($ump45, 'ump45') &&
		checkVariable($p90, 'p90') &&
		checkVariable($galilar, 'galilar') &&
		checkVariable($ak47, 'ak47') &&
		checkVariable($scar20, 'scar20') &&
		checkVariable($famas, 'famas') &&
		checkVariable($m4a1, 'm4a1') &&
		checkVariable($m4a1_silencer, 'm4a1_silencer') &&
		checkVariable($aug, 'aug') &&
		checkVariable($ssg08, 'ssg08') &&
		checkVariable($sg556, 'sg556') &&
		checkVariable($awp, 'awp') &&
		checkVariable($g3sg1, 'g3sg1') &&
		checkVariable($m249, 'm249') &&
		checkVariable($negev, 'negev') &&
		checkVariable($hegrenade, 'hegrenade') &&
		checkVariable($flashbang, 'flashbang') &&
		checkVariable($smokegrenade, 'smokegrenade') &&
		checkVariable($inferno, 'inferno') &&
		checkVariable($incgrenade, 'incgrenade') &&
		checkVariable($molotov, 'molotov') &&
		checkVariable($decoy, 'decoy') &&
		checkVariable($taser, 'taser') &&
		checkVariable($generic, 'generic') &&
		checkVariable($head, 'head') &&
		checkVariable($chest, 'chest') &&
		checkVariable($stomach, 'stomach') &&
		checkVariable($left_arm, 'left_arm') &&
		checkVariable($right_arm, 'right_arm') &&
		checkVariable($left_leg, 'left_leg') &&
		checkVariable($right_leg, 'right_leg') &&
		checkVariable($defuse, 'defuse') &&
		checkVariable($plant, 'plant') &&
		checkVariable($explode, 'explode') &&
		checkVariable($hostage, 'hostage'))	
		{		
			$result=mysqli_query($link, "SELECT * FROM ".$license_table." WHERE token = '".$token."'");		
			mysqli_set_charset ($link , "utf-8");		
			$numb=mysqli_num_rows($result); 		
			if (!empty($numb)) 		
			{			
				for($i=0; $i<count($aid); $i++)
				{
					// update rws
					$sql = "UPDATE ".$player_table." SET `rws` = '".$rws[$i]."' WHERE `steam_id_64` = '".$aid[$i]."'";
					$result=mysqli_query($link, $sql);	

					$sql = "			
					UPDATE ".$stats_table." SET 			
					`rounds_played` = '".$round[$i]."', 
					`team` = '".$team[$i]."', 
					`kills` = '".$k[$i]."', 
					`deaths` = '".$d[$i]."', 
					`assists` = '".$a[$i]."',			
					`head_shots` = '".$hs[$i]."', 
					`team_kills` = '".$tk[$i]."', 
					`assists_team_attack` = '".$ata[$i]."', 
					`damage` = '".$dmg[$i]."', 			
					`hits` = '".$hit[$i]."', 
					`shots` = '".$shot[$i]."', 
					`last_alive` = '".$last[$i]."', 
					`clutch_won` = '".$won[$i]."', 			
					`1k` = '".$k1[$i]."', `2k` = '".$k2[$i]."', `3k` = '".$k3[$i]."', `4k` = '".$k4[$i]."', `5k` = '".$k5[$i]."', 			
					`mvp` = '".$mvp[$i]."', `rws` = '".$rws2[$i]."', 
					`knife` ='". $knife[$i]."',
					`glock` = '".$glock[$i]."',
					`hkp2000` = '".$hkp2000[$i]."',
					`usp_silencer` = '".$usp_silencer[$i]."',
					`p250` = '".$p250[$i]."',
					`deagle` = '".$deagle[$i]."',
					`elite` = '".$elite[$i]."',
					`fiveseven` = '".$fiveseven[$i]."',
					`tec9` = '".$tec9[$i]."',
					`cz75a` = '".$cz75a[$i]."',
					`revolver` = '".$revolver[$i]."',
					`nova` = '".$nova[$i]."',
					`xm1014` = '".$xm1014[$i]."',
					`mag7` = '".$mag7[$i]."',
					`sawedoff` = '".$sawedoff[$i]."',
					`bizon` = '".$bizon[$i]."',
					`mac10` = '".$mac10[$i]."',
					`mp9` = '".$mp9[$i]."',
					`mp7` = '".$mp7[$i]."',
					`mp5sd` = '".$mp5sd[$i]."',
					`ump45` = '".$ump45[$i]."',
					`p90` = '".$p90[$i]."',
					`galilar` = '".$galilar[$i]."',
					`ak47` = '".$ak47[$i]."',
					`scar20` = '".$scar20[$i]."',
					`famas` = '".$famas[$i]."',
					`m4a1` = '".$m4a1[$i]."',
					`m4a1_silencer` = '".$m4a1_silencer[$i]."',
					`aug` = '".$aug[$i]."',
					`ssg08` = '".$ssg08[$i]."',
					`sg556` = '".$sg556[$i]."',
					`awp` = '".$awp[$i]."',
					`g3sg1` = '".$g3sg1[$i]."',
					`m249` = '".$m249[$i]."',
					`negev` = '".$negev[$i]."',
					`hegrenade` = '".$hegrenade[$i]."',
					`flashbang` = '".$flashbang[$i]."',
					`smokegrenade` = '".$smokegrenade[$i]."',
					`inferno` = '".$inferno[$i]."',
					`incgrenade` = '".$incgrenade[$i]."',
					`molotov` = '".$molotov[$i]."',
					`decoy` = '".$decoy[$i]."',
					`taser` = '".$taser[$i]."',
					`generic` = '".$generic[$i]."',
					`head` = '".$head[$i]."',
					`chest` = '".$chest[$i]."',
					`stomach` = '".$stomach[$i]."',
					`left_arm` = '".$left_arm[$i]."',
					`right_arm` = '".$right_arm[$i]."',
					`left_leg` = '".$left_leg[$i]."',
					`right_leg` = '".$right_leg[$i]."',
					`c4_planted` = '".$plant[$i]."',
					`c4_exploded` = '".$explode[$i]."',
					`c4_defused` = '".$defuse[$i]."',
					`hostages_rescued` = '".$hostage[$i]."'
					WHERE `match_id` = '".$mid."' AND `steam_id_64` = '".$aid[$i]."'";
					// update stats
					$result=mysqli_query($link, $sql);	
					// insert
					$affrow = mysqli_affected_rows($link);						
					if($affrow < 1)			
					{	
						$sql = "INSERT INTO ".$stats_table." 				
						VALUES 				
						(NULL, 
						'".$mid."', 
						'".$round[$i]."', 
						'".$aid[$i]."', 				
						'".$team[$i]."', 
						'".$k[$i]."', 
						'".$d[$i]."', 
						'".$a[$i]."', 
						'".$hs[$i]."', 
						'".$tk[$i]."',				
						'".$ata[$i]."', 
						'".$dmg[$i]."', 
						'".$hit[$i]."', 
						'".$shot[$i]."', 
						'".$last[$i]."', 
						'".$won[$i]."', 				
						'".$k1[$i]."', 
						'".$k2[$i]."', 
						'".$k3[$i]."', 
						'".$k4[$i]."', 
						'".$k5[$i]."', 
						'".$mvp[$i]."', 
						'".$rws2[$i]."',
						'".$knife[$i]."',
						'".$glock[$i]."',
						'".$hkp2000[$i]."',
						'".$usp_silencer[$i]."',
						'".$p250[$i]."',
						'".$deagle[$i]."',
						'".$elite[$i]."',
						'".$fiveseven[$i]."',
						'".$tec9[$i]."',
						'".$cz75a[$i]."',
						'".$revolver[$i]."',
						'".$nova[$i]."',
						'".$xm1014[$i]."',
						'".$mag7[$i]."',
						'".$sawedoff[$i]."',
						'".$bizon[$i]."',
						'".$mac10[$i]."',
						'".$mp9[$i]."',
						'".$mp7[$i]."',
						'".$mp5sd[$i]."',
						'".$ump45[$i]."',
						'".$p90[$i]."',
						'".$galilar[$i]."',
						'".$ak47[$i]."',
						'".$scar20[$i]."',
						'".$famas[$i]."',
						'".$m4a1[$i]."',
						'".$m4a1_silencer[$i]."',
						'".$aug[$i]."',
						'".$ssg08[$i]."',
						'".$sg556[$i]."',
						'".$awp[$i]."',
						'".$g3sg1[$i]."',
						'".$m249[$i]."',
						'".$negev[$i]."',
						'".$hegrenade[$i]."',
						'".$flashbang[$i]."',
						'".$smokegrenade[$i]."',
						'".$inferno[$i]."',
						'".$incgrenade[$i]."',
						'".$molotov[$i]."',
						'".$decoy[$i]."',
						'".$taser[$i]."',
						'".$generic[$i]."',
						'".$head[$i]."',
						'".$chest[$i]."',
						'".$stomach[$i]."',
						'".$left_arm[$i]."',
						'".$right_arm[$i]."',
						'".$left_leg[$i]."',
						'".$right_leg[$i]."', '".$plant[$i]."', '".$explode[$i]."', '".$defuse[$i]."', '".$hostage[$i]."'
						)";
						$result=mysqli_query($link, $sql);	
					}
				}						
				echo '"response" { "response" { "response" "success" } }';		
			}	
		}	
	mysqli_close($link);
?>