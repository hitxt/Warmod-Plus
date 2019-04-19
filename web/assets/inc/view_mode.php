<?php
	if (isset($_GET['view_mode_list']))
	{
		$_SESSION['view_mode'] = "list";
		if (isset($_GET['from_match'])) { header('Location: ./matches.php'); exit;}
		elseif (isset($_GET['from_player'])) {	header('Location: ./players.php'); exit;}
		elseif (isset($_GET['from_team'])) {	header('Location: ./teams.php'); exit;}
	}
	if (isset($_GET['view_mode_module']))
	{
		$_SESSION['view_mode'] = "module";
		if (isset($_GET['from_match'])) { header('Location: ./matches.php'); exit;}
		elseif (isset($_GET['from_player'])) {	header('Location: ./players.php'); exit;}
		elseif (isset($_GET['from_team'])) {	header('Location: ./teams.php'); exit;}
	}
?>