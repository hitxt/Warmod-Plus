<?php
// ---------------------------------------------------
//  Database Settings
// ---------------------------------------------------
$db_host = "localhost";
$db_name = "wm";
$db_user = "root";
$db_password = "";

// ---------------------------------------------------
//  Database Table Settings
// ---------------------------------------------------
$stats_table = "wm_match_stats";
$result_table = "wm_results";
$player_table = "wm_players";
$team_table = "wm_teams";
$server_table = "wm_servers";
$invite_table = "wm_notify";
$license_table = "wm_license";
$game_logo_table = "wm_logo";

// ---------------------------------------------------
//  Plugin Settings
//  $plugin_version = Your plugin version, CSGO server side plugin will not load if version doesn't match.
//  $admins = Steam64 ID, admins who can manage server tokens and other mod settings.
// ---------------------------------------------------
$plugin_version = "18.05.24.0924.WM+.1";
$admins = ["76561198012825619"];

// ---------------------------------------------------
//  Upload Settings
//  $upload_dir = Path to upload team web logo.
//  $upload_dir2 = Path to upload team in-game logo.
//  $upload_dir3 = Path to your fastdl folder (My host doesn't allow me to create symlink).
// ---------------------------------------------------
$upload_dir = "/home/user/public_html/warmod/assets/img/teams/";
$upload_dir2 = "/home/user/public_html/warmod/assets/img/teams_game/";
$upload_dir3 = "/path/to/ur/fastdl/folder/";


// ---------------------------------------------------
//  Steam API Settings
//  $SteamAPI_Key = Your Steam API key.
//  $domainname = Your domain name for steam login.
//  $discord = Your discord server invite link
// ---------------------------------------------------
$SteamAPI_Key = "573449F51C4C9508A96DB87D3C83E67A";
$domainname = ""; 
$discord = "https://discord.gg/twVDJKC";

// ---------------------------------------------------
//  Other Settings
//  $server_list = Show server list on web? (my host doesn't allow me to use socket write permission.)
//  $timezone = Your server timezone.
// ---------------------------------------------------
$server_list = false;
$timezone = "Asia/Taipei";
?>
