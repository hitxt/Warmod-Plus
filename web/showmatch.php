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
	require_once("./libs/class/match.php");
	require_once("./libs/class/player.php");
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
	
	if(!empty($_GET["id"]) && is_numeric($_GET["id"]))	$id = $_GET["id"];
	else{
		echo "<script type='text/javascript'>alert('Invalid Match ID');window.history.back();</script>";
		exit;
	}
	$input = array(
		":id" => $id,
	);
	$sql = "SELECT * from ".$result_table." WHERE id = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result = $sth->fetchAll();
	if(count($result) < 1){
		echo "<script type='text/javascript'>alert('Invalid Match ID');window.history.back();</script>";
		$pdo = null;
		exit;
	}
	else $match = new Match($result[0], $timezone);

	if(empty($match->ct_name)){
		$match->ct_name="Unknown"; 
		$match->ct_logo="./assets/img/teams/unknown.png"; 
		$match->ct_id="0";
	}
	else
	{
		$sql = "SELECT * FROM ".$team_table." WHERE name = '".$match->ct_name."'";
		$sth = $pdo->prepare($sql);
		$sth->execute();
		$result = $sth->fetchAll();
		if(count($result) > 0){
			foreach($result as $row)
			{
				$match->ct_id = $row['id'];
				$match->ct_logo = "./assets/img/teams/".$row['logo']."";
				if($match->ct_logo == "./assets/img/teams/" || !file_exists($match->ct_logo))	$match->ct_logo = "./assets/img/teams/unknown.png";
			}
		}
		else {
			$match->ct_name="Unknown";
			$match->ct_logo="./assets/img/teams/unknown.png";
			$match->ct_id="0";
		}
	}

	if(empty($match->t_name)){
		$match->t_name="Unknown"; 
		$match->t_logo="./assets/img/teams/unknown.png"; 
		$match->t_id="0";
	}
	else
	{
		$sql = "SELECT * FROM ".$team_table." WHERE name = '".$match->t_name."'";
		$sth = $pdo->prepare($sql);
		$sth->execute();
		$result = $sth->fetchAll();
		if(count($result) > 0){
			foreach($result as $row)
			{
				$match->t_id = $row['id'];
				$match->t_logo = "./assets/img/teams/".$row['logo']."";
				if($match->t_logo == "./assets/img/teams/" || !file_exists($match->t_logo))	$match->t_logo = "./assets/img/teams/unknown.png";
			}
		}
		else {
			$match->t_name="Unknown";
			$match->t_logo="./assets/img/teams/unknown.png";
			$match->t_id="0";
		}
	}
?>
<html lang="en">

<head>
	<meta charset="utf-8" />

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

	<title>Welcome To Warmod+</title>
	
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
	<link href="./assets/css/warmod_plus.css" rel="stylesheet" />

	<!-- Facebook Meta -->
	<meta property="og:title" content="Warmod+">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Warmod+">
	<meta property="og:image" content="./assets/img/logo.png">
	<meta property="og:description" content="Warmod+">
</head>

<body class="">
	<div class="wrapper ">
		<?php require_once("./libs/pages/sidebar.php");?>
		<div class="main-panel">
			<div class='blurbg' style='background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),url(./assets/img/maps/<?=$match->map?>.jpg);'></div>
			<?php require_once("./libs/pages/navbar.php");?>
			<div class="content">
				<div class="content">
					<div class="container">
						<!-- SCORE -->
						<div class="row mb-4">
							<!-- CT TEAM -->
							<div class="col-12 col-lg-4 text-center">
								<img src="<?=$match->ct_logo?>" class="align-middle" style="width:64px"/>
								<h2 class="text-ct text-match-title font-weight-bold align-middle d-inline">
									<?php
										if($match->ct_id != "0") echo "<a href='./showteam.php?id=".$match->ct_id."' class='text-ct font-weight-bold'>".$match->ct_name."</a>";
										else echo $match->ct_name;
									?>
								</h2>
							</div>
							<!-- SCORE -->
							<div class="col-12 col-lg-4 text-white text-center">
								<h2 class="font-weight-bold"><span class="text-ct"><?=$match->ct_overall_score?></span>&nbsp;&nbsp;VS&nbsp;&nbsp;<span class="text-t"><?=$match->t_overall_score?></span></h2>
							</div>
							<!-- T TEAM -->
							<div class="col-12 col-lg-4 d-flex justify-content-center">
								<img src="<?=$match->t_logo?>" class="align-middle order-lg-2" style="width:64px; height:64px"/>
								<h2 class="text-t text-match-title font-weight-bold align-middle d-inline order-lg-1">
									<?php
										if($match->t_id != "0") echo "<a href='./showteam.php?id=".$match->t_id."' class='text-ct font-weight-bold'>".$match->t_name."</a>";
										else echo $match->t_name;
									?>
								</h2>
							</div>
						</div>
						<hr class="border-bottom border-white">
						<!-- INFO -->
						<div class="row my-4">
							<div class="col-6 col-lg-3 my-2 text-white font-weight-bold"><i class="material-icons">info</i>&nbsp;&nbsp;ID:<?=$match->id?></div>
							<div class="col-6 col-lg-3 my-2 text-white font-weight-bold"><i class="material-icons">map</i>&nbsp;&nbsp;<?=$match->map?></div>
							<div class="col-12 col-sm-6 col-lg-3 my-2 text-white font-weight-bold"><i class="material-icons">access_time</i>&nbsp;&nbsp;<?=$match->match_start?></div>
							<div class="col-12 col-sm-6 col-lg-3 my-2 text-white font-weight-bold"><i class="material-icons">date_range</i>&nbsp;&nbsp;<?=$match->competition." - ".$match->event?></div>
						</div>
						<hr class="border-bottom border-white">
						<!-- STATS -->
						<div class="row text-white my-4">
							<div class="col-12">
								<table class="w-100 table-stats table-rwd-match">
									<tr class='tr-only-hide'>
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
									</tr>
									<?php
										$sql = "SELECT ".$stats_table.".*, ".$player_table.".last_ip FROM ".$stats_table.", ".$player_table." WHERE ".$stats_table.".match_id = ".$match->id." AND ".$stats_table.".team = 1 AND ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64";
										$sth = $pdo->prepare($sql);
										$sth->execute();
										$result = $sth->fetchAll();
										if(!empty($result)){
											$sth = $pdo->prepare($sql);
											$sth->execute();
											$steamids = $sth->fetchAll(PDO::FETCH_COLUMN, 3);
											$steam = SteamData::GetData($SteamAPI_Key, $steamids);
											foreach($result as $row){
												$stats = new Player($row);
												$stats->cc = geoip_country_code_by_addr($gi, $stats->last_ip);
												$stats->matchStats($steam["name"][$row["steam_id_64"]]);
											}
										}
										else echo '<tr><td colspan="21" class="text-match-ct td-empty font-weight-bold">No player in this team.</td></tr>'
									?>
								</table>
							</div>
						</div>
						<hr class="d-xl-none border-bottom border-white">
						<div class="row text-white my-4">
							<div class="col-12 ">
								<table class="w-100 table-stats table-rwd-match">
									<tr class='tr-only-hide'>
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
									</tr>
									<?php
										$sql = "SELECT ".$stats_table.".*, ".$player_table.".last_ip FROM ".$stats_table.", ".$player_table." WHERE ".$stats_table.".match_id = ".$match->id." AND ".$stats_table.".team = 2 AND ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64";
										$sth = $pdo->prepare($sql);
										$sth->execute();
										$result = $sth->fetchAll();
										if(!empty($result)){
											$sth = $pdo->prepare($sql);
											$sth->execute();
											$steamids = $sth->fetchAll(PDO::FETCH_COLUMN, 3);
											$steam = SteamData::GetData($SteamAPI_Key, $steamids);
											foreach($result as $row){
												$stats = new Player($row);
												$stats->cc = geoip_country_code_by_addr($gi, $stats->last_ip);
												$stats->matchStats($steam["name"][$row["steam_id_64"]]);
											}
										}
										else echo '<tr><td colspan="21" class="text-match-t td-empty font-weight-bold">No player in this team.</td></tr>'
									?>
								</table>
							</div>
						</div>
						<!-- DOWNLOAD AND SHARE -->
						<div class="row my-5 justify-content-center">
							<div class="col-3">
								<?php
									if(file_exists("./demos/warmod/_".$match->demo.".dem.bz2")){
										?>
											<a href='./demos/warmod/_<?=$match->demo?>.dem.bz2' class='btn btn-success'>
												<i class='material-icons'>get_app</i> Download Demo
											</a>
										<?php
									}
									else{
										?>
											<button class='btn btn-danger'>
												<i class='material-icons'>get_app</i> Download Demo
											</button>
										<?php
									}
								?>
							</div>
							<div class="col-3">
								<button class="btn btn-social btn-fill btn-facebook " onclick="window.open('http://www.facebook.com/sharer/sharer.php?u='+window.location.href);return false;">
									<i class="fab fa-facebook-square"></i> Share On Facebook
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer class="footer">
				<div class="container-fluid">
					<div class="copyright float-right text-white">
						&copy;
						<script>
							document.write(new Date().getFullYear())
						</script>, Made by 
						<a href="https://kento520.tw/" class="text-white" target="_blank">Kento</a>.
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
	<!-- Chartist JS -->
	<script src="./assets/js/plugins/chartist.min.js"></script>
	<!--  Notifications Plugin    -->
	<script src="./assets/js/plugins/bootstrap-notify.js"></script>
	<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="./assets/js/core/material-dashboard.min.js" type="text/javascript"></script>
	<!--  Warmod+ JS    -->
	<script src="./assets/js/inc/warmod_plus.js" type="text/javascript"></script>
</body>
<?php
	$pdo = null;
?>
</html>