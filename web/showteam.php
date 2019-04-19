<?php
    require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/view_mode.php";
	require_once "assets/inc/session.php";
	$gi = geoip_open("assets/inc/GeoIP.dat",GEOIP_STANDARD);
	
	// prevent bug
	if (isset($_GET['id']) && !empty($_GET['id']))	$id = $_GET['id'];
	else {echo "<script type='text/javascript'>alert('Invalid Team ID');window.history.back();</script>"; exit;}
	
	if(!is_numeric($id)){echo "<script type='text/javascript'>alert('Invalid Steam64 ID');window.history.back();</script>"; exit;}
	
	$team_name =  "";
	$team_win = 0;
	$team_lose = 0;
	$team_draw = 0;
	$team_wlr = 0.00;
	$team_rank = "Unranked";
	$team_status = "";
	
	$result=mysqli_query($link, "SELECT * FROM ".$team_table." WHERE id= ".$id); 
	mysqli_set_charset ($link , "utf-8"); 
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		while ($row = mysqli_fetch_array($result))
		{
			$team_steam = $row['steam_url'];
			$team_facebook = $row['fb'];
			$team_twitter = $row['twitter'];
			$team_youtube = $row['youtube'];
			$team_twitch = $row['twitch'];
			//$team_logo = getGroupAvatar($team_steam);
			$team_logo = "./assets/img/teams/".$row['logo']."";
			if($team_logo == "./assets/img/teams/" || !file_exists($team_logo))	$team_logo = "./assets/img/teams/unknown.png";
			//$team_logo_game = $row['logo_game'];
			$team_leader = $row['leader'];
			
			if(empty($team_leader))	$team_leader_avatar = "./assets/img/default-avatar.png";
			else $team_leader_avatar = getAvatarFull($team_leader);
			
			$team_leader_name = getName($team_leader);
			$team_id = $row['id'];
			$team_name =  $row['name'];
		}
	}
	else {echo "<script type='text/javascript'>alert('Invalid Team ID');window.history.back();</script>"; exit;}
	
	$result=mysqli_query($link, "
	SELECT *
	FROM
	   (
		  SELECT
			 a.*
		  FROM
			 (
				SELECT
				    stats.*,
				   Ifnull(TRUNCATE (stats.win / stats.win + stats.lose + stats.draw, 2), stats.win) AS 'wlr',
				   @prev := @curr,
				   @curr := Ifnull(TRUNCATE (stats.win / stats.win + stats.lose + stats.draw, 2), stats.win),
				   @rank := IF(@prev = @curr, @rank, @rank + 1) AS rank 
				FROM
				   (
					  SELECT
						 @curr := NULL,
						 @prev := NULL,
						 @rank := 0 
				   )
				   s,
				   (
					  SELECT
						 ".$team_table.".*,
						 Count( 
						 CASE
							WHEN
							   (
								  ".$result_table.".t_id = ".$team_table.".id 
								  AND ".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".ct_id = ".$team_table.".id
								  AND ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'win', Count( 
						 CASE
							WHEN
							   (
								  ".$result_table.".ct_id = ".$team_table.".id
								  AND ".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".t_id = ".$team_table.".id 
								  AND ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'lose', COUNT( 
						 CASE
							WHEN
							   (
								  ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score 
								  AND ".$result_table.".ct_id = ".$team_table.".id 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score 
								  AND ".$result_table.".t_id = ".$team_table.".id 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'draw' 
					  FROM
						 ".$result_table.", ".$team_table." 
					  GROUP BY
						 ".$team_table.".name 
				   )
				   stats 
				ORDER BY
				   wlr DESC 
			 )
			 a 
	   )
	   b
	WHERE id=".$id." ORDER BY wlr DESC"); 
	mysqli_set_charset ($link , "utf-8"); 
	$numb=mysqli_num_rows($result);
	if (!empty($numb)) 
	{	
		while ($row = mysqli_fetch_array($result))
		{
			$team_steam = $row['steam_url'];
			$team_facebook = $row['fb'];
			$team_twitter = $row['twitter'];
			$team_youtube = $row['youtube'];
			$team_twitch = $row['twitch'];
			//$team_logo = getGroupAvatar($team_steam);
			$team_logo = "./assets/img/teams/".$row['logo']."";
			if($team_logo == "./assets/img/teams/" || !file_exists($team_logo))	$team_logo = "./assets/img/teams/unknown.png";
			//$team_logo_game = $row['logo_game'];
			$team_leader = $row['leader'];
			
			if(empty($team_leader))	$team_leader_avatar = "./assets/img/default-avatar.png";
			else $team_leader_avatar = getAvatarFull($team_leader);
			
			$team_leader_name = getName($team_leader);
			$team_id = $row['id'];
			$team_name =  $row['name'];
			$team_win = $row['win'];
			$team_lose = $row['lose'];
			$team_draw = $row['draw'];
			$team_wlr = $row['wlr'];
			$team_rank = $row['rank'];
			$team_status = $row['status'];
		}
	}
	
	// memeber_numb因為上面就要用到所以放上面
	$member_result=mysqli_query($link, "
	SELECT rank,
		   steam_id_64,
				rws,
				team,
				kills,
				deaths,
				hits,
				shots
	FROM
		(
		SELECT
			a.rank AS 'Rank',
		   a.steam_id_64,
				a.rws,
				a.team,
				a.kills,
				a.deaths,
				a.hits,
				a.shots
		FROM
			(
			SELECT
				stats.steam_id_64,
				stats.rws,
				stats.team,
				stats.kills,
				stats.deaths,
				stats.hits,
				stats.shots,
			@prev := @curr,
			@curr := stats.rws,
		@rank := IF(@prev = @curr, @rank, @rank + 1) AS rank
	FROM
		(
		SELECT
			@curr := NULL,
			@prev := NULL,
			@rank := 0
	) s,
	(
		SELECT
			".$player_table.".steam_id_64,
			".$player_table.".rws,
			".$player_table.".team,
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".kills,
					0
				)
			) AS 'kills',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".deaths,
					0
				)
			) AS 'deaths',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".hits,
					0
				)
			) AS 'hits',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".shots,
					0
				)
			) AS 'shots'
		FROM
			`".$stats_table."`,
			`".$player_table."`
		WHERE
			".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR'
		GROUP BY
			".$player_table.".steam_id_64
		ORDER BY
			".$player_table.".rws
		DESC
	) stats
	ORDER BY
		rws
	DESC
		) a
	) b
	WHERE team='".$team_id."'
	ORDER BY
		rws
	DESC"); 
	
	mysqli_set_charset ($link , "utf-8");
	$member_numb=mysqli_num_rows($member_result); 
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Warmod+ | Teams</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
   
    <meta property="og:site_name" content="Warmod+">
    <meta property="og:title" content="Warmod+ | Team:<?=$team_name?>"/>
	<meta property="og:type" content="website">
	<meta property="og:image" content="<?=$team_logo?>">
	<meta property="og:description" content="Warmod+ Team:<?=$team_name?>"/>
    
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
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-active-color="rose" data-background-color="black" data-image="./assets/img/sidebar-train.jpg">
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
					<li>
                        <a href="players.php">
                            <i class="material-icons">person</i>
                            <p>Players</p>
                        </a>
                    </li>
					<li class="active">
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
                        <a class="navbar-brand" href="./matches.php" style='display:inline;'><i class="material-icons">keyboard_arrow_left</i>Back To List</a>
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
								   <img src="<?=$team_logo?>"/>
								</div>
								<div class="card-content" style="text-align:left !important">
									<font color="#999999"><h3 style="margin:0 0 0;">&nbsp;&nbsp;&nbsp;Team Name</h3></font>
									<h2 class="card-title" style="margin-top: 0px;">&nbsp;&nbsp;<?=$team_name?></h2>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-6">
							<div class="card card-plain card-stats card-wmprofile">
								<div class="card-header" data-background-image="" data-header-animation="false">
								  <img src="<?=$team_leader_avatar?>" style="width:124px; border-radius:3px; border-radius:3px;"/>
								</div>
								<div class="card-content" style="text-align:left !important">
								   <font color="#999999"><h3 style="margin:0 0 0;">&nbsp;&nbsp;&nbsp;Leader</h3></font>
									<?php
										if(empty($team_leader)) echo "<h2 class='card-title' style='margin-top: 0px;'>&nbsp;&nbsp;Unknown</h2>";
										else echo	"<a href='./showplayer.php?id=".$team_leader."'><h2 class='card-title' style='margin-top: 0px;'>&nbsp;&nbsp;".$team_leader_name."</h2></a>";
									?>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-8 col-sm-12">
							<div class="card card-plain card-stats">
								<div class="card-content" style="text-align:center !important">
									&emsp;
									<?php
										if(isset($team_steam) && !empty($team_steam))
											echo "<button class='btn btn-just-icon btn-github' onclick='window.open(\"".$team_steam."\");return false;'>
														<i class='fab fa-steam-symbol'> </i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-steam-symbol'> </i>
													</button>&emsp;";
										if(isset($team_fb) && !empty($team_fb))
											echo "<button class='btn btn-just-icon btn-facebook' onclick='window.open(\"".$team_fb."\");return false;'>
														<i class='fab fa-facebook'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-facebook'></i>
													</button>&emsp;";
										if(isset($team_twitter) && !empty($team_twitter))
											echo "<button class='btn btn-just-icon btn-twitter' onclick='window.open(\"".$team_twitter."\");return false;'>
														<i class='fab fa-twitter'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-twitter'></i>
													</button>&emsp;";
										if(isset($team_twitch) && !empty($team_twitch))
											echo "<button class='btn btn-just-icon btn-primary' onclick='window.open(\"".$team_twitch."\");return false;'>
														<i class='fab fa-twitch'></i>
													</button>&emsp;";
										else
											echo "<button class='btn btn-just-icon btn-twitch'>
														<i class='fab fa-twitch'></i>
													</button>&emsp;";
										if(isset($team_youtube) && !empty($team_youtube))
											echo "<button class='btn btn-just-icon btn-youtube' onclick='window.open(\"".$team_youtube."\");return false;'>
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
									<h3 class="card-title"><?=$team_rank?></h3>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6">
							<div class="card card-stats">
								<div class="card-header" data-background-color="purple" data-header-animation="true">
									<i class="pe-7s-graph3"></i>
								</div>
								<div class="card-content">
									<p class="category">Wins/Matches</p>
									<h3 class="card-title"><?=$team_wlr?></h3>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6">
							<div class="card card-stats">
								<div class="card-header" data-background-color="green" data-header-animation="true">
									<i class="material-icons">public</i>
								</div>
								<div class="card-content">
									<p class="category">Matches</p>
									<h3 class="card-title"><?php echo "".$team_win." W / ".$team_lose." L / ".$team_draw." D";?></h3>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6">
							<div class="card card-stats">
								<div class="card-header" data-background-color="blue" data-header-animation="true">
								   <i class="material-icons">group</i>
								</div>
								<div class="card-content">
									<p class="category">Members</p>
									<h3 class="card-title"><?=$member_numb?></h3>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-icon" data-background-color="red">
									<i class="material-icons">person</i>
								</div>
								<div class="card-content">
									<h4 class="card-title">Members</h4>
									<div class="material-datatables">
										<table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
											<thead class="thead-inverse">
												<tr>
													<th>Role</th>
													<th>Rank</th>
													<th>Name</th>
													<th>RWS</th>
													<th>Kills</th>
													<th>Deaths</th>
													<th>KDR</th>
													<th>AC</th>
													<th>Profile</th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th>Role</th>
													<th>Rank</th>
													<th>Name</th>
													<th>RWS</th>
													<th>Kills</th>
													<th>Deaths</th>
													<th>KDR</th>
													<th>AC</th>
													<th>Profile</th>
												</tr>
											</tfoot>
											<tbody>
											<?php
											if (!empty($member_numb)) 
											{
												$i=1;
												while ($member_row = mysqli_fetch_array($member_result))
												{
													// get steam id and name
													$steam[$i] = $member_row['steam_id_64'];
													
													if($steam[$i] == $team_leader)	$role[$i] = "Leader";
													else $role[$i] = "";
															
													$rws[$i] = $member_row['rws'];
													if($rws[$i] != 0.00)	$rws[$i] = floor_dec($rws[$i],2);
														
													$kills[$i] = $member_row['kills'];
													$deaths[$i] = $member_row['deaths'];
													$hits[$i] = $member_row['hits'];
													$shots[$i] = $member_row['shots'];
																
													if($shots[$i] == 0) $ac[$i] = 0;
													else	$ac[$i] = round($hits[$i]/$shots[$i], 2);
														
													if($deaths[$i] == 0)	$kdr[$i] = round($kills[$i]/1, 2);
													else	$kdr[$i] = round($kills[$i]/$deaths[$i], 2);
													
													if($rws[$i] > 0)	$rank[$i] = $member_row['rank'];
													else $rank[$i] = "Unranked";
													
													$i++;
												}
												
												$i-=1;
												
												$query_count=ceil($i/100);
												
												// $i = 資料筆數
												// $query_count = api總共要查詢的次數
												// $k=已查詢的次數
												// $j = api查詢次數loop 
												// $l = api查詢url的steamid筆數
												for ($j=1;$j<=$query_count;$j++) 
												{
													$url[$j] = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
													
													// 如果只要查一次
													if($query_count==1)
													{
														for ($l=1;$l<=$i;$l++) 
														{
															if($l == 1)	$url[$j].="".$steam[$l]."";
															else $url[$j].=",".$steam[$l]."";
														}
													}
													// 如果多於一次
													else
													{
														// 第一次查詢
														if($j == 1)
														{
															for($l=1;$l<=100;$l++) 
															{
																if($l == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
															}
														}
														// 最後一次查詢
														elseif($j == $query_count)
														{
															$k= 1;
															for($l=($j*100)-99;$l<=$i;$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
														// 中間查詢
														else
														{ 
															$k=1;
															for($l=($j*100)-99;$l<=($j*200);$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
													}

													$cURL = curl_init();
													curl_setopt($cURL, CURLOPT_URL, $url[$j]);
													curl_setopt($cURL, CURLOPT_HTTPGET, true);
													curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
													$result = curl_exec($cURL);
													curl_close($cURL);
													$json = json_decode($result, true);
													
													foreach($json['response']['players'] as $item)
													{
														$personname[$item['steamid']] = $item['personaname'];
														//$avatarfull[$item['steamid']] = $item['avatarfull'];
													}
												}

												for ($j=1;$j<=$i;$j++) 
												{
													echo "
													<tr height='40px'>
														<td>".$role[$j]."</td>
														<td>".$rank[$j]."</td>
														<td>".$personname[$steam[$j]]."</td>
														<td>".$rws[$j]."</td>
														<td>".$kills[$j]."</td>
														<td>".$deaths[$j]."</td>
														<td>".$kdr[$j]."</td>
														<td>".$ac[$j]."</td>
														<td><a href='./showplayer.php?id=".$steam[$j]."'>View</a></td>
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
                                <div class="card-header card-header-icon" data-background-color="rose">
									<i class="material-icons">public</i>
								</div>
								<div class="card-content">
									<h4 class="card-title">Matches</h4>
									<div class="material-datatables">
										<table id="datatables2" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
											<thead class="thead-inverse">
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
											SELECT
												".$result_table.".*,
											IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
											IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name,
												(
													CASE WHEN(
														(
															".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND ".$result_table.".t_id = '".$team_id."'
														) OR(
															".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND ".$result_table.".ct_id = '".$team_id."'
														)
													) THEN 'win' WHEN(
														(
															".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND ".$result_table.".ct_id = '".$team_id."'
														) OR(
															".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND ".$result_table.".t_id = '".$team_id."'
														)
													) THEN 'lose' ELSE 'draw'
												END
											) AS 'result'
											FROM
												".$result_table."
											WHERE ".$result_table.".t_id = '".$team_id."' OR ".$result_table.".ct_id = '".$team_id."'
											ORDER BY
												".$result_table.".match_id
											DESC");

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
													$mresult=$row['result']; 
																					
													date_default_timezone_set('UTC');
																					
													$match_start=$row['match_start']; 
													$timestamp = strtotime($match_start);
													$dt = new DateTime("now", new DateTimeZone('Asia/Taipei'));
													$dt->setTimestamp($timestamp);
													$timestamp = $dt->format('Y/m/d g:i:s A');
													
													/*													
													if(empty($ct_id) || $ct_id==" ")	$ct_id="Unknown";
													if(empty($t_id) || $t_id==" ")	$t_id="Unknown";
													
													if(empty($competition) || $competition==" ")	$competition="Unknown";
													if(empty($event) || $event==" ")	$event="Unknown";
													*/
													
													$mappic="./assets/img/maps/".$map.".png";
													
													echo "
													<tr height='40px'>
														<td>".$match_id."</td>
														<td>".$timestamp."</td>
														<td>".$competition."</td>
														<td>".$event."</td>
														<td>".$map."</td>
														<td>".$t_name."</td>
														<td>".$t_overall_score." VS ".$ct_overall_score."</td>
														<td>".$ct_name."</td>";
													
													if($mresult == "win")	echo "<td><font color='#5cb85c'>Win</font></td>";
													elseif($mresult == "lose")	echo "<td><font color='#d9534f'>Lose</font></td>";
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
<?php mysqli_close ($link); ?>
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


        var table = $('#datatables').DataTable();
    });
	$(document).ready(function() {
        $('#datatables2').DataTable({
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


        var table = $('#datatables').DataTable();
    });
</script>
<?php
if($team_status == "delete")
	echo "
		<script type='text/javascript'>
			$(document).ready(function() {
				wm.showSwal(\"disbanded-team\", \"\", \"\")
		});
		</script>
	";
?>
</html>