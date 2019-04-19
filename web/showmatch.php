<?php
    require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/view_mode.php";
	require_once "assets/inc/session.php";
	$gi = geoip_open("assets/inc/GeoIP.dat",GEOIP_STANDARD);
	
	// prevent bug, if match id not set
	if (isset($_GET['id']) && !empty($_GET['id']))	$matchid = $_GET['id'];
	else {echo "<script type='text/javascript'>alert('Invalid Match ID');window.history.back();</script>"; exit;}
	
	if(!is_numeric($matchid)){echo "<script type='text/javascript'>alert('Invalid Steam64 ID');window.history.back();</script>"; exit;}
	
	// prevent bug, if match id invalid
	$result=mysqli_query($link, "select * from `".$result_table."` where match_id = ".$matchid."");
	$numb=mysqli_num_rows($result);
	if($numb<1){ echo "<script type='text/javascript'>alert('Invalid Match ID');window.history.back();</script>"; exit;}

	$result=mysqli_query($link, "
	SELECT ".$result_table.".*, 
	IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
	IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
	FROM ".$result_table." WHERE `match_id` = ".$matchid."");
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
			
			date_default_timezone_set('UTC');
			
			$match_start=$row['match_start'];
			$timestamp = strtotime($match_start);
			$dt = new DateTime("now", new DateTimeZone('Asia/Taipei'));
			$dt->setTimestamp($timestamp);
			$timestamp = $dt->format('Y/m/d g:i:s A');
			
			$match_end=$row['match_end'];
			
			$mappic = "./assets/img/maps/".$map.".jpg";
			if(!file_exists($mappic))	$mappic = "./assets/img/csgo.jpg";
		}
	}
	
	if(empty($ct_name) || is_null($ct_name))	{$ct_name="Unknown"; $ct_logo="./assets/img/teams/unknown.png"; $ct_id="0";}
	else
	{
		$result=mysqli_query($link, "SELECT * FROM `".$team_table."` WHERE `name` = '".$ct_name."'");
		mysqli_set_charset ($link , "utf-8");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{
			while ($row = mysqli_fetch_array($result))
			{
				$ct_id = $row['id'];
				//$ct_steam = $team_row['steam_url'];
				$ct_logo = "./assets/img/teams/".$row['logo']."";
				if($ct_logo == "./assets/img/teams/" || !file_exists($ct_logo))	$ct_logo = "./assets/img/teams/unknown.png";
			}
		}
		else {$ct_name="Unknown"; $ct_logo="./assets/img/teams/unknown.png"; $ct_id="0";}
	}
	if(empty($t_name) || is_null($t_name))	{$t_name="Unknown"; $t_logo="./assets/img/teams/unknown.png"; $t_id="0";}
	else
	{
		$result=mysqli_query($link, "SELECT * FROM `".$team_table."` WHERE `name` = '".$t_name."'");
		mysqli_set_charset ($link , "utf-8");
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{
			while ($row = mysqli_fetch_array($result))
			{
				$t_id = $row['id'];
				//$t_steam = $team_row['steam_url'];
				$t_logo = "./assets/img/teams/".$row['logo']."";
				if($t_logo == "./assets/img/teams/" || !file_exists($t_logo))	$t_logo = "./assets/img/teams/unknown.png";
			}
		}
		else {$t_name="Unknown"; $t_logo="./assets/img/teams/unknown.png"; $t_id="0";}
	}
	
	if(empty($competition) || $competition==" ")	$competition="";
	if(empty($event) || $event==" ")	$event="";
	
	$rounds=$ct_overall_score+$t_overall_score;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Warmod+ | <?php echo "".$ct_name." VS ".$t_name.""; ?></title>
	
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
	
	<meta property="og:site_name" content="Warmod+">
    <meta property="og:title" content="Warmod+ | <?php echo "".$ct_name." VS ".$t_name.""; ?>"/>
	<meta property="og:type" content="website">
	<meta property='og:image' content='<?=$mappic?>'>
	<meta property="og:description" content="<?php echo "".$ct_name." ".$ct_overall_score." - VS - ".$ct_overall_score." ".$t_name.""; ?>"/>
	
	<!-- Bootstrap core CSS     -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
   
    <!--  Material Dashboard CSS    -->
    <link href="./assets/css/material-dashboard.css" rel="stylesheet" />
	
	<!--  Warmod+ CSS    -->
    <link href="./assets/css/warmod_plus.css" rel="stylesheet" />
    
	<!--     Fonts and icons     -->
    <link href="./assets/css/fontawesome-all.css" rel="stylesheet" />
    <link href="./assets/css/google-roboto-300-700.css" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
		<div class="sidebar" data-active-color="rose" data-background-color="black" data-image="./assets/img/sidebar-inferno.jpg">
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
					<li  class="active">
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
		<div class='blurbg' style='background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),url(<?=$mappic?>);'></div>
			<nav class="navbar navbar-transparent navbar-absolute">
				<div class="container-fluid">
					<div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <font color="#FFFFFF"><a class="navbar-brand" href="./matches.php" style='display:inline;'><i class="material-icons">keyboard_arrow_left</i>Back To List</a></font>
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
						<div class="col-md-12">
							<div class="match-data-list table-responsive">
								<table class="odd-even col-md-12" align="center" style="width:100% !important">
									<tbody>
										<tr class='even'>
											<th style="text-align:right; font-size:35px;">
												<img src="<?=$ct_logo?>" style="width:64px; border-radius:3px;"/>
											</th>
											<th style="text-align:left; font-size:35px;">
												<?php
													if($ct_id != "0") echo "<a href='./showteam.php?id=".$ct_id."' style='color:#3399ff;'>".$ct_name."</a>";
													else echo "<font color='#3399ff'>".$ct_name."</font>";
												?>
											</th>	
											<th style="font-size:35px;"><font color="#3399ff"><?=$ct_overall_score?></font>&nbsp;&nbsp;VS&nbsp;&nbsp;<font color="#ff4040"><?=$t_overall_score?></font></th>
											<th style="text-align:right; font-size:35px;">
												<?php
													if($t_id != "0") echo "<a href='./showteam.php?id=".$t_id."' style='color:#ff4040;'>".$t_name."</a>";
													else echo "<font color='#ff4040'>".$t_name."</font>";
												?>
											</th>
											<th style="text-align:left; font-size:35px;">
												<img src="<?=$t_logo?>" style="width:64px; border-radius:3px;"/>
											</th>	
										</tr>
									</tbody>
								</table>
								<table class="odd-even" align="center">
								<tbody>
									<tr class='even' style="height:54px">
									<th style="vertical-align:middle; text-align:center; width:25%"><i class="material-icons">info</i>&nbsp;&nbsp;ID:<?php echo "".$match_id.""?></th>
									<th style="vertical-align:middle; text-align:center; width:25%"><i class="material-icons">map</i>&nbsp;&nbsp;<?php echo "".$map.""?></th>
									<th style="vertical-align:middle; text-align:center; width:25%"><i class="material-icons">access_time</i>&nbsp;&nbsp;<?php echo "".$timestamp.""; ?>
									<th style="vertical-align:middle; text-align:center; width:25%"><i class="material-icons">date_range</i>&nbsp;&nbsp;<?php echo "".$competition." - ".$event.""; ?>
									</tr>
								</tbody>
								</table>
								<table class="odd-even group1" align="center">
									<tbody>
									<?php
									// team 1 players
									$result=mysqli_query($link, "SELECT * FROM `".$stats_table."` WHERE `match_id` = ".$matchid." AND `team` = 1");
									mysqli_set_charset ($link , "utf-8");
									$numb1=mysqli_num_rows($result);

									if (!empty($numb1)) 
									{ 
										echo "<tr class='even'>
											<th class='icon'></th>
											<th class='name'>Name</th>
											<th>K</th>
											<th>D</th>
											<th>A</th>
											<th>HS</th>
											<th>TK</th>
											<th>ATA</th>
											<th>KDR</th>
											<th>KPR</th>
											<th>DPR</th>
											<th>1k</th>
											<th>2k</th>
											<th>3k</th>
											<th>4k</th>
											<th>Ace</th>
											<th>MVP</th>
											<th>LA%</th>
											<th>CW%</th>
											<th>AC%</th>
											<th>RWS</th>
										</tr>";
										
										$class="odd";
										
										while ($row = mysqli_fetch_array($result))
										{
											$steam = $row['steam_id_64'];
											
											//$name = $row['player_name'];
											if($steam == "STEAM_ID_STOP_IGNOR")	$name = "BOT";
											else {$name = getName($steam);}
											
											$kills = $row['kills'];
											$deaths = $row['deaths'];
											$assists = $row['assists'];
											$hs = $row['head_shots'];
											$tks = $row['team_kills'];
											$atks = $row['assists_team_attack'];
											$damage = $row['damage'];
											$hits = $row['hits'];
											$shots = $row['shots'];
											$la = $row['last_alive'];
											$cw = $row['clutch_won'];
											$k1 = $row['1k'];
											$k2 = $row['2k'];
											$k3 = $row['3k'];
											$k4 = $row['4k'];
											$k5 = $row['5k'];
											$mvps = $row['mvp'];
											$rws = $row['rws'];
											
											$rws2 = substr($rws, 0, 1);
											$rws = floor_dec($rws,2);
												
											if($deaths == 0)	$kdr = round($kills/1, 2);
											else	$kdr = round($kills/$deaths, 2);
												
											$kpr = round($kills/$rounds, 2);
											$dpr = round($damage/$rounds, 2);
											
											if($shots == 0) $ac = 0;
											else	$ac = round($hits/$shots, 2);
											
											$la2 = round($cw/$rounds, 2);
											
											if($la == 0)	$ca = 0;
											else	$ca = round($cw/$la, 2);
											
											$ip_result=mysqli_query($link, "select * from `".$player_table."` where `steam_id_64` = ".$steam."");
											while ($ip_row = mysqli_fetch_array($ip_result))
											{
												$ip = $ip_row['last_ip'];
											}

											$cc = geoip_country_code_by_addr($gi,$ip);
												
											echo "
											<tr class='".$class."'>
											<td class='icon'>
											";
											if($cc != "") 
											{
												echo "<img src='./assets/img/flags/".geoip_country_code_by_addr($gi,$ip).".png' width='25'/>";
											} 
											echo "</td>
											<td style='text-align:left;'><b><a href='./showplayer.php?id=".$steam."' style='text-decoration: none;' target='_blank'>
											<font color='#3399ff'>".$name."</font></a></b></td>
											<td>".$kills."</td>
											<td>".$deaths."</td>
											<td>".$assists."</td>
											<td>".$hs."</td>
											<td>".$tks."</td>
											<td>".$atks."</td>
											<td>".$kdr."</td>
											<td>".$kpr."</td>
											<td>".$dpr."</td>
											<td>".$k1."</td>
											<td>".$k2."</td>
											<td>".$k3."</td>
											<td>".$k4."</td>
											<td>".$k5."</td>
											<td>".$mvps."</td>
											<td>".$la2."</a></td>
											<td>".$ca."</a></td>
											<td>".$ac."</td>";
											
											if($rws2 == "-")	echo "<td style='color:#ff0000'>".$rws."</td>";	// -
											elseif($rws2 != "-" && empty($rws2))	echo "<td>".$rws."</td>";		// 0
											else 	echo "<td style='color:#00ff00'>+".$rws."</td>";				// +
											
											echo "</tr>";
											
											if($class=="odd")	$class="even";
											else $class="odd";
										}
									}
									?>
									</tbody>
								</table>
								<table class="odd-even group2" align='center'>
									<tbody>
										<?php
										// team 2 players
										$result=mysqli_query($link, "SELECT * FROM `".$stats_table."` WHERE `match_id` = ".$matchid." AND `team` = 2");
										mysqli_set_charset ($link , "utf-8");

										$numb2=mysqli_num_rows($result);

										if (!empty($numb2)) 
										{ 
											echo "<tr class='even'>
											<th class='icon'></th>
											<th class='name'>Name</th>
											<th>K</th>
											<th>D</th>
											<th>A</th>
											<th>HS</th>
											<th>TK</th>
											<th>ATA</th>
											<th>KDR</th>
											<th>KPR</th>
											<th>DPR</th>
											<th>1k</th>
											<th>2k</th>
											<th>3k</th>
											<th>4k</th>
											<th>Ace</th>
											<th>MVP</th>
											<th>LA%</th>
											<th>CW%</th>
											<th>AC%</th>
											<th>RWS</th>
										</tr>";
											
											$class="odd";
											
											while ($row = mysqli_fetch_array($result))
											{
												$steam = $row['steam_id_64'];
												
												//$name = $row['player_name'];
												if($steam == "STEAM_ID_STOP_IGNOR")	$name = "BOT";
												else {$name = getName($steam);}
												
												$kills = $row['kills'];
												$deaths = $row['deaths'];
												$assists = $row['assists'];
												$hs = $row['head_shots'];
												$tks = $row['team_kills'];
												$atks = $row['assists_team_attack'];
												$damage = $row['damage'];
												$hits = $row['hits'];
												$shots = $row['shots'];
												$la = $row['last_alive'];
												$cw = $row['clutch_won'];
												$k1 = $row['1k'];
												$k2 = $row['2k'];
												$k3 = $row['3k'];
												$k4 = $row['4k'];
												$k5 = $row['5k'];
												$mvps = $row['mvp'];
												$rws = $row['rws'];
												
												$rws2 = substr($rws, 0, 1);
												$rws = floor_dec($rws,2);
													
												if($deaths == 0)	$kdr = round($kills/1, 2);
												else	$kdr = round($kills/$deaths, 2);
													
												$kpr = round($kills/$rounds, 2);
												$dpr = round($damage/$rounds, 2);
												
												if($shots == 0) $ac = 0;
												else	$ac = round($hits/$shots,2);
												
												$la2 = round($cw/$rounds, 2);
												
												if($la == 0)	$ca = 0;
												else	$ca = round($cw/$la, 2);
												
												$ip_result2=mysqli_query($link, "select * from `".$player_table."` where `steam_id_64` = ".$steam."");
												while ($ip_row2 = mysqli_fetch_array($ip_result2))
												{
													$ip2 = $ip_row2['last_ip'];
												}

												$cc = geoip_country_code_by_addr($gi,$ip2);
													
												echo "
												<tr class='".$class."'>
												<td class='icon'>
												";
												if($cc != "") 
												{
													echo "<img src='./assets/img/flags/".geoip_country_code_by_addr($gi,$ip2).".png' width='25'/>";
												} 
												echo "</td>
													<td style='text-align:left;'><b><a href='./showplayer.php?id=".$steam."' style='text-decoration: none;' target='_blank'>
												<font color='#ff4040'>".$name."</font></a></b></td>
												<td>".$kills."</td>
												<td>".$deaths."</td>
												<td>".$assists."</td>
												<td>".$hs."</td>
												<td>".$tks."</td>
												<td>".$atks."</td>
												<td>".$kdr."</td>
												<td>".$kpr."</td>
												<td>".$dpr."</td>
												<td>".$k1."</td>
												<td>".$k2."</td>
												<td>".$k3."</td>
												<td>".$k4."</td>
												<td>".$k5."</td>
												<td>".$mvps."</td>
												<td>".$la2."</a></td>
												<td>".$ca."</a></td>
												<td>".$ac."</td>";
												
												if($rws2 == "-")	echo "<td style='color:#ff0000'>".$rws."</td>";	// -
												elseif($rws2 != "-" && empty($rws))	echo "<td>".$rws."</td>";		// 0
												else 	echo "<td style='color:#00ff00'>+".$rws."</td>";				// +
												
												echo "</tr>";
													
												if($class=="odd")	$class="even";
												else $class="odd";
											}
										}

										if(empty($numb1) && empty($numb2))
										{
											echo "<td align='center' colspan='20'>No Player In This Match.</td>";
										}
										?>
									</tbody>
								</table>
								<br>
								<table class="odd-even" align='center'>
									<tbody>
										<tr class='even' style="height:54px">
											<th style="vertical-align:middle; text-align:center; width:50%">
												<?php
													if(file_exists("./demos/warmod/_".$demo.".dem.bz2"))
														echo "
														<a href='./demos/warmod/_".$demo.".dem.bz2' class='btn btn-success'>
															<i class='material-icons'>get_app</i>Download Demo
														</a>";
													else echo "
														<button class='btn btn-danger'>
															<i class='material-icons'>get_app</i>Download Demo
														</button>";
												?>
											</th>
											<th style="vertical-align:middle; text-align:center; width:50%">
												<button class="btn btn-social btn-fill btn-facebook " onclick="window.open('http://www.facebook.com/sharer/sharer.php?u='+window.location.href);return false;">
													<i class="fab fa-facebook-square"></i> Share On Facebook
												</button>
											</th>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
            </div>
            <footer class="footer" style="border-top:0px">
                <div class="container-fluid">
                    <p class="copyright pull-right"><font color="#FFFFFF">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                       Made by <a href="http://steamcommunity.com/id/kentomatoryoshika/"  style="color:#ffffff" target="_blank">Kento</a></font>
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
</script>
</html>