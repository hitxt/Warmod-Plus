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
	require_once("./libs/steam/SteamID.php");
	$activePage = basename($_SERVER['PHP_SELF'], ".php");

	if(!empty($_GET["id"]) && is_numeric($_GET["id"]))	$id = $_GET["id"];
	else
	{
		echo "<script type='text/javascript'>alert('Invalid Team ID');window.history.back();</script>";
		exit;
	}

	// team data
	$input = array(
		":id" => $id,
	);
	$sql = $teamSQL." WHERE id = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result = $sth->fetchAll();
	if(count($result) == 0){
		echo "<script type='text/javascript'>alert('Invalid Team ID');window.history.back();</script>";
		exit;
	}
	else{
		$team = new Team($result[0]);
	}

	// team members
	$sth = $pdo->prepare($teamMemberSQL);
	$sth->execute($input);
	$steamids = $sth->fetchAll(PDO::FETCH_COLUMN, 1);
	$data = SteamData::GetData($SteamAPI_Key, $steamids);
?>
<html lang="en">

<head>
	<meta charset="utf-8" />

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

	<title>Warmod+ Team | <?=$team->name?></title>
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
	<meta property="og:title" content="Warmod+ Team:<?=$team->name?>">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Warmod+">
	<meta property="og:image" content="<?=$team->logo?>">
	<meta property="og:description" content="Warmod+ Team:<?=$team->name?>">
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
									<div class="col-6 text-right">
										<img src="<?=$team->logo?>" class="rounded" height="124px">
									</div>
									<div class="col-6 text-left align-self-center">
										<h2>Team Name</h2>
										<h3 class="mt-1"><?=(!empty($team->name))?$team->name:"Unknown"?></h3>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-6 col-xl-4 mt-4 mt-lg-0">
								<div class="row">
									<div class="col-6 text-right">
										<?=(!empty($team->id))?"<a href='./showplayer.php?id=".$team->leader."'>":""?>
										<img src="<?=$data["avatar"][$team->leader]?>" class="rounded" height="124px">
										<?=(!empty($team->id))?"</a>":""?>
									</div>
									<div class="col-6 text-left align-self-center">
										<?=(!empty($team->leader))?"<a href='./showplayer.php?id=".$team->leader."' style='color:inherit'>":""?>
										<h2>Leader</h2>
										<h3 class="mt-1"><?=$data["name"][$team->leader]?></h3>
										<?=(!empty($team->leader))?"</a>":""?>
									</div>
								</div>
							</div>
							<div class="col-12 col-lg-12 col-xl-4 mt-4 mt-xl-0 text-center align-self-center">
								<a class="btn btn-just-icon btn-github text-white" href="http://steamcommunity.com/profiles/<?=$id?>" target="_blank">
									<i class="fab fa-steam-symbol"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($team->fb))?"btn-facebook":"btn-twitch"?>" href="<?=(!empty($team->fb))?$team->fb:"#"?>">
									<i class="fab fa-facebook"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($team->twitter))?"btn-twitter":"btn-twitch"?>" href="<?=(!empty($team->twitter))?$team->twitter:"#"?>">
									<i class="fab fa-twitter"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($team->twitch))?"btn-primary":"btn-twitch"?>" href="<?=(!empty($team->twitch))?$team->twitch:"#"?>">
									<i class="fab fa-twitch"></i>
								</a>
								&emsp;
								<a class="btn btn-just-icon text-white <?=(!empty($team->youtube))?"btn-youtube":"btn-twitch"?>" href="<?=(!empty($team->youtube))?$team->youtube:"#"?>">
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
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($team->rank))?$team->rank:"Unranked"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-rose card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
										<i class="material-icons">flag</i>
										</div>
										<p class="card-category">Wins/Matches</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=(!empty($team->wlr))?$team->wlr:"0.00"?></h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-success card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
											<i class="material-icons">public</i>
										</div>
										<p class="card-category">Matches</p>
										<h3 class="card-title" style="padding-bottom:10px;">
											<?=$team->win?> W / <?=$team->lose?> L / <?=$team->draw?> D
										</h3>
									</div>
								</div>
							</div>
							<div class="col-6 col-xl-3">
								<div class="card card-stats">
									<div class="card-header card-header-info card-header-icon">
										<div class="card-icon"  data-header-animation='true'>
										<i class="material-icons">group</i>
										</div>
										<p class="card-category">Members</p>
										<h3 class="card-title" style="padding-bottom:10px;"><?=count($steamids)?></h3>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header card-header-danger card-header-icon">
										<div class="card-icon">
											<i class="material-icons">group</i>
										</div>
										<h4 class="card-title">Members</h4>
									</div>
									<div class="card-body">
										<div class="toolbar"></div>
										<div class="material-datatables">
											<table id="memberTable" class="table table-striped table-no-bordered table-hover table-rwd w-100">
												<thead>
													<tr class="tr-only-hide">
														<th>Role</th>
														<th>Name</th>
														<th>Rank</th>
														<th>RWS</th>
														<th>KDR</th>
														<th>AC</th>
														<th>Profile</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$sth = $pdo->prepare($teamMemberSQL);
														$sth->execute($input);
														$result = $sth->fetchAll();
														foreach($result as $row){
															$player = new Player($row);
															?>
															<tr>
																<td><?=($player->steam_id_64 == $team->leader)?"Leader":"Member"?></td>
																<td><?=$data["name"][$player->steam_id_64]?></td>
																<td><?=$player->rank?></td>
																<td><?=$player->rws?></td>
																<td><?=$player->kdr?></td>
																<td><?=$player->ac?></td>
																<td>
																	<a href="./showplayer.php?id=<?=$player->steam_id_64?>">View</a>
																</td>
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
									<div class="card-header card-header-rose card-header-icon">
										<div class="card-icon">
											<i class="material-icons">public</i>
										</div>
										<h4 class="card-title">Matches</h4>
									</div>
									<div class="card-body">
										<div class="toolbar"></div>
										<div class="material-datatables">
											<table id="matchTable" class="table table-striped table-no-bordered table-hover table-rwd w-100">
												<thead>
													<tr class="tr-only-hide">
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
												<tbody>
													<?php
														$sth = $pdo->prepare($teamMatchSQL);
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
		// Matches
		let matchTable = $('#matchTable').DataTable( {
			"lengthMenu": [
				[15, 25, 50, -1],
				 [15, 25, 50, "All"]
			],
		});
		// Members
		let memberTable = $('#memberTable').DataTable( {
			"lengthMenu": [
				[10, 25, -1],
				[10, 25, "All"]
			],
		});
	</script>
</body>
<?php
	$pdo = null;
?>
</html>