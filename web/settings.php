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
	require_once("./libs/class/team.php");
    $activePage = basename($_SERVER['PHP_SELF'], ".php");
    
    if(empty($_SESSION['steamid'])){
        header("location: ./index.php");
	}

	$input = array(
		":id" => $_SESSION['steamid'],
	);
	$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result = $sth->fetchAll();
	$player = new Player($result[0]);

	$sql = "SELECT * FROM ".$team_table." WHERE leader = :id";
	$sth = $pdo->prepare($sql);
	$sth->execute($input);
	$result = $sth->fetchAll();
	if(!empty($result))	$team = new Team($result[0]);
?>
<html lang="en">

<head>
	<meta charset="utf-8" />

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

	<title>Warmod+ | Settings</title>
	
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
			<?php require_once("./libs/pages/navbar.php");?>
			<div class="content">
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								<ul class="nav nav-pills nav-pills-icons justify-content-center" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" href="#profile" role="tab" data-toggle="tab">
											<i class="material-icons">person</i>
											Profile
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#<?=(isset($team))?"team":"create"?>" role="tab" data-toggle="tab" id="teamtab">
											<i class="material-icons">people</i>
											Team
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="#notify" role="tab" data-toggle="tab">
											<i class="material-icons">notifications</i>
											Notifications
										</a>
									</li>
									<?php
										if(in_array($_SESSION['steamid'], $admins))
										{
											?>
												<li class="nav-item">
													<a class="nav-link" href="#mod" role="tab" data-toggle="tab">
														<i class="material-icons">build</i>
														MOD
													</a>
												</li>
											<?php
										}
									?>
								</ul>
							</div>
							<div class="col-12">
								<div class="tab-content tab-space">
									<?php  
										// profile settings
										require_once("./libs/pages/settings-profile.php");

										// team setttings
										if(isset($team))	require_once("./libs/pages/settings-team.php");
										else	require_once("./libs/pages/settings-cteam.php");

										// notify
										require_once("./libs/pages/settings-notify.php");

										if(in_array($_SESSION['steamid'], $admins))	require_once("./libs/pages/settings-mod.php");
									?>
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
	<div class="modal bd-example-modal-lg fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
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