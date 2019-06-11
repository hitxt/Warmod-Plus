<!DOCTYPE html>
<?php
	require_once("./configs/configs.php");
	require_once("./libs/sql.php");
	require_once("./libs/steamauth/steamauth.php");
	require_once("./libs/steamauth/userInfo.php");
	require_once("./libs/steam/SourceQuery.php");
	require_once("./libs/geoip/geoip.php");
	$gi = geoip_open("./libs/geoip/GeoIP.dat",GEOIP_STANDARD);	
	require_once("./libs/functions.php");
	require_once("./libs/class/player.php");
	require_once("./libs/class/match.php");
	require_once("./libs/class/team.php");
	require_once("./libs/class/weapons.php");
	require_once("./libs/steam/SteamID.php");
	$activePage = basename($_SERVER['PHP_SELF'], ".php");

	if(!empty($_GET["id"]) && is_numeric($_GET["id"]))	$id = $_GET["id"];
	else
	{
		echo "<script type='text/javascript'>alert('Invalid Steam ID');window.history.back();</script>";
		exit;
	}

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
			$pdo = null;
			exit;
		}	
	}

	// player data
	$input = array(
		":id" => $id,
	);
	$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result = $sth->fetchAll();

	// player stats
	$sql = $playerStatsSQL." WHERE ".$stats_table.".steam_id_64 = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result2 = $sth->fetchAll();
	
	// player rank
	$sql = $playerRankSQL." WHERE steam_id_64 = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result3 = $sth->fetchAll();

	// matches
	$sth = $pdo->prepare($playerWinSQL);
	$sth->execute($input);
	$win = count( $sth->fetchAll() );

	$sth = $pdo->prepare($playerLoseSQL);
	$sth->execute($input);
	$lose = count( $sth->fetchAll() );

	$sth = $pdo->prepare($playerDrawSQL);
	$sth->execute($input);
	$draw = count( $sth->fetchAll() );

	if(!empty($result) && !empty($result2) && !empty($result3)){
		$result = array_merge($result[0], $result2[0], $result3[0]);
		$player = new Player($result);
		$player->cc = geoip_country_code_by_addr($gi, $player->last_ip);
		$player->cn = geoip_country_name_by_addr($gi, $player->last_ip);
		
		// team
		if(!empty($player->team)){
			$sql = "SELECT * FROM ".$team_table." WHERE id = ".$player->team;
			$sth = $pdo->prepare($sql);
			$sth->execute();
			$result4 = $sth->fetchAll();
			$team = new Team($result4[0]);
		}
	}

	$steamids[] = $id;
	$steam = SteamData::GetData($SteamAPI_Key, $steamids);
?>
<html lang="en">

<head>
	<meta charset="utf-8" />

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

	<title>Warmod+ Profile | <?=$steam["name"][$id]?></title>
	<!-- Icons -->
	<link rel="apple-touch-icon" href="./assets/img/icon.ico">
	<link rel="icon" href="./assets/img/icon.ico">
	<link rel="shortcut icon" href="./assets/img/icon.ico"/>
	<link rel="bookmark" href="./assets/img/icon.ico"/>

	<!-- Fonts and icons -->
	<link rel="stylesheet" href="./assets/css/googlefonts.css" />
	<link rel="stylesheet" href="./assets/css/font-awesome.css">
	<link rel="stylesheet" href="./assets/css/Pe-icon-7-stroke.css">

	<!-- CSS Files -->
	<link href="./assets/css/material-dashboard.min.css" rel="stylesheet" />
	<link href="./assets/css/Chart.min.css" rel="stylesheet">
	<link href="./assets/css/warmod_plus.css" rel="stylesheet" />

	<!-- Facebook Meta -->
	<meta property="og:title" content="Warmod+ Profile:<?=$steam["name"][$id]?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Warmod+">
	<meta property="og:image" content="<?=$steam["avatar"][$id]?>">
	<meta property="og:description" content="Warmod+ Profile:<?=$steam["name"][$id]?>">
</head>

<body class="">
	<div class="wrapper ">
		<?php require_once("./libs/pages/sidebar.php");?>
		<div class="main-panel">
			<?php require_once("./libs/pages/navbar.php");?>
			<div class="content">
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12 col-lg-6 col-xl-4">
								<div class="row">
									<div class="col-6 text-right"><img src="<?=$steam["avatar"][$id]?>" class="rounded" height="124px"></div>
									<div class="col-6 text-left align-self-center">
										<h2>Name</h2>
										<h3 class="mt-1"><?=$steam["name"][$id]?></h3>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-6 col-xl-4 mt-4 mt-lg-0">
								<div class="row">
									<div class="col-6 text-right">
										<?=(!empty($team->id))?"<a href='./showteam.php?id=".$team->id."'>":""?>
										<img src="<?=(!empty($team->logo))?$team->logo:"./assets/img/teams/unknown.png"?>" class="rounded" height="124px">
										<?=(!empty($team->id))?"</a>":""?>
									</div>
									<div class="col-6 text-left align-self-center">
										<?=(!empty($team->id))?"<a href='./showteam.php?id=".$team->id."' style='color:inherit'>":""?>
										<h2>Team</h2>
										<h3 class="mt-1"><?=(!empty($team->name))?$team->name:"Unknown"?></h3>
										<?=(!empty($team->id))?"</a>":""?>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-12 col-xl-4 mt-4 mt-xl-0 text-center align-self-center">
								<a class="btn btn-just-icon btn-github text-white" href="http://steamcommunity.com/profiles/<?=$id?>" target="_blank">
									<i class="fab fa-steam-symbol"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($player->fb))?"btn-facebook":"btn-twitch"?>" href="<?=(!empty($player->fb))?$player->fb:"#"?>">
									<i class="fab fa-facebook"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($player->twitter))?"btn-twitter":"btn-twitch"?>" href="<?=(!empty($player->twitter))?$player->twitter:"#"?>">
									<i class="fab fa-twitter"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($player->twitch))?"btn-primary":"btn-twitch"?>" href="<?=(!empty($player->twitch))?$player->twitch:"#"?>">
									<i class="fab fa-twitch"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($player->youtube))?"btn-youtube":"btn-twitch"?>" href="<?=(!empty($player->youtube))?$player->youtube:"#"?>">
									<i class="fab fa-youtube"></i>
								</a>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-warning card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-medal"></i>
										</div>
										<p class="card-category">Rank</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->rank))?$player->rank:"Unranked"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-rose card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-skull"></i>
										</div>
										<p class="card-category">KDR</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->kdr))?$player->kdr:"0.00"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-success card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="material-icons">equalizer</i>
										</div>
										<p class="card-category">RWS</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->rws))?$player->rws:"0.00"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-info card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-crosshairs"></i>
										</div>
										<p class="card-category">Accuracy</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->ac))?sprintf("%01.2f",$player->ac):"0.00"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-warning card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="material-icons">place</i>
										</div>
										<p class="card-category">Country</p>
										<h3 class="card-title" style="padding-bottom:10px;">
											<?=(!empty($player->cc))?"<img src='./assets/img/flags/".$player->cc.".png' width='25'/> ".$player->cn:"Unknown"?>
										</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-rose card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="material-icons">public</i>
										</div>
										<p class="card-category">Matches</p>
										<h3 class="card-title" style="padding-bottom:10px;">
											<?=$win?> W / <?=$lose?> L / <?=$draw?> D
										</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-success card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-thumbs-up"></i>
										</div>
										<p class="card-category">Aces</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->k5))?$player->k5:"0"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-info card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-star"></i>
										</div>
										<p class="card-category">MVP</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->mvp))?$player->mvp:"0"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-warning card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-bomb"></i>
										</div>
										<p class="card-category">Bombs Planted</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->c4_planted))?$player->c4_planted:"0"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-rose card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-wrench"></i>
										</div>
										<p class="card-category">Bombs Defused</p>
											<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->c4_defused))?$player->c4_defused:"0"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-success card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-fire"></i>
										</div>
										<p class="card-category">Bombs Exploded</p>
											<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->c4_exploded))?$player->c4_exploded:"0"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-info card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="fas fa-hands-helping"></i>
										</div>
										<p class="card-category">Hostage Rescued</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($player->hostages_rescued))?$player->hostages_rescued:"0"?></h3>
									</div>
								</div>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-12 col-lg-6">
								<div class="card">
									<div class="card-header card-header-icon card-header-primary">
										<div class="card-icon">
											<i class="material-icons">timeline</i>
										</div>
										<h4 class="card-title">RWS History</h4>
									</div>
									<div class="card-body">
										<div id="rwsBarChart" class="ct-chart">
											<canvas id="rwsChart" class="w-100 h-100"></canvas>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-6">
								<div class="card">
									<div class="card-header card-header-icon card-header-danger">
										<div class="card-icon">
											<i class="material-icons">timeline</i>
										</div>
										<h4 class="card-title">Hixboxes</h4>
									</div>
									<div class="card-body">
										<div id="hitboxBarChart" class="ct-chart">
											<canvas id="hitboxChart" class="w-100 h-100"></canvas>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header card-header-primary card-header-icon">
										<div class="card-icon">
											<i class="material-icons">assignment</i>
										</div>
										<h4 class="card-title">Matches Played</h4>
									</div>
									<div class="card-body">
										<div class="toolbar"></div>
										<div class="material-datatables">
											<table id="matchTable" class="table table-striped table-no-bordered table-hover table-rwd w-100">
												<thead>
													<tr class="tr-only-hide">
														<th>ID</th>
														<th>Date</th>
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
												<tbody>
													<?php
														$sth = $pdo->prepare($playerMatchSQL);
														$sth->execute($input);
														$result = $sth->fetchAll();
														foreach($result as $row){
															switch($row["result"]){
																case "win":
																	$color = "#5cb85c";
																	break;
																case "lose":
																	$color = "#d9534f";
																	break;
																case "draw":
																	$color = "#5bc0de";
																	break;
															}
															$match = new Match($row, $timezone);
															?>
																<tr>
																	<td data-th="ID"><?=$match->id?></td>
																	<td data-th="Date"><?=$match->timestamp?></td>
																	<td data-th="Competition"><?=$match->competition?></td>
																	<td data-th="Event"><?=$match->event?></td>
																	<td data-th="Map"><?=$match->map?></td>
																	<td data-th="Team A"><?=(!empty($match->t_name))?$match->t_name:"Unknown"?></td>
																	<td data-th="Score"><?=$match->t_overall_score?> VS <?=$match->ct_overall_score?></td>
																	<td data-th="Team B"><?=(!empty($match->ct_name))?$match->ct_name:"Unknown"?></td>
																	<td data-th="Result" style="color:<?=$color?>"><?=strtoupper($match->result)?></td>
																	<td data-th="View"><a href='./showmatch.php?id=<?=$match->id?>'>View</a></td>
																</tr>
															<?php
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="card">
									<div class="card-header card-header-primary card-header-icon">
										<div class="card-icon">
											<i class="material-icons">assignment</i>
										</div>
										<h4 class="card-title">Weapon Stats</h4>
									</div>
									<div class="card-body">
										<div class="toolbar"></div>
										<div class="material-datatables">
											<table id="weaponTable" class="table table-striped table-no-bordered table-hover table-rwd w-100">
												<thead>
													<tr class="tr-only-hide">
														<th>Name</th>
														<th>Kills</th>
														<th>Precent Total Kill</th>
													</tr>
												</thead>
												<tbody>
													<?php
														if(!empty($player)){
															for($i=0; $i<43; $i++)
															{
																if($player->kills > 0){	
																	$kp = floor_dec(($player->{$weapon["sql"][$i]}/$player->kills)*100,2);
																	$kp .= "%";
																}
																else $kp = "0%";
																?>
																<tr>
																	<td data-th="Name">
																		<img src='assets/img/weapons/<?=$weapon['name'][$i]?>.png' style='width:50px !important; <?=($weapon['name'][$i] == "Inferno")?"height:37px":""?>' />
																		&emsp; <?=$weapon['name'][$i]?>
																	</td>
																	<td data-th="Kills"><?=$player->{$weapon["sql"][$i]}?></td>
																	<td data-th="Precent Total Kill"><?=$kp?></td>
																</tr>
																<?php
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
			</div>
			<footer class="footer">
				<div class="container-fluid">
					<div class="copyright float-right">
						&copy;
						<script>
							document.write(new Date().getFullYear())
						</script>, Made by 
						<a href="https://kento520.tw/" target="_blank">Kento</a>.
					</div>
				</div>
			</footer>
		</div>
	</div>
	<!--   Core JS Files   -->
	<script src="./assets/js/core/jquery.min.js"></script>
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap-material-design.min.js"></script>
	<script src="./assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	<!-- Plugin for the momentJs  -->
	<script src="./assets/js/plugins/moment.min.js"></script>
	<!--  Plugin for Sweet Alert -->
	<script src="./assets/js/plugins/sweetalert2.js"></script>
	<!-- Forms Validations Plugin -->
	<script src="./assets/js/plugins/jquery.validate.min.js"></script>
	<!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
	<script src="./assets/js/plugins/jquery.bootstrap-wizard.js"></script>
	<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
	<script src="./assets/js/plugins/bootstrap-selectpicker.js"></script>
	<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
	<script src="./assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
	<!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
	<script src="./assets/js/plugins/jquery.dataTables.min.js"></script>
	<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
	<script src="./assets/js/plugins/bootstrap-tagsinput.js"></script>
	<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
	<script src="./assets/js/plugins/jasny-bootstrap.min.js"></script>
	<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
	<script src="./assets/js/plugins/fullcalendar.min.js"></script>
	<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
	<script src="./assets/js/plugins/jquery-jvectormap.js"></script>
	<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
	<script src="./assets/js/plugins/nouislider.min.js"></script>
	<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
	<script src="./assets/js/plugins/core.js"></script>
	<!-- Library for adding dinamically elements -->
	<script src="./assets/js/plugins/arrive.min.js"></script>
	<!-- Char JS -->
	<script src="./assets/js/plugins/Chart.min.js"></script>
	<!--  Notifications Plugin    -->
	<script src="./assets/js/plugins/bootstrap-notify.js"></script>
	<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="./assets/js/core/material-dashboard.min.js" type="text/javascript"></script>
	<!--  Warmod+ JS    -->
	<script src="./assets/js/inc/warmod_plus.js" type="text/javascript"></script>
	<?php
		// rws chart
		$sql = "SELECT * FROM ".$stats_table." WHERE steam_id_64 = :id ORDER BY match_id DESC LIMIT 7";
		$sth = $pdo->prepare($sql);
		$sth->execute($input);
		$result = $sth->fetchAll();
		$num = count($result);
		if($num > 0 && !empty($player)){
			$i = $num;
			$rws_change[$i] = $player->rws;
			foreach($result as $row){
				$mid[$i] = $row['match_id'];
				$rws_change[$i-1] = $rws_change[$i] - $row['rws'];
				$i--;
			}
		}
	?>
	<script>
		// Match datatable
		let matchTable = $('#matchTable').DataTable( {
			"lengthMenu": [
				[15, 25, 50, -1],
				[15, 25, 50, "All"]
			],
		});
		// Weapon stats
		let weaponTable = $('#weaponTable').DataTable( {
			"lengthMenu": [
				[10, 25, -1],
				[10, 25, "All"]
			],
		});
		// RWS chart
		let rwsCtx = $('#rwsChart');
		let rwsChart = new Chart(rwsCtx, {
			type: 'line',
			data: {
				labels: [
					<?php
					if($num > 0 && !empty($player)){
						for($i=1; $i<=$num; $i++)
						{
							echo $mid[$i].",";
						}
					}
					else{
						for($i=1; $i<=5; $i++)
						{
							echo "0,";
						}
					};
					?>
				],
				datasets: [{
					label: 'RWS',
					backgroundColor: "#ea464d",
					borderColor: "#ea464d",
					borderWidth: 5,
					data: [
						<?php
						if($num > 0 && !empty($player)){
							for($i=1; $i<=$num; $i++)
							{
								echo $rws_change[$i].",";
							}
						}
						else{
							for($i=1; $i<=5; $i++)
							{
								echo "0.00,";
							}
						}
						?>
					],
					fill: false,
				}]
			},
			options: {
				responsive: true,
			}
		});

		let hitboxCtx = $('#hitboxChart');
		let hitboxChart = new Chart(hitboxCtx, {
			type: 'bar',
			data: {
				labels: ['Head', 'Chest', 'Stomach', 'L-Arms', 'R-Arms', 'L-Legs', 'R-Legs'],
				datasets: [{
						label: 'Hits',
						backgroundColor: "#ea464d",
						borderColor: "#ea464d",
						borderWidth: 5,
						data: [
						<?=(!empty($player->head))?$player->head:"0"?>, 
						<?=(!empty($player->chest))?$player->chest:"0"?>, 
						<?=(!empty($player->stomach))?$player->stomach:"0"?>, 
						<?=(!empty($player->left_arm))?$player->left_arm:"0"?>, 
						<?=(!empty($player->right_arm))?$player->right_arm:"0"?>, 
						<?=(!empty($player->left_leg))?$player->left_leg:"0"?>, 
						<?=(!empty($player->right_leg))?$player->right_leg:"0"?>]
					}
				]
			},
			options: {
				responsive: true,
				options: {
					tooltips: {
						mode: 'index',
						callbacks: {
							afterLabel: function(tooltipItem, data) {
								var sum = data.datasets.reduce((sum, dataset) => {
								return sum + dataset.data[tooltipItem.index];
								}, 0);
								var percent = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] / sum * 100;
								percent = percent.toFixed(2); // make a nice string
								return data.datasets[tooltipItem.datasetIndex].label + ': ' + percent + '%';
							}
						}
					}
				}
			}
		});
	</script>
</body>
<?php
	$pdo = null;
?>
</html>