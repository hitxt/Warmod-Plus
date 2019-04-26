<?php
	switch($activePage){
		case "index":
			$navBrand = '<a class="navbar-brand" href="#">Dashboard</a>';
			break;
		
		case "matches":
			$navBrand = '<a class="navbar-brand" href="#">Matches</a>';
			break;

		case "players":
			$navBrand = '<a class="navbar-brand" href="#">Players</a>';
			break;
		
		case "teams":
			$navBrand = '<a class="navbar-brand" href="#">Teams</a>';
			break;
			
		case "showmatch":
			$navBrand = '<a class="navbar-brand text-white" href="./matches.php"><i class="material-icons">keyboard_arrow_left</i> Back To List</a>';
			break;

		case "showplayer":
			$navBrand = '<a class="navbar-brand" href="./players.php"><i class="material-icons">keyboard_arrow_left</i> Back To List</a>';
			break;
		
		case "showteam":
			$navBrand = '<a class="navbar-brand" href="./teams.php"><i class="material-icons">keyboard_arrow_left</i> Back To List</a>';
			break;
	}
?>
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
	<div class="container-fluid">
		<div class="navbar-wrapper">
			<?=$navBrand?>
		</div>
		<button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
			aria-expanded="false" aria-label="Toggle navigation">
			<span class="sr-only">Toggle navigation</span>
			<span class="navbar-toggler-icon icon-bar" style="<?=($activePage == "showmatch")?"background-color:#fff !important":""?>"></span>
			<span class="navbar-toggler-icon icon-bar" style="<?=($activePage == "showmatch")?"background-color:#fff !important":""?>"></span>
			<span class="navbar-toggler-icon icon-bar" style="<?=($activePage == "showmatch")?"background-color:#fff !important":""?>"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end">
			<form class="navbar-form" action="./libs/api.php?action=search" method="post">
				<div class="input-group no-border">
					<input type="text" class="form-control" placeholder="Match ID or Steam64 ID" name="id">
					<button type="submit" class="btn btn-white btn-round btn-just-icon">
						<i class="material-icons">search</i>
						<div class="ripple-container"></div>
					</button>
				</div>
			</form>
			<ul class="navbar-nav">
				<li class="nav-item dropdown">
					<a class="nav-link" href="http://example.com/" id="navbarDropdownMenuLink" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">
						<i class="material-icons <?=($activePage == "showmatch")?"text-white":""?>">notifications</i>
						<span class="notification">5</span>
						<p class="d-lg-none d-md-block">
							Some Actions
						</p>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
						<a class="dropdown-item" href="#">Mike John responded to your email</a>
						<a class="dropdown-item" href="#">You have 5 new tasks</a>
						<a class="dropdown-item" href="#">You're now friend with Andrew</a>
						<a class="dropdown-item" href="#">Another Notification</a>
						<a class="dropdown-item" href="#">Another One</a>
					</div>
				</li>
			</ul>
		</div>
	</div>
</nav>
<!-- End Navbar -->