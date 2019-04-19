<?php
	require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/SourceQuery.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/view_mode.php";
	require_once "assets/inc/session.php";
	$gi = geoip_open("./assets/inc/GeoIP.dat",GEOIP_STANDARD);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Welcome To Warmod+</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
   
	<meta property="og:title" content="Warmod+">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Warmod+">
	<meta property="og:image" content="./assets/img/logo.png">
	<meta property="og:description" content="Best CSGO TP/Mix Service In Taiwan!">

	<!-- Bootstrap core CSS     -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
   
    <!--  Material Dashboard CSS    -->
    <link href="./assets/css/material-dashboard.css" rel="stylesheet" />
	
	<!--  Warmod+ CSS    -->
    <link href="./assets/css/warmod_plus.css" rel="stylesheet" />
    
	<!--     Fonts and icons     -->
    <link href="./assets/css/fontawesome-all.css" rel="stylesheet" />
    <link href="./assets/css/google-roboto-300-700.css" rel="stylesheet" />
	
	<link rel="manifest" href="./assets/manifest.json">
</head>

<body>
    <div class="wrapper">
		<div class="sidebar" data-active-color="rose" data-background-color="black" data-image="./assets/img/sidebar-dust2.jpg">
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
                    <li class="active">
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
                        <a class="navbar-brand" href="./index.php"> Dashboard </a>
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
					<?php
					if($server_list)
					{
						echo "
					<div class='row'>
                        <div class='col-md-12'>
                            <div class='card'>
                                <div class='card-header card-header-icon' data-background-color='purple'>
									<i class='material-icons'>assignment</i>
								</div>
								<div class='card-content'>
									<h4 class='card-title'>Official Servers</h4>
									<div class='material-datatables'>
									<table id='datatables' class='table table-striped table-no-bordered table-hover' cellspacing='0' width='100%' style='width:100%'>
										<thead class='thead-inverse'>
											<tr>
												<th>Name</th>
												<th>Map</th>
												<th>Players</th>
												<th>Connect</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>Name</th>
												<th>Map</th>
												<th>Players</th>
												<th>Connect</th>
											</tr>
										</tfoot>
										<tbody>";

										$result=mysqli_query($link, "SELECT * FROM ".$server_table." WHERE official = '1' AND enabled = '1' ORDER BY id DESC");
										mysqli_set_charset ($link , "utf-8"); 
										$numb=mysqli_num_rows($result); 
										while ($row = mysqli_fetch_array($result)) 
										{
											$server = new SourceQuery($row['ip'],$row['port']);
											$infos  = $server->getInfos();
											if($infos['mod'] != null)
											{
												echo "
												<tr>
												<td>".$infos['name']."</td>
												<td>".$infos["map"]."</td>
												<td>".$infos["players"]."/".$infos["places"]."</td>
												<td><a href ='steam://connect/".$row['ip'].":".$row['port']."'>CONNECT</a></td>
												</tr>";
											}
										}
										
										echo"
										</tbody>
									</table>
									</div>
								</div>
                            </div>
						</div>
					</div>	";
					}
					?>
					<h3>Latest Matches</h3>
					<br>
					<?php
						$result=mysqli_query($link, "SELECT ".$result_table.".*, 
						IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
						IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
						FROM ".$result_table." order by match_id DESC limit 3");
						mysqli_set_charset ($link , "utf-8");
						$numb=mysqli_num_rows($result); 

						if (!empty($numb)) 
						{ 
							echo "<div class='row'>";
							
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
																
								date_default_timezone_set('UTC');
																
								$match_start=$row['match_start']; 
								$timestamp = strtotime($match_start);
								$dt = new DateTime("now", new DateTimeZone('Asia/Taipei'));
								$dt->setTimestamp($timestamp);
								$timestamp = $dt->format('Y/m/d g:i:s A');
								
								$mappic="./assets/img/maps/".$map.".png";
								
								if(empty($ct_name) || $ct_name==" ")	$ct_name="Unknown";
								if(empty($t_name) || $t_name==" ")	$t_name="Unknown";
								
								if(empty($competition) || $competition==" ")	$competition="Unknown";
								if(empty($event) || $event==" ")	$event="Unknown";

								echo "
								<div class='col-lg-4 col-md-6 col-sm-6'>
									<a href='./showmatch.php?id=".$match_id."'>
									<div class='card card-product'>
										<div class='card-image' data-header-animation='true'>";
										
								if(file_exists($mappic))	echo "<img class='img' src='".$mappic."' />";
								else echo "<img class='img' src='./assets/img/maps/unknown.png' />";
								
								echo"</div>
										<div class='card-content'>
											<h4 class='card-title'>
													<font color='#2793e8'>".$ct_name."&nbsp;-&nbsp;".$ct_overall_score."</font>
													&nbsp;&nbsp;&nbsp;VS&nbsp;&nbsp;&nbsp;
													<font color='#cc181e'>".$t_overall_score."&nbsp;-&nbsp;".$t_name."</font>
											</h4>
											<div class='card-description'>
												<font color='#343d46'>Event:&nbsp;".$competition."&nbsp;-&nbsp;".$event."&nbsp;&nbsp;&nbsp;</font><br>
												<font color='#343d46'>Match ID:&nbsp;".$match_id."</font>
											</div>
										</div>
										<div class='card-footer'>
											<div class='stats'>
												<i class='material-icons'>access_time</i>&nbsp;&nbsp;Match Starts at ".$timestamp."
											</div>
										</div>
									</div>
									</a>
								</div>
								";
							}
							echo "</div>";
						}
						else
						{
							echo 
							"<div class='row'>
								<div class='col-lg-3 col-md-6 col-sm-6'>
									<div class='card card-product'>
										<div class='card-image' data-header-animation='true'>
												<img class='img' src='./assets/img/maps/unknown.png' />
										</div>
										<div class='card-content'>
											<h4 class='card-title'>
												<i class='material-icons text-danger'>warning</i> Can not found a match.
											</h4>
											<div class='card-description'>
												Please start a match in server first.
											</div>
										</div>
									</div>
								</div>
							</div>";
						}
					?>
					<h3>Top Players</h3>
					<br>
					<div class="row">
						<?php
							$result=mysqli_query($link, "
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
									".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR' AND `".$player_table."`.rws > 0
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
							ORDER BY
								rws
							DESC LIMIT 3;");
							mysqli_set_charset ($link , "utf-8");
							$numb=mysqli_num_rows($result); 

							if (!empty($numb)) 
							{ 
								$i=1;
								
								while ($row = mysqli_fetch_array($result))
								{
									// get steam id and name
									$steam = $row['steam_id_64'];
										
									// get name and avatar from steam api
									$name = getName($steam);
									$avatar = getAvatarFull($steam);
										
									$rws = $row['rws'];
									if($rws != 0.00)	$rws = floor_dec($rws,2);
									
									$rank = $row['rank'];
									
									$kills = $row['kills'];
									$deaths = $row['deaths'];
									$hits = $row['hits'];
									$shots = $row['shots'];
											
									if($shots == 0) $ac = 0;
									else	$ac = round($hits/$shots, 2);
									
									if($deaths == 0)	$kdr = round($kills/1, 2);
									else	$kdr = round($kills/$deaths, 2);
									
									echo "
									 <div class='col-md-4'>
										<div class='card card-stats'>";
											
									if($rank==1)
									{
										echo "<div class='card-header' data-background-color='rose' data-header-animation='true'>
												<div class='ribbon'><span>1 ST</span></div>";
									}
									else if($rank==2)
									{
										echo "<div class='card-header' data-background-color='blue' data-header-animation='true'>
												<div class='ribbon'><span>2 ND</span></div>";
									}
									else if($rank==3)
									{
										echo "<div class='card-header' data-background-color='orange' data-header-animation='true'>
												<div class='ribbon'><span>3 RD</span></div>";
									}
											
									echo "
										<img src='".$avatar."' style='width:94px !important;height:94px !important' class='img' />
											</div>
											<div class='card-content'>
											<p class='category'><font color='#d9534f'>RWS:&nbsp;".$rws."</font></p>
												<a href='./showplayer.php?id=".$steam."'><h3 class='card-title'>".$name."</h3></a>
											</div>
											<div class='card-footer'>
												<div class='stats pull-right'>
												<p class='category'><font color='#428bca'>Kill:&nbsp;".$kills."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deaths:&nbsp;".$deaths."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KDR:&nbsp;".$kdr."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AC:&nbsp;".$ac."%</font></p>
												</div>
											</div>
										</div>
									</div>";
									
									$i++;
								}
							}
							else
							{
								echo" <div class='col-md-4'>
										<div class='card card-stats'>

										<div class='card-header' data-background-color='rose' data-header-animation='true'>
												<img class='img' src='./assets/img/default-avatar.png' style='width:94px !important;height:94px !important' />
										</div>
										
										<div class='card-content'>
											<p class='category'><font color='#d9534f'>RWS:&nbsp;0.00</font></p>
												<h3 class='card-title'>No Name</h3>
											</div>
											<div class='card-footer'>
												<div class='stats pull-right'>
												<p class='category'><font color='#428bca'>Kill:&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deaths:&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KDR:&nbsp;0.00&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AC:&nbsp;0.00%</font></p>
												</div>
											</div>
										</div>
									</div>
								";
							}
							?>
                </div>
				<br>
				<h3>Top Teams</h3>
					<?php	
					$result=mysqli_query($link, "
					SELECT name, leader, steam_url,
					   b.rank,
					   wlr,
					   win,
					   lose,
					   draw,
					   id, logo
					FROM
					   (
						  SELECT
							 a.rank AS 'Rank',
							 a.wlr,
							 a.name,
							 a.leader,
							 a.steam_url,
							 a.win,
							 a.lose,
							 a.draw,
							 a.id,
							 a.logo
						  FROM
							 (
								SELECT
								   stats.name,
								   stats.steam_url,
								   stats.leader,
								   stats.win,
								   stats.lose,
								   stats.draw,
								   stats.id,
								   stats.logo,
								   Ifnull(TRUNCATE (stats.win / stats.lose, 2), stats.win) AS 'wlr',
								   @prev := @curr,
								   @curr := Ifnull(TRUNCATE (stats.win / stats.lose, 2), stats.win),
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
										 ".$team_table.".name,
										 ".$team_table.".id,
										 ".$team_table.".steam_url,
										 ".$team_table.".leader,
										 ".$team_table.".logo,
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
					ORDER BY wlr DESC LIMIT 3");
					mysqli_set_charset ($link , "utf-8"); 
					$numb=mysqli_num_rows($result); 
					
					echo "<div class='row'>";
							
					if (!empty($numb)) 
					{ 
						$i = 1;
						while ($row = mysqli_fetch_array($result))
						{
							$team_steam[$i] = $row['steam_url'];
							$team_logo[$i] = "./assets/img/teams/".$row['logo']."";
							
							if($team_logo[$i] == "./assets/img/teams/")
							$team_logo[$i] = "./assets/img/teams/unknown.png";
							
							$team_leader[$i] = $row['leader'];
							$team_id[$i] = $row['id'];
							$team_name[$i] =  $row['name'];
							$team_win[$i] = $row['win'];
							$team_lose[$i] = $row['lose'];
							$team_draw[$i] = $row['draw'];
							$team_wlr[$i] = $row['wlr'];
							$team_rank[$i] = $row['rank'];
							
							$i++;
						}
						$i -= 1;
						
						$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
						for ($j=1;$j<=$i;$j++) 
						{
							if(!empty($team_leader[$j]))
							{
								if($j == 1)	$url.="".$team_leader[$j]."";
								else $url.=",".$team_leader[$j]."";
							}
							else $personname[$team_leader[$i]] = "Unknown";
						}
						$cURL = curl_init();
						curl_setopt($cURL, CURLOPT_URL, $url);
						curl_setopt($cURL, CURLOPT_HTTPGET, true);
						curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
						$result = curl_exec($cURL);
						curl_close($cURL);
						$json = json_decode($result, true);
						
						foreach($json['response']['players'] as $item)
						{
							$personname[$item['steamid']] = $item['personaname'];
						}
						
						for($j=1;$j<=$i;$j++) 
						{
							echo "
							<div class='col-md-4'>
							<div class='card card-stats'>";
								
							if($team_rank[$j] == 1)	echo "<div class='card-header' data-background-color='rose' data-header-animation='true'><div class='ribbon'><span>1 ST</span></div>";
							elseif($team_rank[$j] == 2) echo "<div class='card-header' data-background-color='blue' data-header-animation='true'><div class='ribbon'><span>2 ND</span></div>";
							elseif($team_rank[$j] == 3) echo "<div class='card-header' data-background-color='orange' data-header-animation='true'><div class='ribbon'><span>3 RD</span></div>";
							else echo "<div class='card-header' data-background-color='green' data-header-animation='true'>";
							
							echo "
								<img src='".$team_logo[$j]."' style='width:94px !important;height:94px !important' class='img' />
									</div>
									<div class='card-content'>
									<p class='category'><font color='#d9534f'>Win / Lose : ".$team_wlr[$j]."</font></p>
										<a href='./showteam.php?id=".$team_id[$j]."'><h3 class='card-title'>".$team_name[$j]."</h3></a>
									</div>
									<div class='card-footer'>
										<div class='stats pull-right'>";
										if(!empty($team_leader[$j]))	echo"
										<p class='category'><font color='#428bca'>".$team_win[$j]." W / ".$team_lose[$j]." L / ".$team_draw[$j]." D&emsp;Leader: ".$personname[$team_leader[$j]]."</font></p>";
										else echo"	
												<p class='category'><font color='#428bca'>".$team_win[$j]." W / ".$team_lose[$j]." L / ".$team_draw[$j]." D&emsp;Leader: Unknown</font></p>";
											echo"
										</div>
									</div>
								</div>
							</div>";
						}
					}
					else
					{
						echo" <div class='col-md-4'>
								<div class='card card-stats'>

								<div class='card-header' data-background-color='rose' data-header-animation='true'>
										<img class='img' src='./assets/img/default-avatar.png' style='width:94px !important;height:94px !important' />
								</div>
									
								<div class='card-content'>
									<p class='category'><font color='#d9534f'>0 W / 0 L / 0 D</font></p>
										<h3 class='card-title'>No Name</h3>
									</div>
									<div class='card-footer'>
										<div class='stats pull-right'>
										<p class='category'><font color='#428bca'>Leader: Unknown</font></p>
										</div>
									</div>
								</div>
							</div>
						";
					}
					echo "</div>";
					?>
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
<script src="./assets/js/scrollreveal.js"></script>
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
		
		window.sr = ScrollReveal();
		sr.reveal('.card', { duration: 1000 }, 50);
    });
</script>
</html>
