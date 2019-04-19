<?php
    require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/SteamID.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/weapons.php";
	require_once "assets/inc/view_mode.php";
	require_once "assets/inc/session.php";
	$gi = geoip_open("assets/inc/GeoIP.dat",GEOIP_STANDARD);
	
	// prevent bug
	if (isset($_GET['id']) && !empty($_GET['id']))	$id = $_GET['id'];
	else {echo "<script type='text/javascript'>alert('Invalid Steam64 ID');window.history.back();</script>"; exit;}
	
	if(!is_numeric($id)){echo "<script type='text/javascript'>alert('Invalid Steam64 ID');window.history.back();</script>"; exit;}
	
	// prevent bug, if steam id invalid
	try
	{
		$steam_id = new SteamID( $id );
		if( !$steam_id->IsValid() )
		{
			throw new InvalidArgumentException( "exit" );
		}
		else {throw new InvalidArgumentException("exist");}
	}
	catch( InvalidArgumentException $e )
	{
		if( $e->getMessage() == "exit" )
		{
			echo "<script type='text/javascript'>
			alert('Invalid Steam64 ID');
			window.history.back();
			</script>";
			exit;
		}	
	}
	
	// profile
	$name = getName($id);
	$avatar = getAvatarFull($id);
	
	$fb = "";
	$twitter = "";
	$twitch = "";
	$youtube = "";
	$ip = "";
	$cc = "";
	$rws = 0.00;
			
	// stats
	$result=mysqli_query($link, "
	SELECT ".$player_table.".steam_id_64, ".$player_table.".rws, ".$player_table.".last_ip, ".$player_table.".team, 
	".$player_table.".fb, ".$player_table.".twitter, ".$player_table.".twitch, ".$player_table.".youtube
	FROM `".$player_table."`
	WHERE ".$player_table.".steam_id_64 = ".$id."");
	
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		while ($row = mysqli_fetch_array($result))
		{
			$rws = $row['rws'];
			$rws2 = $row['rws'];
			if($rws != 0.00 && $rws != NULL)	$rws = floor_dec($rws,2);
			else $rws = 0.00;
									
			$ip = $row['last_ip'];
			$cc = geoip_country_code_by_addr($gi,$ip);
			$team = $row['team'];
			
			$fb = $row['fb'];
			$twitter = $row['twitter'];
			$twitch = $row['twitch'];
			$youtube = $row['youtube'];
		}
	}
	
	// stats
	$result=mysqli_query($link, "
	SELECT 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".kills, 0)) AS 'kills', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".deaths, 0)) AS 'deaths', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".head_shots, 0)) AS 'head_shots',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hits, 0)) AS 'hits',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".shots, 0)) AS 'shots',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".5k, 0)) AS 'ace', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mvp, 0)) AS 'mvp',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".knife ,0)) AS  'knife', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".glock ,0)) AS  'glock', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hkp2000 ,0)) AS  'hkp2000', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".usp_silencer ,0)) AS  'usp_silencer', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".p250 ,0)) AS  'p250', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".deagle ,0)) AS  'deagle', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".elite ,0)) AS  'elite', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".fiveseven ,0)) AS  'fiveseven', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".tec9 ,0)) AS  'tec9', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".cz75a ,0)) AS  'cz75a', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".revolver ,0)) AS  'revolver', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".nova ,0)) AS  'nova', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".xm1014 ,0)) AS  'xm1014', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mag7 ,0)) AS  'mag7', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".sawedoff ,0)) AS  'sawedoff', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".bizon ,0)) AS  'bizon', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mac10 ,0)) AS  'mac10', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp9 ,0)) AS  'mp9', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp7 ,0)) AS  'mp7', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp5sd ,0)) AS  'mp5sd', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ump45 ,0)) AS  'ump45', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".p90 ,0)) AS  'p90', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".galilar ,0)) AS  'galilar', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ak47 ,0)) AS  'ak47', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".scar20 ,0)) AS  'scar20', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".famas ,0)) AS  'famas', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m4a1 ,0)) AS  'm4a1', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m4a1_silencer ,0)) AS  'm4a1_silencer', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".aug ,0)) AS  'aug', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ssg08 ,0)) AS  'ssg08', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".sg556 ,0)) AS  'sg556', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".awp ,0)) AS  'awp', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".g3sg1 ,0)) AS  'g3sg1', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m249 ,0)) AS  'm249', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".negev ,0)) AS  'negev', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hegrenade ,0)) AS  'hegrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".flashbang ,0)) AS  'flashbang', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".smokegrenade ,0)) AS  'smokegrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".inferno ,0)) AS  'inferno', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".incgrenade ,0)) AS  'incgrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".molotov ,0)) AS  'molotov', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".decoy ,0)) AS  'decoy', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".taser ,0)) AS  'taser', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".generic ,0)) AS  'generic', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".head ,0)) AS  'head', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".chest ,0)) AS  'chest', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".stomach ,0)) AS  'stomach', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".left_arm ,0)) AS  'left_arm', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".right_arm ,0)) AS  'right_arm', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".left_leg ,0)) AS  'left_leg', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".right_leg ,0)) AS  'right_leg', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_planted ,0)) AS  'c4_planted', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_exploded ,0)) AS  'c4_exploded', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_defused ,0)) AS  'c4_defused', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hostages_rescued ,0)) AS  'hostages_rescued'
	FROM `".$stats_table."`, `".$player_table."`
	WHERE ".$player_table.".steam_id_64 = ".$id."");
	
	mysqli_set_charset ($link , "utf-8");
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		while ($row = mysqli_fetch_array($result))
		{
			$kills = $row['kills'];
			$deaths = $row['deaths'];
			$hs = $row['head_shots'];
			$hits = $row['hits'];
			$shots = $row['shots'];
			$ace = $row['ace'];
			$mvp = $row['mvp'];
			
			$num = 1;
			// weapons are stord between 23 and 65, total 43
			for($i=7; $i<=49; $i++)
			{
				$weapon[$num]['kills'] = $row[$i];
				if($kills > 0){	$weapon[$num]['kp'] = floor_dec($weapon[$num]['kills']/$kills*100,2);	$weapon[$num]['kp'].="%";}
				else $weapon[$num]['kp'] = "0%";
				if($num>1 && $weapon[$num]['kills'] > $weapon[$num-1]['kills'])	$fav_weapon = $weapon[$num]['name'];
				$num++;
			}
			
			$generic = $row ['generic'];
			$head = $row ['head'];
			$chest = $row ['chest'];
			$stomach = $row ['stomach'];
			$left_arm = $row ['left_arm'];
			$right_arm = $row ['right_arm'];
			$left_leg = $row ['left_leg'];
			$right_leg = $row ['right_leg'];
			$defuse = $row ['c4_defused'];
			$plant = $row ['c4_planted'];
			$explode = $row ['c4_exploded'];
			$hostage = $row ['hostages_rescued'];
											
			if($shots == 0) $ac = 0;
			else	$ac = round($shots/$hits, 2);
									
			if($deaths == 0)	$kdr = round($kills/1, 2);
			elseif($deaths == NULL)	$kdr = 0;
			else	$kdr = round($kills/$deaths, 2);
			
			if($hits != 0)
			{
				$headpre = round($head/$hits, 2)*100;
				$chestpre = round($chest/$hits, 2)*100;
				$stomachpre = round($stomach/$hits, 2)*100;
				$left_armpre = round($left_arm/$hits, 2)*100;
				$right_armpre = round($right_arm/$hits, 2)*100;
				$left_legpre = round($left_leg/$hits, 2)*100;
				$right_legpre = round($right_leg/$hits, 2)*100;
			}
			else
			{
				$headpre = 0;
				$chestpre = 0;
				$stomachpre = 0;
				$left_armpre = 0;
				$right_armpre = 0;
				$left_legpre = 0;
				$right_legpre = 0;
			}
		}
	}
	
	if(empty($kills))	$kills = 0;
	if(empty($deaths))	$deaths = 0;
	if(empty($hs))		$hs = 0;
	if(empty($hits))	$hits = 0;
	if(empty($shots))	$shots = 0;
	if(empty($ace)) 	$ace = 0;
	if(empty($mvp)) 	$mvp = 0;
	
	if(empty($rws)) $rws = 0.00;
	
	// check weapons
	for($i=1; $i<=42; $i++)
	{
		if(empty($weapon[$i]['kills']))
		{
			$weapon[$i]['kills'] = 0;
			$weapon[$i]['kp'] = 0;
		}
	}
	
	if(empty($generic)) 	$generic = 0;
	if(empty($head)) 		$head = 0;
	if(empty($chest)) 		$chest = 0;;
	if(empty($stomach)) 	$stomach = 0;
	if(empty($left_arm)) 	$left_arm = 0;
	if(empty($right_arm )) 	$right_arm = 0;
	if(empty($left_leg)) 	$left_leg = 0;
	if(empty($right_leg)) 	$right_leg = 0;
	if(empty($defuse)) 		$defuse = 0;
	if(empty($plant)) 		$plant = 0;
	if(empty($explode)) 	$explode = 0;
	if(empty($hostage)) 	$hostage = 0;
	
	if(empty($headpre))			$headpre = 0;
	if(empty($chestpre))		$chestpre = 0;
	if(empty($stomachpre))		$stomachpre = 0;
	if(empty($left_armpre))		$left_armpre = 0;
	if(empty($right_armpre))	$right_armpre = 0;
	if(empty($left_legpre))		$left_legpre = 0;
	if(empty($right_legpre))	$right_legpre = 0;

	if(!isset($team) || empty($team)) { $team_name = "Unknown"; $team_logo = "./assets/img/teams/unknown.png"; $team = "0";}
	else
	{
		$result=mysqli_query($link, "
		SELECT * FROM ".$team_table." WHERE id = '".$team."'");
		mysqli_set_charset ($link , "utf-8");
		$team_numb=mysqli_num_rows($result);
		if (!empty($numb)) 
		{	
			while ($row = mysqli_fetch_array($result))
			{
				$team_logo = "./assets/img/teams/".$row['logo']."";
				if($team_logo == "./assets/img/teams/" || !file_exists($team_logo))	$team_logo = "./assets/img/teams/unknown.png";
				$team_name = $row['name'];
			}
		}
	}
	
	// rank
	if($rws > 0)
	{
		$result=mysqli_query($link, "
		SELECT steam_id_64, b.Rank
		FROM (SELECT a.rank AS 'Rank', a.rws, a.steam_id_64 
			FROM (SELECT steam_id_64, rws, @prev := @curr, @curr := rws, @rank := IF(@prev = @curr, @rank, @rank+1) AS rank
				FROM ".$player_table.", (SELECT @curr := null, @prev := null, @rank := 0) s
				ORDER BY rws DESC) a) b
		WHERE steam_id_64 = ".$id.";");
		
		mysqli_set_charset ($link , "utf-8");
		$rank_numb=mysqli_num_rows($result);
		if (!empty($numb)) 
		{	
			while ($row = mysqli_fetch_array($result))
			{
				$rank = $row['Rank'];
			}
		}
		else	$rank = "Unranked";
	}
	else	$rank = "Unranked";
	
	// matches
	$result=mysqli_query($link, "
	SELECT DISTINCT ".$result_table.".match_id from ".$stats_table.", ".$result_table." WHERE (".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND ".$stats_table.".team = 1 AND ".$result_table.".match_id = ".$stats_table.".match_id AND ".$stats_table.".steam_id_64 = ".$id.") OR (".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND ".$stats_table.".team = 2 AND ".$result_table.".match_id = ".$stats_table.".match_id AND ".$stats_table.".steam_id_64 = ".$id.")");
	mysqli_set_charset ($link , "utf-8");
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		$lose = $numb;
		if($lose == NULL) $lose = 0;
	}
	else $lose = 0;
	
	$result=mysqli_query($link, "
	SELECT DISTINCT ".$result_table.".match_id from ".$stats_table.", ".$result_table." WHERE (".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND ".$stats_table.".team = 2 AND ".$result_table.".match_id = ".$stats_table.".match_id AND ".$stats_table.".steam_id_64 = ".$id.") OR (".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND ".$stats_table.".team = 1 AND ".$result_table.".match_id = ".$stats_table.".match_id AND ".$stats_table.".steam_id_64 = ".$id.")");
	mysqli_set_charset ($link , "utf-8");
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		$win = $numb;
		if($win == NULL) $win = 0;
	}
	else $win = 0;

	$result=mysqli_query($link, "
	SELECT DISTINCT ".$result_table.".match_id from ".$stats_table.", ".$result_table." WHERE ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score AND ".$stats_table.".steam_id_64 = ".$id."");
	mysqli_set_charset ($link , "utf-8");
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		$draw = $numb;
		if($draw == NULL) $draw = 0;
	}
	else $draw = 0;
	
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Warmod+ | Profile:<?=$name?></title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
	
	<meta property="og:site_name" content="Warmod+">
    <meta property="og:title" content="Warmod+ | Profile:<?=$name?>"/>
	<meta property="og:type" content="website">
	<meta property="og:image" content="<?=$avatar?>">
	<meta property="og:description" content="Warmod+ Profile:<?=$name?>"/>
    
	<!-- Bootstrap core CSS     -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
   
    <!--  Material Dashboard CSS    -->
    <link href="./assets/css/material-dashboard.css" rel="stylesheet" />
	
	<!--  Warmod+ CSS    -->
    <link href="./assets/css/warmod_plus.css" rel="stylesheet" />
    
	<!--     Fonts and icons     -->
    <link href="./assets/css/fontawesome-all.css" rel="stylesheet" />
    <link href="./assets/css/google-roboto-300-700.css" rel="stylesheet" />
	<link href="./assets/css/Pe-icon-7-stroke.css" rel="stylesheet" />
	<link href="./assets/css/warmod_plus.css" rel="stylesheet" />
	<link href="./assets/css/chartist-plugin-tooltip.css" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
		<div class="sidebar" data-active-color="rose" data-background-color="black" data-image="./assets/img/sidebar-mirage.jpg">
            <div class="logo">
                <a href="./index.php" class="simple-text">
                    <img src="./assets/img/logo_white.png" height="40px">armod+
                </a>
            </div>
            <div class="logo logo-mini">
                <a href="./index.php" class="simple-text">
                    <img src="./assets/img/logo_white.png" height="40px">
                </a>
            </div>
            <div class="sidebar-wrapper">
                 <div class="user">
                    <div class="photo">
					<?php
						if(!isset($_SESSION['steamid'])) { echo "<img src='./assets/img/default-avatar.png' />";}
						else{ echo "<img src='".$steamprofile['avatarfull']."'>";}
					?>
                    </div>
                    <div class="info">
                        <a data-toggle="collapse" href="#infocollapse" class="collapsed">
                            <?php
								if(!isset($_SESSION['steamid'])) echo "Guest";
								else echo "".$steamprofile['personaname']."";
							?>
                            <b class="caret"></b>
                        </a>
                        <div class="collapse" id="infocollapse">
                            <?php
								include_once "./assets/inc/info.php"
							?>
                        </div>
                    </div>
                </div>
                <ul class="nav">
                    <li>
                        <a href="index.php">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>
					<?php
					if($server_list)
					echo"<li>
							<a href='servers.php'>
								<i class='fas fa-server'></i>
								<p>Servers</p>
							</a>
						</li>";
					?>
					<li>
                        <a href="matches.php">
                            <i class="material-icons">public</i>
                            <p>Matches</p>
                        </a>
                    </li>
					<li   class="active">
                        <a href="players.php">
                            <i class="material-icons">person</i>
                            <p>Players</p>
                        </a>
                    </li>
					<li>
						<a href="./teams.php">
							<i class="material-icons">group</i>
							<p>Teams</p>
						</a>
                    </li>
					<li>
						<a href="<?=$discord?>" target="_blank">
							<i class="fab fa-2x fa-discord"></i>
							<p>Discord</p>
						</a>
                    </li>
                </ul>
            </div>
        </div>
		<div class="main-panel">
			<nav class="navbar navbar-transparent navbar-absolute">
				<div class="container-fluid">
					<div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
						<a class="navbar-brand" href="./players.php" style='display:inline;'><i class="material-icons">keyboard_arrow_left</i>Back To List</a>
                    </div>
					<div class="collapse navbar-collapse">
						<?php
							include_once "./assets/inc/navbar.php";
						?>
					</div>
				</div>
			</nav>
            <div class="content">
                <div class="container-fluid">
					<div class="row">
						<div class="col-lg-4 col-md-6 col-sm-6">
							<div class="card card-plain card-stats card-wmprofile">
								<div class="card-header"  data-background-image="" data-header-animation="false">
								   <img src="<?=$avatar?>"/>
								</div>
								<div class="card-content" style="text-align:left !important">
									<font color="#999999"><h3 style="margin:0 0 0;">&nbsp;&nbsp;&nbsp;Name</h3></font>
									<h2 class="card-title" style="margin-top: 0px;">&nbsp;&nbsp;<?=$name?></h2>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-6">
							<div class="card card-plain card-stats card-wmprofile">
								<div class="card-header" data-background-image="" data-header-animation="false">
								  <img src="<?=$team_logo?>"/>
								</div>
								<div class="card-content" style="text-align:left !important">
								   <font color="#999999"><h3 style="margin:0 0 0;">&nbsp;&nbsp;&nbsp;Team</h3></font>
									<?php
										if($team != "0")
											echo "<a href='./showteam.php?id=".$team."'><h2 class='card-title' style='margin-top: 0px;'>&nbsp;&nbsp;".$team_name."</h2></a>";
										else echo "<h2 class='card-title' style='margin-top: 0px;'>&nbsp;&nbsp;".$team_name."</h2>";
									?>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-8 col-sm-12">
							<div class="card card-plain card-stats">
								<div class="card-content" style="text-align:center !important">
									<button class="btn btn-just-icon btn-github" onclick="window.open('http://steamcommunity.com/profiles/<?=$id?>');return false;">
										<i class="fab fa-steam-symbol"></i>
									</button>
									&emsp;
									<?php
										if(isset($fb) && !empty($fb))
											echo "<button class='btn btn-just-icon btn-facebook' onclick='window.open(\"".$fb."\");return false;'>
														<i class='fab fa-facebook'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-facebook'></i>
													</button>&emsp;";
										if(isset($twitter) && !empty($twitter))
											echo "<button class='btn btn-just-icon btn-twitter' onclick='window.open(\"".$twitter."\");return false;'>
														<i class='fab fa-twitter'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-twitter'></i>
													</button>&emsp;";
										if(isset($twitch) && !empty($twitch))
											echo "<button class='btn btn-just-icon btn-primary' onclick='window.open(\"".$twitch."\");return false;'>
														<i class='fab fa-twitch'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-twitch'></i>
													</button>&emsp;";
										if(isset($youtube) && !empty($youtube))
											echo "<button class='btn btn-just-icon btn-youtube' onclick='window.open(\"".$youtube."\");return false;'>
														<i class='fab fa-youtube'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-youtube'></i>
													</button>&emsp;";
										?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="orange" data-header-animation="true">
                                    <i class="pe-7s-medal"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Rank</p>
                                    <h3 class="card-title"><?=$rank?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="rose" data-header-animation="true">
                                    <i class="material-icons">info</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">KDR</p>
                                    <h3 class="card-title"><?=$kdr?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="green" data-header-animation="true">
									<i class="pe-7s-graph3"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">RWS</p>
                                    <h3 class="card-title"><?=$rws?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="blue" data-header-animation="true">
                                   <i class="pe-7s-target"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Accuracy</p>
                                    <h3 class="card-title"><?=$ac?></h3>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="row">	
						<div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="orange" data-header-animation="true">
                                    <i class="material-icons">place</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Country</p>
                                    <h3 class="card-title">
										<?php
										if($cc != "") 
										{
											echo "<img src='./assets/img/flags/".geoip_country_code_by_addr($gi,$ip).".png' style='width:25px'/>&nbsp;&nbsp;".geoip_country_name_by_addr($gi,$ip)."";
										} 
										else echo "Unknown";
										?>
									</h3>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="rose" data-header-animation="true">
                                    <i class="material-icons">public</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Matches</p>
                                    <h3 class="card-title">
										<?php
											echo "".$win." W / ".$lose." L / ".$draw." D"
										?>
									</h3>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="green" data-header-animation="true">
                                    <i class="pe-7s-like2"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Ace</p>
                                    <h3 class="card-title"><?=$ace?></h3>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="blue" data-header-animation="true">
                                    <i class="pe-7s-star"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">MVP</p>
                                    <h3 class="card-title"><?=$mvp?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="orange" data-header-animation="true">
                                    <i class="fas fa-bomb"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Bomb Planted</p>
                                    <h3 class="card-title"><?=$plant?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="rose" data-header-animation="true">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Bomb Defused</p>
                                    <h3 class="card-title"><?=$defuse?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="green" data-header-animation="true">
									<i class="fas fa-fire"></i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Bomb Exploded</p>
                                    <h3 class="card-title"><?=$explode?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="blue" data-header-animation="true">
                                   <i class="material-icons">face</i>
                                </div>
                                <div class="card-content">
                                    <p class="category">Hostage Rescued</p>
                                    <h3 class="card-title"><?=$hostage?></h3>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="row">	
						<div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card card-chart">
                                <div class="card-header" data-background-color="purple" data-header-animation="true">
                                    <div id="RWSChart" class="ct-chart"></div>
                                </div>
                                <div class="card-content">
                                   <h4 class="card-title">RWS History</h4>
								   <p class="category">RWS changes in latest matches.</p>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-header card-header-icon" data-background-color="red" >
                                    <i class="material-icons">pie_chart</i>
                                </div>
                                <div class="card-content">
                                    <h4 class="card-title">Hitboxes</h4>
									<?php 
										if($hits > 0) echo"<div id='HitboxPie' class='ct-chart'></div>";
										else echo "<h4 style='text-align:center'>No Hitbox Stats.</h4>"
									?>
								</div>
                                <div class="card-footer">
                                    <h6>Hits:&nbsp;<?=$hits?>&emsp;/&emsp;Shots:&nbsp;<?=$shots?>&emsp;/&emsp;AC:&nbsp;<?=$ac?></h6>
									<i class="fa fa-circle" style="color:#0074D9"></i> Head:&nbsp;<?=$head?>hits,&nbsp;<?=$headpre?>%&emsp;
                                    <i class="fa fa-circle" style="color:#FF4136"></i> Chest:&nbsp;<?=$chest?>hits,&nbsp;<?=$chestpre?>%&emsp;
                                    <i class="fa fa-circle" style="color:#2ECC40"></i> Stomach:&nbsp;<?=$stomach?>hits,&nbsp;<?=$stomachpre?>%&emsp;
									<br>
									<i class="fa fa-circle" style="color:#FF851B"></i> Left Arm:&nbsp;<?=$left_arm?>hits,&nbsp;<?=$left_armpre?>%&emsp;
									<i class="fa fa-circle" style="color:#7FDBFF"></i> Right Arm:&nbsp;<?=$right_arm?>hits,&nbsp;<?=$right_armpre?>%&emsp;
									<i class="fa fa-circle" style="color:#B10DC9"></i> Left Leg:&nbsp;<?=$left_leg?>hits,&nbsp;<?=$left_legpre?>%&emsp;
									<i class="fa fa-circle" style="color:#FFDC00"></i> Right Leg:&nbsp;<?=$right_leg?>hits,&nbsp;<?=$right_legpre?>%
                                </div>
                            </div>
                        </div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header card-header-icon" data-background-color="purple">
									<i class="material-icons">assignment</i>
								</div>
								<div class="card-content">
									<h4 class="card-title">
									Matches Played
									</h4>
									<div class="material-datatables">
									<table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
									<thead>
											<tr>
												<th>ID</th>
												<th>Date (GMT+8)</th>
												<th>Competition</th>
												<th>Event</th>
												<th>Map</th>
												<th>Team A</th>
												<th>Score</th>
												<th>Team B</th>
												<th>Result</th>
												<th>More</th>
											</tr>
										</thead>
										<tfoot>
                                               <tr>
                                                <th>ID</th>
												<th>Date (GMT+8)</th>
												<th>Competition</th>
												<th>Event</th>
												<th>Map</th>
												<th>Team A</th>
												<th>Score</th>
												<th>Team B</th>
												<th>Result</th>
												<th>More</th>
                                               </tr>
                                        </tfoot>
										<tbody>
										<?php
											$result=mysqli_query($link, "
											select
											   ".$result_table.".*,
											   (
												  CASE
													 WHEN
														(
											(". $result_table.".t_overall_score > ". $result_table.".ct_overall_score 
														   AND ".$stats_table.".team = 1) 
														   OR 
														   (
															  ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
															  AND ".$stats_table.".team = 2
														   )
														)
													 THEN
														'lose' 
													 WHEN
														(
											(".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
														   AND ".$stats_table.".team = 2) 
														   OR 
														   (
															  ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
															  AND ".$stats_table.".team = 1
														   )
														)
													 THEN
														'win' 
													 ELSE
														'draw' 
												  END
											   )
											   AS 'result',
											IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
											IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
											from
											   ".$result_table.", ".$stats_table." 
											where
											   ".$result_table.".match_id = ".$stats_table.".match_id 
											   and ".$stats_table.".steam_id_64 = ".$id." 
											order by
											   ".$result_table.".match_id desc
											");
											mysqli_set_charset ($link , "utf-8");
											$numb=mysqli_num_rows($result);
											if (!empty($numb)) 
											{	
												while ($row = mysqli_fetch_array($result))
												{
													$match_id = $row['match_id'];
													$map=$row['map']; 
													$t_name=$row['t_name']; 
													$t_overall_score=$row['t_overall_score']; 
													$ct_overall_score=$row['ct_overall_score']; 
													$ct_name=$row['ct_name']; 
													$demo=$row['demo']; 
													$competition=$row['competition']; 
													$event=$row['event']; 
													$match_result=$row['result']; 
																					
													date_default_timezone_set('UTC');
																					
													$match_start=$row['match_start']; 
													$timestamp = strtotime($match_start);
													$dt = new DateTime("now", new DateTimeZone('Asia/Taipei'));
													$dt->setTimestamp($timestamp);
													$timestamp = $dt->format('Y/m/d g:i:s A');
													
													/*
													if(empty($ct_name) || $ct_name==" ")	$ct_name="Unknown";
													if(empty($t_name) || $t_name==" ")	$t_name="Unknown";
													
													if(empty($competition) || $competition==" ")	$competition="Unknown";
													if(empty($event) || $event==" ")	$event="Unknown";
													*/
													
													$mappic="./assets/img/maps/".$map.".png";
													
													echo "
													<tr height='54px'>
														<td>".$match_id."</td>
														<td>".$timestamp."</td>
														<td>".$competition."</td>
														<td>".$event."</td>
														<td>".$map."</td>
														<td>".$t_name."</td>
														<td>".$t_overall_score." VS ".$ct_overall_score."</td>
														<td>".$ct_name."</td>";
													
													if($match_result == "win")	echo "<td><font color='#5cb85c'>Win</font></td>";
													elseif($match_result == "lose")	echo "<td><font color='#d9534f'>Lose</font></td>";
													else	echo "<td><font color='#5bc0de'>Draw</font></td>";
													
													echo "
														<td><a href='./showmatch.php?id=".$match_id."'>View</a></td>
													</tr>";
												}
											}
										?>
										</tbody>
									</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header card-header-icon" data-background-color="purple">
									<i class="material-icons">assignment</i>
								</div>
								<div class="card-content">
									<h4 class="card-title">Weapon Stats</h4>
									<div class="material-datatables">
									<table id="weapontable" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
										<thead>
											<tr>
												<th>Name</th>
												<th>Kills</th>
												<th>Precent Total Kill</th>
											</tr>
										</thead>
										<tfoot>
                                            <tr>
                                                <th>Name</th>
												<th>Kills</th>
												<th>Precent Total Kill</th>
                                            </tr>
                                        </tfoot>
										<tbody>
										<?php
											for($i=1; $i<=43; $i++)
											{
												echo "<tr style='height:50px'>";
												
												if($weapon[$i]['name'] != "Inferno")
													echo "<td><img src='assets/img/weapons/".$weapon[$i]['name'].".png' style='width:50px !important;' />&emsp;".$weapon[$i]['name']."</td>";
												else
													echo "<td><img src='assets/img/weapons/".$weapon[$i]['name'].".png' style='height:37px !important; width:50px !important' />&emsp;".$weapon[$i]['name']."</td>";
												echo "<td>".$weapon[$i]['kills']."</td>";
												echo "<td>".$weapon[$i]['kp']."</td>";
												echo "</tr>";
											}
										?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <p class="copyright pull-right">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                       Made by <a href="http://steamcommunity.com/id/kentomatoryoshika/" target="_blank">Kento</a>
                    </p>
                </div>
            </footer>
		</div>
	</div>
</body>

<!--   Core JS Files   -->
<script src="./assets/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="./assets/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./assets/js/material.min.js" type="text/javascript"></script>
<script src="./assets/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<!-- Forms Validations Plugin -->
<script src="./assets/js/jquery.validate.min.js"></script>
<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
<script src="./assets/js/moment.min.js"></script>
<!--  Charts Plugin -->
<script src="./assets/js/chartist.min.js"></script>
<!--  Charts Tooltip Plugin -->
<script src="./assets/js/chartist-plugin-tooltip.js"></script>
<!--  Plugin for the Wizard -->
<script src="./assets/js/jquery.bootstrap-wizard.js"></script>
<!--  Notifications Plugin    -->
<script src="./assets/js/bootstrap-notify.js"></script>
<!--   Sharrre Library    -->
<script src="./assets/js/jquery.sharrre.js"></script>
<!-- DateTimePicker Plugin -->
<script src="./assets/js/bootstrap-datetimepicker.js"></script>
<!-- Vector Map plugin -->
<script src="./assets/js/jquery-jvectormap.js"></script>
<!-- Sliders Plugin -->
<script src="./assets/js/nouislider.min.js"></script>
<!--  Google Maps Plugin    -->
<!--<script src="./assets/js/jquery.select-bootstrap.js"></script>-->
<!-- Select Plugin -->
<script src="./assets/js/jquery.select-bootstrap.js"></script>
<!--  DataTables.net Plugin    -->
<script src="./assets/js/jquery.datatables.js"></script>
<!-- Sweet Alert 2 plugin -->
<script src="./assets/js/sweetalert2.js"></script>
<!--	Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="./assets/js/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin    -->
<script src="./assets/js/fullcalendar.min.js"></script>
<!-- TagsInput Plugin -->
<script src="./assets/js/jquery.tagsinput.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="./assets/js/material-dashboard.js"></script>
<!-- Warmod+ javascript methods -->
<script src="./assets/js/warmod_plus.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#datatables').DataTable({
            "pagingType": "full_numbers",
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }

        });
		
		$('#weapontable').DataTable({
            "pagingType": "full_numbers",
            "lengthMenu": [
                [10, 25, -1],
                [10, 25, "All"]
            ],
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }

        });
    });
</script>
<?php
	$result=mysqli_query($link, "
	select * FROM ".$stats_table." WHERE steam_id_64='".$id."' ORDER BY match_id DESC LIMIT 7");
	mysqli_set_charset ($link , "utf-8");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		$i = $numb;
		$rws_change[$i] = $rws2;
		$high = $rws2;
		while ($row = mysqli_fetch_array($result))
		{
			$mid[$i] = $row['match_id'];
			
			$rws_change[$i-1] = $rws_change[$i] - $row['rws'];
		
			if($rws_change[$i-1] > $rws_change[$i] )	$high = $rws_change[$i-1];
			$i--;
		}
		
		$high+=3;
		
		echo "
		<script type='text/javascript'>
		$(document).ready(function() {
			var rwschart = new Chartist.Line('#RWSChart', {
			labels: [";
		for($i=1; $i<=$numb; $i++)
		{
			echo $mid[$i];
			if($i != $numb)	echo ",";
		}
		echo "],series: [[";
		for($i=1; $i<=$numb; $i++)
		{
			echo $rws_change[$i];
			if($i != $numb)	echo ",";
		}
		echo "]]
			},{
				lineSmooth: Chartist.Interpolation.cardinal({
					tension: 0
				}),
				chartPadding: { top: 0, right: 0, bottom: 0, left: 0},
				high: ".$high.",
				classNames: {
					point: 'ct-point ct-white',
					line: 'ct-line ct-white'
				},plugins: [
					Chartist.plugins.tooltip()
				]
			});
			
			md.startAnimationForLineChart(rwschart);
		});
		</script>";
	}
	else echo "
		<script type='text/javascript'>
		$(document).ready(function() {
			var rwschart = new Chartist.Line('#RWSChart', {
			labels: [0,0,0,0,0,0,0],series: [[0,0,0,0,0,0,0]]
			},{
			lineSmooth: Chartist.Interpolation.cardinal({
                tension: 0
            }),
            chartPadding: { top: 0, right: 0, bottom: 0, left: 0},
            classNames: {
                point: 'ct-point ct-white',
                line: 'ct-line ct-white'
            }});
			
			md.startAnimationForLineChart(rwschart);
		});
		</script>";
		
	if($hits > 0)
	{
		echo "
		<script type='text/javascript'>
		$(document).ready(function() {
			new Chartist.Pie('#HitboxPie', {
			series: [
			{ meta: 'Head', value: ".$head.",},
			{ meta: 'Chest', value: ".$chest."},
			{ meta: 'Stomach', value: ".$stomach."},
			{ meta: 'Left Arm', value: ".$left_arm."},
			{ meta: 'Right Arm', value: ".$right_arm."},
			{ meta: 'Left Leg', value: ".$left_leg."},
			{ meta: 'Right Leg', value: ".$right_leg."}]
			},{
				showLabel: false,
				plugins: [
					Chartist.plugins.tooltip()
				]
			});
		});
		</script>";
	}
 ?>
</html>
<?php mysqli_close ($link); ?>