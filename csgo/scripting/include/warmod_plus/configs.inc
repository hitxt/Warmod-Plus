void CheckConFigFiles(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	char dirName[PLATFORM_MAX_PATH];
	
	Format(dirName, sizeof(dirName), "cfg/sourcemod");
	if (!DirExists(dirName))
	{
		CreateDirectory(dirName, 511);
	}
	
	
	Format(cfgFile, sizeof(cfgFile), "cfg/sourcemod/plugin.kento_warmod+.cfg");
	if (!FileExists(cfgFile))
	{
		CreateAutoExecConfigFile(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/gamemode_competitive_server.cfg");
	if (!FileExists(cfgFile))
	{
		Gamemode_Competitive_Server(WM_VERSION);
	}
	
	Format(dirName, sizeof(dirName), "cfg/warmod+");
	if (!DirExists(dirName))
	{
		CreateDirectory(dirName, 511);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_global.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_Global(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_default.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_Default(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_overtime.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_OverTime(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_playout.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_PlayOut(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_warmup.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_WarmUp(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/practice.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_Practice(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/on_map_load.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_OnMapLoad(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_knife.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_Knife(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/on_match_end.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_OnMatchEnd(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_veto.cfg");
	if (!FileExists(cfgFile))
	{
		RuleSet_Veto(WM_VERSION);
	}
	
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/veto_maps_list.txt");
	if (!FileExists(cfgFile))
	{
		CreateVetoMapListFile();
	}
}

static void CreateAutoExecConfigFile(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/sourcemod/plugin.kento_warmod+.cfg");
	
	Handle file = OpenFile(cfgFile, "w");
	WriteFileLine(file, "// WarMod - Auto Executed Config File");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// This file will execute when warmod is loaded");
	WriteFileString(file, "exec warmod+/on_map_load.cfg", false); // no newline at the end
	CloseHandle(file);
}

static void Gamemode_Competitive_Server(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/gamemode_competitive_server.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Gamemode Competitive Server Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// This config is the base for all the CS:GO server commands");
	WriteFileLine(file, "// Only edit this file for NON-warmod commands");
	WriteFileLine(file, "// This is a modified version of the ESL 5v5 ladder config");
	WriteFileLine(file, "");
	WriteFileLine(file, "ammo_grenade_limit_default 1");
	WriteFileLine(file, "ammo_grenade_limit_flashbang 2");
	WriteFileLine(file, "ammo_grenade_limit_total 4");
	WriteFileLine(file, "");
	WriteFileLine(file, "bot_quota \"0\"					// Determines the total number of bots in the game");
	WriteFileLine(file, "");
	WriteFileLine(file, "player_bomb_defused							300");
	WriteFileLine(file, "player_bomb_planted							300");
	WriteFileLine(file, "player_damage_hostage							-30");
	WriteFileLine(file, "player_interact_with_hostage					300");
	WriteFileLine(file, "player_killed_enemy_default					300");
	WriteFileLine(file, "player_killed_enemy_factor						1");
	WriteFileLine(file, "player_killed_hostage							-1000");
	WriteFileLine(file, "player_killed_teammate							-300");
	WriteFileLine(file, "player_rescued_hostage							1000");
	WriteFileLine(file, "team_elimination_bomb_map						3250");
	WriteFileLine(file, "team_elimination_hostage_map_t					3000");
	WriteFileLine(file, "team_elimination_hostage_map_ct					3000");
	WriteFileLine(file, "team_hostage_alive								0");
	WriteFileLine(file, "team_hostage_interaction						600");
	WriteFileLine(file, "team_loser_bonus								1400");
	WriteFileLine(file, "team_loser_bonus_consecutive_rounds			500");
	WriteFileLine(file, "team_planted_bomb_but_defused					800");
	WriteFileLine(file, "team_rescued_hostage						600");
	WriteFileLine(file, "team_terrorist_win_bomb						3500");
	WriteFileLine(file, "team_win_by_defusing_bomb						3500");
	WriteFileLine(file, "team_win_by_hostage_rescue						2900");
	WriteFileLine(file, "team_win_by_time_running_out_hostage			3250");
	WriteFileLine(file, "team_win_by_time_running_out_bomb				3250");
	WriteFileLine(file, "");
	WriteFileLine(file, "ff_damage_reduction_grenade 0.85           	// How much to reduce damage done to teammates by a thrown grenade.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)");
	WriteFileLine(file, "ff_damage_reduction_bullets 0.33           	// How much to reduce damage done to teammates when shot.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)");
	WriteFileLine(file, "ff_damage_reduction_other 0.4              	// How much to reduce damage done to teammates by things other than bullets and grenades.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)");
	WriteFileLine(file, "ff_damage_reduction_grenade_self 1         	// How much to damage a player does to himself with his own grenade.  Range is from 0 - 1 (with 1 being damage equal to what is done to an enemy)");
	WriteFileLine(file, "");
	WriteFileLine(file, "mp_afterroundmoney 0				// amount of money awared to every player after each round");
	WriteFileLine(file, "mp_autokick 0					// Kick idle/team-killing players");
	WriteFileLine(file, "mp_autoteambalance 0");
	WriteFileLine(file, "mp_buytime 15                           	// How many seconds after round start players can buy items for.");
	WriteFileLine(file, "mp_c4timer 40                           	// How long from when the C4 is armed until it blows");
	WriteFileLine(file, "mp_death_drop_defuser 1				// Drop defuser on player death");
	WriteFileLine(file, "mp_death_drop_grenade 2				// Which grenade to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "mp_death_drop_gun 1				// Which gun to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "mp_defuser_allocation 0				// How to allocate defusers to CTs at start or round: 0=none, 1=random, 2=everyone");
	WriteFileLine(file, "mp_do_warmup_period 1				// Whether or not to do a warmup period at the start of a match.");
	WriteFileLine(file, "mp_forcecamera 1                        	// Restricts spectator modes for dead players");
	WriteFileLine(file, "mp_force_pick_time 160				// The amount of time a player has on the team screen to make a selection before being auto-teamed ");
	WriteFileLine(file, "mp_free_armor 0					// Determines whether armor and helmet are given automatically.");
	WriteFileLine(file, "mp_freezetime 12                        	// How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "mp_friendlyfire 1                       	// Allows team members to injure other members of their team");
	WriteFileLine(file, "mp_halftime 1					// Determines whether or not the match has a team-swapping halftime event.");
	WriteFileLine(file, "mp_halftime_duration 15				// Number of seconds that halftime lasts");
	WriteFileLine(file, "mp_join_grace_time 30				// Number of seconds after round start to allow a player to join a game");
	WriteFileLine(file, "mp_limitteams 0                         	// Max # of players 1 team can have over another (0 disables check)");
	WriteFileLine(file, "mp_logdetail 3                          	// Logs attacks.  Values are: 0=off, 1=enemy, 2=teammate, 3=both)");
	WriteFileLine(file, "mp_molotovusedelay 0                    	// Number of seconds to delay before the molotov can be used after acquiring it");
	WriteFileLine(file, "mp_playercashawards 1				// Players can earn money by performing in-game actions");
	WriteFileLine(file, "mp_playerid 0					// Controls what information player see in the status bar: 0 all names; 1 team names; 2 no names ");
	WriteFileLine(file, "mp_playerid_delay 0.5				// Number of seconds to delay showing information in the status bar");
	WriteFileLine(file, "mp_playerid_hold 0.25				// Number of seconds to keep showing old information in the status bar");
	WriteFileLine(file, "mp_round_restart_delay 5			// Number of seconds to delay before restarting a round after a win");
	WriteFileLine(file, "mp_roundtime 1.92                    	// How many minutes each round takes.");
	WriteFileLine(file, "mp_roundtime_defuse 1.92                	// How many minutes each round takes on defusal maps.");
	WriteFileLine(file, "mp_solid_teammates 1 				// Determines whether teammates are solid or not.");
	WriteFileLine(file, "mp_teamcashawards 1				// Teams can earn money by performing in-game actions");
	WriteFileLine(file, "mp_timelimit 0                           	// game time per map in minutes");
	WriteFileLine(file, "mp_tkpunish 0					// Will a TK'er be punished in the next round?  {0=no,  1=yes}");
	WriteFileLine(file, "mp_endmatch_votenextmap 0			// Whether or not players vote for the next map at the end of the match when the final scoreboard comes up");
	WriteFileLine(file, "mp_warmuptime 1					// If true, there will be a warmup period/round at the start of each match to allow");
	WriteFileLine(file, "mp_weapons_allow_map_placed 1             	// If this convar is set, when a match starts, the game will not delete weapons placed in the map.");
	WriteFileLine(file, "mp_weapons_allow_zeus 1				// Determines whether the Zeus is purchasable or not.");
	WriteFileLine(file, "");
	WriteFileLine(file, "spec_freeze_time 5.0                            // Time spend frozen in observer freeze cam.");
	WriteFileLine(file, "spec_freeze_panel_extended_time 0               // Time spent with the freeze panel still up after observer freeze cam is done.");
	WriteFileLine(file, "");
	WriteFileLine(file, "sv_accelerate 5.5			        // ( def. \"10\" ) client notify replicated ");
	WriteFileLine(file, "sv_stopspeed 80					//");
	WriteFileLine(file, "sv_allow_votes 1				// Allow voting?");
	WriteFileLine(file, "sv_vote_allow_in_warmup 1				// Allow voting in warmup?");
	WriteFileLine(file, "sv_allow_wait_command 0			        // Allow or disallow the wait command on clients connected to this server.");
	WriteFileLine(file, "sv_alltalk 1					// Players can hear all other players' voice communication, no team restrictions");
	WriteFileLine(file, "sv_talk_enemy_dead		1		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		1		//Alltalk");
	WriteFileLine(file, "sv_alternateticks 0				// If set, server only simulates entities on even numbered ticks.");
	WriteFileLine(file, "sv_cheats 0                                     // Allow cheats on server");
	WriteFileLine(file, "sv_clockcorrection_msecs 15                     // The server tries to keep each player's m_nTickBase withing this many msecs of the server absolute tickcount");
	WriteFileLine(file, "sv_consistency 0				// Whether the server enforces file consistency for critical files");
	WriteFileLine(file, "sv_contact 0					// Contact email for server sysop");
	WriteFileLine(file, "sv_damage_print_enable 1                        // Turn this off to disable the player's damage feed in the console after getting killed.");
	WriteFileLine(file, "sv_dc_friends_reqd 0			        // Set this to 0 to allow direct connects to a game in progress even if no presents");
	WriteFileLine(file, "sv_deadtalk 1					// Dead players can speak (voice, text) to the living");
	WriteFileLine(file, "sv_forcepreload 0				// Force server side preloading.");
	WriteFileLine(file, "sv_friction 5.2					// World friction.");
	WriteFileLine(file, "sv_full_alltalk 0				// Any player (including Spectator team) can speak to any other player");
	WriteFileLine(file, "sv_gameinstructor_disable 1		        // Force all clients to disable their game instructors.");
	WriteFileLine(file, "sv_ignoregrenaderadio 0                         // Turn off Fire in the hole messages");
	WriteFileLine(file, "sv_kick_players_with_cooldown 0                 // (0: do not kick; 1: kick Untrusted players; 2: kick players with any cooldown)");
	WriteFileLine(file, "sv_kick_ban_duration 0                          // How long should a kick ban from the server should last (in minutes)");
	WriteFileLine(file, "sv_lan 0                                        // Server is a lan server ( no heartbeat, no authentication, no non-class C addresses )");
	WriteFileLine(file, "sv_log_onefile 0				// Log server information to only one file.");
	WriteFileLine(file, "sv_logbans 1					// Log server bans in the server logs.");
	WriteFileLine(file, "sv_logecho 1					// Echo log information to the console.");
	WriteFileLine(file, "sv_logfile 1					// Log server information in the log file.");
	WriteFileLine(file, "sv_logflush 0					// Flush the log file to disk on each write (slow).");
	WriteFileLine(file, "sv_logsdir logfiles                             // Folder in the game directory where server logs will be stored.");
	WriteFileLine(file, "sv_maxrate 0					// min. 0.000000 max. 30000.000000 replicated  Max bandwidth rate allowed on server, 0 == unlimited");
	WriteFileLine(file, "sv_mincmdrate 64				// This sets the minimum value for cl_cmdrate. 0 == unlimited.");
	WriteFileLine(file, "sv_minupdaterate 64			// This sets the minimum value for cl_updaterate. 0 == unlimited.");
	WriteFileLine(file, "sv_minrate 80000				// Min bandwidth rate allowed on server, 0 == unlimited");
	WriteFileLine(file, "sv_competitive_minspec 1                        // Enable to force certain client convars to minimum/maximum values to help prevent competitive advantages.");
	WriteFileLine(file, "sv_competitive_official_5v5 1			// Enable to force the server to show 5v5 scoreboards and allows spectators to see characters through walls.");
	WriteFileLine(file, "sv_pausable 1                                   // Is the server pausable.");
	WriteFileLine(file, "sv_pure 1");
	WriteFileLine(file, "sv_pure_kick_clients 1                          // If set to 1, the server will kick clients with mismatching files. Otherwise, it will issue a warning to the client.");
	WriteFileLine(file, "sv_pure_trace 0					// If set to 1, the server will print a message whenever a client is verifying a CR");
	WriteFileLine(file, "sv_spawn_afk_bomb_drop_time 30                 	// Players that spawn and don't move for longer than sv_spawn_afk_bomb_drop_time (default 15 seconds) will automatically drop the bomb.");
	WriteFileLine(file, "sv_steamgroup_exclusive 0                     	// If set, only members of Steam group will be able to join the server when it's empty, public people will be able to join the server only if it has players.");
	WriteFileLine(file, "sv_voiceenable 1");
	WriteFileLine(file, "sv_coaching_enabled 1			// To enable coaches.  To coach a team, type either 'coach t' or 'coach ct'.", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_Global(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_global.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Global Ruleset Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// This config is executed by all other rulesets");
	WriteFileLine(file, "// It holds the majority of commands, so that each ruleset can use as a base configuration");
	WriteFileLine(file, "//Remove prac commands");
	WriteFileLine(file, "");
	WriteFileLine(file, "sv_infinite_ammo				\"0\"	//Players active weapon will never run out of ammo. If set to 2 then player has infinite total ammo but still has to reload the weapon");
	WriteFileLine(file, "sv_showimpacts				\"0\"	//Shows client (red) and server (blue) bullet impact point (1=both, 2=client-only, 3=server-only)");
	WriteFileLine(file, "sv_cheats				\"0\"	//Allow cheats on server (cheat console commands not hacks)");
	WriteFileLine(file, "mp_friendlyfire				\"1\"	//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Multiplayer");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_active				\"1\"	//Enable or disable WarMod as active");
	WriteFileLine(file, "wm_max_players				\"10\"	//Sets the maximum players allowed on both teams combined, others will be forced to spectator (0 = unlimited)");
	WriteFileLine(file, "wm_round_money				\"1\"	//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "wm_min_ready				\"10\"	//Sets the minimum required ready players to Live on 3");
	WriteFileLine(file, "wm_ingame_scores			\"1\"	//Enable or disable ingame scores to be showed at the end of each round");
	WriteFileLine(file, "wm_lock_teams				\"1\"	//Enable or disable locked teams when a match is running");
	WriteFileLine(file, "tv_enable					\"1\"	//GOTV enabled?");
	WriteFileLine(file, "wm_auto_record				\"1\"	//Enable or disable auto SourceTV demo record on Live on 3");
	WriteFileLine(file, "mp_overtime_enable		\"0\"		//If a match ends in a tie, use overtime rules to determine winner");
	WriteFileLine(file, "");
	WriteFileLine(file, "//WarMod Knife");
	WriteFileLine(file, "wm_auto_knife				\"1\"	//Enable or disable the knife round before going live");
	WriteFileLine(file, "wm_knife_auto_start			\"1\"	//Enable or disable after knife round to be forced lived");
	WriteFileLine(file, "wm_knife_hegrenade			\"0\"	//Enable or disable giving a player a hegrenade on Knife on 3");
	WriteFileLine(file, "wm_knife_flashbang			\"0\"	//Sets how many flashbangs to give a player on Knife on 3");
	WriteFileLine(file, "wm_knife_smokegrenade			\"0\"	//Enable or disable giving a player a smokegrenade on Knife on 3");
	WriteFileLine(file, "wm_knife_zeus				\"0\"	//Enable or disable giving a player a zeus on Knife on 3");
	WriteFileLine(file, "wm_knife_armor				\"0\"	//Enable or disable giving a player Armor on Knife on 3");
	WriteFileLine(file, "wm_knife_helmet				\"0\"	//Enable or disable giving a player a Helmet on Knife on 3 [requires armor active]");
	WriteFileLine(file, "");
	WriteFileLine(file, "//WarMod Pause");
	WriteFileLine(file, "");
	WriteFileLine(file, "sv_pausable				\"1\"	//Is the server pausable");
	WriteFileLine(file, "wm_auto_pause			\"1\"	//Will pause server if team players equals less than half of wm_max_players: 0 = off, 1 = on");
	WriteFileLine(file, "wm_pause_confirm			\"0\"	//Wait for other team to confirm pause: 0 = off, 1 = on");
	WriteFileLine(file, "wm_unpause_confirm		\"0\"	//Wait for other team to confirm unpause: 0 = off, 1 = on");
	WriteFileLine(file, "wm_auto_unpause			\"1\"	//Sets auto unpause: 0 = off, 1 = on");
	WriteFileLine(file, "wm_auto_unpause_delay		\"60\"	//Sets the seconds to wait before auto unpause");
	WriteFileLine(file, "wm_pause_limit			\"3\"	//Sets max pause count per team");
	WriteFileLine(file, "wm_pause_limit_half			\"0\"	//Reset pause count in half? 0 = No, 1 = Yes");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Misc");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_show_info				\"1\"	//Enable or disable the display of the Ready System to players");
	WriteFileLine(file, "wm_rcon_only				\"0\"	//Enable or disable admin commands to be only executed via RCON or console");
	WriteFileLine(file, "wm_auto_ready				\"1\"	//Enable or disable the ready system being automatically enabled on map change");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Ban");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_ban_on_disconnect		\"0\"	//Enable or disable players banned on disconnect if match is live");
	WriteFileLine(file, "wm_ban_percentage		\"0.75\"	//Percentage of wm_max_players that will be banned on disconnect");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Warmod Veto");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_veto					\"1\"	//Veto Style: 0 = off, 1 = Bo1, 2 = Bo2, 3 = Bo3, 5 = Bo5");
	WriteFileLine(file, "wm_veto_knife				\"1\"	//Requires a knife round to determine who votes first: 0 = off, 1 = on");
	WriteFileLine(file, "wm_veto_bo3				\"1\"	//Veto Style: 0 = Normal, 1 = New");
	WriteFileLine(file, "wm_veto_random				\"0\"	//After the vetoing is done, will a map be picked at random?");
	WriteFileLine(file, "wm_veto_select				\"0\"	//On last two maps of Veto Bo1 will it be select map to play: 0 = No, 1 = Yes");
	WriteFileLine(file, "wm_pugsetup_maplist_file			\"warmod+/veto_maps_list.txt\"	//Veto Map List to read from. Located in cfg folder");
	WriteFileLine(file, "wm_pugsetup_randomize_maps		\"1\"	//When maps are shown in the map vote/veto, should their order be randomized?");
	WriteFileLine(file, "");
	WriteFileLine(file, "exec gamemode_competitive_server.cfg", false);// no newline at the end
	CloseHandle(file);
}

static void RuleSet_Default(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_default.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Default Ruleset Config");
	WriteFileLine(file, "// Created via WarMod for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Default commands");
	WriteFileLine(file, "sv_alltalk		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"0\"		//Alltalk");
	WriteFileLine(file, "mp_buyanywhere		\"0\"		//Buy Anywhere");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_default.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_enable		\"0\"		//If a match ends in a tie, use overtime rules to determine winner");
	WriteFileLine(file, "mp_overtime_maxrounds		\"6\"		//When overtime is enabled play additional rounds to determine winner");
	WriteFileLine(file, "mp_overtime_startmoney		\"10000\"		//Money assigned to all players at start of every overtime half");
	WriteFileLine(file, "mp_startmoney		\"800\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Default Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_PlayOut(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_playout.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - PlayOut Ruleset Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change PlayOut commands");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_playout.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"0\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_enable		\"0\"		//If a match ends in a tie, use overtime rules to determine winner");
	WriteFileLine(file, "mp_overtime_maxrounds		\"6\"		//When overtime is enabled play additional rounds to determine winner");
	WriteFileLine(file, "mp_overtime_startmoney		\"10000\"		//Money assigned to all players at start of every overtime half");
	WriteFileLine(file, "mp_startmoney		\"10000\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say PlayOut Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_OverTime(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_overtime.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - OverTime Ruleset Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change OverTime commands");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_overtime.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_maxrounds		\"6\"		//When overtime is enabled play additional rounds to determine winner");
	WriteFileLine(file, "mp_overtime_startmoney 		\"800\"		//Money assigned to all players at start of every overtime half");
	WriteFileLine(file, "mp_startmoney		\"800\"		//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say OverTime Config Loaded", false);// no newline at the end
	CloseHandle(file);
}

static void RuleSet_WarmUp(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_warmup.cfg");

	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - WarmUp Ruleset Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change WarmUp commands");
	WriteFileLine(file, "sv_alltalk		\"1\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"1\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"1\"		//Alltalk");
	WriteFileLine(file, "wm_warmup_respawn		\"1\"		//Enable or disable the respawning of players in warmup");
	WriteFileLine(file, "wm_block_warm_up_grenades		\"1\"	//Enable or disable grenade blocking in warmup");
	WriteFileLine(file, "mp_warmuptime		\"300\"		//How long the warmup period lasts. Changing this value resets warmup.");
	WriteFileLine(file, "mp_buytime		\"120\"		//How many seconds after round start players can buy items for.");
	WriteFileLine(file, "mp_freezetime		\"0\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "mp_autokick		\"0\"		//Kick idle/team-killing/team-damaging players");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "mp_startmoney		\"16000\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_warmup_pausetimer		\"1\"		//Set to 1 to stay in warmup indefinitely. Set to 0 to resume the timer.");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_infinite_ammo		\"0\"		//Player's active weapon will never run out of ammo.");
	WriteFileLine(file, "sv_showimpacts		\"0\"			//Shows client (red) and server (blue) bullet impact point");
	WriteFileLine(file, "sv_cheats			\"0\"			//Allow cheats on server");
	WriteFileLine(file, "mp_buy_anywhere		\"0\"		//When set, players can buy anywhere, not only in buyzones");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_death_drop_defuser	\"0\"			// Drop defuser on player death");
	WriteFileLine(file, "mp_death_drop_grenade	\"0\"			// Which grenade to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "mp_death_drop_gun	\"0\"			// Which gun to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "wm_active			\"1\"		//Enable or disable WarMod as active");
	WriteFileLine(file, "sv_allow_votes	\"1\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say WarmUp Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_Practice(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/practice.cfg");

	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Practice Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change WarmUp commands");
	WriteFileLine(file, "wm_warmup_respawn		\"0\"		//Enable or disable the respawning of players in warmup");
	WriteFileLine(file, "wm_block_warm_up_grenades		\"0\"	//Enable or disable grenade blocking in warmup");
	WriteFileLine(file, "mp_buytime		\"120\"		//How many seconds after round start players can buy items for.");
	WriteFileLine(file, "mp_freezetime		\"0\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "mp_autokick		\"0\"		//Kick idle/team-killing/team-damaging players");
	WriteFileLine(file, "mp_startmoney		\"16000\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_infinite_ammo		\"2\"		//Player's active weapon will never run out of ammo.");
	WriteFileLine(file, "sv_showimpacts		\"2\"			//Shows client (red) and server (blue) bullet impact point");
	WriteFileLine(file, "sv_cheats			\"1\"			//Allow cheats on server");
	WriteFileLine(file, "mp_buy_anywhere		\"1\"		//When set, players can buy anywhere, not only in buyzones");
	WriteFileLine(file, "mp_roundtime_defuse		\"9999\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_friendlyfire		\"0\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "bot_quota			\"0\"		//Determines the total number of bots in the game.");
	WriteFileLine(file, "wm_active			\"0\"		//Enable or disable WarMod as active");
	WriteFileLine(file, "sv_allow_votes	\"1\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Practice Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_OnMapLoad(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/on_map_load.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - On Map Load Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// This config file is executed on every map change, including when the server first starts");
	WriteFileLine(file, "// Note: Plugins have been loaded by now");
	WriteFileLine(file, "");
	WriteFileLine(file, "//WarMod Configs");
	WriteFileLine(file, "wm_warmod_safemode		\"0\"		//This disables features that usually break on a CS:GO update");
	WriteFileLine(file, "wm_match_config			\"warmod+/ruleset_default.cfg\"	//Sets the match config to load on Live on 3");
	WriteFileLine(file, "wm_default_config			\"warmod+/ruleset_default.cfg\"	//Sets the match config to load when the default command is called");
	WriteFileLine(file, "wm_overtime_config			\"warmod+/ruleset_overtime.cfg\"	//Sets the match config to load when the overtime command is called");
	WriteFileLine(file, "wm_playout_config			\"warmod+/ruleset_playout.cfg\"	//Sets the match config to load when the play out command is called");
	WriteFileLine(file, "wm_prac_config			\"warmod+/practice.cfg\"	//Loads when the practice command is called");
	WriteFileLine(file, "wm_reset_config			\"warmod+/on_match_end.cfg\"	//Sets the config to load at the end/reset of a match");
	WriteFileLine(file, "wm_veto_config			\"warmod+/ruleset_veto.cfg\"	//Sets the veto knife config to load on Veto Knife on 3");
	WriteFileLine(file, "wm_reset_config_delay			\"1\"	//The amount of time before executing the reset config after a match");
	WriteFileLine(file, "wm_damageprint_enabled			\"1\"	//Whether to enabled damage print to client on round end");
	WriteFileLine(file, "wm_gamename_enable			\"1\"	//Enable or disable WarMod game name");
	WriteFileLine(file, "wm_gamename			\"- WARMOD+ -\"	//WarMod game name");
	WriteFileLine(file, "wm_user_token			\"\"	//WarMod+ user token.");
	WriteFileLine(file, "wm_save_database		\"1\"	//0 - Will not save match record, gotv demo and player stats to database");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Stats");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_save_dir				\"warmod\"	//Directory to store SourceTV demos and WarMod logs");
	WriteFileLine(file, "wm_prefix_logs			\"1\"		//Enable or disable the prefixing of \"_\" to uncompleted match SourceTV demos and WarMod logs");
	WriteFileLine(file, "wm_stats_method			\"2\"		//Sets the stats logging method: 0 = UDP stream/server logs, 1 = WarMod logs, 2 = both");
	WriteFileLine(file, "wm_stats_trace			\"0\"		//Enable or disable updating all player positions, every wm_stats_trace_delay seconds");
	WriteFileLine(file, "wm_stats_trace_delay		\"5\"		//The ammount of time between sending player position updates");
	WriteFileLine(file, "wm_competition			\"\"	//Name of host for a competition. eg. ESEA, Cybergamer, CEVO, ESL");
	WriteFileLine(file, "wm_event					\"\"		//Name of event. eg. Season #, ODC #, Ladder");
	WriteFileLine(file, "wm_chat_prefix			\"Warmod+\"	//Change the chat prefix. Default is WarMod+");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Voice Communications");
	WriteFileLine(file, "");
	WriteFileLine(file, "sm_deadtalk					\"0\"			//Controls how dead communicate 0 - Off, 1 - Dead players ignore teams, 2 - Dead players talk to living teammates");
	WriteFileLine(file, "");
	WriteFileLine(file, "//C4 Timer");
	WriteFileLine(file, "wm_c4_all			\"0\"				//Show c4 message to all? 1=all 0=gotv & spec.");
	WriteFileLine(file, "");
	WriteFileLine(file, "exec warmod+/ruleset_warmup.cfg", false);// no newline at the end
	CloseHandle(file);
}

static void RuleSet_Knife(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_knife.cfg");
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_knife.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Knife Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Knife commands");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_startmoney		\"0\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"60.00\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"60.00\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "sv_alltalk		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"0\"		//Alltalk");
	WriteFileLine(file, "");
	
	WriteFileLine(file, "say Knife Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void RuleSet_OnMatchEnd(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/on_match_end.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - On Match End Config");
	WriteFileLine(file, "// Created via WarMod v%s", WM_VERSION);
	WriteFileLine(file, "// This config is executed at the end or reset of a match");
	WriteFileLine(file, "mp_teamname_1 			\"\"		//A non-empty string overrides the first team's name");
	WriteFileLine(file, "mp_teamname_2 			\"\"		//A non-empty string overrides the second team's name");
	WriteFileLine(file, "mp_teamlogo_1 			\"\"		//Enter a team's shorthand image name to display their logo");
	WriteFileLine(file, "mp_teamlogo_2 			\"\"		//Enter a team's shorthand image name to display their logo");
	WriteFileLine(file, "mp_teamflag_1 			\"\"		//Team Country Flag");
	WriteFileLine(file, "mp_teamflag_2 			\"\"		//Team Country Flag");
	WriteFileLine(file, "mp_teammatchstat_holdtime \"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	WriteFileLine(file, "mp_teammatchstat_txt 	\"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	WriteFileLine(file, "mp_teammatchstat_1 	\"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	WriteFileLine(file, "mp_teammatchstat_2		\"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	WriteFileLine(file, "mp_teamprediction_txt 	\"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	WriteFileLine(file, "mp_teamprediction_pct 	\"\"		//http://www.tobyscs.com/csgo-custom-team-logos-flags/");
	CloseHandle(file);
}

static void RuleSet_Veto(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_veto.cfg");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Knife Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Knife commands");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_startmoney		\"0\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"60.00\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"60.00\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "mp_freezetime		\"12\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "sv_alltalk		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"0\"		//Alltalk");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Knife Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
}

static void CreateVetoMapListFile()
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/veto_maps_list.txt");
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "de_mirage");
	WriteFileLine(file, "de_train");
	WriteFileLine(file, "de_overpass");
	WriteFileLine(file, "de_cache");
	WriteFileLine(file, "de_nuke");
	WriteFileLine(file, "de_cbble", false); // no newline at the end
	CloseHandle(file);
}

void Update_Configs(char[] WM_VERSION)
{
	UpdateRuleSet_Global(WM_VERSION);
}

static void UpdateRuleSet_Global(char[] WM_VERSION)
{
	//ServerCommand("exec warmod+/ruleset_global.cfg");
	
	char dirName[PLATFORM_MAX_PATH];
	Format(dirName, sizeof(dirName), "cfg/warmod+");
	if (!DirExists(dirName))
		CreateDirectory(dirName, 751);
	
	char cfgFile[PLATFORM_MAX_PATH];
	char convarString[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_global.cfg");
	
	DeleteFile(cfgFile);
	
	
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Global Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// This config is executed by all other rulesets");
	WriteFileLine(file, "// It holds the majority of commands, so that each ruleset can use a base configuration");
	WriteFileLine(file, "//Remove prac commands");
	WriteFileLine(file, "");
	WriteFileLine(file, "sv_infinite_ammo				\"0\"	//Players active weapon will never run out of ammo. If set to 2 then player has infinite total ammo but still has to reload the weapon");
	WriteFileLine(file, "sv_showimpacts				\"0\"	//Shows client (red) and server (blue) bullet impact point (1=both, 2=client-only, 3=server-only)");
	WriteFileLine(file, "sv_cheats				\"0\"	//Allow cheats on server (cheat console commands not hacks)");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Multiplayer");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_active				\"%i\"	//Enable or disable WarMod as active", GetConVarInt(FindConVar("wm_active")));
	WriteFileLine(file, "wm_max_players				\"%i\"	//Sets the maximum players allowed on both teams combined, others will be forced to spectator (0 = unlimited)", GetConVarInt(FindConVar("wm_max_players")));
	WriteFileLine(file, "wm_round_money				\"%i\"	//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)", GetConVarInt(FindConVar("wm_round_money")));
	WriteFileLine(file, "wm_min_ready				\"%i\"	//Sets the minimum required ready players to Live on 3", GetConVarInt(FindConVar("wm_min_ready")));
	WriteFileLine(file, "wm_ingame_scores			\"%i\"	//Enable or disable ingame scores to be showed at the end of each round", GetConVarInt(FindConVar("wm_ingame_scores")));
	WriteFileLine(file, "wm_lock_teams				\"%i\"	//Enable or disable locked teams when a match is running", GetConVarInt(FindConVar("wm_lock_teams")));
	WriteFileLine(file, "tv_enable					\"%i\"	//GOTV enabled?", GetConVarInt(FindConVar("tv_enable")));
	WriteFileLine(file, "wm_auto_record				\"%i\"	//Enable or disable auto SourceTV demo record on Live on 3", GetConVarInt(FindConVar("wm_auto_record")));
	WriteFileLine(file, "mp_overtime_enable		\"%i\"		//If a match ends in a tie, use overtime rules to determine winner", GetConVarInt(FindConVar("mp_overtime_enable")));
	WriteFileLine(file, "");
	WriteFileLine(file, "//WarMod Knife");
	WriteFileLine(file, "wm_auto_knife				\"%i\"	//Enable or disable the knife round before going live", GetConVarInt(FindConVar("wm_auto_knife")));
	WriteFileLine(file, "wm_knife_auto_start			\"%i\"	//Enable or disable after knife round to be forced lived", GetConVarInt(FindConVar("wm_knife_auto_start")));
	WriteFileLine(file, "wm_knife_hegrenade			\"%i\"	//Enable or disable giving a player a hegrenade on Knife on 3", GetConVarInt(FindConVar("wm_knife_hegrenade")));
	WriteFileLine(file, "wm_knife_flashbang			\"%i\"	//Sets how many flashbangs to give a player on Knife on 3", GetConVarInt(FindConVar("wm_knife_flashbang")));
	WriteFileLine(file, "wm_knife_smokegrenade			\"%i\"	//Enable or disable giving a player a smokegrenade on Knife on 3", GetConVarInt(FindConVar("wm_knife_smokegrenade")));
	WriteFileLine(file, "wm_knife_zeus				\"%i\"	//Enable or disable giving a player a zeus on Knife on 3", GetConVarInt(FindConVar("wm_knife_zeus")));
	WriteFileLine(file, "wm_knife_armor				\"%i\"	//Enable or disable giving a player Armor on Knife on 3", GetConVarInt(FindConVar("wm_knife_armor")));
	WriteFileLine(file, "wm_knife_helmet				\"%i\"	//Enable or disable giving a player a Helmet on Knife on 3 [requires armor active]", GetConVarInt(FindConVar("wm_knife_helmet")));
	WriteFileLine(file, "");
	WriteFileLine(file, "//WarMod Pause");
	WriteFileLine(file, "");
	WriteFileLine(file, "sv_pausable				\"%i\"	//Is the server pausable.", GetConVarInt(FindConVar("sv_pausable")));
	WriteFileLine(file, "wm_auto_pause			\"%i\"	//Will pause server if team players equals less than half of wm_max_players: 0 = off, 1 = on", GetConVarInt(FindConVar("wm_auto_pause")));
	WriteFileLine(file, "wm_pause_confirm			\"%i\"	//Wait for other team to confirm pause: 0 = off, 1 = on", GetConVarInt(FindConVar("wm_pause_confirm")));
	WriteFileLine(file, "wm_unpause_confirm			\"%i\"	//Wait for other team to confirm unpause: 0 = off, 1 = on", GetConVarInt(FindConVar("wm_unpause_confirm")));
	WriteFileLine(file, "wm_auto_unpause				\"%i\"	//Sets auto unpause: 0 = off, 1 = on", GetConVarInt(FindConVar("wm_auto_unpause")));
	WriteFileLine(file, "wm_auto_unpause_delay			\"%i\"	//Sets the seconds to wait before auto unpause", GetConVarInt(FindConVar("wm_auto_unpause_delay")));
	WriteFileLine(file, "wm_pause_limit				\"%i\"	//Sets max pause count per team", GetConVarInt(FindConVar("wm_pause_limit")));
	WriteFileLine(file, "wm_pause_limit_half			\"i\"	//Reset pause count in half? 0 = No, 1 = Yes", GetConVarInt(FindConVar("wm_pause_limit_half")));
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Misc");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_show_info				\"%i\"	//Enable or disable the display of the Ready System to players", GetConVarInt(FindConVar("wm_show_info")));
	WriteFileLine(file, "wm_rcon_only				\"%i\"	//Enable or disable admin commands to be only executed via RCON or console", GetConVarInt(FindConVar("wm_rcon_only")));
	WriteFileLine(file, "wm_auto_ready				\"%i\"	//Enable or disable the ready system being automatically enabled on map change", GetConVarInt(FindConVar("wm_auto_ready")));
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Ban");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_ban_on_disconnect		\"%i\"	//Enable or disable players banned on disconnect if match is live", GetConVarInt(FindConVar("wm_ban_on_disconnect")));
	WriteFileLine(file, "wm_ban_percentage		\"%-.2f%\"	//Percentage of wm_max_players that will be banned on disconnect", GetConVarFloat(FindConVar("wm_ban_percentage")));
	WriteFileLine(file, "");
	WriteFileLine(file, "// Warmod Veto");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_veto					\"%i\"	//Veto Style: 0 = off, 1 = Bo1, 2 = Bo2, 3 = Bo3, 5 = Bo5", GetConVarInt(FindConVar("wm_veto")));
	WriteFileLine(file, "wm_veto_knife				\"%i\"	//Requires a knife round to determine who votes first: 0 = off, 1 = on", GetConVarInt(FindConVar("wm_veto_knife")));
	WriteFileLine(file, "wm_veto_bo3				\"%i\"	//Veto Style: 0 = Normal, 1 = New", GetConVarInt(FindConVar("wm_veto_bo3")));
	WriteFileLine(file, "wm_veto_random				\"%i\"	//After the vetoing is done, will a map be picked at random?", GetConVarInt(FindConVar("wm_veto_random")));
	WriteFileLine(file, "wm_veto_select				\"%i\"	//On last two maps of Veto Bo1 will it be select map to play: 0 = No, 1 = Yes", GetConVarInt(FindConVar("wm_veto_select")));
	GetConVarString(FindConVar("wm_pugsetup_maplist_file"), convarString, sizeof(convarString));
	if (StrEqual(convarString, "configs/maps.txt", false))
	{
		Format(convarString, sizeof(convarString), "warmod+/veto_maps_list.txt");
	}
	WriteFileLine(file, "wm_pugsetup_maplist_file			\"%s\"	//Veto Map List to read from. Located in cfg folder", convarString);
	WriteFileLine(file, "wm_pugsetup_randomize_maps		\"%i\"	//When maps are shown in the map vote/veto, should their order be randomized?", GetConVarInt(FindConVar("wm_pugsetup_randomize_maps")));
	WriteFileLine(file, "");
	WriteFileLine(file, "exec gamemode_competitive_server.cfg", false);// no newline at the end
	CloseHandle(file);
	
	UpdateRuleSet_Default(WM_VERSION);
}

static void UpdateRuleSet_Default(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_default.cfg");
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_default.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Default Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Default commands");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_default.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_enable		\"0\"		//If a match ends in a tie, use overtime rules to determine winner");
	WriteFileLine(file, "mp_overtime_maxrounds		\"%i\"		//When overtime is enabled play additional rounds to determine winner", GetConVarInt(FindConVar("mp_overtime_maxrounds")));
	WriteFileLine(file, "mp_overtime_startmoney		\"%i\"		//Money assigned to all players at start of every overtime half", GetConVarInt(FindConVar("mp_overtime_startmoney")));
	WriteFileLine(file, "mp_startmoney		\"800\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes \"0\"			// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Default Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
	
	UpdateRuleSet_PlayOut(WM_VERSION);
}

static void UpdateRuleSet_PlayOut(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_playout.cfg");
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_playout.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - PlayOut Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change PlayOut commands");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_playout.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"0\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_enable		\"0\"		//If a match ends in a tie, use overtime rules to determine winner");
	WriteFileLine(file, "mp_overtime_maxrounds		\"%i\"		//When overtime is enabled play additional rounds to determine winner", GetConVarInt(FindConVar("mp_overtime_maxrounds")));
	WriteFileLine(file, "mp_overtime_startmoney		\"%i\"		//Money assigned to all players at start of every overtime half", GetConVarInt(FindConVar("mp_overtime_startmoney")));
	WriteFileLine(file, "mp_startmoney		\"800\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes \"0\"			// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Play Out Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
	
	UpdateRuleSet_OverTime(WM_VERSION);
}

static void UpdateRuleSet_OverTime(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_overtime.cfg");
	
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_overtime.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - OverTime Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change OverTime commands");
	WriteFileLine(file, "wm_match_config		\"warmod+/ruleset_overtime.cfg\"		//Sets the match config to load on Live on 3");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_overtime_maxrounds		\"%i\"		//When overtime is enabled play additional rounds to determine winner", GetConVarInt(FindConVar("mp_overtime_maxrounds")));
	WriteFileLine(file, "mp_overtime_startmoney 		\"%i\"		//Money assigned to all players at start of every overtime half", GetConVarInt(FindConVar("mp_overtime_startmoney")));
	WriteFileLine(file, "mp_startmoney		\"800\"		//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"1.92\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "wm_round_money		\"1\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes \"0\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say OverTime Config Loaded", false);// no newline at the end
	CloseHandle(file);
	
	UpdateWarmUp(WM_VERSION);
}

static void UpdateWarmUp(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_warmup.cfg");

	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - WarmUp Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change WarmUp commands");
	WriteFileLine(file, "wm_warmup_respawn		\"1\"		//Enable or disable the respawning of players in warmup");
	WriteFileLine(file, "wm_block_warm_up_grenades		\"1\"	//Enable or disable grenade blocking in warmup");
	WriteFileLine(file, "mp_warmuptime		\"300\"		//How long the warmup period lasts. Changing this value resets warmup.");
	WriteFileLine(file, "mp_buytime		\"120\"		//How many seconds after round start players can buy items for.");
	WriteFileLine(file, "mp_freezetime		\"0\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "mp_autokick		\"0\"		//Kick idle/team-killing/team-damaging players");
	WriteFileLine(file, "mp_friendlyfire		\"0\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "mp_startmoney		\"16000\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_warmup_pausetimer		\"1\"		//Set to 1 to stay in warmup indefinitely. Set to 0 to resume the timer.");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_infinite_ammo		\"0\"		//Player's active weapon will never run out of ammo.");
	WriteFileLine(file, "sv_showimpacts		\"0\"			//Shows client (red) and server (blue) bullet impact point");
	WriteFileLine(file, "sv_cheats			\"0\"			//Allow cheats on server");
	WriteFileLine(file, "mp_buy_anywhere		\"0\"		//When set, players can buy anywhere, not only in buyzones");
	WriteFileLine(file, "mp_roundtime_defuse		\"1.92\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_death_drop_defuser	\"0\"			// Drop defuser on player death");
	WriteFileLine(file, "mp_death_drop_grenade	\"0\"			// Which grenade to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "mp_death_drop_gun	\"0\"			// Which gun to drop on player death: 0=none, 1=best, 2=current or best");
	WriteFileLine(file, "wm_active			\"1\"		//Enable or disable WarMod as active");
	WriteFileLine(file, "sv_allow_votes	\"1\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say WarmUp Config Loaded", false); // no newline at the end
	CloseHandle(file);
	UpdatePractice(WM_VERSION);
}

static void UpdatePractice(char[] WM_VERSION)
{
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/practice.cfg");

	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Practice Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change WarmUp commands");
	WriteFileLine(file, "wm_warmup_respawn		\"0\"		//Enable or disable the respawning of players in warmup");
	WriteFileLine(file, "wm_block_warm_up_grenades		\"0\"	//Enable or disable grenade blocking in warmup");
	WriteFileLine(file, "mp_buytime		\"120\"		//How many seconds after round start players can buy items for.");
	WriteFileLine(file, "mp_freezetime		\"0\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "mp_autokick		\"0\"		//Kick idle/team-killing/team-damaging players");
	WriteFileLine(file, "mp_startmoney		\"16000\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_infinite_ammo		\"2\"		//Player's active weapon will never run out of ammo.");
	WriteFileLine(file, "sv_showimpacts		\"2\"			//Shows client (red) and server (blue) bullet impact point");
	WriteFileLine(file, "sv_cheats			\"1\"			//Allow cheats on server");
	WriteFileLine(file, "mp_buy_anywhere		\"1\"		//When set, players can buy anywhere, not only in buyzones");
	WriteFileLine(file, "mp_roundtime_defuse		\"9999\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "bot_quota			\"0\"		//Determines the total number of bots in the game.");
	WriteFileLine(file, "wm_active			\"0\"		//Enable or disable WarMod as active");
	WriteFileLine(file, "mp_friendlyfire		\"0\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "sv_allow_votes	\"1\"				// Allow voting?");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Practice Config Loaded", false); // no newline at the end
	CloseHandle(file);
	UpdateRuleSet_Knife(WM_VERSION);
}

static void UpdateRuleSet_Knife(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_knife.cfg");
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_knife.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Knife Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// Exec global config");
	WriteFileLine(file, "exec warmod+/ruleset_global.cfg");
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Knife commands");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_startmoney		\"0\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"60.00\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"60.00\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "sv_alltalk		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"0\"		//Alltalk");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Knife Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
	
	UpdateRuleSet_Veto(WM_VERSION);
}

static void UpdateRuleSet_Veto(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/ruleset_knife.cfg");
	char cfgFile[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/ruleset_knife.cfg");
	
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - Knife Ruleset Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "");
	WriteFileLine(file, "// Change Knife commands");
	WriteFileLine(file, "mp_maxrounds		\"30\"		//max number of rounds to play before server changes maps");
	WriteFileLine(file, "mp_match_can_clinch		\"1\"		//Can a team clinch and end the match by being so far ahead that the other team has no way to catching up?");
	WriteFileLine(file, "mp_startmoney		\"0\"			//amount of money each player gets when they reset");
	WriteFileLine(file, "mp_roundtime		\"60.00\"		//How many minutes each round takes");
	WriteFileLine(file, "mp_roundtime_defuse		\"60.00\"		//How many minutes each round of Bomb Defuse takes. If 0 then use mp_roundtime instead");
	WriteFileLine(file, "mp_friendlyfire		\"1\"		//Allows team members to injure other members of their team");
	WriteFileLine(file, "mp_freezetime		\"12\"		//How many seconds to keep players frozen when the round starts");
	WriteFileLine(file, "wm_round_money		\"0\"		//Enable or disable a client's team mates money to be displayed at the start of a round (to him only)");
	WriteFileLine(file, "sv_allow_votes	\"0\"				// Allow voting?");
	WriteFileLine(file, "sv_alltalk		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_dead		\"0\"		//Alltalk");
	WriteFileLine(file, "sv_talk_enemy_living		\"0\"		//Alltalk");
	WriteFileLine(file, "");
	WriteFileLine(file, "say Knife Match Config Loaded", false); // no newline at the end
	CloseHandle(file);
	
	UpdateOn_Map_Load(WM_VERSION);
}

static void UpdateOn_Map_Load(char[] WM_VERSION)
{
	ServerCommand("exec warmod+/on_map_load.cfg");
	
	char cfgFile[PLATFORM_MAX_PATH];
	char convarString[PLATFORM_MAX_PATH];
	Format(cfgFile, sizeof(cfgFile), "cfg/warmod+/on_map_load.cfg");
	DeleteFile(cfgFile);
	Handle file = OpenFile(cfgFile, "w");

	WriteFileLine(file, "// WarMod - On Map Load Config");
	WriteFileLine(file, "// Updated via WarMod Config Updater for Warmod v%s", WM_VERSION);
	WriteFileLine(file, "// This config file is executed on every map change, including when the server first starts");
	WriteFileLine(file, "// Note: Plugins have been loaded by now");
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Configs");
	WriteFileLine(file, "");
	WriteFileLine(file, "wm_warmod_safemode		\"%i\"		//This disables features that usually break on a CS:GO update", GetConVarInt(FindConVar("wm_warmod_safemode")));
	GetConVarString(FindConVar("wm_match_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_match_config			\"%s\"	//Sets the match config to load on Live on 3", convarString);
	GetConVarString(FindConVar("wm_default_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_default_config				\"%s\"	//Sets the default match config to load on Live on 3", convarString);
	GetConVarString(FindConVar("wm_overtime_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_overtime_config				\"%s\"	//Sets the overtime match config to load on Live on 3", convarString);
	GetConVarString(FindConVar("wm_playout_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_playout_config				\"%s\"	//Sets the play out match config to load on Live on 3", convarString);
	GetConVarString(FindConVar("wm_prac_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_prac_config				\"%s\"	//Loads when the practice command is called", convarString);
	GetConVarString(FindConVar("wm_reset_config"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_reset_config				\"%s\"	//Sets the config to load at the end/reset of a match", convarString);
	GetConVarString(FindConVar("wm_reset_config_delay"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_reset_config_delay			\"%s\"	//The amount of time before executing the reset config after a match", convarString);
	GetConVarString(FindConVar("wm_damageprint_enabled"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_damageprint_enabled			\"%i\"	//Whether to enabled damage print to client on round end", convarString);
	GetConVarString(FindConVar("wm_gamename_enable"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_gamename_enable			\"%i\"	//Enable or disable WarMod game name");
	GetConVarString(FindConVar("wm_gamename"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_gamename			\"%s\"	//WarMod game name");
	GetConVarString(FindConVar("wm_user_token"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_user_token			\"%s\"	//WarMod user token");
	WriteFileLine(file, "wm_save_database		\"%i\"	//0 - Will not save match record, gotv demo and player stats to database", GetConVarInt(FindConVar("wm_save_database")));
	WriteFileLine(file, "");
	WriteFileLine(file, "// WarMod Stats");
	WriteFileLine(file, "");
	GetConVarString(FindConVar("wm_save_dir"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_save_dir			\"%s\"	//Directory to store SourceTV demos and WarMod logs", convarString);
	WriteFileLine(file, "wm_prefix_logs			\"%i\"		//Enable or disable the prefixing of \"_\" to uncompleted match SourceTV demos and WarMod logs", GetConVarInt(FindConVar("wm_prefix_logs")));
	WriteFileLine(file, "wm_stats_method			\"%i\"		//Sets the stats logging method: 0 = UDP stream/server logs, 1 = WarMod logs, 2 = both", GetConVarInt(FindConVar("wm_stats_method")));
	WriteFileLine(file, "wm_stats_trace			\"%i\"		//Enable or disable updating all player positions, every wm_stats_trace_delay seconds", GetConVarInt(FindConVar("wm_stats_trace")));
	WriteFileLine(file, "wm_stats_trace_delay		\"%i\"		//The ammount of time between sending player position updates", GetConVarInt(FindConVar("wm_stats_trace_delay")));
	GetConVarString(FindConVar("wm_competition"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_competition			\"%s\"	//Name of host for a competition. eg. ESEA, Cybergamer, CEVO, ESL", convarString);
	GetConVarString(FindConVar("wm_event"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_event			\"%s\"		//Name of event. eg. Season #, ODC #, Ladder", convarString);
	GetConVarString(FindConVar("wm_chat_prefix"), convarString, sizeof(convarString));
	WriteFileLine(file, "wm_chat_prefix			\"%s\"	//Change the chat prefix. Default is WarMod+", convarString);
	WriteFileLine(file, "");
	WriteFileLine(file, "// Voice Communications");
	WriteFileLine(file, "");
	if (LibraryExists("basecomm"))
	{
		WriteFileLine(file, "sm_deadtalk					\"%i\"			//Controls how dead communicate. 0 - Off. 1 - Dead players ignore teams. 2 - Dead players talk to living teammates.", GetConVarInt(FindConVar("sm_deadtalk")));
	}
	else
	{
		WriteFileLine(file, "sm_deadtalk					\"0\"			//Controls how dead communicate. 0 - Off. 1 - Dead players ignore teams. 2 - Dead players talk to living teammates.");
	}
	WriteFileLine(file, "");
	WriteFileLine(file, "//C4 Timer");
	WriteFileLine(file, "wm_c4_all			\"%i\"				//Show c4 message to all? 1=all 0=gotv & spec.", GetConVarInt(FindConVar("wm_c4_all")));
	WriteFileLine(file, "");
	WriteFileLine(file, "exec warmod+/ruleset_warmup.cfg", false);// no newline at the end
	CloseHandle(file);
}