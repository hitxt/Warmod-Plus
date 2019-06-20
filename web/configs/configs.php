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
$notify_table = "wm_notify";
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
//  $symlink = Is your host allow symlink? If not, set your fastdl path below.
//  $fastdl_path = Path to your fastdl root folder.
// ---------------------------------------------------
$symlink = false;
$fastdl_path = "D:/csgoserver/csgo/";

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
$server_list = true;
$timezone = "Asia/Taipei";
?>
