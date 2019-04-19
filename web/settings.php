<?php
    require_once "config.php";
    require_once "steamauth/steamauth.php";
	require_once "assets/inc/SourceQuery.php";
	require_once "assets/inc/steam.php";
	require_once "assets/inc/geoip.php";
	require_once "assets/inc/inc.php";
	require_once "assets/inc/view_mode.php";
	if(!isset($_SESSION['steamid']))
	{
		echo "<script type='text/javascript'>alert('Please login first');window.history.back();</script>";
		exit;
	}
	else	require_once "assets/inc/session.php";
	$gi = geoip_open("assets/inc/GeoIP.dat",GEOIP_STANDARD);
	
	if (isset($_GET['page']) && !empty($_GET['page']))	$page = $_GET['page'];
	else $page = "profile";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/icon.ico" />
    <link rel="icon" type="image/png" href="./assets/img/icon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Warmod+ | Settings</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
   
    <meta property="og:site_name" content="Warmod+ Settings">
    <meta property="og:title" content="Warmod+ | Settings"/>
	<meta property="og:type" content="website">
	<meta property="og:image" content="./assets/img/logo.png">
	<meta property="og:description" content="Warmod+ Settings"/>
    
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
		<div class="sidebar" data-active-color="rose" data-background-color="black" data-image="./assets/img/sidebar-nuke.jpg">
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
                        <a class="navbar-brand" href="./settings.php"> Settings </a>
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
                        <div class="col-md-8 col-md-offset-2">
                            <br />
                            <div class="nav-center">
                                <ul class="nav nav-pills nav-pills-primary nav-pills-icons" role="tablist">
                                    <?php
										if($page == "profile")	echo "<li class='active'>";
										else echo "<li>";
									?>
                                        <a href="#profile" role="tab" data-toggle="tab">
                                            <i class="material-icons">person</i> Profile
                                        </a>
                                    </li>
                                    <?php
										if($page == "team")	echo "<li class='active'>";
										else echo "<li>";
									?>
                                        <a href="#team" role="tab" data-toggle="tab">
                                            <i class="material-icons">group</i> Team
                                        </a>
                                    </li>
                                    <?php
										if($page == "notifications")	echo "<li class='active'>";
										else echo "<li>";
									?>
                                        <a href="#notifications" role="tab" data-toggle="tab">
                                            <i class="material-icons">notifications</i> Notifications
                                        </a>
                                    </li>
									<?php
										if($steamprofile['steamid'] == $root_admin)
											echo "<li>
												<a href='#mod' role='tab' data-toggle='tab'>
													<i class='material-icons'>build</i> Mod
												</a>
											</li>";
									?>
                                </ul>
                            </div>
                            <div class="tab-content">
								<?php
									if($page == "profile") echo "<div class='tab-pane active' id='profile'>";
									else echo "<div class='tab-pane' id='profile'>";
									
									include_once("./assets/inc/settings-player.php");
								?> 
                                </div>
								<?php
									if($page == "team") echo "<div class='tab-pane active' id='team'>";
									else echo "<div class='tab-pane' id='team'>";
									
									include_once("./assets/inc/settings-team.php");
								?>
								</div>
								<?php
								if($page == "notifications") echo "<div class='tab-pane active' id='notifications'>";
									else echo "<div class='tab-pane' id='notifications'>";
									
								include_once("./assets/inc/notifications.php");
								?>
                                </div>
								<?php
								if($page == "mod" && $steamprofile['steamid'] == $root_admin) echo "<div class='tab-pane active' id='mod'>";
									else echo "<div class='tab-pane' id='mod'>";
									
								include_once("./assets/inc/mod.php");
								?>
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
<!-- Edit datatables -->
<script src="./assets/js/dataTables.cellEdit.js"></script>
<!-- Warmod+ javascript methods -->
<script src="./assets/js/warmod_plus.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
        var table = $('#TeamMemberTable').DataTable({
            "pagingType": "full_numbers",
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            },
        });
		
		var playerform = $( "#PlayerProfile" );
		playerform.validate();
		
		$('#PlayerProfile_Save').on( 'click', function () {
			if(playerform.valid())	wm.showSwal('player-save', $('#PlayerProfile').serialize(), '', '')
		});
	} );
</script>
<?php
if(isset($session_team_id) && !empty($session_team_id) && $steamprofile['steamid'] == $session_team_leader)
{
	echo "<script type='text/javascript'>";
	include("./assets/js/settings_team.php");
	echo "</script>";
}

if(!isset($session_team_id) || empty($session_team_id))
{
	echo "<script type='text/javascript'>";
	include("./assets/js/create_team.js");
	echo "</script>";
}

if (!empty($notify_numb))
{
	echo "<script type='text/javascript'>";	
	include("./assets/js/notify.js");
	echo "</script>";
}

if($steamprofile['steamid'] == $root_admin)
{
	echo "<script type='text/javascript'>";	
	include("./assets/js/mod.js");
	echo "</script>";
}
?>
</html>