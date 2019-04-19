<?php
    require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/view_mode.php";
	require_once "assets/inc/session.php";
	$gi = geoip_open("assets/inc/GeoIP.dat",GEOIP_STANDARD);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Warmod+ | Players</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
   
    <meta property="og:site_name" content="Warmod+">
    <meta property="og:title" content="Warmod+ | Players"/>
	<meta property="og:type" content="website">
	<meta property="og:image" content="./assets/img/logo.png">
	<meta property="og:description" content="Warmod+ Players"/>
    
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
					<li class="active">
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
                        <a class="navbar-brand" href="./players.php" style='display:inline;'> Players </a>
                    </div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-right">
							<li>
                                <a href="?view_mode_module&from_player">
                                    <i class='material-icons'>view_module</i>
                                    <p class="hidden-lg hidden-md">Module View</p>
                                </a>
                            </li>
							<li>
                                <a href="?view_mode_list&from_player">
                                    <i class='material-icons'>list</i>
                                    <p class="hidden-lg hidden-md">List View</p>
                                </a>
                            </li>
							<li class="separator hidden-lg hidden-md"></li>
						</ul>
						<?php
							include_once "./assets/inc/navbar.php";
						?>
					</div>
				</div>
			</nav>
            <div class="content">
                <div class="container-fluid">
					<?php	
					if($_SESSION['view_mode'] == "module")
					{
						$limit=12;
						if (isset($_GET['start'])) $start=$_GET["start"];
						if (isset($_GET['s']))	$s=$_GET["s"];
						if (empty($start)) $start=0; 
						if (empty($s)) $s=0;
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
						DESC"); 
						mysqli_set_charset ($link , "utf-8");
						$nummax=mysqli_num_rows($result); 
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
						ORDER BY rws DESC limit ".$start.",".$limit."");
						mysqli_set_charset ($link , "utf-8"); 
						$numb=mysqli_num_rows($result); 
					
						if (!empty($numb)) 
						{ 
							$i=1;
							
							while ($row = mysqli_fetch_array($result))
							{
								// get steam id and name
								$steam[$i] = $row['steam_id_64'];
										
								$rws[$i] = $row['rws'];
								$rank[$i] = $row['rank'];
								
								/*
								if($rws[$i] > 0.00)	$rws[$i] = floor_dec($rws[$i],2);
								else $rws[$i] = "Unranked";
								*/
								
								$kills[$i] = $row['kills'];
								$deaths[$i] = $row['deaths'];
								//$hs[$i] = $row['head_shots'];
								$hits[$i] = $row['hits'];
								$shots[$i] = $row['shots'];
											
								if($shots[$i] == 0) $ac[$i] = 0;
								else	$ac[$i] = round($hits[$i]/$shots[$i], 2);
									
								if($deaths[$i] == 0)	$kdr[$i] = round($kills[$i]/1, 2);
								else	$kdr[$i] = round($kills[$i]/$deaths[$i], 2);
								
								$i++;
							}
							
							$i-=1;
							
							$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
							for ($j=1;$j<=$i;$j++) 
							{
								if($j == 1)	$url.="".$steam[$j]."";
								else $url.=",".$steam[$j]."";
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
								$avatarfull[$item['steamid']] = $item['avatarfull'];
							}
							
							for ($j=1;$j<=$i;$j++) 
							{
								echo "
								 <div class='col-md-4'>
								 <a href='./showplayer.php?id=".$steam[$j]."'>
									<div class='card card-stats'>";
									
								if($rank[$j] == "1")
								{
									echo "<div class='card-header' data-background-color='rose' data-header-animation='true'>
										<div class='ribbon'><span>1 ST</span></div>";
									}
								elseif($rank[$j] == "2")
								{
									echo "<div class='card-header' data-background-color='blue' data-header-animation='true'>
										<div class='ribbon'><span>2 ND</span></div>";
								}
								elseif($rank[$j] == "3")
								{
									echo "<div class='card-header' data-background-color='orange' data-header-animation='true'>
										<div class='ribbon'><span>3 RD</span></div>";
								}
								else
								{
									echo "<div class='card-header' data-background-color='green' data-header-animation='true'>";
								}
											
								echo "
									<img src='".$avatarfull[$steam[$j]]."' style='width:94px !important;height:94px !important; border-radius:3px;' class='img' />
										</div>
										<div class='card-content'>
										<p class='category'><font color='#d9534f'>RWS:&nbsp;".$rws[$j]."</font></p>
											<h3 class='card-title'>".$personname[$steam[$j]]."</h3>
										</div>
										<div class='card-footer'>
											<div class='stats pull-right'>
											<p class='category'><font color='#428bca'>Kills:&nbsp;".$kills[$j]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deaths:&nbsp;".$deaths[$j]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KDR:&nbsp;".$kdr[$j]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AC:&nbsp;".$ac[$j]."%</font></p>
											</div>
										</div>
									</div>
									</a>
								</div>";
							}
							echo "
							<div class='row'>
								<div class='col-md-6 col-md-offset-3' style='text-align:center;'>
									<div class='card-content'>
										<ul class='pagination pagination-primary'>";
							
							if ($s>10)
							{ 
								if ($s==11){ 
									$st = $s-11; 
								}
								else
								{ 
									$st = $s-10; 
								} 
								$pstart = $st*$limit; 
								echo "
								<li class='page-item'>
								<a class='page-link' href='players.php?start=$pstart&s=$st' tabindex='-1'>
								<span aria-hidden='true'>&laquo;</span>
								<span class='sr-only'>Previous</span>
								</a>
								</li>";
							} 

							//設置當前頁數無連接功能
							$star = $start; 
							if ($s<=10)
							{ 
								echo "<li class='page-item'>
								</li>";
							}
							for ($page=$s;$page<($nummax/$limit);$page++) 
							{ 
								$start=$page*$limit; 
								 
								if($page!=$star/$limit) 
								{ 
									echo "
									<li class='page-item'>
									<a  class='page-link' href=players.php?start=$start&s=$s>";
								} 
								else
								{
									echo "<li class='page-item active'><a  class='page-link'>";
								}
								echo $page+1; 
								echo "</a></li>"; 

								if ($page>0 && ($page%10)==0) 
								{ 
									if ($s==0) 
									{ 
										$s = $s+11; 
									}
									else
									{ 
										$s = $s+10; 
									} 
									$start = $start+$limit; 

									if ((($nummax/$limit)-1)>$page) 
									{ 
										echo "<li class='page-item'>
										<a class='page-link' href=players.php?start=$start&s=$s  aria-label='Next'>
										<span aria-hidden='true'>&raquo;</span>
										<span class='sr-only'>Next</span>
										</a>
										</li>";
									} 
									break; 
								} 
							} 
							echo"</ul></div></div></div>";
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
					}
					elseif($_SESSION['view_mode'] == "list")
					{
						echo "
						<div class='row'>
						<div class='col-md-12'>
							<div class='card'>
								<div class='card-header card-header-icon' data-background-color='purple'>
									<i class='material-icons'>assignment</i>
								</div>
								<div class='card-content'>
									<h4 class='card-title'>&nbsp;</h4>
									<!--  
										<div class='toolbar'>       
										</div>
									-->
									<div class='material-datatables'>
									<table id='datatables' class='table table-striped table-no-bordered table-hover' cellspacing='0' width='100%' style='width:100%'>
									<COL width='3%'><COL width='20%'><COL width='14%'><COL width='14%'><COL width='14%'><COL width='14%'><COL width='14%'><COL width='7%'>
									<thead class='thead-inverse'>
										<tr>
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
								";
										
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
						DESC");
						mysqli_set_charset ($link , "utf-8");
						$numb=mysqli_num_rows($result); 

						if (!empty($numb)) 
						{ 		
							$i=1;
							while ($row = mysqli_fetch_array($result))
							{
								// get steam id and name
								$steam[$i] = $row['steam_id_64'];
								$team[$i] = $row['team'];
										
								$rws[$i] = $row['rws'];
								if($rws[$i] > 0.00)	$rws[$i] = floor_dec($rws[$i],2);
								else $rws[$i] = "Unranked";
									
								$kills[$i] = $row['kills'];
								$deaths[$i] = $row['deaths'];
								$hits[$i] = $row['hits'];
								$shots[$i] = $row['shots'];
											
								if($shots[$i] == 0) $ac[$i] = 0;
								else	$ac[$i] = round($hits[$i]/$shots[$i], 2);
									
								if($deaths[$i] == 0)	$kdr[$i] = round($kills[$i]/1, 2);
								else	$kdr[$i] = round($kills[$i]/$deaths[$i], 2);
								$rank[$i] = $row['rank'];
								
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
								<tr height='54px'>
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
						echo "
							</tbody>
                                        </table>
                                    </div>
                                </div>
								</div>
								</div>
								</div>";
					}				
					?>
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