<?php
	switch($activePage){
		case "index":
			$sideImage = './assets/img/sidebar-inferno.jpg';
			break;
		
		case "matches":
		case "showmatch":
			$sideImage = './assets/img/sidebar-train.jpg';
			break;

		case "players":
		case "showplayer":
			$sideImage = './assets/img/sidebar-cbble.jpg';
			break;
		
		case "teams":
		case "showteam":
			$sideImage = './assets/img/sidebar-nuke.jpg';
			break;
		
		case "settings":
			$sideImage = './assets/img/sidebar-cache.jpg';
			break;
		
		case "servers":
			$sideImage = './assets/img/sidebar-overpass.jpg';
			break;
	}

	if(!empty($_SESSION['steamid'])){
		$input = array(
			":id" => $_SESSION['steamid'],
		);
		$sql = "SELECT * FROM ".$player_table." WHERE steam_id_64 = :id";
		$sth = $pdo->prepare($sql);
		$sth->execute($input);
		$result = $sth->fetchAll();
		$user = new Player($result[0]);
	}
?>
<div class="sidebar" data-color="rose" data-background-color="black" data-image="<?=$sideImage?>">
	<div class="logo">
		<a href="./index.php" class="simple-text logo-mini">
			<img src="./assets/img/logo_white.png" width="30px" class="align-baseline">
		</a>
		<a href="./index.php" class="simple-text logo-normal">
			WARMOD+
		</a>
	</div>
	<div class="sidebar-wrapper">
		<div class="user">
			<div class="photo">
				<img src="<?=(!empty($_SESSION['steamid'])) ? $_SESSION['steam_avatarfull'] : './assets/img/default-avatar.png'?>">
			</div>
			<div class="user-info">
				<a data-toggle="collapse" href="#collapseUser" class="username">
					<span>
						<?=(!empty($_SESSION['steamid'])) ? $_SESSION['steam_personaname'] : "Guest"?> 
						<b class="caret"></b>
					</span>
				</a>
				<div class="collapse" id="collapseUser">
					<ul class="nav">
						<?php
							if((empty($_SESSION['steamid']))){
								?>
									<li class="nav-item">
										<a class="nav-link text-center" href="?login">
											<img src="./assets/img/sign-in.jpg" width="200px">
										</a>
									</li>
								<?php
							}
							else
							{
								?>
									<li class="nav-item">
										<a class="nav-link" href="./showplayer.php?id=<?=$_SESSION['steamid']?>">
											<span class="sidebar-normal"><i class="material-icons">person</i></span>
											<span class="sidebar-normal">My Profile</span>
										</a>
									</li>
									<?php
										if(!empty($user->team)){
											?>
												<li class="nav-item">
													<a class="nav-link" href="./showteam.php?id=<?=$user->team?>">
														<span class="sidebar-normal"><i class="material-icons">people</i></span>
														<span class="sidebar-normal">My Team</span>
													</a>
												</li>
											<?php
										}
									?>
									<li class="nav-item">
										<a class="nav-link" href="./settings.php">
											<span class="sidebar-normal"><i class="material-icons">settings</i></span>
											<span class="sidebar-normal">Settings</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="?logout">
											<span class="sidebar-normal"><i class="material-icons">power_settings_new</i></span>
											<span class="sidebar-normal">Logout</span>
										</a>
									</li>
								<?php
							}
						?>
					</ul>
				</div>
			</div>
		</div>
		<ul class="nav">
			<li class="nav-item <?=($activePage == "index") ? "active" : ""?>">
				<a class="nav-link" href="index.php">
					<i class="material-icons">dashboard</i>
					<p> Dashboard </p>
				</a>
			</li>
			<?php
				if($server_list){
					?>
						<li class="nav-item <?=($activePage == "servers") ? "active" : ""?>">
							<a class="nav-link" href="servers.php">
								<i class="material-icons">games</i>
								<p> Servers </p>
							</a>
						</li>
					<?php
				}
			?>
			<li class="nav-item <?=($activePage == "matches" || $activePage == "showmatch") ? "active" : ""?>">
				<a class="nav-link" href="matches.php">
					<i class="material-icons">public</i>
					<p> Matches </p>
				</a>
			</li>
			<li class="nav-item <?=($activePage == "players" || $activePage == "showplayer") ? "active" : ""?>">
				<a class="nav-link" href="players.php">
					<i class="material-icons">person</i>
					<p> Players </p>
				</a>
			</li>
			<li class="nav-item <?=($activePage == "teams" || $activePage == "showteam") ? "active" : ""?>">
				<a class="nav-link" href="teams.php">
					<i class="material-icons">people</i>
					<p> Teams </p>
				</a>
			</li>
			<?php
				if(!empty($discord)){
					?>
						<li class="nav-item">
							<a class="nav-link" href="<?=$discord?>">
								<i class="fab fa-2x fa-discord"></i>
								<p> Discord </p>
							</a>
						</li>
					<?php
				}
			?>
		</ul>
	</div>
</div>