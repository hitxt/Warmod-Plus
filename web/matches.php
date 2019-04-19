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
    <title>Warmod+ | Matches</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
   
	<meta property="og:site_name" content="Warmod+">
    <meta property="og:title" content="Warmod+ | Matches"/>
	<meta property="og:type" content="website">
	<meta property="og:image" content="./assets/img/logo.png">
	<meta property="og:description" content="Warmod+ Matches"/>
    
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
			<nav class="navbar navbar-transparent navbar-absolute">
				<div class="container-fluid">
					<div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="./matches.php" style='display:inline;'> Matches </a>
                    </div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-right">
							<li>
                                <a href="?view_mode_module&from_match">
                                    <i class='material-icons'>view_module</i>
                                    <p class="hidden-lg hidden-md">Module View</p>
                                </a>
                            </li>
							<li>
                                <a href="?view_mode_list&from_match">
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
						$limit=9;
						if (isset($_GET['start'])) $start=$_GET["start"];
						if (isset($_GET['s']))	$s=$_GET["s"];
						if (empty($start)) $start=0; 
						if (empty($s)) $s=0;
						$result=mysqli_query($link, "
						SELECT ".$result_table.".*, 
						IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
						IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
						FROM ".$result_table." order by match_id DESC"); 
						mysqli_set_charset ($link , "utf-8");
						$nummax=mysqli_num_rows($result); 
						$result=mysqli_query($link, "
						SELECT ".$result_table.".*, 
						IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
						IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
						FROM ".$result_table." order by match_id DESC limit ".$start.",".$limit."");
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
							echo "</div>
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
								<a class='page-link' href='matches.php?start=$pstart&s=$st' tabindex='-1'>
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
									<a  class='page-link' href=matches.php?start=$start&s=$s>";
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
										<a class='page-link' href=matches.php?start=$start&s=$s  aria-label='Next'>
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
					}
					elseif($_SESSION['view_mode'] == "list")
					{
						//$limit=9;
						//if (isset($_GET['start'])) $start=$_GET["start"];
						//if (isset($_GET['s']))	$s=$_GET["s"];
						//if (empty($start)) $start=0; 
						//if (empty($s)) $s=0;
						$result=mysqli_query($link, "SELECT ".$result_table.".*, 
						IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
						IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
						FROM ".$result_table." order by match_id DESC"); 
						mysqli_set_charset ($link , "utf-8");
						//$nummax=mysqli_num_rows($result); 
						//$result=mysqli_query($link, "select * from `".$result_table."` order by match_id DESC limit ".$start.",".$limit."");
						mysqli_set_charset ($link , "utf-8"); 
						$numb=mysqli_num_rows($result); 
						
						if (!empty($numb)) 
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
											<COL width='5%'><COL width='15%'><COL width='10%'><COL width='7%'><COL width='8%'><COL width='20%'><COL width='10%'><COL width='20%'><COL width='5%'>
											<thead class='thead-inverse'>
												<tr>
													<th>ID</th>
													<th>Date (GMT+8)</th>
													<th>Competition</th>
													<th>Event</th>
													<th>Map</th>
													<th>Team A</th>
													<th>Score</th>
													<th>Team B</th>
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
													<th>More</th>
                                                </tr>
                                            </tfoot>
											<tbody>
										";
										
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
									<td>".$ct_name."</td>
									<td><a href='./showmatch.php?id=".$match_id."'>View</a></td>
								</tr>";
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
						else echo "<h3>No Match Records</h3>";
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