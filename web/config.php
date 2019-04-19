<?php
// mysql
$mysql_ip = "localhost";
$mysql_db = "";
$mysql_user = "";
$mysql_pass = "";
$stats_table = "wm_match_stats_test";
$result_table = "wm_results_test";
$player_table = "wm_players_test";
$team_table = "wm_teams_test";
$server_table = "wm_servers_test";
$invite_table = "wm_notify_test";
$license_table = "wm_license_test";
$game_logo_table = "wm_logo_test";
$root_admin = "76561198012825619";
$plugin_version = "18.05.24.0924.WM+.1";
$server_list = false;	// my host doesn't allow me to use socket write permission.
$link = mysqli_connect($mysql_ip, $mysql_user, $mysql_pass, $mysql_db);
mysqli_set_charset ($link , "utf-8");

// steamapi
$SteamAPI_Key = "";
$domainname = ""; // The main URL of your website displayed in steam the login page

// discord
$discord = "";

// logo upload
$upload_dir = "/home/user/public_html/warmod/assets/img/teams/";
$upload_dir2 = "/home/user/public_html/warmod/assets/img/teams_game/";

// my host doesn't allow me to create symlink
$upload_dir3 = "/path/to/ur/fastdl/folder/"
?>
