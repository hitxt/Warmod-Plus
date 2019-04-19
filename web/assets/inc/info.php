<?php
	if(!isset($_SESSION['steamid']))
		echo "<ul class='nav'>
		<li><a href='?login'><img src='./assets/img/sign-in.jpg' width='200px'></img></a></li></ul>";
	else 
	{
		echo "
		<ul class='nav'>
		<li><a href='./showplayer.php?id=".$_SESSION['steamid']."'>My Profile</a></li>
		";
		
		if(isset($session_team_id) && !empty($session_team_id))
			echo "<li><a href='./showteam.php?id=".$session_team_id."'>My Team</a></li>";
		else echo "<li><a href='javascript:wm.showNotification(\"top\",\"center\",\"danger\",\"You are not in any team, please join or create a team first.\")'>My Team</a></li>";
		
		echo "
		<li><a href='./settings.php'>Settings</a></li>
		<li><a href='?logout'>Log Out</a></li>
		</ul>";
	}
?>