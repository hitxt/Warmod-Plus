// Todo?
// [ ] win in lo3 will count team score wtf
// [ ] load backup score not right?
// [-] announce vetomaps when match start and map end?
// [ ] Save backup to mysql? (now warmod can load by match id)
// [ ] "wm_auto_pause_count" count autopause in pause limit or not
// [ ] "wm_ban_wait" wait client reconnect

#pragma semicolon 1

#include <sourcemod>
#include <sdktools>
#include <geoip>
#include <cstrike>
#include <kento_csgocolors>
#undef REQUIRE_PLUGIN
#include <adminmenu>
#undef REQUIRE_EXTENSIONS
#include <bzip2>

#include <warmod_plus>
#include <warmod_plus/configs>

//Game name
#include <SteamWorks>
#include <curl>

/* RWS */
#include <priorityqueue>

/* download logo */
// #include <latedl>

/* discord */
#include <discord>

/* API */
#include <ripext>

#pragma newdecls required

// license
ConVar wm_user_token;
ConVar wm_save_database;
bool save_db;
char user_token[512];
char time_exp[512];
char time_now[512];
char ftp_a[512];
char ftp_p[512];
bool authed = false;

/* discord */
int Call_Cooldown = 0;
Handle h_CallTimer;
bool CanCall = true;

// team re-rewrite
char player_team_name[MAXPLAYERS + 1][512];
char player_team_logo[MAXPLAYERS + 1][512];
bool player_team_use[MAXPLAYERS + 1];
bool player_team_leader[MAXPLAYERS + 1];
int player_team_id[MAXPLAYERS + 1];
Handle LogoTimer[MAXPLAYERS + 1] = INVALID_HANDLE;
bool player_loaded[MAXPLAYERS + 1];

int g_player_list[MAXPLAYERS + 1];
bool g_cancel_list[MAXPLAYERS + 1];
int g_scores[2][2];
int g_scores_overtime[2][256][2];
int g_overtime_count = 0;

/* miscellaneous */
char g_map[64];
char date[32];
char startHour[4];
char startMin[4];
float g_match_start;

/* Last Match settings */
int lt_match_length;
char lt_map[64];
int lt_max_rounds;
int lt_overtime_max_rounds;
int lt_overtime_count;
int lt_played_out;
char lt_t_name[64];
int lt_t_overall_score;
int lt_t_first_half_score;
int lt_t_second_half_score;
int lt_t_overtime_score;
char lt_ct_name[64];
int lt_ct_overall_score;
int lt_ct_first_half_score;
int lt_ct_second_half_score;
int lt_ct_overtime_score;
char lt_log_file_name[128];
int match_id;

// Offsets
int g_iAccount = -1;

/* stats */
bool g_log_warmod_dir = false;
char g_log_filename[128];
Handle g_log_file = INVALID_HANDLE;
char g_log_veto_filename[128];
Handle g_log_veto_file = INVALID_HANDLE;
char weapon_list[][] = {"weapon_ak47", "weapon_xm1014", "weapon_m4a1_silencer", "weapon_m4a1", "weapon_galilar", "weapon_famas", "weapon_awp", "weapon_p250", "weapon_cz75a", "weapon_glock", "weapon_hkp2000", "weapon_usp_silencer", "weapon_ump45", "weapon_p90", "weapon_bizon", "weapon_mp7", "weapon_nova", "weapon_knife", "weapon_elite", "weapon_fiveseven", "weapon_deagle", "weapon_revolver", "weapon_tec9", "weapon_ssg08", "weapon_scar20", "weapon_aug", "weapon_sg556", "weapon_g3sg1", "weapon_mac10", "weapon_mp9", "weapon_mag7", "weapon_negev", "weapon_m249", "weapon_sawedoff", "weapon_incgrenade", "weapon_flashbang", "weapon_smokegrenade", "weapon_hegrenade", "weapon_molotov", "weapon_decoy", "weapon_taser", "weapon_inferno", "weapon_mp5sd"};
int weapon_stats[MAXPLAYERS + 1][NUM_WEAPONS][LOG_HIT_NUM];
int clutch_stats[MAXPLAYERS + 1][CLUTCH_NUM];
int assist_stats[MAXPLAYERS + 1][ASSIST_NUM];
int round_health[MAXPLAYERS + 1];
int g_round = 1;
char last_weapon[MAXPLAYERS + 1][64];
char force_team_t[10][64];
char force_team_ct[10][64];
int force_team_t_count = 0;
int force_team_ct_count = 0;
bool g_planted = false;
Handle g_stats_trace_timer = INVALID_HANDLE;
ConVar wm_competition;
ConVar wm_event;
char g_competition[255];
char g_event[255];
char g_server[255];
int hitbox[MAXPLAYERS + 1][8];
int weapon_kills[MAXPLAYERS + 1][NUM_WEAPONS];
int addedtorequest = 0;

/* Stats Rewrite */
bool WasClientInGameAtAll[MAXPLAYERS + 1] = false;
bool WasClientInGame[MAXPLAYERS + 1] = false;
int WasClientTeam[MAXPLAYERS + 1] = 0;
int match_stats[MAXPLAYERS + 1][MATCH_NUM];
int round_kills[MAXPLAYERS + 1];
char match_client_name[MAXPLAYERS + 1][64];
char match_client_steam2[MAXPLAYERS + 1][32];
char match_client_steam64[MAXPLAYERS + 1][18];

/* forwards */
Handle g_f_on_lo3 = INVALID_HANDLE;
Handle g_f_on_round_end = INVALID_HANDLE;
Handle g_f_on_half_time = INVALID_HANDLE;
Handle g_f_on_reset_half = INVALID_HANDLE;
Handle g_f_on_reset_match = INVALID_HANDLE;
Handle g_f_on_end_match = INVALID_HANDLE;
Handle g_f_livewire_log_event = INVALID_HANDLE;

/* cvars */
ConVar wm_active;
ConVar wm_stats_method;
ConVar wm_stats_trace;
ConVar wm_stats_trace_delay;
ConVar wm_rcon_only;
ConVar wm_lock_teams;
ConVar wm_min_ready;
ConVar wm_max_players;
ConVar wm_match_config;
ConVar wm_reset_config;
ConVar wm_reset_config_delay;
ConVar wm_warmup_config;
ConVar wm_prac_config;
ConVar wm_playout_config;
ConVar wm_overtime_config;
ConVar wm_default_config;
ConVar wm_knife_config;
ConVar wm_half_time_break;
ConVar wm_over_time_break;
ConVar wm_round_money;
ConVar wm_ingame_scores;
ConVar mp_maxrounds;
ConVar wm_block_warm_up_grenades;
ConVar wm_knife_auto_start;
ConVar wm_knife_hegrenade;
ConVar wm_knife_flashbang;
ConVar wm_knife_smokegrenade;
ConVar wm_knife_zeus;
ConVar wm_knife_armor;
ConVar wm_knife_helmet;
ConVar wm_show_info;
ConVar wm_auto_ready;
ConVar wm_auto_knife;
ConVar mp_overtime_enable;
ConVar mp_overtime_maxrounds;
ConVar tv_enable;
ConVar wm_auto_record;
ConVar wm_save_dir;
ConVar wm_prefix_logs;
ConVar wm_warmup_respawn;
ConVar wm_chat_prefix;
ConVar mp_match_can_clinch;
ConVar mp_teamname_1;
ConVar mp_teamname_2;
ConVar mp_teamlogo_1;
ConVar mp_teamlogo_2;
ConVar mp_teamflag_1;
ConVar mp_teamflag_2;
ConVar wm_ready_tag;
ConVar wm_ready_panel;
ConVar wm_veto_config;

ConVar mp_startmoney;
ConVar hostname;

/* ready system */
Handle g_m_ready_up = INVALID_HANDLE;
bool g_ready_enabled = false;

/* switches */
bool g_active = true;
bool g_start = false;
bool g_match = false;
bool g_max_lock = false;
bool g_live = false;
bool g_log_live = false;
bool g_restore = false;
bool g_half_swap = true;
bool g_first_half = true;
bool g_overtime = false;
bool g_t_money = false;
bool g_t_score = false;
bool g_t_knife = true;
bool g_t_had_knife = false;
bool g_second_half_first = false;
bool g_DispInfoLimiter = true;
bool LiveOn2 = false;
bool LiveOn1 = false;
bool LiveOn3Text = false;
bool KnifeOn2 = false;
bool KnifeOn1 = false;
bool KnifeOn3Text = false;
int g_knife_winner = 0;
bool g_knife_vote = false;

/* FTP Auto upload code [By Thrawn from tAutoDemoUpload] */
char g_sDemoPath[PLATFORM_MAX_PATH];
char g_sDemoName[64];
char g_sLogPath[PLATFORM_MAX_PATH];
bool g_bRecording = false;
bool g_MatchComplete = false;
Handle g_hProccessingFile;

/* Warmod safemode */
ConVar wm_warmod_safemode;

/* modes */
int g_overtime_mode = 0;

/* chat prefix */
char CHAT_PREFIX[64];

/* teams */
char g_t_name[64];
char g_t_name_escaped[64]; // pre-escaped for warmod logs
char g_ct_name[64];
char g_ct_name_escaped[64]; // pre-escaped for warmod logs
int g_t_id = 0;
int g_ct_id = 0;

#define SPEC 1
#define TR 2
#define CT 3

/* clan tag */
char g_clanTags[MAXPLAYERS +1 ][MAX_NAME_LENGTH];
bool g_clanTagsChecked[MAXPLAYERS + 1] = false;

/* Config Offers */
bool default_offer_ct = false;
bool default_offer_t = false;
Handle g_h_stored_timer_def = INVALID_HANDLE;

bool overtime_offer_ct = false;
bool overtime_offer_t = false;
Handle g_h_stored_timer_ot = INVALID_HANDLE;

bool playout_offer_ct = false;
bool playout_offer_t = false;
Handle g_h_stored_timer_pl = INVALID_HANDLE;

/* Pause and Unpause */
bool g_pause_freezetime = false;
bool g_pause_offered_t = false;
bool g_pause_offered_ct = false;
bool g_auto_pause = false;
bool FreezeTime = false;

ConVar sv_pausable;
ConVar sv_matchpause_auto_5v5;
ConVar wm_auto_pause;
ConVar wm_auto_unpause;
ConVar wm_auto_unpause_delay;
//ConVar wm_auto_pause_count;
ConVar wm_pause_confirm;
ConVar wm_unpause_confirm;
ConVar wm_pause_limit;
ConVar wm_pause_limit_half;
int g_t_pause_count = 0;
int g_ct_pause_count = 0;
Handle g_h_stored_timer = INVALID_HANDLE;
Handle g_h_stored_timer_p = INVALID_HANDLE;
char g_c_backup[128];

/* Veto Settings */
ConVar wm_pugsetup_maplist_file;
ConVar wm_pugsetup_randomize_maps;
Handle g_MapNames = INVALID_HANDLE;
Handle g_MapVetoed = INVALID_HANDLE;
ConVar wm_veto;
ConVar wm_veto_bo3;
ConVar wm_veto_random;
ConVar wm_veto_select;
ConVar wm_veto_knife;
ConVar tv_delaymapchange;
ConVar tv_delay;
ConVar mp_match_end_restart;
int g_bo3_count = -1;
int g_bo5_count = -1;
int g_ChosenMapBo2[2] = -1;
int g_ChosenMapBo3[3] = -1;
int g_ChosenMapBo5[5] = -1;
int g_ChosenMap = -1;
int g_MapListCount = 0;
bool g_veto_s = false;
bool g_t_veto = false;
bool g_veto_active = false;
bool g_veto_bo5_active = false;
bool g_veto_bo3_active = false;
bool g_veto_bo2_active = false;
int g_veto_map_number = 0;
int g_veto_number = 0;
int g_capt1 = -1;
int g_capt2 = -1;
bool veto_offer_ct = false;
bool veto_offer_t = false;
Handle g_h_stored_timer_v = INVALID_HANDLE;

/* Print Damage */
int g_DamageDone[MAXPLAYERS+1][MAXPLAYERS+1];
int g_DamageDoneHits[MAXPLAYERS+1][MAXPLAYERS+1];
bool g_GotKill[MAXPLAYERS+1][MAXPLAYERS+1];
ConVar wm_damageprint_enabled;
bool Hasdamagereport[MAXPLAYERS+1];

/* Teams */
bool team_switch = false;
ConVar wm_name_fix;

/* BanOn Disconnect */
bool g_disconnect[MAXPLAYERS + 1] = false;
ConVar wm_ban_on_disconnect;
ConVar wm_ban_percentage;
ConVar sv_kick_ban_duration;

bool g_first_load = true;
int g_map_loaded = 0;

/* admin menu */
Handle g_h_menu = INVALID_HANDLE;

/* Backup */
bool g_bBackuping;
//char g_iBackupTeam;
//char g_iBackupClient;

/* game name */
ConVar wm_gamename;

/* adm pause */
bool admpause;

/* rws */
float f_PlayerRWS[MAXPLAYERS + 1];
float f_PlayerRWS_join[MAXPLAYERS + 1];
int i_PlayerRounds[MAXPLAYERS + 1];
int i_RoundPoints[MAXPLAYERS + 1];
#define ALPHA_INIT 0.1
#define ALPHA_FINAL 0.003
#define ROUNDS_FINAL 250.0

/* pug */
bool b_waitpick[MAXPLAYERS + 1] = false;
bool IsPug = false;


/* Plugin info */
#define UPDATE_URL				""
#define WM_VERSION				"18.05.24.0924.WM+.1"
#define WM_DESCRIPTION			"An automative service for CS:GO competition matches"

#include <warmod_plus/custom>
#include <warmod_plus/api>

public Plugin myinfo = {
	name = "[CS:GO] WarMod+",
	author = "Versatile_BFG & Kento",
	description = WM_DESCRIPTION,
	version = WM_VERSION,
	url = "http://steamcommunity.com/id/kentomatoryoshika/"
};

public APLRes AskPluginLoad2(Handle myself, bool late, char[] error, int err_max)
{
	RegPluginLibrary("warmod");
    
	CreateNative("WM_IsMatchLive", Native_IsMatchLive);
    
	return APLRes_Success;
}

public void OnPluginStart()
{
	g_first_load = true;
	
	CheckConFigFiles(WM_VERSION);
	
	LoadTranslations("kento.warmod+.phrases");
	LoadTranslations("common.phrases");
	
	AutoExecConfig();
	
	Handle topmenu;
	if (LibraryExists("adminmenu") && ((topmenu = GetAdminTopMenu()) != INVALID_HANDLE))
	{
		OnAdminMenuReady(topmenu);
	}
	
	g_f_on_lo3 = CreateGlobalForward("OnLiveOn3", ET_Ignore);
	g_f_on_round_end = CreateGlobalForward("OnRoundEnd", ET_Ignore, Param_String, Param_Cell, Param_Cell, Param_String);
	g_f_on_half_time = CreateGlobalForward("OnHalfTime", ET_Ignore, Param_String, Param_Cell, Param_Cell, Param_String);
	g_f_on_reset_half = CreateGlobalForward("OnResetHalf", ET_Ignore);
	g_f_on_reset_match = CreateGlobalForward("OnResetMatch", ET_Ignore);
	g_f_on_end_match = CreateGlobalForward("OnEndMatch", ET_Ignore, Param_String, Param_Cell, Param_Cell, Param_String);
	g_f_livewire_log_event = CreateGlobalForward("LiveWireLogEvent", ET_Ignore, Param_String);
	
	AddCommandListener(Command_JoinTeam, "jointeam");
	AddCommandListener(UnpauseMatch, "mp_unpause_match");
	AddCommandListener(MatchRestore, "mp_backup_restore_load_file");
	
	RegConsoleCmd("score", ConsoleScore);
	RegConsoleCmd("wm_version", WMVersion);
	RegConsoleCmd("buy", RestrictBuy);
	RegConsoleCmd("jointeam", ChooseTeam);
	RegConsoleCmd("spectate", ChooseTeam);
	RegConsoleCmd("wm_readylist", ReadyList);
	RegConsoleCmd("wmrl", ReadyList);
	RegConsoleCmd("wm_cash", AskTeamMoney);
	
	RegConsoleCmd("sm_team", SetTeam, "Sets the team name, logo, flag.");
	RegConsoleCmd("sm_ready", ReadyUp, "Readies up the client");
	RegConsoleCmd("sm_r", ReadyUp, "Readies up the client");
	RegConsoleCmd("sm_rdy", ReadyUp, "Readies up the client");
	RegConsoleCmd("sm_unready", ReadyDown, "Readies down the client");
	RegConsoleCmd("sm_ur", ReadyDown, "Readies down the client");
	RegConsoleCmd("sm_urdy", ReadyDown, "Readies down the client");
	RegConsoleCmd("sm_info", ReadyInfoPriv, "Shows ready info");
	RegConsoleCmd("sm_i", ReadyInfoPriv, "Shows ready info");
	RegConsoleCmd("sm_score", ShowScore, "Shows score to client");
	RegConsoleCmd("sm_s", ShowScore, "Shows score to client");
	RegConsoleCmd("sm_stay", Stay, "Stay command for knife round");
	RegConsoleCmd("sm_switch", Switch, "Switch command for knife round");
	RegConsoleCmd("sm_swap", Switch, "Switch command for knife round");
	RegConsoleCmd("sm_pause", Pause, "Pauses the match");
	RegConsoleCmd("sm_unpause", Unpause, "Resumes the match");
	
	RegConsoleCmd("sm_playout", PlayOut_Offer, "Sets the match to be in play out mode");
	RegConsoleCmd("sm_pl", PlayOut_Offer, "Sets the match to be in play out mode");
	RegConsoleCmd("sm_hardprac", PlayOut_Offer, "Sets the match to be in play out mode");
	RegConsoleCmd("sm_hp", PlayOut_Offer, "Sets the match to be in play out mode");
	
	RegConsoleCmd("sm_overtime", OverTime_Offer, "Sets the match to be in overtime mode");
	RegConsoleCmd("sm_ot", OverTime_Offer, "Sets the match to be in overtime mode");
	
	RegConsoleCmd("sm_normal", Default_Offer, "Sets the match to be in default mode");
	RegConsoleCmd("sm_norm", Default_Offer, "Sets the match to be in default mode");
	RegConsoleCmd("sm_default", Default_Offer, "Sets the match to be in default mode");
	RegConsoleCmd("sm_def", Default_Offer, "Sets the match to be in default mode");
	
	/* Veto cmds */
	RegConsoleCmd("sm_vetobo1", Veto_Bo1, "Ask for a Bo1 Veto");
	RegConsoleCmd("sm_vetobo2", Veto_Bo2, "Ask for a Bo2 Veto");
	RegConsoleCmd("sm_vetobo3", Veto_Bo3, "Ask for a Bo3 Veto");
	RegConsoleCmd("sm_vetobo5", Veto_Bo5, "Ask for a Bo5 Veto");
	RegConsoleCmd("sm_veto", Veto_Setup, "Ask for Veto");
	RegConsoleCmd("sm_veto1", Veto_Bo1, "Ask for a Bo1 Veto");
	RegConsoleCmd("sm_veto2", Veto_Bo2, "Ask for a Bo2 Veto");
	RegConsoleCmd("sm_veto3", Veto_Bo3, "Ask for a Bo3 Veto");
	RegConsoleCmd("sm_veto5", Veto_Bo5, "Ask for a Bo5 Veto");
	RegConsoleCmd("sm_vetomaps", Veto_Bo3_Maps, "Veto Bo3 Maps");
	
	/* rws */
	RegConsoleCmd("sm_rws", CMD_RWS);
	RegConsoleCmd("sm_balance", CMD_Balance, "Vote balance team by kd");
	RegAdminCmd("sm_fbalace", CMD_ForceBalance, ADMFLAG_CUSTOM1, "Force team balanced by rws.");
	RegAdminCmd("fb", CMD_ForceBalance, ADMFLAG_CUSTOM1, "Force team balanced by rws.");
	
	/* pug */
	RegConsoleCmd("sm_pug", CMD_Pug, "Vote pug.");
	RegAdminCmd("sm_fpug", CMD_ForcePug, ADMFLAG_CUSTOM1, "Force pug.");
	RegAdminCmd("fp", CMD_ForcePug, ADMFLAG_CUSTOM1, "Force pug.");
	
	/* call player */
	RegConsoleCmd("sm_call", CMD_CallPlayer);
	
	/* test */
	RegConsoleCmd("sm_test", CMD_Test);

	/* admin commands */
	
	RegAdminCmd("notlive", NotLive, ADMFLAG_CUSTOM1, "Declares half not live and restarts the round");
	RegAdminCmd("nl", NotLive, ADMFLAG_CUSTOM1, "Declares half not live and restarts the round");
	RegAdminCmd("cancelhalf", NotLive, ADMFLAG_CUSTOM1, "Declares half not live and restarts the round");
	RegAdminCmd("ch", NotLive, ADMFLAG_CUSTOM1, "Declares half not live and restarts the round");
	
	RegAdminCmd("cancelmatch", CancelMatch, ADMFLAG_CUSTOM1, "Declares match not live and restarts round");
	RegAdminCmd("cm", CancelMatch, ADMFLAG_CUSTOM1, "Declares match not live and restarts round");
	
	RegAdminCmd("readyup", ReadyToggle, ADMFLAG_CUSTOM1, "Starts or stops the ReadyUp System");
	RegAdminCmd("ru", ReadyToggle, ADMFLAG_CUSTOM1, "Starts or stops the ReadyUp System");
	
	RegAdminCmd("t", ChangeT, ADMFLAG_CUSTOM1, "Team starting terrorists - Designed for score purposes");
	RegAdminCmd("ct", ChangeCT, ADMFLAG_CUSTOM1, "Team starting counter-terrorists - Designed for score purposes");
	RegAdminCmd("sst", SetScoreT, ADMFLAG_CUSTOM1, "Setting terrorists score");
	RegAdminCmd("ssct", SetScoreCT, ADMFLAG_CUSTOM1, "Setting counter-terrorists scores");
	
	RegAdminCmd("aswap", SwapAll, ADMFLAG_CUSTOM1, "Swap all players to the opposite team");
	
	RegAdminCmd("prac", Practice, ADMFLAG_CUSTOM1, "Puts server into a practice mode state");
	RegAdminCmd("warmup", WarmUp, ADMFLAG_CUSTOM1, "Puts server into a warm up state");
	
	RegAdminCmd("pwd", ChangePassword, ADMFLAG_PASSWORD, "Set or display the sv_password console variable");
	RegAdminCmd("pw", ChangePassword, ADMFLAG_PASSWORD, "Set or display the sv_password console variable");
	
	RegAdminCmd("active", ActiveToggle, ADMFLAG_CUSTOM1, "Toggle the wm_active console variable");
	
	RegAdminCmd("minready", ChangeMinReady, ADMFLAG_CUSTOM1, "Set or display the wm_min_ready console variable");
	
	RegAdminCmd("maxrounds", ChangeMaxRounds, ADMFLAG_CUSTOM1, "Set or display the wm_max_rounds console variable");
	
	RegAdminCmd("knife", KnifeOn3, ADMFLAG_CUSTOM1, "Remove all weapons except knife and lo3");
	RegAdminCmd("ko3", KnifeOn3, ADMFLAG_CUSTOM1, "Remove all weapons except knife and lo3");
	
	RegAdminCmd("cancelknife", CancelKnife, ADMFLAG_CUSTOM1, "Declares knife not live and restarts round");
	RegAdminCmd("ck", CancelKnife, ADMFLAG_CUSTOM1, "Declares knife not live and restarts round");
	
	RegAdminCmd("forceallready", ForceAllReady, ADMFLAG_CUSTOM1, "Forces all players to become ready");
	RegAdminCmd("far", ForceAllReady, ADMFLAG_CUSTOM1, "Forces all players to become ready");
	RegAdminCmd("forceallunready", ForceAllUnready, ADMFLAG_CUSTOM1, "Forces all players to become unready");
	RegAdminCmd("faur", ForceAllUnready, ADMFLAG_CUSTOM1, "Forces all players to become unready");
	RegAdminCmd("forceallspectate", ForceAllSpectate, ADMFLAG_CUSTOM1, "Forces all players to become a spectator");
	RegAdminCmd("fas", ForceAllSpectate, ADMFLAG_CUSTOM1, "Forces all players to become a spectator");
	
	RegAdminCmd("lo3", ForceStart, ADMFLAG_CUSTOM1, "Starts the match regardless of player and ready count");
	RegAdminCmd("forcestart", ForceStart, ADMFLAG_CUSTOM1, "Starts the match regardless of player and ready count");
	RegAdminCmd("fs", ForceStart, ADMFLAG_CUSTOM1, "Starts the match regardless of player and ready count");
	RegAdminCmd("forceend", ForceEnd, ADMFLAG_CUSTOM1, "Ends the match regardless of status");
	RegAdminCmd("fe", ForceEnd, ADMFLAG_CUSTOM1, "Ends the match regardless of status");
	
	RegAdminCmd("readyon", ReadyOn, ADMFLAG_CUSTOM1, "Turns on or restarts the ReadyUp System");
	RegAdminCmd("ron", ReadyOn, ADMFLAG_CUSTOM1, "Turns on or restarts the ReadyUp System");
	RegAdminCmd("readyoff", ReadyOff, ADMFLAG_CUSTOM1, "Turns off the ReadyUp System if enabled");
	RegAdminCmd("roff", ReadyOff, ADMFLAG_CUSTOM1, "Turns off the ReadyUp System if enabled");
	
	RegAdminCmd("updatecfgs", UpdateCFGs, ADMFLAG_CUSTOM1, "Updates configs with the latest format");
	
	// admin pause
	RegAdminCmd("admpause", AdmPause, ADMFLAG_CUSTOM1, "Admin pause command.");
	RegAdminCmd("admunpause", AdmUnpause, ADMFLAG_CUSTOM1, "Admin unpause command");
	
	// server commands
	RegServerCmd("wm_status", WarMod_Status);
	RegServerCmd("wm_forceteam", ForceTeam, "Force the SteamID64 client to a team");
	RegServerCmd("wm_clear_forceteam_all", ClearForceTeamAll, "Clears the list for forced teams");
	RegServerCmd("wm_clear_forceteam_t", ClearForceTeamT, "Clears the list for forced terrorist team");
	RegServerCmd("wm_clear_forceteam_ct", ClearForceTeamCT, "Clears the list for forced counter-terrorist team");
	RegServerCmd("wm_forcename", ForceClientName, "Force the SteamID64 client's name");
	
	/* Warmod Convars */
	wm_active = CreateConVar("wm_active", "1", "Enable or disable WarMod as active", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_warmod_safemode = CreateConVar("wm_warmod_safemode", "0", "This disables features that usually break on a CS:GO update", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_rcon_only = CreateConVar("wm_rcon_only", "0", "Enable or disable admin commands to be only executed via RCON or console", FCVAR_NONE, true, 0.0, true, 1.0);
	CreateConVar("wm_version_notify", WM_VERSION, WM_DESCRIPTION, FCVAR_SPONLY|FCVAR_REPLICATED|FCVAR_NOTIFY|FCVAR_DONTRECORD);
	
	wm_save_database = CreateConVar("wm_save_database", "1", "0 - Will not save match record, gotv demo and player stats to database", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	wm_chat_prefix = CreateConVar("wm_chat_prefix", "Warmod+", "Change the chat prefix. Default is Warmod+", FCVAR_PROTECTED);
	wm_ready_panel = CreateConVar("wm_ready_panel", "1", "Enable Ready Panel or text based system, Text = 0, Panel = 1", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_ready_tag = CreateConVar("wm_ready_tag", "1", "Enable or disable the ready & not ready clan tags", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_lock_teams = CreateConVar("wm_lock_teams", "1", "Enable or disable locked teams when a match is running", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_min_ready = CreateConVar("wm_min_ready", "10", "Sets the minimum required ready players to Live on 3", FCVAR_NOTIFY);
	wm_max_players = CreateConVar("wm_max_players", "10", "Sets the maximum players allowed on both teams combined, others will be forced to spectator (0 = unlimited)", FCVAR_NOTIFY, true, 0.0);
	wm_half_time_break = CreateConVar("wm_half_time_break", "0", "Pause game at halftime for a break, No break = 0, break = 1", FCVAR_NONE, true, 0.0, true, 1.0);
	wm_over_time_break = CreateConVar("wm_over_time_break", "0", "Pause game at overtime for a break, No break = 0, break = 1", FCVAR_NONE, true, 0.0, true, 1.0);
	wm_round_money = CreateConVar("wm_round_money", "1", "Enable or disable a client's team mates money to be displayed at the start of a round (to him only)", FCVAR_NONE, true, 0.0, true, 1.0);
	wm_ingame_scores = CreateConVar("wm_ingame_scores", "1", "Enable or disable ingame scores to be showed at the end of each round", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_show_info = CreateConVar("wm_show_info", "1", "Enable or disable the display of the Ready System to players", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_auto_ready = CreateConVar("wm_auto_ready", "1", "Enable or disable the ready system being automatically enabled on map change", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	/* Ban Convars */
	wm_ban_on_disconnect = CreateConVar("wm_ban_on_disconnect", "0", "Enable or disable players banned on disconnect if match is live", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_ban_percentage = CreateConVar("wm_ban_percentage", "0.75", "Percentage of wm_max_players that will be banned on disconnect", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	sv_kick_ban_duration = FindConVar("sv_kick_ban_duration");
	
	/* Stats & Demo Convars */
	tv_enable  = FindConVar("tv_enable");
	wm_stats_method = CreateConVar("wm_stats_method", "2", "Sets the stats logging method: 0 = UDP stream/server logs, 1 = WarMod logs, 2 = both", FCVAR_NOTIFY, true, 0.0, true, 2.0);
	wm_stats_trace = CreateConVar("wm_stats_trace", "0", "Enable or disable updating all player positions, every wm_stats_trace_delay seconds", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_stats_trace_delay = CreateConVar("wm_stats_trace_delay", "5", "The amount of time between sending player position updates", FCVAR_NOTIFY, true, 0.0);
	wm_auto_record = CreateConVar("wm_auto_record", "1", "Enable or disable auto SourceTV demo record on Live on 3", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_save_dir = CreateConVar("wm_save_dir", "warmod", "Directory to store SourceTV demos and WarMod logs");
	wm_prefix_logs = CreateConVar("wm_prefix_logs", "1", "Enable or disable the prefixing of \"_\" to uncompleted match SourceTV demos and WarMod logs", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_competition = CreateConVar("wm_competition", "", "Name of host for a competition. eg. ESEA, Cybergamer, CEVO, ESL");		
	wm_event = CreateConVar("wm_event", "", "Name of event. eg. Season #, ODC #, Ladder");
	
	/* Config Convars */
	wm_match_config = CreateConVar("wm_match_config", "warmod+/ruleset_default.cfg", "Sets the match config to load on Live on 3");
	wm_reset_config = CreateConVar("wm_reset_config", "warmod+/on_match_end.cfg", "Sets the config to load at the end/reset of a match");
	wm_reset_config_delay = CreateConVar("wm_reset_config_delay", "1", "The amount of time before executing the reset config after a match", FCVAR_NOTIFY, true, 0.0);
	wm_prac_config = CreateConVar("wm_prac_config", "warmod+/practice.cfg", "Sets the config to load up for practice");
	wm_playout_config = CreateConVar("wm_playout_config", "warmod+/ruleset_playout.cfg", "Sets the play out match config to load on Live on 3");
	wm_overtime_config = CreateConVar("wm_overtime_config", "warmod+/ruleset_overtime.cfg", "Sets the overtime match config to load on Live on 3");
	wm_default_config = CreateConVar("wm_default_config", "warmod+/ruleset_default.cfg", "Sets the default match config to load on Live on 3");
	wm_knife_config = CreateConVar("wm_knife_config", "warmod+/ruleset_knife.cfg", "Sets the knife config to load on Knife on 3");
	wm_veto_config = CreateConVar("wm_veto_config", "warmod+/ruleset_veto.cfg", "Sets the veto knife config to load on Veto Knife on 3");
	
	/* Warmup Convars */
	wm_warmup_config = CreateConVar("wm_warmup_config", "warmod+/ruleset_warmup.cfg", "Sets the config to load up for warmup");
	wm_block_warm_up_grenades = CreateConVar("wm_block_warm_up_grenades", "0", "Enable or disable grenade blocking in warmup", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_warmup_respawn = CreateConVar("wm_warmup_respawn", "0", "Enable or disable the respawning of players in warmup", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	/* Knife Convars */
	wm_auto_knife = CreateConVar("wm_auto_knife", "0", "Enable or disable the knife round before going live", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_auto_start = CreateConVar("wm_knife_auto_start", "0", "Enable or disable after knife round to be forced lived", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_hegrenade = CreateConVar("wm_knife_hegrenade", "0", "Enable or disable giving a player a hegrenade on Knife on 3", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_flashbang = CreateConVar("wm_knife_flashbang", "0", "Sets how many flashbangs to give a player on Knife on 3", FCVAR_NOTIFY, true, 0.0, true, 2.0);
	wm_knife_smokegrenade = CreateConVar("wm_knife_smokegrenade", "0", "Enable or disable giving a player a smokegrenade on Knife on 3", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_zeus = CreateConVar("wm_knife_zeus", "0", "Enable or disable giving a player a zeus on Knife on 3", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_armor = CreateConVar("wm_knife_armor", "1", "Enable or disable giving a player Armor on Knife on 3", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_knife_helmet = CreateConVar("wm_knife_helmet", "0", "Enable or disable giving a player a Helmet on Knife on 3 [requires armor active]", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_name_fix = CreateConVar("wm_name_fix", "1", "Fix name swap after knife round", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	/* Pause Convars */
	sv_pausable = FindConVar("sv_pausable");
	sv_matchpause_auto_5v5 = FindConVar("sv_matchpause_auto_5v5");
	wm_auto_pause = CreateConVar("wm_auto_pause", "0", "Will pause server if team players equals less than half of wm_max_players: 0 = off, 1 = on", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	//wm_auto_pause_count = CreateConVar("wm_auto_pause_count", "0", "Count autopause in pause limit? 0 = No, 1 = Yes", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_pause_confirm = CreateConVar("wm_pause_confirm", "0", "Wait for other team to confirm pause: 0 = off, 1 = on", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_unpause_confirm = CreateConVar("wm_unpause_confirm", "0", "Wait for other team to confirm unpause: 0 = off, 1 = on", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_auto_unpause = CreateConVar("wm_auto_unpause", "1", "Sets auto unpause: 0 = off, 1 = on", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_auto_unpause_delay = CreateConVar("wm_auto_unpause_delay", "60", "Sets the seconds to wait before auto unpause", FCVAR_NOTIFY, true, 0.0);
	wm_pause_limit = CreateConVar("wm_pause_limit", "1", "Sets max pause count per team per half", FCVAR_NOTIFY, true, 0.0);
	wm_pause_limit_half = CreateConVar("wm_pause_limit_half", "0", "Reset pause count in half? 0 = No, 1 = Yes", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	/* Veto Convars */
	wm_pugsetup_maplist_file = CreateConVar("wm_pugsetup_maplist_file", "warmod+/veto_maps_list.txt", "Veto Map List to read from", FCVAR_NOTIFY);
	wm_pugsetup_randomize_maps = CreateConVar("wm_pugsetup_randomize_maps", "1", "When maps are shown in the map vote/veto, should their order be randomized?", FCVAR_NOTIFY);
	wm_veto = CreateConVar("wm_veto", "1", "Veto Style: 0 = off, 1 = Bo1, 2 = Bo2, 3 = Bo3, 5 = Bo5", FCVAR_NOTIFY, true, 0.0, true, 5.0);
	wm_veto_knife = CreateConVar("wm_veto_knife", "1", "Requires a knife round to determine who votes first: 0 = off, 1 = on", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_veto_bo3 = CreateConVar("wm_veto_bo3", "1", "Veto Style: 0 = Normal, 1 = New", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_veto_random = CreateConVar("wm_veto_random", "0", "After the vetoing is done, will a map be picked at random?", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	wm_veto_select = CreateConVar("wm_veto_select", "1", "On last two maps of Veto Bo1 will it be select map to play: 0 = No, 1 = Yes", FCVAR_NOTIFY, true, 0.0, true, 1.0);
	
	 /* Damage Printer */
	wm_damageprint_enabled = CreateConVar("wm_damageprint_enabled", "0", "Whether to enabled damage print to client on round end");
	
	/* License */
	wm_user_token = CreateConVar("wm_user_token", "", "Warmod+ user token.");
	
	g_MapNames = CreateArray(PLATFORM_MAX_PATH);
	
	g_iAccount = FindSendPropInfo("CCSPlayer", "m_iAccount");
	hostname = FindConVar("hostname");
	
	mp_startmoney = FindConVar("mp_startmoney");
	mp_match_can_clinch = FindConVar("mp_match_can_clinch");
	mp_maxrounds = FindConVar("mp_maxrounds");
	mp_overtime_enable = FindConVar("mp_overtime_enable");
	mp_overtime_maxrounds = FindConVar("mp_overtime_maxrounds");
	mp_teamname_1 = FindConVar("mp_teamname_1");
	mp_teamname_2 = FindConVar("mp_teamname_2");
	mp_teamlogo_1 = FindConVar("mp_teamlogo_1");
	mp_teamlogo_2 = FindConVar("mp_teamlogo_2");
	mp_teamflag_1 = FindConVar("mp_teamflag_1");
	mp_teamflag_2 = FindConVar("mp_teamflag_2");
	
	tv_delaymapchange = FindConVar("tv_delaymapchange");
	tv_delay = FindConVar("tv_delay");
	mp_match_end_restart = FindConVar("mp_match_end_restart");
	
	HookConVarChange(wm_active, OnActiveChange);
	HookConVarChange(wm_min_ready, OnMinReadyChange);
	HookConVarChange(wm_stats_trace, OnStatsTraceChange);
	HookConVarChange(wm_stats_trace_delay, OnStatsTraceDelayChange);
	HookConVarChange(wm_auto_ready, OnAutoReadyChange);
	HookConVarChange(mp_teamname_2, OnTChange);
	HookConVarChange(mp_teamname_1, OnCTChange);
	
	HookConVarChange(sv_matchpause_auto_5v5, Cvar_Changed);
	HookConVarChange(wm_chat_prefix, Cvar_Changed);
	HookConVarChange(wm_competition, Cvar_Changed);
	HookConVarChange(wm_event, Cvar_Changed);
	
	HookConVarChange(FindConVar("mp_restartgame"), Event_Round_Restart);
	
	HookEvent("round_start", Event_Round_Start);
	HookEvent("round_end", Event_Round_End);
	HookEvent("round_freeze_end", Event_Round_Freeze_End);
	
	HookEvent("player_blind", Event_Player_Blind);
	HookEvent("player_hurt",  Event_Player_Hurt);
	HookEvent("player_death",  Event_Player_Death);
	HookEvent("player_changename", Event_Player_Name);
	HookEvent("player_disconnect", Event_Player_Disc_Pre, EventHookMode_Pre);
	HookEvent("player_team", Event_Player_Team);
	HookEvent("player_team", Event_Player_Team_Post, EventHookMode_Post);
	HookEvent("player_spawned", Event_Player_Spawned);
	
	HookEvent("bomb_pickup", Event_Bomb_PickUp);
	HookEvent("bomb_dropped", Event_Bomb_Dropped);
	HookEvent("bomb_beginplant", Event_Bomb_Plant_Begin);
	HookEvent("bomb_abortplant", Event_Bomb_Plant_Abort);
	HookEvent("bomb_planted", Event_Bomb_Planted);
	HookEvent("bomb_begindefuse", Event_Bomb_Defuse_Begin);
	HookEvent("bomb_abortdefuse", Event_Bomb_Defuse_Abort);
	HookEvent("bomb_defused", Event_Bomb_Defused);
	HookEvent("bomb_exploded", Event_Bomb_Exploded);
	
	HookEvent("hostage_rescued", Event_Hostage_Rescued);
	

	HookEvent("weapon_fire", Event_Weapon_Fire);
	
	HookEvent("flashbang_detonate", Event_Detonate_Flash);
	HookEvent("smokegrenade_detonate", Event_Detonate_Smoke);
	HookEvent("hegrenade_detonate", Event_Detonate_HeGrenade);
	HookEvent("molotov_detonate", Event_Detonate_Molotov);
	HookEvent("decoy_detonate", Event_Detonate_Decoy);
	
	HookEvent("item_pickup", Event_Item_Pickup);
	HookEvent("round_mvp", Event_Round_MVP);
	
	CreateTimer(15.0, HelpText, 0, TIMER_REPEAT);
	
	/* Backup */
	RegAdminCmd("sm_backup", Command_MatchRestore, ADMFLAG_GENERIC, "Load backup round");
	//HookEvent("round_prestart", Event_PreRoundStart);
	
	/* Game Name*/
	wm_gamename = CreateConVar("wm_gamename", "- WARMOD+ -", "WarMod game name", FCVAR_NOTIFY);
}

public void OnConfigsExecuted()
{
	wm_chat_prefix.GetString(CHAT_PREFIX, sizeof(CHAT_PREFIX));
	wm_event.GetString(g_event, sizeof(g_event));
	wm_competition.GetString(g_competition, sizeof(g_competition));
	hostname.GetString(g_server, sizeof(g_server));
	
	wm_user_token.GetString(user_token, sizeof(user_token));
	if(StrEqual(user_token, ""))	SetFailState("wm_user_token can not be empty.");
	CheckLicense();
	
	save_db = wm_save_database.BoolValue;
	
	if (GetConVarBool(sv_matchpause_auto_5v5))
	{
		ServerCommand("wm_auto_pause 1");
		ServerCommand("sv_matchpause_auto_5v5 0");
	}
	
	char sBuffer[128];
	wm_gamename.GetString(sBuffer, sizeof(sBuffer));
	if(!StrEqual(sBuffer, ""))	SteamWorks_SetGameDescription(sBuffer);
}

public void OnMapStart()
{
	Handle dir = OpenDirectory("materials/panorama/images/tournaments/teams/");
	if (dir == null)
	{
		if(!DirExists("materials/panorama/images/tournaments/"))
		{
			CreateDirectory("materials/panorama/images/tournaments/", 777);
			
		}
		if(!DirExists("materials/panorama/images/tournaments/teams/"))
		{
			CreateDirectory("materials/panorama/images/tournaments/teams/", 777);
		}
	}
	
	DownloadTeamLogo();
	
	g_map_loaded++;
	ServerCommand("exec warmod+/on_map_load.cfg");
	
	ServerCommand("mp_teamname_1 %s", DEFAULT_CT_NAME);
	ServerCommand("mp_teamname_2 %s", DEFAULT_T_NAME);
	
	char g_MapName[64];
	char g_WorkShopID[64];
	char g_CurMap[128];
	GetCurrentMap(g_CurMap, sizeof(g_CurMap));
	if (StrContains(g_CurMap, "workshop", false) != -1)
	{
		GetCurrentWorkshopMap(g_MapName, sizeof(g_MapName), g_WorkShopID, sizeof(g_WorkShopID));
		LogMessage("Current Map: %s,  Workshop ID: %s, Warmod Version: %s", g_MapName, g_WorkShopID, WM_VERSION);
	}
	else
	{
		strcopy(g_map, sizeof(g_map), g_CurMap);
		LogMessage("Current Map: %s, Warmod Version: %s", g_CurMap, WM_VERSION);
	}
	StringToLower(g_map, sizeof(g_map));
		
	if (!GetConVarBool(mp_match_end_restart) && ((g_veto_bo3_active && g_veto_map_number < 3) || (g_veto_bo5_active && g_veto_map_number < 5) || (g_veto_bo2_active && g_veto_map_number < 2)))
	{
		if (g_veto_bo3_active)
		{
			g_ChosenMap = g_ChosenMapBo3[g_veto_map_number];
		}
		else if (g_veto_bo5_active)
		{
			g_ChosenMap = g_ChosenMapBo5[g_veto_map_number];
		}
		else
		{
			g_ChosenMap = g_ChosenMapBo2[g_veto_map_number];
		}
		
		g_veto_map_number++;
		
		char map[PLATFORM_MAX_PATH];
		GetArrayString(g_MapNames, g_ChosenMap, map, sizeof(map));
		ServerCommand("nextlevel %s", map);
	}
	
	if (GetConVarBool(wm_stats_trace))
	{
		// start trace timer
		g_stats_trace_timer = CreateTimer(GetConVarFloat(wm_stats_trace_delay), Stats_Trace, _, TIMER_REPEAT|TIMER_FLAG_NO_MAPCHANGE);
	}
	
	// reset any matches
	ResetMatch(true, false);
	g_bRecording = false;
	
	// Veto
	g_MapVetoed = CreateArray();
	g_veto_s = false;
	g_veto_number = 0;
	g_t_knife = false;
	
	// Call
	CanCall = true;
}

public void OnMapEnd()
{
	CloseHandle(g_MapVetoed);
	ServerCommand("hostname %s", g_server);
}

public void OnLibraryRemoved(const char[]name)
{
	if (StrEqual(name, "adminmenu"))
	{
		g_h_menu = INVALID_HANDLE;
	}
}

public void OnAdminMenuReady(Handle topmenu)
{
	if (topmenu == g_h_menu)
	{
		return;
	}
	
	g_h_menu = topmenu;
	TopMenuObject new_menu = AddToTopMenu(g_h_menu, "WarModCommands", TopMenuObject_Category, MenuHandler, INVALID_TOPMENUOBJECT);
	
	if (new_menu == INVALID_TOPMENUOBJECT)
	{
		return;
	}
	
	// add menu items
	AddToTopMenu(g_h_menu, "forcestart", TopMenuObject_Item, MenuHandler, new_menu, "forcestart", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "readyup", TopMenuObject_Item, MenuHandler, new_menu, "readyup", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "knife", TopMenuObject_Item, MenuHandler, new_menu, "knife", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "cancelhalf", TopMenuObject_Item, MenuHandler, new_menu, "cancelhalf", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "cancelmatch", TopMenuObject_Item, MenuHandler, new_menu, "cancelmatch", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "forceallready", TopMenuObject_Item, MenuHandler, new_menu, "forceallready", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "forceallunready", TopMenuObject_Item, MenuHandler, new_menu, "forceallunready", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "forceallspectate", TopMenuObject_Item, MenuHandler, new_menu, "forceallspectate", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "toggleactive", TopMenuObject_Item, MenuHandler, new_menu, "toggleactive", ADMFLAG_CUSTOM1);
	
	AddToTopMenu(g_h_menu, "admpause", TopMenuObject_Item, MenuHandler, new_menu, "admpause", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "admunpause", TopMenuObject_Item, MenuHandler, new_menu, "admunpause", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "fb", TopMenuObject_Item, MenuHandler, new_menu, "fb", ADMFLAG_CUSTOM1);
	AddToTopMenu(g_h_menu, "fp", TopMenuObject_Item, MenuHandler, new_menu, "fp", ADMFLAG_CUSTOM1);
}

public Action UpdateCFGs(int client, int args)
{
	Update_Configs(WM_VERSION);
	return Plugin_Handled;
}

public void OnClientPostAdminCheck(int client)
{
	if (!IsValidClient(client) || !IsActive(0, true))	return;
	
	// reset client state
	g_player_list[client] = PLAYER_DISC;
	g_cancel_list[client] = false;
	g_disconnect[client] = false;
	g_clanTagsChecked[client] = false;
	
	f_PlayerRWS[client] = 0.00;
	f_PlayerRWS_join[client] = 0.00;
	i_PlayerRounds[client] = 0;
	i_RoundPoints[client] = 0;
	
	player_team_name[client] = "";
	player_team_logo[client] = "";
	player_team_use[client] = false;
	player_team_leader[client] = false;
	player_loaded[client] = false;
	player_team_id[client] = 0;
	
	if(authed && !IsFakeClient(client))	LoadPlayerInfo(client);
}

public void OnClientDisconnect(int client)
{
	// reset client state
	g_player_list[client] = PLAYER_DISC;
	g_cancel_list[client] = false;
	g_clanTagsChecked[client] = false;
	Hasdamagereport[client] = false;
	
	player_team_name[client] = "";
	player_team_logo[client] = "";
	player_team_use[client] = false;
	player_team_leader[client] = false;
	f_PlayerRWS[client] = 0.00;
	f_PlayerRWS_join[client] = 0.00;
	i_PlayerRounds[client] = 0;
	i_RoundPoints[client] = 0;
	player_loaded[client] = false;
	if (LogoTimer[client] != INVALID_HANDLE)
    {
        KillTimer(LogoTimer[client]);
        LogoTimer[client] = INVALID_HANDLE;
    }
	
	// log player stats
	if (g_live && GameRules_GetProp("m_bFreezePeriod") == 0 && (GetTTotalScore() + GetCTTotalScore()) != 0) 
	{
		if(IsValidClient(client) && !IsFakeClient(client))
		{
			LogPlayerStats(client);
			if (match_id > 0 )	SaveStats(client); 
		}
	}
	
	if (!IsActive(client, true))
	{
		// warmod is disabled
		return;
	}
	
	if (g_ready_enabled && !g_live)
	{
		// display ready system
		ShowInfo(client, true, false, 0);
	}
	
	if ((g_match) && GetConVarBool(wm_auto_pause))
	{
		AutoPause();
	}
	
	if ((g_match || g_t_knife) && GetConVarBool(wm_ban_on_disconnect) && g_disconnect[client] == true)
	{
		int count = CS_GetPlayerListCount();
		float percent = GetConVarFloat(wm_ban_percentage);
		
		if (count > (GetConVarInt(wm_max_players) * percent))
		{
			char reason[32] = "Disconnected from live match";
			char authid[32];
			GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
			
			ServerCommand("sm_addban %i %s %s", GetConVarInt(sv_kick_ban_duration), authid, reason);
			CPrintToChatAll("%t", "Banned player reason", CHAT_PREFIX, authid, GetConVarInt(sv_kick_ban_duration), reason);
			g_disconnect[client] = false;
		}
	}
	
	// all players disconnect in match
	if(GetTeamClientCount(CT) == 0 && GetTeamClientCount(TR) == 0 && g_match)
	{
		CancelMatch(0, 0);
	}
	
	// reset team if not in pug and not in match
	if(!IsPug && !g_match && !g_t_knife)
	{
		if(client == g_capt1)
		{
			Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
			Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
			EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
			g_capt1 = -1;
			g_t_id = 0;
			ServerCommand("mp_teamname_2 %s", DEFAULT_T_NAME);
			ServerCommand("mp_teamlogo_2 \"\"");
			ServerCommand("mp_teamflag_2 \"\"");
			CPrintToChatAll("%t", "T Captain Reset", CHAT_PREFIX);
		}
		else if(client == g_capt2)
		{
			Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
			Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
			EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
			g_capt2 = -1;
			g_ct_id = 0;
			ServerCommand("mp_teamname_1 %s", DEFAULT_CT_NAME);
			ServerCommand("mp_teamlogo_1 \"\"");
			ServerCommand("mp_teamflag_1 \"\"");
			CPrintToChatAll("%t", "CT Captain Reset", CHAT_PREFIX);
		}
	}
}

void ResetMatch(bool silent, bool complete) {
	
	if (g_bBackuping == true)
	{
		return;
	}
	
	if (g_match) {
		Call_StartForward(g_f_on_reset_match);
		Call_Finish();
		
		char event_name[] = "match_reset";
		LogSimpleEvent(event_name, sizeof(event_name));

		// end of log
		char event_name2[] = "log_end";
		LogSimpleEvent(event_name2, sizeof(event_name2));
	}
	
	if (!complete) {
		//stop demo from uploading
		g_MatchComplete = false;
	}
	
	if (g_match) {
		// execute relevant server config
		CreateTimer(GetConVarFloat(wm_reset_config_delay), Timer_DelayedResetConfig);
		if (!silent) //Reset
		{
			CPrintToChatAll("%t", "Match End 1", CHAT_PREFIX);
			
			if(match_id > 0)	CPrintToChatAll("%t", "MySQL Match_ID", CHAT_PREFIX, match_id);
		}
		
		else if (silent) //Match End
		{
			CPrintToChatAll("%t", "Match End 1", CHAT_PREFIX);
			if(!StrEqual(siteLocation, ""))	CPrintToChatAll("%t", "Match End 2", CHAT_PREFIX, siteLocation);
			if(match_id > 0)	CPrintToChatAll("%t", "MySQL Match_ID", CHAT_PREFIX, match_id);
			ServerCommand("sm_vetomaps");
		}
	}
	
	if (g_log_file != INVALID_HANDLE) {
		// close log file
		FlushFile(g_log_file);
		CloseHandle(g_log_file);
		g_log_file = INVALID_HANDLE;
	}
	
	if (g_log_veto_file != INVALID_HANDLE) {
		// close log file
		FlushFile(g_log_veto_file);
		CloseHandle(g_log_veto_file);
		g_log_veto_file = INVALID_HANDLE;
	}
	
	// reset state
	g_start = false;
	g_match = false;
	g_max_lock = false;
	g_live = false;
	g_log_live = false;
	g_half_swap = true;
	g_first_half = true;
	g_second_half_first = false;
	g_t_money = false;
	g_t_score = false;
	g_t_knife = false;
	g_t_had_knife = false;
	g_DispInfoLimiter = true;
	SetAllCancelled(false);
	ReadyChangeAll(0, false, true);
	ResetMatchScores();
	if (!g_first_load && g_map_loaded > 2) {
		ResetTeams();
	}
	g_first_load = false;
	g_overtime = false;
	g_overtime_count = 0;
	g_t_pause_count = 0;
	g_ct_pause_count = 0;
	g_round = 1;
	
	LiveOn2 = false;
	LiveOn1 = false;
	LiveOn3Text = false;
	KnifeOn2 = false;
	KnifeOn1 = false;
	KnifeOn3Text = false;
	
	g_t_veto = false;
	veto_offer_ct = false;
	veto_offer_t = false;
	g_veto_active = false;
	
	default_offer_ct = false;
	default_offer_t = false;
	
	overtime_offer_ct = false;
	overtime_offer_t = false;
	
	playout_offer_ct = false;
	playout_offer_t = false;
	
	team_switch = false;
	
	g_auto_pause = false;
	ServerCommand("mp_unpause_match 1");
	if (g_h_stored_timer != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer);
		g_h_stored_timer = INVALID_HANDLE;
	}
	if (g_h_stored_timer_p != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_p);
		g_h_stored_timer_p = INVALID_HANDLE;
	}
	if (g_h_stored_timer_pl != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_pl);
		g_h_stored_timer_pl = INVALID_HANDLE;
	}
	if (g_h_stored_timer_ot != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_ot);
		g_h_stored_timer_ot = INVALID_HANDLE;
	}
	if (g_h_stored_timer_def != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_def);
		g_h_stored_timer_def = INVALID_HANDLE;
	}
	UpdateStatus();
	
	// stop tv recording after 5 seconds
	CreateTimer(5.0, StopRecord);
	//CreateTimer(5.0, LogFileUpload);
	
	if (GetConVarBool(wm_auto_ready))
	{
		// enable ready system
		ReadySystem(true);
		ShowInfo(0, true, false, 0);
		// update status code
		UpdateStatus();
	}
	else if (g_ready_enabled)
	{
		// disable ready system
		ReadySystem(false);
		ShowInfo(0, false, false, 1);
	}
	
	if (!silent)
	{
		// message display to players
		for (int x = 1; x <= 3; x++)
		{
			CPrintToChatAll("%t", "Match Reset", CHAT_PREFIX);
		}
		// restart round
		ServerCommand("mp_restartgame 1");
	}
	
	ResetClutchStats();
	for (int i = 1; i < MAXPLAYERS; i++)
	{
		round_kills[i] = 0;
		round_health[i] = 100;
		WasClientInGameAtAll[i] = false;
		WasClientTeam[i] = 0;
		for (int y = 0; y < MATCH_NUM; y++)
		{
			match_stats[i][y] = 0;
		}
	}
}

void ResetHalf(bool silent)
{
	if (g_bBackuping == true)
	{
		return;
	}
	if (g_match)
	{
		Call_StartForward(g_f_on_reset_half);
		Call_Finish();
		
		char event_name[] = "match_half_reset";
		LogSimpleEvent(event_name, sizeof(event_name));
	}
	if (!g_first_half)
	{
		g_half_swap = true;
	}
	
	// reset half state
	g_live = false;
	g_t_money = false;
	g_t_score = false;
	g_t_knife = false;
	SetAllCancelled(false);
	ReadyChangeAll(0, false, true);
	ResetHalfScores();
	UpdateStatus();
	g_t_pause_count = 0;
	g_ct_pause_count = 0;
	
	ServerCommand("mp_unpause_match 1");
	if (g_h_stored_timer != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer);
		g_h_stored_timer = INVALID_HANDLE;
	}
	if (g_h_stored_timer_p != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_p);
		g_h_stored_timer_p = INVALID_HANDLE;
	}
	
	if (GetConVarBool(wm_auto_ready))
	{
		// display ready system
		ReadySystem(true);
		ShowInfo(0, true, false, 0);
		UpdateStatus();
	}
	else
	{
		// disable ready system
		ReadySystem(false);
		ShowInfo(0, false, false, 1);
	}
	
	if (!silent)
	{
		// display message for players
		for (int x = 1; x <= 3; x++)
		{
			CPrintToChatAll("%t", "Half Reset", CHAT_PREFIX);
		}
		// restart round
		ServerCommand("mp_restartgame 1");
	}
}

void ResetTeams()
{
	Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
	Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
	EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
	Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
	Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
	EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
	ServerCommand("mp_teamname_2 %s", g_t_name);
	ServerCommand("mp_teamlogo_2 \"\"");
	ServerCommand("mp_teamflag_2 \"\"");
	ServerCommand("mp_teamname_1 %s", g_ct_name);
	ServerCommand("mp_teamlogo_1 \"\"");
	ServerCommand("mp_teamflag_1 \"\"");
	
	g_ct_id = 0;
	g_t_id = 0;
	g_capt1 = -1;
	g_capt2 = -1;
}

void ResetMatchScores()
{
	// reset match scores
	g_scores[SCORE_T][SCORE_FIRST_HALF] = 0;
	g_scores[SCORE_T][SCORE_SECOND_HALF] = 0;
	
	g_scores[SCORE_CT][SCORE_FIRST_HALF] = 0;
	g_scores[SCORE_CT][SCORE_SECOND_HALF] = 0;
	
	// reset overtime scores
	for (int i = 0; i <= g_overtime_count; i++)
	{
		g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF] = 0;
		g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF] = 0;
		
		g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF] = 0;
		g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF] = 0;
	}
}

void ResetHalfScores()
{
	// reset scores for the current half
	if (!g_overtime)
	{
		// not overtime
		if (g_first_half)
		{
			// first half
			g_scores[SCORE_T][SCORE_FIRST_HALF] = 0;
			g_scores[SCORE_CT][SCORE_FIRST_HALF] = 0;
		}
		else
		{
			// second half
			g_scores[SCORE_T][SCORE_SECOND_HALF] = 0;
			g_scores[SCORE_CT][SCORE_SECOND_HALF] = 0;
		}
	}
	else
	{
		// overtime
		if (g_first_half)
		{
			// first half overtime
			g_scores_overtime[SCORE_T][g_overtime_count][SCORE_FIRST_HALF] = 0;
			g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_FIRST_HALF] = 0;
		}
		else
		{
			// second half overtime
			g_scores_overtime[SCORE_T][g_overtime_count][SCORE_SECOND_HALF] = 0;
			g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_SECOND_HALF] = 0;
		}
	}
}

public Action ReadyToggle(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (IsLive(client, false))
	{
		// match is live
		return Plugin_Handled;
	}
	
	// change ready state
	ReadyChangeAll(client, false, true);
	SetAllCancelled(false);
	
	if (!IsReadyEnabled(client, true))
	{
		// display ready system
		ReadySystem(true);
		ShowInfo(client, true, false, 0);
		if (client != 0)
		{
			PrintToConsole(client, "%t", "Ready System Enabled", CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Ready System Enabled", CHAT_PREFIX);
		}
		// check if anyone is ready
		CheckReady();
	}
	else
	{
		// disable ready system
		ShowInfo(client, false, false, 1);
		ReadySystem(false);
		if (client != 0)
		{
			PrintToConsole(client, "%t", "Ready System Disabled", CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Ready System Disabled", CHAT_PREFIX);
		}
	}
	
	LogAction(client, -1, "\"ready_toggle\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ActiveToggle(int client, int args)
{
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (GetConVarBool(wm_active))
	{
		// disable warmod
		SetConVarBool(wm_active, false);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Set Inactive", client, CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Set Inactive Server", CHAT_PREFIX);
		}
	}
	else
	{
		// enable warmod
		SetConVarBool(wm_active, true);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Set Active", client, CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Set Active Server", CHAT_PREFIX);
		}
	}
	
	LogAction(client, -1, "\"active_toggle\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

//Pause and Unpause Commands + timers
static void AutoPause()
{
	if (g_match && !g_auto_pause && !admpause)
	{
		if (GetTeamClientCount(CS_TEAM_CT) < (GetConVarInt(wm_max_players)/2))
		{
			char name[64];
			if (StrEqual(g_ct_name, ""))
			{
				Format(name, sizeof(name), DEFAULT_CT_NAME);
			}
			else
			{
				Format(name, sizeof(name), g_ct_name);
			}
			CPrintToChatAll("%t", "Auto Pause Notice CT", CHAT_PREFIX, name);
				
			if (GameRules_GetProp("m_bFreezePeriod") == 1)
			{
				CPrintToChatAll("%t", "Auto Unpause Notice CT", CHAT_PREFIX, name);
				CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
				ServerCommand("mp_pause_match 1");				
				g_auto_pause = false;
			}
			else
			{
				g_auto_pause = true;
			}
		}
		else if (GetTeamClientCount(CS_TEAM_T) < (GetConVarInt(wm_max_players)/2))
		{
			char name[64];
			if (StrEqual(g_t_name, ""))
			{
				Format(name, sizeof(name), DEFAULT_T_NAME);
			}
			else
			{
				Format(name, sizeof(name), g_t_name);
			}
			CPrintToChatAll("%t", "Auto Pause Notice T", CHAT_PREFIX, name);
			if (GameRules_GetProp("m_bFreezePeriod") == 1)
			{
				CPrintToChatAll("%t", "Auto Unpause Notice T", CHAT_PREFIX, name);
				CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
				ServerCommand("mp_pause_match 1");
				g_auto_pause = false;
			}
			else
			{
				g_auto_pause = true;
			}
		}
	}
}

public Action Pause(int client, int args)
{
	if (client == 0){
		return Plugin_Handled;
	}
	
	if (GetConVarBool(sv_pausable) && g_live && !admpause)
	{
		if (GetConVarBool(wm_pause_confirm))
		{
			if (GetClientTeam(client) == 2 && g_pause_offered_ct == true)
			{
				if (g_h_stored_timer_p != INVALID_HANDLE)
				{
					KillTimer(g_h_stored_timer_p);
					g_h_stored_timer_p = INVALID_HANDLE;
				}
				
				g_pause_offered_ct = false;
				g_ct_pause_count++;
				
				CPrintToChatAll("%t", "Pause Freeze Time", CHAT_PREFIX);
				g_pause_freezetime = true;
				
				if (FreezeTime)
				{
					//Pause command fire on round end May change to on round start
					if (g_pause_freezetime == true)
					{
						g_pause_freezetime = false;
						CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
						if (GetConVarBool(wm_auto_unpause))
						{
							CPrintToChatAll("%t", "Unpause Timer", CHAT_PREFIX, GetConVarInt(wm_auto_unpause_delay));
							g_h_stored_timer = CreateTimer(GetConVarFloat(wm_auto_unpause_delay), UnPauseTimer);
						}
						ServerCommand("mp_pause_match 1");
					}
				}
				return Plugin_Handled;
			}
			else if (GetClientTeam(client) == 3 && g_pause_offered_t == true)
			{
				if (g_h_stored_timer_p != INVALID_HANDLE)
				{
					KillTimer(g_h_stored_timer_p);
					g_h_stored_timer_p = INVALID_HANDLE;
				}
				g_pause_offered_t = false;
				g_t_pause_count++;
				
				CPrintToChatAll("%t", "Pause Round End", CHAT_PREFIX);
				g_pause_freezetime = true;
				
				
				if (FreezeTime)
				{
					//Pause command fire on round end May change to on round start
					if (g_pause_freezetime == true)
					{
						g_pause_freezetime = false;
						CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
						if (GetConVarBool(wm_auto_unpause))
						{
							CPrintToChatAll("%t", "Unpause Timer", CHAT_PREFIX, GetConVarInt(wm_auto_unpause_delay));
							g_h_stored_timer = CreateTimer(GetConVarFloat(wm_auto_unpause_delay), UnPauseTimer);
						}
						ServerCommand("mp_pause_match 1");
					}
				}
				return Plugin_Handled;
			}
			else if (GetClientTeam(client) == 2 && g_t_pause_count == GetConVarInt(wm_pause_limit))
			{
				CPrintToChat(client, "%T", "Pause Limit", client, CHAT_PREFIX);
			}
			else if (GetClientTeam(client) == 3 && g_ct_pause_count == GetConVarInt(wm_pause_limit))
			{
				CPrintToChat(client, "%T", "Pause Limit", client, CHAT_PREFIX);
			}
			else if (GetClientTeam(client) < 2 )
			{
				CPrintToChat(client, "%T", "Pause Non-player", client, CHAT_PREFIX);
			}
			else if (GetClientTeam(client) == 3 && g_ct_pause_count != GetConVarInt(wm_pause_limit) && g_pause_offered_ct == false)
			{
				g_pause_offered_ct = true;
				CPrintToChatAll("%t", "Pause Offer CT", CHAT_PREFIX, g_ct_name);
				g_h_stored_timer_p = CreateTimer(30.0, PauseTimeout);
			}
			else if (GetClientTeam(client) == 2 && g_t_pause_count != GetConVarInt(wm_pause_limit) && g_pause_offered_t == false)
			{
				g_pause_offered_t = true;
				CPrintToChatAll("%t", "Pause Offer T", CHAT_PREFIX, g_t_name);
				g_h_stored_timer_p = CreateTimer(30.0, PauseTimeout);
			}
		}
		else if (GetClientTeam(client) == 3 && g_ct_pause_count != GetConVarInt(wm_pause_limit) && !GetConVarBool(wm_pause_confirm))
		{
			char player_name[64];
			char authid[32];
			
			GetClientName(client, player_name, sizeof(player_name));
			GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
			
			EscapeString(player_name, sizeof(player_name));
			EscapeString(authid, sizeof(authid));
			
			g_ct_pause_count++;
			CPrintToChatAll("%t", "Has paused the match CT", CHAT_PREFIX, player_name, g_ct_name);
			CPrintToChatAll("%t", "Pause Freeze Time", CHAT_PREFIX);
			g_pause_freezetime = true;
			
			
			LogEvent("{\"event\": \"match_paused\", \"round\": %i, \"team\": 3, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
			
			if (FreezeTime)
			{
				//Pause command fire on round end May change to on round start
				if (g_pause_freezetime == true)
				{
					g_pause_freezetime = false;
					CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
					if (GetConVarBool(wm_auto_unpause))
					{
						CPrintToChatAll("%t", "Unpause Timer", CHAT_PREFIX, GetConVarInt(wm_auto_unpause_delay));
						g_h_stored_timer = CreateTimer(GetConVarFloat(wm_auto_unpause_delay), UnPauseTimer);
					}
					ServerCommand("mp_pause_match 1");
				}
			}
			return Plugin_Handled;
		}
		else if (GetClientTeam(client) == 2 &&  g_t_pause_count != GetConVarInt(wm_pause_limit) && GetConVarBool(wm_pause_confirm) == false)
		{
			char player_name[64];
			char authid[32];
			
			GetClientName(client, player_name, sizeof(player_name));
			GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
			
			EscapeString(player_name, sizeof(player_name));
			EscapeString(authid, sizeof(authid));
			
			g_t_pause_count++;
			CPrintToChatAll("%t", "Has paused the match T", CHAT_PREFIX, player_name, g_t_name);
			CPrintToChatAll("%t", "Pause Freeze Time", CHAT_PREFIX);
			g_pause_freezetime = true;
			
			
			LogEvent("{\"event\": \"match_paused\", \"round\": %i, \"team\": 2, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
			
			if (FreezeTime)
			{
				//Pause command fire on round end May change to on round start
				if (g_pause_freezetime == true)
				{
					g_pause_freezetime = false;
					CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
					if (GetConVarBool(wm_auto_unpause))
					{
						CPrintToChatAll("%t", "Unpause Timer", CHAT_PREFIX, GetConVarInt(wm_auto_unpause_delay));
						g_h_stored_timer = CreateTimer(GetConVarFloat(wm_auto_unpause_delay), UnPauseTimer);
					}
					ServerCommand("mp_pause_match 1");
				}
			}
			return Plugin_Handled;
		}
		else if (GetClientTeam(client) == 2 && g_t_pause_count == GetConVarInt(wm_pause_limit))
		{
			CPrintToChat(client, "%T", "Pause Limit", client, CHAT_PREFIX);
		}
		else if (GetClientTeam(client) == 3 && g_ct_pause_count == GetConVarInt(wm_pause_limit))
		{
			CPrintToChat(client, "%T", "Pause Limit", client, CHAT_PREFIX);
		}
		else if (GetClientTeam(client) < 2)
		{
			CPrintToChat(client, "%T", "Pause Non-player", client, CHAT_PREFIX);
		}
	}
	else if (!g_live)
	{
		CPrintToChatAll("%t", "Match Not In Progress", CHAT_PREFIX);
	}
	else if(!GetConVarBool(sv_pausable) && g_live && !admpause)
	{
		CPrintToChatAll("%t", "Pause Not Enabled", CHAT_PREFIX);
	}
	else if(GetConVarBool(sv_pausable) && g_live && admpause)
	{
		CPrintToChatAll("%t", "Match Has Been Paused By Admin", CHAT_PREFIX);
	}
	
	return Plugin_Handled;
}

public Action Unpause(int client, int args)
{
	if (IsPaused() && client != 0)
	{
		if(!admpause)
		{
			if (GetConVarBool(wm_unpause_confirm))
			{
				if (GetClientTeam(client) == 3 && g_pause_offered_ct == false && g_pause_offered_t == false)
				{
					g_pause_offered_ct = true;
					for (int i = 1; i <= MaxClients; i++)
					{
						if (IsValidClient(i) && !IsFakeClient(i))
						{
							PrintToConsole(i, "%t", "Unpause Offer Console", CHAT_PREFIX, g_ct_name);
						}
					}
					CPrintToChatAll("%t", "Unpause Offer CT", CHAT_PREFIX, g_ct_name);
				}
				else if (GetClientTeam(client) == 2 && g_pause_offered_t == false && g_pause_offered_ct == false)
				{
					g_pause_offered_t = true;
					for (int i = 1; i <= MaxClients; i++)
					{
						if (IsValidClient(i) && !IsFakeClient(i))
						{
							PrintToConsole(i, "%t", "Unpause Offer Console", CHAT_PREFIX, g_t_name);
						}
					}
					CPrintToChatAll("%t", "Unpause Offer T", CHAT_PREFIX, g_t_name);
				}
				else if (GetClientTeam(client) == 2 && g_pause_offered_ct == true)
				{
					g_pause_offered_ct = false;
				
					ServerCommand("mp_unpause_match 1");
					if (g_h_stored_timer != INVALID_HANDLE)
					{
						KillTimer(g_h_stored_timer);
						g_h_stored_timer = INVALID_HANDLE;
					}
				}
				else if (GetClientTeam(client) == 3 && g_pause_offered_t == true)
				{
					g_pause_offered_t = false;
					
					ServerCommand("mp_unpause_match 1");
					if (g_h_stored_timer != INVALID_HANDLE)
					{
						KillTimer(g_h_stored_timer);
						g_h_stored_timer = INVALID_HANDLE;
					}
				}
				else if (GetClientTeam(client) < 2 )
				{
					PrintToConsole(client, "%t", "Unpause Non-player Console", CHAT_PREFIX);
					CPrintToChat(client, "%T", "Unpause Non-player", client, CHAT_PREFIX);
				}
			}
			else
			{
				if (GetClientTeam(client) == 2)
				{
					char player_name[64];
					char authid[32];
				
					GetClientName(client, player_name, sizeof(player_name));
					GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
				
					EscapeString(player_name, sizeof(player_name));
					EscapeString(authid, sizeof(authid));
				
					CPrintToChatAll("%t", "Unpaused Match T", CHAT_PREFIX, player_name, g_t_name);
				
					ServerCommand("mp_unpause_match 1");
					if (g_h_stored_timer != INVALID_HANDLE)
					{
						KillTimer(g_h_stored_timer);
						g_h_stored_timer = INVALID_HANDLE;
					}
				
					LogEvent("{\"event\": \"match_resumed\", \"round\": %i, \"team\": 2, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
				}
				else if (GetClientTeam(client) == 3)
				{
					char player_name[64];
					char authid[32];
				
					GetClientName(client, player_name, sizeof(player_name));
					GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
				
					EscapeString(player_name, sizeof(player_name));
					EscapeString(authid, sizeof(authid));
				
					CPrintToChatAll("%t", "Unpaused Match CT", CHAT_PREFIX, player_name, g_ct_name);
				
					ServerCommand("mp_unpause_match 1");
					if (g_h_stored_timer != INVALID_HANDLE)
					{
						KillTimer(g_h_stored_timer);
						g_h_stored_timer = INVALID_HANDLE;
					}
					LogEvent("{\"event\": \"match_resumed\", \"round\": %i, \"team\": 3, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
				}
				else if (GetClientTeam(client) < 2 )
				{
					PrintToConsole(client, "%t", "Unpause Non-player Console", CHAT_PREFIX);
					CPrintToChat(client, "%T", "Unpause Non-player", client, CHAT_PREFIX);
				}
			}
		}
	}
	return Plugin_Handled;
}

public Action UnpauseMatch(int client, const char[]command, int args)
{
	g_h_stored_timer = INVALID_HANDLE;
	g_pause_offered_ct = false;
	g_pause_offered_t = false;
	return Plugin_Continue;
}

public Action PauseTimeout(Handle timer)
{
	g_h_stored_timer_p = INVALID_HANDLE;
	CPrintToChatAll("%t", "Pause Not Confirmed", CHAT_PREFIX);
	g_pause_offered_ct = false;
	g_pause_offered_t = false;
}

public Action UnPauseTimer(Handle timer)
{
	g_h_stored_timer = INVALID_HANDLE;
	if (IsPaused())
	{
		CPrintToChatAll("%t", "Unpause Auto", CHAT_PREFIX);
	}
	ServerCommand("mp_unpause_match 1");
	g_pause_offered_ct = false;
	g_pause_offered_t = false;
}

public Action ResetMatchTimer(Handle timer)
{
	ResetMatch(true, true);
}

stock bool IsValidClient(int client)
{
	if (client <= 0) return false;
	if (client > MaxClients) return false;
	if (!IsClientConnected(client)) return false;
	return IsClientInGame(client);
}

public Action ChangeMinReady(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char arg[128];
	int minready;
	
	if (GetCmdArgs() > 0)
	{
		// setter
		GetCmdArg(1, arg, sizeof(arg));
		minready = StringToInt(arg);
		SetConVarInt(wm_min_ready, minready);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Set Minready", client, CHAT_PREFIX, minready);
		}
		else
		{
			PrintToServer("%t", "Set Minready Server", CHAT_PREFIX, minready);
		}
		LogAction(client, -1, "\"set_min_ready\" (player \"%L\") (min_ready \"%d\")", client, minready);
	}
	else
	{
		// getter
		if (client != 0)
		{
			CPrintToChat(client, "%T", "wm_min_ready", client, CHAT_PREFIX, GetConVarInt(wm_min_ready));
		}
		else
		{
			PrintToServer("%t", "wm_min_ready Server", CHAT_PREFIX, GetConVarInt(wm_min_ready));
		}
	}
	
	return Plugin_Handled;
}

public Action ChangeMaxRounds(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char arg[128];
	int maxrounds;
	
	if (GetCmdArgs() > 0)
	{
		// setter
		GetCmdArg(1, arg, sizeof(arg));
		maxrounds = StringToInt(arg);
		int rounds = (maxrounds*2);
		ServerCommand("mp_maxrounds %i", rounds);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Set Maxrounds", client, CHAT_PREFIX, maxrounds);
		}
		else
		{
			PrintToServer("%t", "Set Maxrounds Server", CHAT_PREFIX, maxrounds);
		}
		LogAction(client, -1, "\"set_max_rounds\" (player \"%L\") (max_rounds \"%d\")", client, maxrounds);
	}
	
	return Plugin_Handled;
}

public Action ChangePassword(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char new_password[128];
	
	if (GetCmdArgs() > 0)
	{
		// setter
		GetCmdArg(1, new_password, sizeof(new_password));
		ServerCommand("sv_password \"%s\"", new_password);
		
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Set Password", client, CHAT_PREFIX, new_password);
		}
		else
		{
			PrintToServer("%t", "Set Password Server", CHAT_PREFIX, new_password);
		}
		
		LogAction(client, -1, "\"set_password\" (player \"%L\")", client);
	}
	else
	{
		// getter
		char passwd[128];
		GetConVarString(FindConVar("sv_password"), passwd, sizeof(passwd));
		if (client != 0)
		{
			CPrintToChat(client, "%T", "sv_password", client, CHAT_PREFIX, passwd);
		}
		else
		{
			PrintToServer("%t", "sv_password Server", CHAT_PREFIX, passwd);
		}
	}
	
	return Plugin_Handled;
}

public Action ReadyUp(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsReadyEnabled(client, false) || client == 0)
	{
		// ready system not enabled or client is the console
		return Plugin_Handled;
	}
	
	if (IsLive(client, false))
	{
		// match already live
		return Plugin_Handled;
	}
	
	if(!authed)
	{
		CPrintToChat(client, "%T", "Not Authed", client,  CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if(IsPug)	return Plugin_Handled;
	
	if (g_player_list[client] != PLAYER_READY)
	{
		if (GetClientTeam(client) > 1)
		{
			// set player as ready if profile loaded
			if(player_loaded[client])	ReadyServ(client, true, false, true, false);
			else	CPrintToChat(client, "%T", "Profile not ready", client, CHAT_PREFIX, f_PlayerRWS[client]);
		}
		else
		{
			// player is not on a valid team
			CPrintToChat(client, "%T", "Not on Team", client, CHAT_PREFIX);
		}
	}
	else
	{
		// player is already ready
		CPrintToChat(client, "%T", "Already Ready", client, CHAT_PREFIX);
	}
	return Plugin_Handled;
}

public Action ReadyDown(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsReadyEnabled(client, false) || client == 0)
	{
		// ready system not enabled or client is the console
		return Plugin_Handled;
	}
	
	if (IsLive(client, false))
	{
		return Plugin_Handled;
	}
	
	if (g_player_list[client] != PLAYER_UNREADY)
	{
		if (GetClientTeam(client) > 1)
		{
			// set player as ready
			ReadyServ(client, false, false, true, false);
		}
		else
		{
			// player is not on a valid team
			CPrintToChat(client, "%T", "Not on Team", client, CHAT_PREFIX);
		}
	}
	else
	{
		// player is already ready
		CPrintToChat(client, "%T", "Already Not Ready", client, CHAT_PREFIX);
	}
	return Plugin_Handled;
}

public Action ForceAllReady(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (g_ready_enabled)
	{
		// force all players to ready
		ReadyChangeAll(client, true, true);
		// check if there is enough players
		CheckReady();
		
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Forced Ready", client, CHAT_PREFIX);
		}
		else
		{
			PrintToConsole(client, "%t", "Forced Ready Console", CHAT_PREFIX);
		}
		
		// display ready system
		ShowInfo(client, true, false, 0);
	}
	else
	{
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Ready System Disabled2", client, CHAT_PREFIX);
		}
		else
		{
			PrintToConsole(client, "%t", "Ready System Disabled2 Console", CHAT_PREFIX);
		}
	}
	
	LogAction(client, -1, "\"force_all_ready\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ForceAllUnready(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (g_ready_enabled)
	{
		// force all players to unready
		ReadyChangeAll(client, false, true);
		CheckReady();
		
		if (client != 0)
		{
			CPrintToChatAll("%t", "Forced Not Ready", CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Forced Not Ready Server", CHAT_PREFIX);
		}
		
		// display readym system
		ShowInfo(client, true, false, 0);
	}
	else
	{
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Ready System Disabled2", client, CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Ready System Disabled2 Console", CHAT_PREFIX);
		}
	}
	
	LogAction(client, -1, "\"force_all_unready\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ForceAllSpectate(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	// reset half and restart
	ForceSpectate();
	ResetHalf(true);
	ShowInfo(0, false, false, 1);
	SetAllCancelled(false);
	ReadySystem(false);
	
	LogAction(client, -1, "\"force__all_spec\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

void ForceSpectate()
{
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && !IsFakeClient(i))
		{
			if (GetClientTeam(i) != 1)
			{
				ChangeClientTeam(i, 1);
			}
			else
			{
				CPrintToChat(i, "%T", "You are already a spectator", i, CHAT_PREFIX);
			}
		}
	}
}

public Action PlayOut_Offer(int client, int args)
{
	if (client == 0)
	{
		PlayOut(client, args);
		return Plugin_Handled;
	}
	
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (GetClientTeam(client) == 2)
	{
		if (playout_offer_t)
		{
			return Plugin_Handled;
		}
		playout_offer_t = true;
	}
	else if (GetClientTeam(client) == 3)
	{
		if (playout_offer_ct)
		{
			return Plugin_Handled;
		}
		playout_offer_ct = true;
	}
	else
	{
		CPrintToChat(client, "%T", "Non-player", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (playout_offer_ct && playout_offer_t)
	{
		if (g_h_stored_timer_pl != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer_pl);
			g_h_stored_timer_pl = INVALID_HANDLE;
		}
	
		char match_config[64];
		GetConVarString(wm_playout_config, match_config, sizeof(match_config));
	
		if (!StrEqual(match_config, ""))
		{
			ServerCommand("exec %s", match_config);
		}
		g_h_stored_timer_pl = CreateTimer(30.0, PlayOutTimeout);
		return Plugin_Handled;
	}
	else if (playout_offer_ct && !playout_offer_t)
	{
		CPrintToChatAll("%t", "PlayOut Offer CT", CHAT_PREFIX);
		g_h_stored_timer_pl = CreateTimer(30.0, PlayOutTimeout);
	}
	else if (!playout_offer_ct && playout_offer_t)
	{
		CPrintToChatAll("%t", "PlayOut Offer T", CHAT_PREFIX);
		g_h_stored_timer_pl = CreateTimer(30.0, PlayOutTimeout);
	}
	return Plugin_Handled;
}

public Action PlayOutTimeout(Handle timer)
{
	g_h_stored_timer_pl = INVALID_HANDLE;
	if (!playout_offer_ct || !playout_offer_t)
	{
		CPrintToChatAll("%t", "PlayOut Offer Not Confirmed", CHAT_PREFIX);
	}
	playout_offer_t = false;
	playout_offer_ct = false;
}

public Action PlayOut(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	char match_config[64];
	GetConVarString(wm_playout_config, match_config, sizeof(match_config));
	
	if (!StrEqual(match_config, ""))
	{
		ServerCommand("exec %s", match_config);
	}
	return Plugin_Handled;
}

public Action OverTime_Offer(int client, int args)
{
	if (client == 0)
	{
		OverTime(client, args);
		return Plugin_Handled;
	}
	
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (GetClientTeam(client) == 2)
	{
		if (overtime_offer_t)
		{
			return Plugin_Handled;
		}
		overtime_offer_t = true;
	}
	else if (GetClientTeam(client) == 3)
	{
		if (overtime_offer_ct)
		{
			return Plugin_Handled;
		}
		overtime_offer_ct = true;
	}
	else
	{
		CPrintToChat(client, "%T", "Non-player", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (overtime_offer_ct && overtime_offer_t)
	{
		if (g_h_stored_timer_ot != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer_ot);
			g_h_stored_timer_ot = INVALID_HANDLE;
		}
	
		char match_config[64];
		GetConVarString(wm_overtime_config, match_config, sizeof(match_config));
	
		if (!StrEqual(match_config, ""))
		{
			ServerCommand("exec %s", match_config);
		}
		g_h_stored_timer_ot = CreateTimer(30.0, OverTimeTimeout);
		return Plugin_Handled;
	}
	else if (overtime_offer_ct && !overtime_offer_t)
	{
		CPrintToChatAll("%t", "OverTime Offer CT", CHAT_PREFIX);
		g_h_stored_timer_ot = CreateTimer(30.0, OverTimeTimeout);
	}
	else if (!overtime_offer_ct && overtime_offer_t)
	{
		CPrintToChatAll("%t", "OverTime Offer T", CHAT_PREFIX);
		g_h_stored_timer_ot = CreateTimer(30.0, OverTimeTimeout);
	}
	return Plugin_Handled;
}

public Action OverTimeTimeout(Handle timer)
{
	g_h_stored_timer_ot = INVALID_HANDLE;
	if (!overtime_offer_ct || !overtime_offer_t)
	{
		CPrintToChatAll("%t", "OverTime Offer Not Confirmed", CHAT_PREFIX);
	}
	overtime_offer_t = false;
	overtime_offer_ct = false;
}

public Action OverTime(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char match_config[64];
	GetConVarString(wm_overtime_config, match_config, sizeof(match_config));
	
	if (!g_live && !StrEqual(match_config, ""))
	{
		ServerCommand("exec %s", match_config);
	}
	return Plugin_Handled;
}

public Action Default_Offer(int client, int args)
{
	if (client == 0)
	{
		Default(client, args);
		return Plugin_Handled;
	}
	
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (GetClientTeam(client) == 2)
	{
		if (default_offer_t)
		{
			return Plugin_Handled;
		}
		default_offer_t = true;
	}
	else if (GetClientTeam(client) == 3)
	{
		if (default_offer_ct)
		{
			return Plugin_Handled;
		}
		default_offer_ct = true;
	}
	else
	{
		CPrintToChat(client, "%T", "Non-player", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (default_offer_ct && default_offer_t)
	{
		if (g_h_stored_timer_def != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer_def);
			g_h_stored_timer_def = INVALID_HANDLE;
		}
	
		char match_config[64];
		GetConVarString(wm_default_config, match_config, sizeof(match_config));
	
		if (!StrEqual(match_config, ""))
		{
			ServerCommand("exec %s", match_config);
		}
		
		g_h_stored_timer_def = CreateTimer(30.0, DefaultTimeout);
		return Plugin_Handled;
	}
	else if (default_offer_ct && !default_offer_t)
	{
		CPrintToChatAll("%t", "Default Offer CT", CHAT_PREFIX);
		g_h_stored_timer_def = CreateTimer(30.0, DefaultTimeout);
	}
	else if (!default_offer_ct && default_offer_t)
	{
		CPrintToChatAll("%t", "Default Offer T", CHAT_PREFIX);
		g_h_stored_timer_def = CreateTimer(30.0, DefaultTimeout);
	}
	return Plugin_Handled;
}

public Action DefaultTimeout(Handle timer)
{
	g_h_stored_timer_def = INVALID_HANDLE;
	if (!default_offer_ct || !default_offer_t)
	{
		CPrintToChatAll("%t", "Default Offer Not Confirmed", CHAT_PREFIX);
	}
	default_offer_t = false;
	default_offer_ct = false;
}

public Action Default(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char match_config[64];
	GetConVarString(wm_default_config, match_config, sizeof(match_config));
	
	if (!g_live && !StrEqual(match_config, ""))
	{
		ServerCommand("exec %s", match_config);
	}
	return Plugin_Handled;
}

public Action ForceStart(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if(!authed)
	{
		CPrintToChat(client, "%T", "Not Authed", client,  CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (StrEqual(g_t_name, "")) {		
		Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);		
	}		
	if (StrEqual(g_ct_name, "")) {		
		Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);		
	}
	ServerCommand("mp_teamname_1 %s", g_ct_name);			
	ServerCommand("mp_teamname_2 %s", g_t_name);
	
	if (!g_t_had_knife && !g_match && GetConVarBool(wm_auto_knife))
	{
		ShowInfo(0, false, false, 1);
		SetAllCancelled(false);
		ReadySystem(false);
		KnifeOn3(0, 0);
		LogAction(client, -1, "\"force_start\" (player \"%L\")", client);
		return Plugin_Handled;
	}

	if (g_restore)
	{
		ShowInfo(0, false, false, 1);
		SetAllCancelled(false);
		ReadySystem(false);
		LiveOn3(true);
		LogAction(client, -1, "\"force_start\" (player \"%L\")", client);
		return Plugin_Handled;
	}
	// reset half and restart
	ResetHalf(true);
	ShowInfo(0, false, false, 1);
	SetAllCancelled(false);
	ReadySystem(false);
	ServerCommand("mp_warmup_end");
	LiveOn3(true);
	
	LogAction(client, -1, "\"force_start\" (player \"%L\")", client);
	
	return Plugin_Handled;
}


public Action ForceEnd(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char event_name[] = "force_end";
	LogSimpleEvent(event_name, sizeof(event_name));
	
	// reset match
	ResetMatch(true, false);
	
	LogAction(client, -1, "\"force_end\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ReadyOn(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (IsLive(client, false))
	{
		return Plugin_Handled;
	}
	
	// reset ready system
	ReadyChangeAll(client, false, true);
	SetAllCancelled(false);
	
	// enable ready system
	ReadySystem(true);
	ShowInfo(client, true, false, 0);
	if (client != 0)
	{
		PrintToConsole(client, "%t", "Ready System Enabled", CHAT_PREFIX);
	}
	else
	{
		PrintToServer("%t", "Ready System Enabled", CHAT_PREFIX);
	}
	CheckReady();
	
	LogAction(client, -1, "\"ready_on\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ReadyOff(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (IsLive(client, false))
	{
		return Plugin_Handled;
	}
	
	// reset ready system
	ReadyChangeAll(client, false, true);
	SetAllCancelled(false);
	
	if (IsReadyEnabled(client, true))
	{
		// disable ready sytem
		ShowInfo(client, false, false, 1);
		ReadySystem(false);
	}
	
	if (client != 0)
	{
		PrintToConsole(client, "%t", "Ready System Disabled", CHAT_PREFIX);
	}
	else
	{
		PrintToServer("%t", "Ready System Disabled", CHAT_PREFIX);
	}
	
	LogAction(client, -1, "\"ready_off\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action ConsoleScore(int client, int args) {
	// display score
	if (g_match) {
		if (g_live) {
			if (client != 0) {
				PrintToConsole(client, "%t", "Match Is Live Server", CHAT_PREFIX);
			} else {
				PrintToServer("%t", "Match Is Live Server", CHAT_PREFIX);
			}
		}
		
		//不需要寫入phrases
		PrintToConsole(client, "[%s] %s [%d] vs [%d] %s - MR%d", CHAT_PREFIX, g_t_name, GetTScore(), GetCTScore(), g_ct_name, (GetConVarInt(mp_maxrounds)/2));
		
		if (g_overtime){
			PrintToConsole(client, "[%s] %t(%d): %s [%d] vs [%d] %s - MR%d", CHAT_PREFIX, "Score Overtime", g_overtime_count + 1, g_t_name, GetTOTScore(), GetCTOTScore(), g_ct_name, (GetConVarInt(mp_overtime_maxrounds)/2));
		}
	} else {
		if (client != 0) {
			PrintToConsole(client, "%t", "Match Not In Progress Server", CHAT_PREFIX);
			CPrintToChat(client, "%T", "Match Not In Progress", client, CHAT_PREFIX);
			if (lt_t_overall_score + lt_ct_overall_score > 0) {
				PrintToConsole(client, "[%s] Previous: %s [%d] vs [%d] %s - Map: %s - MR%d", CHAT_PREFIX, lt_t_name, lt_t_overall_score, lt_ct_overall_score, lt_ct_name, lt_map, lt_max_rounds);
			}
		} else {
			PrintToServer("%t", "Match Not In Progress Server", CHAT_PREFIX);
			CPrintToChat(client, "%T", "Match Not In Progress", client, CHAT_PREFIX);
			if (lt_t_overall_score + lt_ct_overall_score > 0) {
				
				//不需要寫入phrases
				PrintToServer("[%s] Previous: %s [%d] vs [%d] %s - Map: %s - MR%d", CHAT_PREFIX, lt_t_name, lt_t_overall_score, lt_ct_overall_score, lt_ct_name, lt_map, lt_max_rounds);
			}
		}
	}
	
	return Plugin_Handled;
}

// work in progress
public Action SetScoreT(int client, int args)
{
	if (IsAdminCmd(client, false))
	{
		char argstring[16];
		GetCmdArgString(argstring, sizeof(argstring));
		int intToUse;
		
		if (strlen(argstring) < 1)
		{
			CPrintToChat(client, "%T", "Choose a score", client, CHAT_PREFIX);
		}
		else
		{
			if (isNumeric(argstring))
			{
				intToUse = StringToInt(argstring);
			}
			else
			{
				intToUse = -1;
			}
			
			if (g_live)
			{
				if (intToUse > 30 || intToUse < 0)
				{
					CPrintToChat(client, "%T", "Choose a score", client, CHAT_PREFIX);
				}
				else
				{
					if (!GetConVarBool(wm_warmod_safemode))
					{
						CS_SetTeamScore(CS_TEAM_T, intToUse);
						SetTeamScore(CS_TEAM_T, intToUse);
						g_scores[SCORE_T][SCORE_FIRST_HALF] = intToUse;
						CPrintToChatAll("%t", "T score changed to", CHAT_PREFIX, intToUse);
					}
					else
					{
						CPrintToChatAll("%t", "Safe Mode", CHAT_PREFIX);
					}
				}
			}
		}
	}
	return Plugin_Handled;
}

public Action SetScoreCT(int client, int args)
{
	if (IsAdminCmd(client, false))
	{
		char argstring[16];
		GetCmdArgString(argstring, sizeof(argstring));
		int intToUse;
		
		if (strlen(argstring) < 1)
		{
			CPrintToChat(client, "%T", "Choose a score", client, CHAT_PREFIX);
		}
		else
		{
			if (isNumeric(argstring))
			{
				intToUse = StringToInt(argstring);
			}
			else
			{
				intToUse = -1;
			}
			
			if (g_live)
			{
				if (intToUse > 30 || intToUse < 0)
				{
					CPrintToChat(client, "%T", "Choose a score", client, CHAT_PREFIX);
				}
				else
				{
					if (!GetConVarBool(wm_warmod_safemode))
					{
						CS_SetTeamScore(CS_TEAM_CT, intToUse);
						SetTeamScore(CS_TEAM_CT, intToUse);
						g_scores[SCORE_CT][SCORE_FIRST_HALF] = intToUse;
						CPrintToChatAll("%t", "CT score changed to", CHAT_PREFIX, intToUse);
					}
					else
					{
						CPrintToChatAll("%t", "Safe Mode", CHAT_PREFIX);
					}
				}
			}
		}
	}
	return Plugin_Handled;
}

public Action ShowScore(int client, int args) {
	if (!IsActive(client, false)) {
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (client == 0) {
		ConsoleScore(client, args);
		return Plugin_Handled;
	}
	
	if (g_match) {
		// display score
		if (!g_overtime) {
			DisplayScore(client, 0, true);
		} else {
			DisplayScore(client, 1, true);
		}
	} else {
		CPrintToChat(client, "%T", "Match Not In Progress", client, CHAT_PREFIX);
		if (lt_t_overall_score + lt_ct_overall_score > 0) {
			DisplayScore(client, 3, true);
		}
	}
	
	return Plugin_Handled;
}

void DisplayScore(int client, int msgindex, bool priv)
{
	char temp[64];
	GetConVarString(mp_teamname_1, temp, sizeof(temp));
	if (StrEqual(temp, DEFAULT_T_NAME, false) || StrEqual(temp, DEFAULT_CT_NAME, false)) {
		ServerCommand("mp_teamname_1 \"\"");
	}
	
	if (StrEqual(g_ct_name, DEFAULT_T_NAME, false)) {
		Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
		Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), "%s", g_ct_name);
		EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
	}
	
	GetConVarString(mp_teamname_2, temp, sizeof(temp));
	if (StrEqual(temp, DEFAULT_CT_NAME, false) || StrEqual(temp, DEFAULT_T_NAME, false)) {
		ServerCommand("mp_teamname_2 \"\"");
	}
	
	if (StrEqual(g_t_name, DEFAULT_CT_NAME, false)) {
		Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
		Format(g_t_name_escaped, sizeof(g_t_name_escaped), "%s", g_t_name);
		EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
	}

	if (!GetConVarBool(wm_ingame_scores)) {
		return;
	}
	
	if (msgindex == 0) // standard play score
	{
		char score_msg[192];
		GetScoreMsg(client, score_msg, sizeof(score_msg), GetTScore(), GetCTScore());
		if (priv) {
			CPrintToChat(client, "%T", "Play Score", client, CHAT_PREFIX, score_msg);
		} else {
			CPrintToChatAll("%t", "Play Score", CHAT_PREFIX, score_msg);
		}
	}
	else if (msgindex == 1) // overtime play score
	{
		char score_msg[192];
		GetScoreMsg(client, score_msg, sizeof(score_msg), GetTOTScore(), GetCTOTScore());
		if (priv) {
			CPrintToChat(client, "%T", "Score Overtime", client, CHAT_PREFIX, score_msg);
		} else {
			CPrintToChatAll("%t", "Score Overtime", CHAT_PREFIX, score_msg);
		}
	}
	else if (msgindex == 2) // overall play score
	{
		char score_msg[192];
		GetScoreMsg(client, score_msg, sizeof(score_msg), GetTTotalScore(), GetCTTotalScore());
		if (priv) {
			CPrintToChat(client, "%T", "Score Overall", client, CHAT_PREFIX, score_msg);
		} else {
			CPrintToChatAll("%t", "Score Overall", CHAT_PREFIX, score_msg);
		}
	}
	else if (msgindex == 3) // overall play score
	{
		char score_msg[192];
		Format(score_msg, sizeof(score_msg), "%t", "Score Overall 2", CHAT_PREFIX, lt_t_name, lt_t_overall_score, lt_ct_overall_score, lt_ct_name);
		if (priv) {
			CPrintToChat(client, score_msg);
		} else {
			CPrintToChatAll(score_msg);
		}
	}
}

public void GetScoreMsg(int client, char[] result, int maxlen, int t_score, int ct_score) {
	SetGlobalTransTarget(client);
	if (StrEqual(g_t_name, "")) {
		Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
	}
	if (StrEqual(g_ct_name, "")) {
		Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
	}
	if (t_score > ct_score) {
		Format(result, maxlen, "%t", "T Winning", g_t_name, t_score, ct_score);
	} else if (t_score == ct_score) {
		Format(result, maxlen, "%t", "Tied", t_score, ct_score);
	} else {
		Format(result, maxlen, "%t", "CT Winning", g_ct_name, ct_score, t_score);
	}
}

public Action ReadyInfoPriv(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsReadyEnabled(client, false))
	{
		return Plugin_Handled;
	}
	
	if (client != 0 && !g_live)
	{
		g_cancel_list[client] = false;
		ShowInfo(client, true, true, 0);
	}
	return Plugin_Handled;
}

public Action Event_Round_Start(Handle event, const char[]name, bool dontBroadcast)
{
	Event_Round_Start_CMD();
	
	if (!IsActive(0, true))
	{
		return;
	}
	
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsClientInGame(i))
		{
			WasClientInGame[i] = true;
			WasClientInGameAtAll[i] = true;
			
			if (GetClientTeam(i) > 1)
			{
				WasClientTeam[i] = GetClientTeam(i);
				FreezeTimeSpawn(i);
				Hasdamagereport[i] = false;
			}
			GetClientName(i, match_client_name[i], sizeof(match_client_name[]));
			GetClientAuthId(i, AuthId_Steam2, match_client_steam2[i], sizeof(match_client_steam2[]));
			SteamIDToCommunityID(match_client_steam2[i], match_client_steam64[i], sizeof(match_client_steam64[]));
	
			round_kills[i] = 0;
			round_health[i] = 100;
			
			match_stats[i][MATCH_ROUND]++;
		}
	}
	ResetClutchStats();
//	Round_Start_Player_Names();
}

public void Event_Round_Start_CMD()
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	LiveText();
	
	FreezeTime = true;
	
	//Pause command fire on round end May change to on round start
	if (g_pause_freezetime == true)
	{
		g_pause_freezetime = false;
		CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
		if (GetConVarBool(wm_auto_unpause))
		{
			CPrintToChatAll("%t", "Unpause Timer", CHAT_PREFIX, GetConVarInt(wm_auto_unpause_delay));
			g_h_stored_timer = CreateTimer(GetConVarFloat(wm_auto_unpause_delay), UnPauseTimer);
		}
		
		ServerCommand("mp_pause_match 1");
	}
	
	if (g_auto_pause)
	{
		g_auto_pause = false;
		
		if (GetTeamClientCount(CS_TEAM_CT) < (GetConVarInt(wm_max_players)/2))
		{
			char name[64];
			if (StrEqual(g_ct_name, ""))
			{
				Format(name, sizeof(name), DEFAULT_CT_NAME);
			}
			else
			{
				Format(name, sizeof(name), g_ct_name);
			}
			CPrintToChatAll("%t", "Auto Unpause Notice CT", CHAT_PREFIX, name);
			CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
			ServerCommand("mp_pause_match 1");
		}
		else if (GetTeamClientCount(CS_TEAM_T) < (GetConVarInt(wm_max_players)/2))
		{
			char name[64];
			if (StrEqual(g_t_name, ""))
			{
				Format(name, sizeof(name), DEFAULT_T_NAME);
			}
			else
			{
				Format(name, sizeof(name), g_t_name);
			}
			CPrintToChatAll("%t", "Auto Unpause Notice T", CHAT_PREFIX, name);
			CPrintToChatAll("%t", "Unpause Notice", CHAT_PREFIX);
			ServerCommand("mp_pause_match 1");
		}
	}
	
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_round_start\", \"freezeTime\": %d}", GetConVarInt(FindConVar("mp_freezetime")));
	}
	else
	{
		LogEvent("{\"event\": \"round_start\", \"freezeTime\": %d}", GetConVarInt(FindConVar("mp_freezetime")));
		LogMoney();
	}
	
	if (g_second_half_first)
	{
		LiveOn3Override();
		g_second_half_first = false;
	}
	
	g_planted = false;
	
	ResetClutchStats();
	
	if (!g_t_score)
	{
		g_t_score = true;
	}
	
	if (g_t_knife)
	{
		// give player specified grenades
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsClientInGame(i) && GetClientTeam(i) > 1)
			{
				SetEntData(i, g_iAccount, 0);
				CS_StripButKnife(i);
				if (GetConVarBool(wm_knife_hegrenade))
				{
					GivePlayerItem(i, "weapon_hegrenade");
				}
				if (GetConVarInt(wm_knife_flashbang) >= 1)
				{
					GivePlayerItem(i, "weapon_flashbang");
					if (GetConVarInt(wm_knife_flashbang) >= 2)
					{
						GivePlayerItem(i, "weapon_flashbang");
					}
				}
				if (GetConVarBool(wm_knife_smokegrenade))
				{
					GivePlayerItem(i, "weapon_smokegrenade");
				}
				if (GetConVarBool(wm_knife_zeus))
				{
					GivePlayerItem(i, "weapon_taser");
				}
				if (GetConVarBool(wm_knife_armor))
				{
					SetEntProp(i, Prop_Send, "m_ArmorValue", 100);
					if (GetConVarBool(wm_knife_helmet))
					{
						SetEntProp(i, Prop_Send, "m_bHasHelmet", 1);
					}
				}
			}
		}
	}
	
	if (g_t_money == true && GetConVarBool(wm_round_money))
	{
		int the_money[MAXPLAYERS + 1];
		int num_players;
		
		// sort by money
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) > 1)
			{
				the_money[num_players] = i;
				num_players++;
			}
		}
		
		SortCustom1D(the_money, num_players, SortMoney);
		
		char player_name[64];
		char player_money[10];
		char has_weapon[1];
		int pri_weapon;
		
		// display team players money
		for (int i = 1; i <= MaxClients; i++)
		{
			for (int x = 0; x < num_players; x++)
			{
				GetClientName(the_money[x], player_name, sizeof(player_name));
				if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) == GetClientTeam(the_money[x]))
				{
					pri_weapon = GetPlayerWeaponSlot(the_money[x], 0);
					if (pri_weapon == -1)
					{
						has_weapon = ">";
					}
					else
					{
						has_weapon = "\0";
					}
					IntToMoney(GetEntData(the_money[x], g_iAccount), player_money, sizeof(player_money));
					CPrintToChat(i, "%T", "Player", i, CHAT_PREFIX, player_money, has_weapon, player_name);
				}
			}
		}
	}
}

public Action AskTeamMoney(int client, int args)
{
	// show team money
	ShowTeamMoney(client);
	return Plugin_Handled;
}

stock void ShowTeamMoney(int client)
{
	
	if (client == 0)
	{
		return;
	}
	
	// show team money
	int the_money[MAXPLAYERS + 1];
	int num_players;
	
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) > 1)
		{
			the_money[num_players] = i;
			num_players++;
		}
	}
	
	SortCustom1D(the_money, num_players, SortMoney);
	
	char player_name[64];
	char player_money[10];
	char has_weapon[1];
	int pri_weapon;
	
	CPrintToChat(client, "%T" , "--------", client, CHAT_PREFIX);
	for (int x = 0; x < num_players; x++)
	{
		GetClientName(the_money[x], player_name, sizeof(player_name));
		if (IsClientInGame(client) && !IsFakeClient(client) && GetClientTeam(client) == GetClientTeam(the_money[x]))
		{
			pri_weapon = GetPlayerWeaponSlot(the_money[x], 0);
			if (pri_weapon == -1)
			{
				has_weapon = ">";
			}
			else
			{
				has_weapon = "\0";
			}
			IntToMoney(GetEntData(the_money[x], g_iAccount), player_money, sizeof(player_money));
			CPrintToChat(client, "%T", "Player", client, CHAT_PREFIX, player_money, has_weapon, player_name);
		}
	}
}

stock void GetCurrentWorkshopMap(char[] g_MapName, int iMapBuf, char[] g_WorkShopID, int iWorkShopBuf)
{
	char g_CurMap[128];
	char g_CurMapSplit[2][64];
	
	GetCurrentMap(g_CurMap, sizeof(g_CurMap));
	
	ReplaceString(g_CurMap, sizeof(g_CurMap), "workshop/", "", false);
	
	ExplodeString(g_CurMap, "/", g_CurMapSplit, 2, 64);
	
	strcopy(g_WorkShopID, iWorkShopBuf, g_CurMapSplit[0]);
	strcopy(g_MapName, iMapBuf, g_CurMapSplit[1]);
	strcopy(g_map, iMapBuf, g_CurMapSplit[1]);
}

public Action Event_Round_End(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int winner = GetEventInt(event, "winner");
	
	if (winner > 1 && g_t_score)
	{
		if (g_t_knife)
		{
			if (g_t_veto)
			{
				g_t_veto = false;
				g_t_knife = false;
				ServerCommand("mp_pause_match 1");
				SetCaptains();
				CreateMapVeto(winner);
			}
			else
			{
				if (winner == 2)
				{
					CPrintToChatAll("%t", "Knife Vote Team T", CHAT_PREFIX, g_t_name);
				}
				else
				{
					CPrintToChatAll("%t", "Knife Vote Team CT", CHAT_PREFIX, g_ct_name);
				}
				
				CPrintToChatAll("%t", "Knife Vote", CHAT_PREFIX);
				g_knife_winner = GetEventInt(event, "winner");
				g_knife_vote = true;
				g_t_knife = false;
				g_t_had_knife = true;
				ServerCommand("mp_pause_match 1");
				UpdateStatus();
			}
		}
		
		if (!g_live)	return;
		
		if (!g_t_money)	g_t_money = true;
		
		AddScore(winner);
		CheckScores();
		UpdateStatus();
		Call_StartForward(g_f_on_round_end);
		Call_PushString(g_ct_name);
		Call_PushCell(GetCTTotalScore());
		Call_PushCell(GetTTotalScore());
		Call_PushString(g_t_name);
		Call_Finish();
		
		//If damage report enable
		if (wm_damageprint_enabled.IntValue == 1) 
		{
			for (int i = 1; i <= MaxClients; i++) 
			{
				//is valid client & client is not spec (spec=1, TR=2, CT=3)
				if (IsValidClient(i) && !IsFakeClient(i) && GetClientTeam(i) > 1) 
				{
					PrintDamageInfo(i);
				}
			}
		}
	}
	
	// stats
	for (int j = 1; j <= MaxClients; j++)
	{
		if(IsValidClient(j) && !IsFakeClient(j))
		{
			if (IsClientInGame(j) && GetClientTeam(j) == winner && clutch_stats[j][CLUTCH_LAST] == 1)
			{
				clutch_stats[j][CLUTCH_WON] = 1;
				match_stats[j][MATCH_WON]++;
			}
				
			LogClutchStats(j);
				
			if (g_live && GameRules_GetProp("m_bFreezePeriod") == 0 && (GetTTotalScore() + GetCTTotalScore()) != 0) 
			{		
				/* RWS */
				int team = GetClientTeam(j);
				if (team == CS_TEAM_CT || team == CS_TEAM_T)	RWSUpdate(j, team == winner);
				i_RoundPoints[j] = 0;
				
				LogPlayerStats(j);
			}
		}
	}
	
	if(match_id > 0)	SaveStats(-1);
	
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_round_end\", \"round\": %i, \"winner\": %d, \"reason\": %d}", g_round, winner, GetEventInt(event, "reason"));
	}
	else
	{
		LogEvent("{\"event\": \"round_end\", \"round\": %i, \"winner\": %d, \"reason\": %d}", g_round, winner, GetEventInt(event, "reason"));
	}
}

public void Event_Round_Restart(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	if (!StrEqual(newVal, "0"))
	{
		for (int i = 1; i <= MaxClients; i++)
		{
			ResetPlayerStats(i);
			round_kills[i] = 0;
			round_health[i] = 100;
			for (int z = 0; z < CLUTCH_NUM; z++)
			{
				clutch_stats[i][z] = 0;
			}
			for (int y = 0; y < MATCH_NUM; y++)
			{
				match_stats[i][y] = 0;	
			}
			Format(match_client_name[i], sizeof(match_client_name[]), "");
			Format(match_client_steam2[i], sizeof(match_client_steam2[]), "");
			Format(match_client_steam64[i], sizeof(match_client_steam64[]), "");
		}
		LogEvent("{\"event\": \"round_restart\", \"delay\": %d}", StringToInt(newVal));
	}
}

public Action Event_Round_Freeze_End(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	FreezeTime = false;
	
	// stats
	if (g_t_knife)
	{
		char event_name[] = "knife_round_freeze_end";
		LogSimpleEvent(event_name, sizeof(event_name));
	}
	else
	{
		char event_name[] = "round_freeze_end";
		LogSimpleEvent(event_name, sizeof(event_name));
	}
}

public Action Event_Player_Blind(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		char log_string[384];
		CS_GetAdvLogString(client, log_string, sizeof(log_string));
		LogEvent("{\"event\": \"player_blind\", \"round\": %i, \"player\": %s, \"duration\": %.2f}", g_round, log_string, GetEntPropFloat(client, Prop_Send, "m_flFlashDuration"));
	}
}

public Action Event_Player_Hurt(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	int victim = GetClientOfUserId(GetEventInt(event, "userid"));
	
	Hasdamagereport[attacker] = true;
	Hasdamagereport[victim] = true;
	
	// stats
	int damage = GetEventInt(event, "dmg_health");
	int damage_armor = GetEventInt(event, "dmg_armor");
	int hitgroup = GetEventInt(event, "hitgroup");
	int vHealth = GetEventInt(event, "health");
	
	char weapon[64];
	GetEventString(event, "weapon", weapon, sizeof(weapon));

	if (StrContains(weapon, "knife") != -1 ||
			StrContains(weapon, "bayonet") != -1 ||
			StrEqual(weapon, "melee") || 
			StrEqual(weapon, "axe") || 
			StrEqual(weapon, "hammer") || 
			StrEqual(weapon, "spanner") || 
			StrEqual(weapon, "fists")
			) weapon = "knife";
	
	if (attacker > 0 && victim > 0 && attacker != victim)
	{
		char fullweapon[64];
		Format(fullweapon, sizeof(fullweapon), "weapon_%s", weapon);
		int weapon_index = GetWeaponIndex(fullweapon);
		GetClientWeapon(victim, last_weapon[victim], 64);
		char attacker_log_string[384];
		char victim_log_string[384];
		CS_GetAdvLogString(attacker, attacker_log_string, sizeof(attacker_log_string));
		CS_GetAdvLogString(victim, victim_log_string, sizeof(victim_log_string));
		EscapeString(weapon, sizeof(weapon));
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_player_hurt\", \"round\": %i, \"attacker\": %s, \"victim\": %s, \"weapon\": \"%s\", \"damage\": %d, \"damageArmor\": %d, \"hitGroup\": %d}", g_round, attacker_log_string, victim_log_string, weapon, damage, damage_armor, hitgroup);
		}
		else
		{
			LogEvent("{\"event\": \"player_hurt\", \"round\": %i, \"attacker\": %s, \"victim\": %s, \"weapon\": \"%s\", \"damage\": %d, \"damageArmor\": %d, \"hitGroup\": %d}", g_round, attacker_log_string, victim_log_string, weapon, damage, damage_armor, hitgroup);
		}
			
		if (vHealth == 0) 
		{
			g_DamageDone[attacker][victim] += round_health[victim];
		} else {
			g_DamageDone[attacker][victim] += damage;
		}
		g_DamageDoneHits[attacker][victim]++;
			
		if (weapon_index > -1)
		{
			if (GetClientTeam(attacker) == GetClientTeam(victim)) 
			{
				weapon_stats[attacker][weapon_index][LOG_HIT_TEAM_HITS]++;
				if (vHealth == 0) {
					weapon_stats[attacker][weapon_index][LOG_HIT_TEAM_DAMAGE] += round_health[victim];
				} else {
					weapon_stats[attacker][weapon_index][LOG_HIT_TEAM_DAMAGE] += damage;
				}
			} 
			else 
			{
				weapon_stats[attacker][weapon_index][LOG_HIT_HITS]++;
				
				if (StrContains(weapon, "knife") == -1 && 
				StrContains(weapon, "bayonet") == -1 && 
				!StrEqual(weapon, "weapon_melee") && 
				!StrEqual(weapon, "weapon_axe") && 
				!StrEqual(weapon, "weapon_hammer") && 
				!StrEqual(weapon, "weapon_spanner") && 
				!StrEqual(weapon, "weapon_fists") && 
				!StrEqual(weapon, "weapon_hegrenade") && 
				!StrEqual(weapon, "weapon_flashbang") && 
				!StrEqual(weapon, "weapon_smokegrenade") && 
				!StrEqual(weapon, "weapon_inferno") && 
				!StrEqual(weapon, "weapon_molotov") && 
				!StrEqual(weapon, "weapon_incgrenade") &&
				!StrEqual(weapon, "weapon_decoy"))
				{
					match_stats[attacker][MATCH_HITS]++;
				}
					
				if (vHealth == 0) 
				{
					weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += round_health[victim];
					match_stats[attacker][MATCH_DAMAGE] += round_health[victim];
					i_RoundPoints[attacker] += round_health[victim];
				} 
				else 
				{
					weapon_stats[attacker][weapon_index][LOG_HIT_DAMAGE] += damage;
					match_stats[attacker][MATCH_DAMAGE] += damage;
					i_RoundPoints[attacker] += damage;
				}
			}
			
			if(hitgroup == 8) hitgroup = 1;
			
			if (hitgroup < 8)
			{
				weapon_stats[attacker][weapon_index][hitgroup + LOG_HIT_OFFSET]++;
				hitbox[attacker][hitgroup]++;
			}
		}
	}
	round_health[victim] = vHealth;
}

public Action Event_Player_Death(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int attacker = GetClientOfUserId(GetEventInt(event, "attacker"));
	int assister = GetClientOfUserId(GetEventInt(event, "assister"));
	int victim = GetClientOfUserId(GetEventInt(event, "userid"));
	bool headshot = GetEventBool(event, "headshot");
	char weapontemp[64], weapon[64];
	GetEventString(event, "weapon", weapontemp, sizeof(weapontemp));

	if (StrContains(weapontemp, "knife") != -1 ||  
			StrEqual(weapontemp, "bayonet") ||
			StrEqual(weapontemp, "melee") || 
			StrEqual(weapontemp, "axe") || 
			StrEqual(weapontemp, "hammer") || 
			StrEqual(weapontemp, "spanner") ||
			StrEqual(weapontemp, "fists"))		weapontemp = "knife";
		
	Format(weapon, sizeof(weapon), "weapon_%s", weapontemp);
		
	int victim_team = GetClientTeam(victim);
	
	// stats
	if (attacker > 0 && victim > 0 && attacker != victim)
	{
		// normal frag
		char attacker_log_string[384];
		char assister_log_string[384];
		char victim_log_string[384];
		CS_GetAdvLogString(attacker, attacker_log_string, sizeof(attacker_log_string));
		CS_GetAdvLogString(assister, assister_log_string, sizeof(assister_log_string));
		CS_GetAdvLogString(victim, victim_log_string, sizeof(victim_log_string));
		EscapeString(weapon, sizeof(weapon));
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_player_death\", \"round\": %i, \"attacker\": %s, \"assister\": %s, \"victim\": %s, \"weapon\": \"%s\", \"headshot\": %d}", g_round, attacker_log_string, assister_log_string, victim_log_string, weapon, headshot);
		}
		else
		{
			LogEvent("{\"event\": \"player_death\", \"round\": %i, \"attacker\": %s, \"assister\": %s, \"victim\": %s, \"weapon\": \"%s\", \"headshot\": %d}", g_round, attacker_log_string, assister_log_string, victim_log_string, weapon, headshot);
		}
		
		/* Stats Rewrite */
		if (GetClientTeam(attacker) == GetClientTeam(victim))
		{
			match_stats[attacker][MATCH_TEAMKILLS]++;
		}
		else
		{
			match_stats[attacker][MATCH_KILLS]++;
			round_kills[attacker]++;
			i_RoundPoints[attacker] += 100;
		}
		match_stats[victim][MATCH_DEATHS]++;
		
		if (headshot)
		{
			match_stats[attacker][MATCH_HEADSHOTS]++;
		}
	}
	else if ((victim > 0 && victim == attacker) || StrEqual(weapontemp, "worldspawn"))
	{
		// suicide
		char log_string[384];
		char assister_log_string[384];
		CS_GetAdvLogString(assister, assister_log_string, sizeof(assister_log_string));
		CS_GetAdvLogString(victim, log_string, sizeof(log_string));
		ReplaceString(weapon, sizeof(weapon), "worldspawn", "world");
		EscapeString(weapon, sizeof(weapon));
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_player_suicide\", \"round\": %i, \"player\": %s, \"assister\": %s, \"weapon\": \"%s\"}", g_round, log_string, assister_log_string, weapon);
		}
		else
		{
			LogEvent("{\"event\": \"player_suicide\", \"round\": %i, \"player\": %s, \"assister\": %s, \"weapon\": \"%s\"}", g_round, log_string, assister_log_string, weapon);
		}
		
		match_stats[victim][MATCH_DEATHS]++;
	}
	if (victim > 0)
	{
		// record weapon stats
		int weapon_index = GetWeaponIndex(weapon);
		if (attacker > 0)
		{
			int attacker_team = GetClientTeam(attacker);
			if (weapon_index > -1)
			{
				if (headshot == true)
				{
					weapon_stats[attacker][weapon_index][LOG_HIT_HEADSHOTS]++;
				}
				
				if (attacker_team == victim_team) 
				{
					weapon_stats[attacker][weapon_index][LOG_HIT_TEAMKILLS]++;
				} 
				else 
				{
					weapon_stats[attacker][weapon_index][LOG_HIT_KILLS]++;
					weapon_kills[attacker][weapon_index]++;
				}
			}
			int victim_num_alive = GetNumAlive(victim_team);
			int attacker_num_alive = GetNumAlive(attacker_team);
			if (victim_num_alive == 0)
			{
				clutch_stats[victim][CLUTCH_LAST] = 1;
				if (clutch_stats[victim][CLUTCH_VERSUS] == 0)
				{
					clutch_stats[victim][CLUTCH_VERSUS] = attacker_num_alive;
				}
				
			}
			if (attacker_num_alive == 1)
			{
				if (attacker_team != victim_team)
				{
					clutch_stats[attacker][CLUTCH_FRAGS]++;
					if (clutch_stats[attacker][CLUTCH_LAST] == 0)
					{
						clutch_stats[attacker][CLUTCH_VERSUS] = victim_num_alive + 1;
					}
					clutch_stats[attacker][CLUTCH_LAST] = 1;
				}
			}
			g_GotKill[attacker][victim] = true;
		}
		
		int victim_weapon_index = GetWeaponIndex(last_weapon[victim]);
		if (victim_weapon_index > -1)
		{
			weapon_stats[victim][victim_weapon_index][LOG_HIT_DEATHS]++;
		}
	}
	if (assister > 0)
	{
		if (GetClientTeam(assister) == GetClientTeam(victim))
		{
			match_stats[assister][MATCH_ATA]++;
			assist_stats[assister][ASSIST_COUNT_TK]++;
		}
		else
		{
			match_stats[assister][MATCH_ASSIST]++;
			assist_stats[assister][ASSIST_COUNT]++;
		}
	}
	
	if (!g_match && !g_t_knife && GetConVarBool(wm_warmup_respawn))
	{
		// respawn if warmup
		CreateTimer(0.1, RespawnPlayer, victim);
	}
}

public Action Event_Player_Name(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	// stats
	char log_string[384];
	CS_GetLogString(client, log_string, sizeof(log_string));
	char newName[64];
	GetEventString(event, "newname", newName, sizeof(newName));
	EscapeString(newName, sizeof(newName));
	if (g_t_knife)
	{
	LogEvent("{\"event\": \"knife_player_name\", \"player\": %s, \"newName\": \"%s\"}", log_string, newName);
	}
	else
	{
	LogEvent("{\"event\": \"player_name\", \"player\": %s, \"newName\": \"%s\"}", log_string, newName);
	}

	if (g_ready_enabled && !g_live)
	{
		CreateTimer(0.1, UpdateInfo);
	}
}

public Action Event_Player_Disc_Pre(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	char reason[128];
	GetEventString(event, "reason", reason, sizeof(reason));
	
	g_clanTagsChecked[client] = false;
	
	if ((g_match || g_t_knife) && GetConVarBool(wm_ban_on_disconnect) && StrEqual(reason, "Disconnect") && GetClientTeam(client) > 1)
	{
		float percent = GetConVarFloat(wm_ban_percentage);
		int count = CS_GetPlayerListCount();
		if (count > (GetConVarInt(wm_max_players) * percent))
		{
			g_disconnect[client] = true; 
		}
	}
	
	// stats
	char log_string[384];
	CS_GetLogString(client, log_string, sizeof(log_string));
	EscapeString(reason, sizeof(reason));
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_player_disconnect\", \"round\": %i, \"player\": %s, \"reason\": \"%s\"}", g_round, log_string, reason);
	}
	else
	{
		LogEvent("{\"event\": \"player_disconnect\", \"round\": %i, \"player\": %s, \"reason\": \"%s\"}", g_round, log_string, reason);
	}
}

public Action Event_Player_Team_Post (Handle event, const char[]name, bool dontBroadcast) {
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	FreezeTimeSpawn(client);
}

public void FreezeTimeSpawn(int client) {
	int team = GetClientTeam(client);
	
	if (g_live && GameRules_GetProp("m_bFreezePeriod") == 1 && !IsPlayerAlive(client) && (team == CS_TEAM_CT || team == CS_TEAM_T)) {
		CS_RespawnPlayer(client);
	}
}

public Action Event_Player_Team(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	int old_team = GetEventInt(event, "oldteam");
	int new_team = GetEventInt(event, "team");
	
	// stats
	char log_string[384];
	CS_GetLogString(client, log_string, sizeof(log_string));
	LogEvent("{\"event\": \"player_team\", \"round\": %i, \"player\": %s, \"oldTeam\": %d, \"newTeam\": %d}", g_round, log_string, old_team, new_team);
	
	if (old_team < 2)
	{
		// came from spec/joining server
		CreateTimer(4.0, ShowPluginInfo, client);
		if (!g_live && g_ready_enabled && !GetEventBool(event, "disconnect") && !IsFakeClient(client))
		{
			CreateTimer(4.0, UpdateInfo);
		}
	}
	
	if (old_team == 0)
	{
		// joining server
		CreateTimer(2.0, HelpText, client, TIMER_FLAG_NO_MAPCHANGE);
	}
	
	if (!g_live && g_ready_enabled && !GetEventBool(event, "disconnect") && !IsFakeClient(client))
	{
		// show ready system if applicable
		if (new_team != CS_TEAM_SPECTATOR)
		{
			if (g_player_list[client] == PLAYER_READY)
			{
				ReadyServ(client, false, false, true, false);
			}
			else
			{
				ReadyServ(client, false, true, true, false);
			}
		}
		else
		{
			g_player_list[client] = PLAYER_DISC;
			ShowInfo(client, true, false, 0);
		}
	}
	
	if (new_team > 1 && !g_match && !g_t_knife && GetConVarBool(wm_warmup_respawn))
	{
		// spawn player if warmup
		CreateTimer(0.1, RespawnPlayer, client);
	}
}

public Action Event_Player_Spawned(Handle event, const char[] name, bool dontBroadcast)
{
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	if (g_t_knife)
	{
		// give player specified grenades
		if (IsClientInGame(client) && GetClientTeam(client) > 1)
		{
			SetEntData(client, g_iAccount, 0);
			CS_StripButKnife(client);
			if (GetConVarBool(wm_knife_hegrenade))
			{
				GivePlayerItem(client, "weapon_hegrenade");
			}
			if (GetConVarInt(wm_knife_flashbang) >= 1)
			{
				GivePlayerItem(client, "weapon_flashbang");
				if (GetConVarInt(wm_knife_flashbang) >= 2)
				{
					GivePlayerItem(client, "weapon_flashbang");
				}
			}
			if (GetConVarBool(wm_knife_smokegrenade))
			{
				GivePlayerItem(client, "weapon_smokegrenade");
			}
			if (GetConVarBool(wm_knife_zeus))
			{
				GivePlayerItem(client, "weapon_taser");
			}
			if (GetConVarBool(wm_knife_armor))
			{
				SetEntProp(client, Prop_Send, "m_ArmorValue", 100);
				if (GetConVarBool(wm_knife_helmet))
				{
					SetEntProp(client, Prop_Send, "m_bHasHelmet", 1);
				}
			}
		}
	}
	
	if (!IsActive(0, true))
	{
		return;
	}
	
	/* Stats Rewrite */
	WasClientInGame[client] = true;
	WasClientInGameAtAll[client] = true;
	WasClientTeam[client] = GetClientTeam(client);
	GetClientName(client, match_client_name[client], sizeof(match_client_name[]));
	GetClientAuthId(client, AuthId_Steam2, match_client_steam2[client], sizeof(match_client_steam2[]));
	SteamIDToCommunityID(match_client_steam2[client], match_client_steam64[client], sizeof(match_client_steam64[]));
}

public Action Event_Bomb_PickUp(Handle event, const char[] name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	if (g_t_knife)
	{
	LogEvent("{\"event\": \"knife_bomb_pickup\", \"round\": %i, \"player\": %s}", g_round, log_string);
	}
	else
	{
	LogEvent("{\"event\": \"bomb_pickup\", \"round\": %i, \"player\": %s}", g_round, log_string);
	}
}

public Action Event_Bomb_Dropped(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_bomb_dropped\", \"round\": %i, \"player\": %s}", g_round, log_string);
	}
	else
	{
		LogEvent("{\"event\": \"bomb_dropped\", \"round\": %i, \"player\": %s}", g_round, log_string);
	}
}

public Action Event_Bomb_Plant_Begin(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_plant_begin\", \"round\": %i, \"player\": %s, \"site\": %d}", g_round, log_string, GetEventInt(event, "site"));
}

public Action Event_Bomb_Plant_Abort(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_plant_abort\", \"round\": %i, \"player\": %s, \"site\": %d}", g_round, log_string, GetEventInt(event, "site"));
}

public Action Event_Bomb_Planted(Handle event, const char[]name, bool dontBroadcast)
{
	g_planted = true;
	
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_planted\", \"round\": %i, \"player\": %s, \"site\": %d}", g_round, log_string, GetEventInt(event, "site"));
		
	/* RWS */
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	i_RoundPoints[client] += 50;
	
	match_stats[client][MATCH_PLANT]++;
}

public Action Event_Bomb_Defuse_Begin(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(client, log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_defuse_begin\", \"round\": %i, \"player\": %s, \"kit\": %d}", g_round, log_string, GetEventInt(event, "site"), GetEventBool(event, "haskit"));
}

public Action Event_Bomb_Defuse_Abort(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_defuse_abort\", \"round\": %i, \"player\": %s}", g_round, log_string);
}

public Action Event_Bomb_Defused(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(client, log_string, sizeof(log_string));
	LogEvent("{\"event\": \"bomb_defused\", \"round\": %i, \"player\": %s, \"site\": %d}", g_round, log_string, GetEventInt(event, "site"));
	
	match_stats[client][MATCH_DEFUSE]++;
		
	/* RWS */
	i_RoundPoints[client] += 50;
}

public Action Event_Bomb_Exploded(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	match_stats[client][MATCH_EXPLODE]++;
}

public Action Event_Hostage_Rescued(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	match_stats[client][MATCH_HOSTAGE]++;
}

public Action Event_Weapon_Fire(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		char  weapon[64];
		GetEventString(event, "weapon", weapon, sizeof(weapon));
		if (StrEqual(weapon, "weapon_m4a1"))
		{
			int iWeapon = GetPlayerWeaponSlot(client, CS_SLOT_PRIMARY);
			int pWeapon = GetEntProp(iWeapon, Prop_Send, "m_iItemDefinitionIndex");
			if (pWeapon == 60)
			{
				weapon = "weapon_m4a1_silencer";
			}
		}
		else if (StrEqual(weapon, "weapon_hkp2000") || StrEqual(weapon, "weapon_p250") || StrEqual(weapon, "weapon_fiveseven") || StrEqual(weapon, "weapon_tec9") || StrEqual(weapon, "weapon_deagle"))
		{
			int iWeapon = GetPlayerWeaponSlot(client, CS_SLOT_SECONDARY);
			int pWeapon = GetEntProp(iWeapon, Prop_Send, "m_iItemDefinitionIndex");
			if (pWeapon == 61)
			{
				weapon = "weapon_usp_silencer";
			}
			else if (pWeapon == 63)
			{
				weapon = "weapon_cz75a";
			}
			else if (pWeapon == 64)
			{
				weapon = "weapon_revolver";
			}
		} 
		else if (StrContains(weapon, "knife") != -1 ||  
			StrEqual(weapon, "bayonet") ||
			StrEqual(weapon, "melee") || 
			StrEqual(weapon, "axe") || 
			StrEqual(weapon, "hammer") || 
			StrEqual(weapon, "spanner") ||
			StrEqual(weapon, "fists"))
				weapon = "weapon_knife";
		
		int weapon_index = GetWeaponIndex(weapon);
		if (weapon_index > -1)
		{
			if (StrEqual(weapon, "weapon_knife") || StrEqual(weapon, "weapon_hegrenade") || StrEqual(weapon, "weapon_flashbang") || StrEqual(weapon, "weapon_smokegrenade") || StrEqual(weapon, "weapon_inferno") || StrEqual(weapon, "weapon_molotov") || StrEqual(weapon, "weapon_incgrenade") || StrEqual(weapon, "weapon_decoy"))
			return; // Don't count knife being used neither hegrenade, flashbang and smokegrenade being threw
			
			// check is burst mode or not
			int entity = GetEntPropEnt(client, Prop_Send, "m_hActiveWeapon"); 
			if(GetEntProp(entity, Prop_Send, "m_bBurstMode") == 1)
			{
				int ammo = GetEntProp(entity, Prop_Send, "m_iClip1");
				if(ammo >= 3)
				{
					weapon_stats[client][weapon_index][LOG_HIT_SHOTS]+=3;
					match_stats[client][MATCH_SHOTS]+=3;
				}
				else
				{
					weapon_stats[client][weapon_index][LOG_HIT_SHOTS]+=ammo;
					match_stats[client][MATCH_SHOTS]+=ammo;
				}
			}
			else
			{
				weapon_stats[client][weapon_index][LOG_HIT_SHOTS]++;
				match_stats[client][MATCH_SHOTS]++;
			}
		}
	}
}

public Action Event_Detonate_Flash(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"grenade_detonate\", \"round\": %i, \"player\": %s, \"grenade\": \"flashbang\"}", g_round, log_string);
}

public Action Event_Detonate_Smoke(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats	
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"grenade_detonate\", \"round\": %i, \"player\": %s, \"grenade\": \"smokegrenade\"}", g_round, log_string);
}

public Action Event_Detonate_HeGrenade(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"grenade_detonate\", \"round\": %i, \"player\": %s, \"grenade\": \"hegrenade\"}", g_round, log_string);
}

public Action Event_Detonate_Molotov(Handle event, char[] name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"grenade_detonate\", \"round\": %i, \"player\": %s, \"grenade\": \"molotov\"}", g_round, log_string);
}

public Action Event_Detonate_Decoy(Handle event, char[] name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	LogEvent("{\"event\": \"grenade_detonate\", \"round\": %i, \"player\": %s, \"grenade\": \"decoy\"}", g_round, log_string);
}

public Action Event_Item_Pickup(Handle event, const char[]name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	char log_string[384];
	CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
	char item[64];
	GetEventString(event, "item", item, sizeof(item));
	EscapeString(item, sizeof(item));
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_item_pickup\", \"round\": %i, \"player\": %s, \"item\": \"%s\"}", g_round, log_string, item);
	}
	else
	{
		LogEvent("{\"event\": \"item_pickup\", \"round\": %i, \"player\": %s, \"item\": \"%s\"}", g_round, log_string, item);
	}
}

void AddScore(int team)
{
	if (!g_overtime)
	{
		if (team == CS_TEAM_T)
		{
			if (g_first_half)
			{
				g_scores[SCORE_T][SCORE_FIRST_HALF]++;
			}
			else
			{
				g_scores[SCORE_T][SCORE_SECOND_HALF]++;
			}
		}
		
		if (team == CS_TEAM_CT)
		{
			if (g_first_half)
			{
				g_scores[SCORE_CT][SCORE_FIRST_HALF]++;
			}
			else
			{
				g_scores[SCORE_CT][SCORE_SECOND_HALF]++;
			}
		}
	}
	else
	{
		if (team == CS_TEAM_T)
		{
			if (g_first_half)
			{
				g_scores_overtime[SCORE_T][g_overtime_count][SCORE_FIRST_HALF]++;
			}
			else
			{
				g_scores_overtime[SCORE_T][g_overtime_count][SCORE_SECOND_HALF]++;
			}
		}
		
		if (team == CS_TEAM_CT)
		{
			if (g_first_half)
			{
				g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_FIRST_HALF]++;
			}
			else
			{
				g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_SECOND_HALF]++;
			}
		}
	}
	
	UploadResultsRound();
	
	// stats
	char serverstring[384];
	GetServerString(serverstring, sizeof(serverstring));
	LogEvent("{\"event\": \"score_update\", \"map\": \"%s\", \"round\": %i, \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}], \"settings\": {\"max_rounds\": %d, \"overtime_enabled\": %d, \"overtime_max_rounds\": %d}, \"server\": %s}", g_map, g_round, g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore(), GetConVarInt(mp_maxrounds), GetConVarInt(mp_overtime_enable), GetConVarInt(mp_overtime_maxrounds), serverstring);
	g_round = GetTTotalScore() + GetCTTotalScore() + 1;
}

void CheckScores()
{
	if (!g_overtime)
	{
		if (GetScore() == (GetConVarInt(mp_maxrounds)/2)) // half time
		{
			if (!g_first_half)
			{
				return;
			}
			Call_StartForward(g_f_on_half_time);
			Call_PushString(g_ct_name);
			Call_PushCell(GetCTTotalScore());
			Call_PushCell(GetTTotalScore());
			Call_PushString(g_t_name);
			Call_Finish();
			
			LogEvent("{\"event\": \"half_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());

			DisplayScore(0, 0, false);
			
			if (team_switch) {
				team_switch = false;
			} else {
				team_switch = true;
			}
			
			g_t_money = false;
			g_first_half = false;
			SwitchScores();
			
			if (GetConVarBool(wm_pause_limit_half))
			{
				g_t_pause_count = 0;
				g_ct_pause_count = 0;
			}
			
			if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) && !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				SwitchTeamNames();
			}
			else if (!StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
				SwitchTeamNames();
			}
			else if (!StrEqual(g_t_name, DEFAULT_T_NAME, false))
			{
				Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
				SwitchTeamNames();
			}
			
			//			char half_time_config[128];
			//			GetConVarString(g_h_half_time_config, half_time_config, sizeof(half_time_config));
			//			ServerCommand("exec %s", half_time_config);
			if (GetConVarBool(wm_half_time_break))
			{
				g_half_swap = false;
				g_live = false;
				SetAllCancelled(false);
				ReadyChangeAll(0, false, true);
				ReadySystem(true);
				ShowInfo(0, true, false, 0);
				ServerCommand("mp_halftime_pausetimer 1");
			}
		}
		else if (GetTScore() == (GetConVarInt(mp_maxrounds)/2) && GetCTScore() == (GetConVarInt(mp_maxrounds)/2)) // complete draw
		{
			if (GetConVarBool(mp_overtime_enable))
			{ 
				// max rounds overtime
				LogEvent("{\"event\": \"over_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
				
				DisplayScore(0, 0, false);
				CPrintToChatAll("%t", "Over Time", CHAT_PREFIX, (GetConVarInt(mp_overtime_maxrounds)/2));
				//			g_live = false;
				g_t_money = false;
				g_overtime = true;
				g_overtime_mode = 1;
				g_first_half = true;
				
				if (GetConVarBool(wm_half_time_break) || GetConVarBool(wm_over_time_break))
				{
					g_half_swap = false;
					g_live = false;
					SetAllCancelled(false);
					ReadyChangeAll(0, false, true);
					ReadySystem(true);
					ShowInfo(0, true, false, 0);
					ServerCommand("mp_overtime_halftime_pausetimer 1");
				}
			}
			else
			{
				Call_StartForward(g_f_on_end_match);
				Call_PushString(g_ct_name);
				Call_PushCell(GetCTTotalScore());
				Call_PushCell(GetTTotalScore());
				Call_PushString(g_t_name);
				Call_Finish();
				
				LogEvent("{\"event\": \"full_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
				g_round = GetTTotalScore() + GetCTTotalScore();
				
				if (GetConVarBool(wm_prefix_logs))
				{
					CreateTimer(5.0, RenameLogs);
				}
				DisplayScore(0, 0, false);
				CPrintToChatAll("%t", "Full Time", CHAT_PREFIX);
				SetLastMatchScores();
				
				UploadResults();
				ServerCommand("hostname %s", g_server);
				
				if (GetConVarBool(mp_match_end_restart) && ((g_veto_bo3_active && g_veto_map_number < 3) || (g_veto_bo5_active && g_veto_map_number < 5) || (g_veto_bo2_active && g_veto_map_number < 2)))
				{
					VetoMapChange();
				}
				
				CreateTimer(4.0, ResetMatchTimer);
			}
		}
		else if (GetScore() == GetConVarInt(mp_maxrounds)) // full time (all rounds have been played out)
		{
			Call_StartForward(g_f_on_end_match);
			Call_PushString(g_ct_name);
			Call_PushCell(GetCTTotalScore());
			Call_PushCell(GetTTotalScore());
			Call_PushString(g_t_name);
			Call_Finish();
			
			LogEvent("{\"event\": \"full_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
			g_round = GetTTotalScore() + GetCTTotalScore();
			
			if (GetConVarBool(wm_prefix_logs))
			{
				CreateTimer(5.0, RenameLogs);
			}
			DisplayScore(0, 0, false);
			CPrintToChatAll("%t", "Full Time", CHAT_PREFIX);
			SetLastMatchScores();
			
			UploadResults();
			
			if (GetConVarBool(mp_match_end_restart) && ((g_veto_bo3_active && g_veto_map_number < 3) || (g_veto_bo5_active && g_veto_map_number < 5) || (g_veto_bo2_active && g_veto_map_number < 2)))
			{
				VetoMapChange();
			}
			
			CreateTimer(4.0, ResetMatchTimer);
		}
		else if (GetTScore() == (GetConVarInt(mp_maxrounds)/2) + 1 || GetCTScore() == (GetConVarInt(mp_maxrounds)/2) + 1) // full time
		{
			DisplayScore(0, 0, false);
			CPrintToChatAll("%t", "Full Time", CHAT_PREFIX);
			
			if (GetConVarBool(mp_match_can_clinch))
			{
				Call_StartForward(g_f_on_end_match);
				Call_PushString(g_ct_name);
				Call_PushCell(GetCTTotalScore());
				Call_PushCell(GetTTotalScore());
				Call_PushString(g_t_name);
				Call_Finish();
				
				LogEvent("{\"event\": \"full_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
				g_round = GetTTotalScore() + GetCTTotalScore();
				
				if (GetConVarBool(wm_prefix_logs))
				{
					CreateTimer(5.0, RenameLogs);
				}
				SetLastMatchScores();
				
				UploadResults();
				
				if (GetConVarBool(mp_match_end_restart) && ((g_veto_bo3_active && g_veto_map_number < 3) || (g_veto_bo5_active && g_veto_map_number < 5) || (g_veto_bo2_active && g_veto_map_number < 2)))
				{
					VetoMapChange();
				}
				
				CreateTimer(4.0, ResetMatchTimer);
			}
			else
			{
				LogEvent("{\"event\": \"full_time_playing_out\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
				
				CPrintToChatAll("%t", "Playing Out Notice", CHAT_PREFIX, (GetConVarInt(mp_maxrounds)/2));
			}
		}
		else
		{
			DisplayScore(0, 0, false);
		}
	}
	else
	{
		if (GetOTScore() == (GetConVarInt(mp_overtime_maxrounds)/2)) // half time
		{
			if (!g_first_half)
			{
				return;
			}
			Call_StartForward(g_f_on_half_time);
			Call_PushString(g_ct_name);
			Call_PushCell(GetCTTotalScore());
			Call_PushCell(GetTTotalScore());
			Call_PushString(g_t_name);
			Call_Finish();
			
			LogEvent("{\"event\": \"over_half_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
			
			if (team_switch) {
				team_switch = false;
			} else {
				team_switch = true;
			}
			DisplayScore(0, 1, false);
			
			g_t_money = false;
			g_first_half = false;
			SwitchScores();
			
			if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) && !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				SwitchTeamNames();
			}
			else if (!StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
				SwitchTeamNames();
			}
			else if (!StrEqual(g_t_name, DEFAULT_T_NAME, false))
			{
				Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
				SwitchTeamNames();
			}
			
			if (GetConVarBool(wm_half_time_break))
			{
				g_half_swap = false;
				g_live = false;
				SetAllCancelled(false);
				ReadyChangeAll(0, false, true);
				ReadySystem(true);
				ShowInfo(0, true, false, 0);
				ServerCommand("mp_overtime_halftime_pausetimer 1");
			}
		}
		else if (GetTOTScore() == (GetConVarInt(mp_overtime_maxrounds)/2) && GetCTOTScore() == (GetConVarInt(mp_overtime_maxrounds)/2)) // complete draw
		{
			if (g_overtime_mode == 1)
			{ 
				// max rounds overtime
				LogEvent("{\"event\": \"over_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
				
				DisplayScore(0, 1, false);
				CPrintToChatAll("%t", "Over Time", CHAT_PREFIX, (GetConVarInt(mp_overtime_maxrounds)/2));
				g_overtime_count++;
				g_first_half = true;
				
				if (GetConVarBool(wm_half_time_break) || GetConVarBool(wm_over_time_break))
				{
					g_half_swap = false;
					g_live = false;
					SetAllCancelled(false);
					ReadyChangeAll(0, false, true);
					ReadySystem(true);
					ShowInfo(0, true, false, 0);
					ServerCommand("mp_overtime_halftime_pausetimer 1");
				}
				
				return;
			}
		}
		else if (GetTOTScore() == (GetConVarInt(mp_overtime_maxrounds)/2) + 1 || GetCTOTScore() == (GetConVarInt(mp_overtime_maxrounds)/2) + 1) // full time
		{
			Call_StartForward(g_f_on_end_match);
			Call_PushString(g_ct_name);
			Call_PushCell(GetCTTotalScore());
			Call_PushCell(GetTTotalScore());
			Call_PushString(g_t_name);
			Call_Finish();
			
			LogEvent("{\"event\": \"over_full_time\", \"teams\": [{\"name\": \"%s\", \"team\": %d, \"score\": %d}, {\"name\": \"%s\", \"team\": %d, \"score\": %d}]}", g_t_name_escaped, CS_TEAM_T, GetTTotalScore(), g_ct_name_escaped, CS_TEAM_CT, GetCTTotalScore());
			g_round = GetTTotalScore() + GetCTTotalScore();
			
			if (GetConVarBool(wm_prefix_logs))
			{
				CreateTimer(5.0, RenameLogs);
			}
			DisplayScore(0, 2, false);
			CPrintToChatAll("%t", "Full Time", CHAT_PREFIX);
/*			
			if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) && !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				SwitchTeamNames();
			}
			else if (!StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
			{
				Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
				SwitchTeamNames();
			}
			else if (!StrEqual(g_t_name, DEFAULT_T_NAME, false))
			{
				Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
				SwitchTeamNames();
			}
			SwitchScores();
*/
			SetLastMatchScores();
			
			UploadResults();
			
			if (GetConVarBool(mp_match_end_restart) && ((g_veto_bo3_active && g_veto_map_number < 3) || (g_veto_bo5_active && g_veto_map_number < 5) || (g_veto_bo2_active && g_veto_map_number < 2)))
			{
				VetoMapChange();
			}
			
			CreateTimer(4.0, ResetMatchTimer);
			return;
		}
		else
		{
			DisplayScore(0, 1, false);
		}
	}
}

int GetScore()
{
	return GetTScore() + GetCTScore();
}

int GetTScore()
{
	return g_scores[SCORE_T][SCORE_FIRST_HALF] + g_scores[SCORE_T][SCORE_SECOND_HALF];
}

int GetCTScore()
{
	return g_scores[SCORE_CT][SCORE_FIRST_HALF] + g_scores[SCORE_CT][SCORE_SECOND_HALF];
}

int GetOTScore()
{
	return GetTOTScore() + GetCTOTScore();
}

int GetTOTScore()
{
	return g_scores_overtime[SCORE_T][g_overtime_count][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_T][g_overtime_count][SCORE_SECOND_HALF];
}

int GetCTOTScore()
{	
	return g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_CT][g_overtime_count][SCORE_SECOND_HALF];
}

int GetTOTTotalScore()
{
	int result;
	for (int i = 0; i <= g_overtime_count; i++)
	{
		result += g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF];
	}
	return result;
}

int GetCTOTTotalScore()
{
	int result;
	for (int i = 0; i <= g_overtime_count; i++)
	{
		result += g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF];
	}
	return result;
}

int GetTTotalScore()
{
	int result;
	result = GetTScore();
	for (int i = 0; i <= g_overtime_count; i++)
	{
		result += g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF];
	}
	return result;
}

int GetCTTotalScore()
{
	int result;
	result = GetCTScore();
	for (int i = 0; i <= g_overtime_count; i++)
	{
		result += g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF] + g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF];
	}
	return result;
}

public int SortMoney(int elem1, int elem2, const int[] array, Handle hndl)
{
	int money1 = GetEntData(elem1, g_iAccount);
	int money2 = GetEntData(elem2, g_iAccount);
	
	if (money1 > money2)
	{
		return -1;
	}
	else if (money1 == money2)
	{
		return 0;
	}
	else
	{
		return 1;
	}
}

void ReadyServ(int client, bool ready, bool silent, bool show, bool priv)
{
	char log_string[384];
	CS_GetLogString(client, log_string, sizeof(log_string));
	
	if (ready)
	{
		if (g_player_list[client] == PLAYER_UNREADY)
		{
			LogEvent("{\"event\": \"player_ready\", \"player\": %s}", log_string);
		}
		
		g_player_list[client] = PLAYER_READY;
		SetTagReady(client);
		
		if (!silent)
		{
			CPrintToChat(client, "%T", "Ready", client, CHAT_PREFIX);
		}
	}
	else
	{
		if (g_player_list[client] == PLAYER_READY)
		{
			LogEvent("{\"event\": \"player_unready\", \"player\": %s}", log_string);
		}
		
		g_player_list[client] = PLAYER_UNREADY;
		SetTagNotReady(client);
		
		if (!silent)
		{
			CPrintToChat(client, "%T", "Not Ready", client, CHAT_PREFIX);
		}
	}
	
	if (show)
	{
		ShowInfo(client, true, priv, 0);
	}
	
	CheckReady();
}

void CheckReady()
{	
	if (g_live)
	{
		return;
	}
	
	if (g_ready_enabled && !GetConVarBool(wm_warmod_safemode))
	{
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && !IsFakeClient(i))
			{
				if (g_player_list[i] == PLAYER_READY)
				{
					SetTagReady(i);
				}
				else if (g_player_list[i] == PLAYER_UNREADY)
				{
					SetTagNotReady(i);
				}
			}
		}
	}
	
	char t_name[64];
	char ct_name[64];
	Format(t_name, sizeof(t_name), g_t_name);
	Format(ct_name, sizeof(ct_name), g_ct_name);
	
	StripFilename(t_name, sizeof(t_name));
	StripFilename(ct_name, sizeof(ct_name));
	ReplaceString(t_name, sizeof(t_name), ".", "");
	ReplaceString(ct_name, sizeof(ct_name), ".", "");
	
	int ready_num;
	for (int i = 1; i <= MaxClients; i++)
	{
		if (g_player_list[i] == PLAYER_READY && IsClientInGame(i) && !IsFakeClient(i))
		{
			ready_num++;
		}
	}
	
	if (g_ready_enabled && !g_live && (ready_num >= GetConVarInt(wm_min_ready) || GetConVarInt(wm_min_ready) == 0))
	{
		if (!g_t_had_knife && !g_match && GetConVarBool(wm_auto_knife))
		{
			ShowInfo(0, false, false, 1);
			SetAllCancelled(false);
			ReadySystem(false);
			KnifeOn3(0, 0);
			return;
		}
		ShowInfo(0, false, false, 1);
		SetAllCancelled(false);
		ReadySystem(false);
		ServerCommand("mp_warmup_end");
		LiveOn3(true);
	}
}

void ReadyChecked()
{
	if (!g_start)
	{
		g_match_start = GetEngineTime();
		FormatTime(date, sizeof(date), "%Y-%m-%d");
		FormatTime(startHour, sizeof(startHour), "%H");
		FormatTime(startMin, sizeof(startMin), "%M");
		
		char t_name[64];
		char ct_name[64];
		Format(t_name, sizeof(t_name), g_t_name);
		Format(ct_name, sizeof(ct_name), g_ct_name);
		
		StripFilename(t_name, sizeof(t_name));
		StripFilename(ct_name, sizeof(ct_name));
		ReplaceString(t_name, sizeof(t_name), ".", "");
		ReplaceString(ct_name, sizeof(ct_name), ".", "");
		StringToLower(t_name, sizeof(t_name));
		StringToLower(ct_name, sizeof(ct_name));
		
		if (!GetConVarBool(wm_warmod_safemode))
		{
			if (StrEqual(t_name, DEFAULT_T_NAME, false) || StrEqual(t_name, "Terrorists", false) || StrEqual(t_name, "", false) || StrEqual(t_name, "_", false))
			{
				//getTerroristTeamName();
			}
			
			if (StrEqual(ct_name, DEFAULT_CT_NAME, false) || StrEqual(ct_name, "CounterTerrorists", false) || StrEqual(ct_name, "", false) || StrEqual(ct_name, "_", false))
			{
				//getCounterTerroristTeamName();
			}
		}
		
		Format(t_name, sizeof(t_name), g_t_name);
		Format(ct_name, sizeof(ct_name), g_ct_name);
		
		if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) || !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
		{
			StripFilename(t_name, sizeof(t_name));
			StripFilename(ct_name, sizeof(ct_name));
			ReplaceString(t_name, sizeof(t_name), ".", "");
			ReplaceString(ct_name, sizeof(ct_name), ".", "");
			StringToLower(t_name, sizeof(t_name));
			StringToLower(ct_name, sizeof(ct_name));
			if (StrEqual(t_name, "rrorists", false)) 
			{		
				Format(t_name, sizeof(t_name), "terroists");		
			}			
			if (StrEqual(ct_name, "unterterrorists", false)) 
			{		
				Format(ct_name, sizeof(ct_name), "counterterroists");		
			}
			Format(g_log_filename, sizeof(g_log_filename), "%s-%s%s-%04x-%s-%s-vs-%s", date, startHour, startMin, GetConVarInt(FindConVar("hostport")), g_map, t_name, ct_name);
		}
		else
		{
			Format(g_log_filename, sizeof(g_log_filename), "%s-%s%s-%04x-%s", date, startHour, startMin, GetConVarInt(FindConVar("hostport")), g_map);
		}
		
		char save_dir[128];
		GetConVarString(wm_save_dir, save_dir, sizeof(save_dir));
		char file_prefix[1];
		if (GetConVarBool(wm_prefix_logs))
		{
			file_prefix = "_";
		}
		if (!StrEqual(save_dir, ""))
		{
			if (!DirExists(save_dir))
			{
				CreateDirectory(save_dir, 511);
			}
		}		
		if (GetConVarBool(wm_auto_record))
		{
			ServerCommand("tv_stoprecord");
			if (!GetConVarBool(tv_enable))
			{
				LogError("[Warmod+] GOTV is not enabled - Please enable to record demos or disable wm_auto_record");
			}
			else if (!StrEqual(save_dir, ""))
			{
				if (DirExists(save_dir))
				{
					ServerCommand("tv_record \"%s/%s%s.dem\"", save_dir, file_prefix, g_log_filename);
					g_log_warmod_dir = true;
					Format(g_sDemoPath, sizeof(g_sDemoPath), "%s/%s%s.dem", save_dir, file_prefix, g_log_filename);
					Format(g_sDemoName, sizeof(g_sDemoName), "%s%s.dem", file_prefix, g_log_filename);
					g_bRecording = true;
				}
				else
				{
					ServerCommand("tv_record \"%s%s.dem\"", file_prefix, g_log_filename);
					g_log_warmod_dir = false;
					Format(g_sDemoPath, sizeof(g_sDemoPath), "%s%s.dem", file_prefix, g_log_filename);
					Format(g_sDemoName, sizeof(g_sDemoName), "%s%s.dem", file_prefix, g_log_filename);
					g_bRecording = true;
				}
			}
			else
			{
				ServerCommand("tv_record \"%s%s.dem\"", file_prefix, g_log_filename);
				g_log_warmod_dir = false;
				Format(g_sDemoPath, sizeof(g_sDemoPath), "%s%s.dem", file_prefix, g_log_filename);
				Format(g_sDemoName, sizeof(g_sDemoName), "%s%s.dem", file_prefix, g_log_filename);
				g_bRecording = true;
			}
		}
		
		char filepath[128];
		if (!StrEqual(save_dir, ""))
		{
			if (DirExists(save_dir))
			{
				Format(filepath, sizeof(filepath), "%s/%s%s.log", save_dir, file_prefix, g_log_filename);
				Format(g_sLogPath, sizeof(g_sLogPath), "%s/%s%s.log", save_dir, file_prefix, g_log_filename);
				g_log_file = OpenFile(filepath, "w");
				g_log_warmod_dir = true;
			}
			else if (DirExists("logs"))
			{
				Format(filepath, sizeof(filepath), "logs/%s%s.log", file_prefix, g_log_filename);
				Format(g_sLogPath, sizeof(g_sLogPath), "logs/%s%s.log", file_prefix, g_log_filename);
				g_log_file = OpenFile(filepath, "w");
				g_log_warmod_dir = false;
			}
			else
			{
				Format(filepath, sizeof(filepath), "%s%s.log", file_prefix, g_log_filename);
				Format(g_sLogPath, sizeof(g_sLogPath), "%s%s.log", file_prefix, g_log_filename);
				g_log_file = OpenFile(filepath, "w");
				g_log_warmod_dir = false;
			}
		}
		else if (DirExists("logs"))
		{
			Format(filepath, sizeof(filepath), "logs/%s%s.log", file_prefix, g_log_filename);
			Format(g_sLogPath, sizeof(g_sLogPath), "logs/%s%s.log", file_prefix, g_log_filename);
			g_log_file = OpenFile(filepath, "w");
			g_log_warmod_dir = false;
		}
		else
		{
			Format(filepath, sizeof(filepath), "%s%s.log", file_prefix, g_log_filename);
			Format(g_sLogPath, sizeof(g_sLogPath), "%s%s.log", file_prefix, g_log_filename);
			g_log_file = OpenFile(filepath, "w");
			g_log_warmod_dir = false;
		}
		
		g_log_live = true;
		LogEvent("{\"event\": \"log_start\", \"unixTime\": %d}", GetTime());
		g_log_live = true;
		LogPlayers();
	}
}

void LiveOn3(bool e_war)
{
	ServerCommand("mp_warmup_end");
	Call_StartForward(g_f_on_lo3);
	Call_Finish();
	
	g_t_score = false;
	
	char match_config[64];
	GetConVarString(wm_match_config, match_config, sizeof(match_config));
	
	if (e_war && !StrEqual(match_config, ""))
	{
		ServerCommand("exec %s", match_config);
	}
	
	ReadyChecked();
	g_start = true;
	g_max_lock = true;
	g_match = true;
	g_live = true;
	g_MatchComplete = true;	
	LiveOn3Override();
}

stock bool LiveOn3Override()
{
	if (!g_half_swap)
	{
		ServerCommand("mp_halftime_pausetimer 0");
		ServerCommand("mp_overtime_halftime_pausetimer 0");
		LiveOn3Text = true;
		g_half_swap = true;
		LiveText();
		return true;
	}
	
	CreateResultKey();
	
	if (g_restore)
	{
		LiveOn3Text = true;
		Event_Round_Start_CMD();
		ServerCommand("mp_backup_restore_load_autopause 0");
		ServerCommand("mp_backup_restore_load_file %s", g_c_backup);
		g_restore = false;
		char id_match[16];
		if(SplitString(g_c_backup, "_", id_match, sizeof(id_match)) != -1) {
			match_id = StringToInt(id_match);
		}
		
		char serverstring[384];
		GetServerString(serverstring, sizeof(serverstring));
		LogEvent("{\"event\": \"live_on_3_restored\", \"map\": \"%s\", \"teams\": [{\"name\": \"%s\", \"team\": %d}, {\"name\": \"%s\", \"team\": %d}], \"status\": %d, \"settings\": {\"max_rounds\": %d, \"overtime_enabled\": %d, \"overtime_max_rounds\": %d}, \"server\": %s, \"competition\": \"%s\", \"event_name\": \"%s\", \"version\": \"%s\"}", g_map, g_t_name_escaped, CS_TEAM_T, g_ct_name_escaped, CS_TEAM_CT, UpdateStatus(), GetConVarInt(mp_maxrounds), GetConVarInt(mp_overtime_enable), GetConVarInt(mp_overtime_maxrounds), serverstring, g_competition, g_event, WM_VERSION);
		
		return true;
	}
	
	team_switch = false;
	if (!InWarmup())
	{
		ServerCommand("mp_unpause_match 1");
		ServerCommand("mp_restartgame 1");
		CPrintToChatAll("%t", "Live on 3", CHAT_PREFIX);
		LiveOn2 = true;
		ServerCommand("mp_teamname_1 %s", g_ct_name);
		ServerCommand("mp_teamname_2 %s", g_t_name);
		
		g_t_pause_count = 0;
		g_ct_pause_count = 0;
		
		char serverstring[384];
		GetServerString(serverstring, sizeof(serverstring));
		LogEvent("{\"event\": \"live_on_3\", \"map\": \"%s\", \"teams\": [{\"name\": \"%s\", \"team\": %d}, {\"name\": \"%s\", \"team\": %d}], \"status\": %d, \"settings\": {\"max_rounds\": %d, \"overtime_enabled\": %d, \"overtime_max_rounds\": %d}, \"server\": %s, \"competition\": \"%s\", \"event_name\": \"%s\", \"version\": \"%s\"}", g_map, g_t_name_escaped, CS_TEAM_T, g_ct_name_escaped, CS_TEAM_CT, UpdateStatus(), GetConVarInt(mp_maxrounds), GetConVarInt(mp_overtime_enable), GetConVarInt(mp_overtime_maxrounds), serverstring, g_competition, g_event, WM_VERSION);
		
		return true;
	}
	else
	{
		ServerCommand("mp_warmup_end");
		CreateTimer(1.0, Lo3Timer);
		return true;
	}
}

public Action Lo3Timer(Handle timer)
{
	ServerCommand("mp_unpause_match 1");
	ServerCommand("mp_restartgame 1");
	CPrintToChatAll("%t", "Live on 3", CHAT_PREFIX);
	LiveOn2 = true;
	
	if (StrEqual(g_t_name, "")) {		
		Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);		
	}		
	if (StrEqual(g_ct_name, "")) {		
		Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);		
	}
	ServerCommand("mp_teamname_1 %s", g_ct_name);			
	ServerCommand("mp_teamname_2 %s", g_t_name);
	
	g_t_pause_count = 0;
	g_ct_pause_count = 0;
	
	char serverstring[384];
	GetServerString(serverstring, sizeof(serverstring));
	LogEvent("{\"event\": \"live_on_3\", \"map\": \"%s\", \"teams\": [{\"name\": \"%s\", \"team\": %d}, {\"name\": \"%s\", \"team\": %d}], \"status\": %d, \"settings\": {\"max_rounds\": %d, \"overtime_enabled\": %d, \"overtime_max_rounds\": %d}, \"server\": %s, \"competition\": \"%s\", \"event_name\": \"%s\", \"version\": \"%s\"}", g_map, g_t_name_escaped, CS_TEAM_T, g_ct_name_escaped, CS_TEAM_CT, UpdateStatus(), GetConVarInt(mp_maxrounds), GetConVarInt(mp_overtime_enable), GetConVarInt(mp_overtime_maxrounds), serverstring, g_competition, g_event, WM_VERSION);
}

static void LiveText() {
	if (LiveOn2) {
		LiveOn2 = false;
		LiveOn1 = true;
		ServerCommand("mp_restartgame 1");
		CPrintToChatAll("%t", "Live on 2", CHAT_PREFIX);
	} else if (LiveOn1) {
		LiveOn1 = false;
		LiveOn3Text = true;
		ServerCommand("mp_restartgame 3");
		CPrintToChatAll("%t", "Live on 1", CHAT_PREFIX);
	} else if(LiveOn3Text) {
		LiveOn3Text = false;
		ServerCommand("sm_vetomaps");
		CPrintToChatAll("%t", "Live", CHAT_PREFIX);
		CPrintToChatAll("%t", "Good Luck", CHAT_PREFIX);
		CPrintToChatAll("%t", "Powered By", CHAT_PREFIX);
		
		if (GetConVarBool(FindConVar("tv_enable")))
		{
			char tvport[PLATFORM_MAX_PATH];
			GetConVarString(FindConVar("tv_port"), tvport, sizeof(tvport));
			int ip[4]; SteamWorks_GetPublicIP(ip);
			CPrintToChatAll("%t", "GOTV Is", CHAT_PREFIX, ip[0], ip[1], ip[2], ip[3], tvport);
		}

		CreateTimer(1.0, SetTagClientDefault);
		
		ClearForceTeamList(4);
	} else if(KnifeOn2) {
		KnifeOn2 = false;
		KnifeOn1 = true;
		ServerCommand("mp_restartgame 1");
		CPrintToChatAll("%t", "Knife on 2", CHAT_PREFIX);
	} else if(KnifeOn1) {
		KnifeOn1 = false;
		KnifeOn3Text = true;
		ServerCommand("mp_restartgame 3");
		CPrintToChatAll("%t", "Knife on 1", CHAT_PREFIX);
	} else if(KnifeOn3Text) {
		KnifeOn3Text = false;
		if (GetConVarBool(wm_knife_zeus)) {
			CPrintToChatAll("%t", "Zeus", CHAT_PREFIX);
			CPrintToChatAll("%t", "Knife", CHAT_PREFIX);
			CPrintToChatAll("%t", "Good Luck", CHAT_PREFIX);
			CPrintToChatAll("%t", "Powered By", CHAT_PREFIX);
			
			if (GetConVarBool(FindConVar("tv_enable")))
			{
				char tvport[PLATFORM_MAX_PATH];
				GetConVarString(FindConVar("tv_port"), tvport, sizeof(tvport));
				int ip[4]; SteamWorks_GetPublicIP(ip);
				CPrintToChatAll("%t", "GOTV Is", CHAT_PREFIX, ip[0], ip[1], ip[2], ip[3], tvport);
			}
		} else {
			CPrintToChatAll("%t", "Knife", CHAT_PREFIX);
			CPrintToChatAll("%t", "Knife", CHAT_PREFIX);
			CPrintToChatAll("%t", "Good Luck", CHAT_PREFIX);
			CPrintToChatAll("%t", "Powered By", CHAT_PREFIX);
			
			if (GetConVarBool(FindConVar("tv_enable")))
			{
				char tvport[PLATFORM_MAX_PATH];
				GetConVarString(FindConVar("tv_port"), tvport, sizeof(tvport));
				int ip[4]; SteamWorks_GetPublicIP(ip);
				CPrintToChatAll("%t", "GOTV Is", CHAT_PREFIX, ip[0], ip[1], ip[2], ip[3], tvport);
			}
		}
		
		CreateTimer(1.0, SetTagClientDefault);
		
		ClearForceTeamList(4);
	}
}

public Action KnifeOn3(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	ShowInfo(0, false, false, 1);
	SetAllCancelled(false);
	ReadySystem(false);
	ReadyChecked();
	g_t_knife = true;
	g_max_lock = true;
	g_t_score = false;
	
	char serverstring[384];
	GetServerString(serverstring, sizeof(serverstring));
	LogEvent("{\"event\": \"knife_on_3\", \"map\": \"%s\", \"teams\": [{\"name\": \"%s\", \"team\": %d}, {\"name\": \"%s\", \"team\": %d}], \"server\": %s}", g_map, g_t_name_escaped, CS_TEAM_T, g_ct_name_escaped, CS_TEAM_CT, serverstring);
	
	
	char match_config[64];
	GetConVarString(wm_knife_config, match_config, sizeof(match_config));
	
	if (!StrEqual(match_config, ""))
	{
		ServerCommand("exec %s", match_config);
	}
	g_start = true;
	KnifeOn3Override();
	UpdateStatus();
	LogAction(client, -1, "\"knife_on_3\" (player \"%L\")", client);
	return Plugin_Handled;
}

stock bool KnifeOn3Override()
{
	if (!InWarmup())
	{
		ServerCommand("mp_restartgame 1");
		CPrintToChatAll("%t", "Knife On 3", CHAT_PREFIX);
		KnifeOn2 = true;
		
		return true;
	}
	else
	{
		ServerCommand("mp_warmup_end");
		CreateTimer(1.0, Ko3Timer);
		return true;
	}
}

public Action Ko3Timer(Handle timer)
{
	ServerCommand("mp_restartgame 1");
	CPrintToChatAll("%t", "Knife On 3", CHAT_PREFIX);
	KnifeOn2 = true;
}

public Action Command_JoinTeam(int client, const char[]command, int args)
{
	// wm not active
	if (!IsActive(client, true))
	{
		return Plugin_Continue;
	}
	
	// invalid client and bot
	if (client == 0 || IsFakeClient(client))
	{
		return Plugin_Continue;
	}
	
	// reset team if not in pug and not in match
	if(!IsPug && !g_match && !g_t_knife)
	{
		if(client == g_capt1)
		{
			Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
			Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
			EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
			g_capt1 = -1;
			g_t_id = 0;
			ServerCommand("mp_teamname_2 %s", DEFAULT_T_NAME);
			ServerCommand("mp_teamlogo_2 \"\"");
			ServerCommand("mp_teamflag_2 \"\"");
			CPrintToChatAll("%t", "T Captain Reset", CHAT_PREFIX);
		}
		else if(client == g_capt2)
		{
			Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
			Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
			EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
			g_capt2 = -1;
			g_ct_id = 0;
			ServerCommand("mp_teamname_1 %s", DEFAULT_CT_NAME);
			ServerCommand("mp_teamlogo_1 \"\"");
			ServerCommand("mp_teamflag_1 \"\"");
			CPrintToChatAll("%t", "CT Captain Reset", CHAT_PREFIX);
		}
	}
	
	// in match and lock team
	if ((g_match || g_t_knife) && GetClientTeam(client) > 1 && GetConVarBool(wm_lock_teams))
	{
		CPrintToChat(client, "%T", "Change Teams Midgame", client, CHAT_PREFIX);
		return Plugin_Stop;
	}
	
	// in pug
	if(IsPug)
	{
		CPrintToChat(client, "%T", "Change Teams Pug", client, CHAT_PREFIX);
		return Plugin_Stop;
	}
	
	//char m_szTeam[8];
	//GetCmdArg(1, m_szTeam, sizeof(m_szTeam));
	//int m_iTeam = StringToInt(m_szTeam);
	
	int max_players = GetConVarInt(wm_max_players);
	if ((g_ready_enabled || g_match || g_t_knife || g_max_lock) && max_players != 0 && GetClientTeam(client) <= 1 && CS_GetPlayingCount() >= max_players) {
	//((GetTeamClientCount(CS_TEAM_T) > (max_players/2) && m_iTeam == CS_TEAM_T) || (GetTeamClientCount(CS_TEAM_CT) > (max_players/2) && m_iTeam == CS_TEAM_CT))) {
		CPrintToChat(client, "%T", "Maximum Players", client, CHAT_PREFIX);
		ChangeClientTeam(client, CS_TEAM_SPECTATOR);
		return Plugin_Stop;
	}
	
	// check force team
	char CommunityID[18];
	GetClientAuthId(client, AuthId_SteamID64, CommunityID, sizeof(CommunityID));
	for (int i = 0; i < 10; i++) 
	{
		if (strcmp(force_team_t[i], CommunityID, false) == 0) 
		{
			ChangeClientTeam(client, CS_TEAM_T);
			return Plugin_Stop;
		}
		else if (strcmp(force_team_ct[i], CommunityID, false) == 0) 
		{
			ChangeClientTeam(client, CS_TEAM_CT);
			return Plugin_Stop;
		}
	}
	
	return Plugin_Continue;
}

public Action ChooseTeam(int client, int args)
{
	if (!IsActive(client, true))
	{
		return Plugin_Continue;
	}
	
	if (client == 0 || IsFakeClient(client))
	{
		return Plugin_Continue;
	}
	
	if ((g_match || g_t_knife) && GetClientTeam(client) > 1 && GetConVarBool(wm_lock_teams))
	{
		CPrintToChat(client, "%T", "Change Teams Midgame", client, CHAT_PREFIX);
		return Plugin_Stop;
	}
	
	//char m_szTeam[8];
	//GetCmdArg(1, m_szTeam, sizeof(m_szTeam));
	//int m_iTeam = StringToInt(m_szTeam);
	
	int max_players = GetConVarInt(wm_max_players);
	if ((g_ready_enabled || g_match || g_t_knife || g_max_lock) && max_players != 0 && GetClientTeam(client) <= 1 && CS_GetPlayingCount() >= max_players) {
	//((GetTeamClientCount(CS_TEAM_T) > (max_players/2) && m_iTeam == CS_TEAM_T) || (GetTeamClientCount(CS_TEAM_CT) > (max_players/2) && m_iTeam == CS_TEAM_CT))) {
		CPrintToChat(client, "%T", "Maximum Players", client, CHAT_PREFIX);
		ChangeClientTeam(client, CS_TEAM_SPECTATOR);
		return Plugin_Stop;
	}
	
	char CommunityID[18];
	GetClientAuthId(client, AuthId_SteamID64, CommunityID, sizeof(CommunityID));
	for (int i = 0; i < 10; i++) {
		if (strcmp(force_team_t[i], CommunityID, false) == 0) {
			ChangeClientTeam(client, CS_TEAM_T);
			return Plugin_Stop;
		} else if (strcmp(force_team_ct[i], CommunityID, false) == 0) {
			ChangeClientTeam(client, CS_TEAM_CT);
			return Plugin_Stop;
		}
	}
	
	return Plugin_Continue;
}

public Action RestrictBuy(int client, int args)
{
	if (!IsActive(client, true))
	{
		return Plugin_Continue;
	}
	
	if (client == 0)
	{
		return Plugin_Continue;
	}
	
	char arg[128];
	GetCmdArgString(arg, 128);
	if (!g_live && GetConVarBool(wm_block_warm_up_grenades))
	{
		char the_weapon[32];
		Format(the_weapon, sizeof(the_weapon), "%s", arg);
		ReplaceString(the_weapon, sizeof(the_weapon), "weapon_", "");
		ReplaceString(the_weapon, sizeof(the_weapon), "item_", "");
		
		if (StrContains(the_weapon, "hegren", false) != -1 || StrContains(the_weapon, "flash", false) != -1 || StrContains(the_weapon, "smokegrenade", false) != -1 || StrContains(the_weapon, "molotov", false) != -1 || StrContains(the_weapon, "incgrenade", false) != -1 || StrContains(the_weapon, "decoy", false) != -1)
		{
			CPrintToChat(client, "%T", "Grenades Blocked", client, CHAT_PREFIX);
			return Plugin_Handled;
		}
	}
	
	return Plugin_Continue;
}

public Action ReadyList(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	int player_count;
	
	ReplyToCommand(client, "%T", "Ready System", client, CHAT_PREFIX);
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) > 1)
		{
			if (g_player_list[i] == PLAYER_UNREADY)	{
				ReplyToCommand(client, "%T", "Unready Player", client, CHAT_PREFIX, client);
				player_count++;
			}
		}
	}
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) > 1)
		{
			if (g_player_list[i] == PLAYER_READY)
			{
				ReplyToCommand(client, "%T", "Ready Player", client, CHAT_PREFIX, client);
				player_count++;
			}
		}
	}
	if (player_count == 0)
	{
		ReplyToCommand(client, "%T", "No Players Found", client, CHAT_PREFIX);
	}
	
	return Plugin_Handled;
}

public Action NotLive(int client, int args)
{ 
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	ResetHalf(false);
	
	if (client == 0)
	{
		PrintToServer("%t", "Half Reset Server", CHAT_PREFIX);
	}
	
	LogAction(client, -1, "\"half_reset\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action WarmUp(int client, int args)
{
	if (g_live)
	{
		if (client == 0)
		{
		PrintToServer("%t", "Match Is Live Server", CHAT_PREFIX);
		}
		else
		{
			CPrintToChat(client, "%T", "Match Is Live", client, CHAT_PREFIX);
		}
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char warmup_config[128];
	GetConVarString(wm_warmup_config, warmup_config, sizeof(warmup_config));
	ServerCommand("exec %s", warmup_config);
	ServerCommand("mp_warmup_start");
	
	if (client == 0)
	{
		PrintToServer("%t", "Warm Up Active Server", CHAT_PREFIX);
	}
	
	return Plugin_Handled;
}

public Action Practice(int client, int args)
{
	if (g_live)
	{
		if (client == 0)
		{
			PrintToServer("%t", "Match Is Live Server" , CHAT_PREFIX);
		}
		else
		{
			CPrintToChat(client, "%T", "Match Is Live", client, CHAT_PREFIX);
		}
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char prac_config[128];
	GetConVarString(wm_prac_config, prac_config, sizeof(prac_config));
	ServerCommand("exec %s", prac_config);
	
	if (client == 0)
	{
		PrintToServer("%t", "Practice Mode Active Server", CHAT_PREFIX);
	}

	return Plugin_Handled;
}

public Action CancelMatch(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	ResetMatch(false, false);
	
	if (client == 0)
	{
		PrintToServer("%t", "Match Reset", CHAT_PREFIX);
	}
	
	if(match_id != 0)	SendToDiscord(3);
	
	char warmup_config[128];
	GetConVarString(wm_warmup_config, warmup_config, sizeof(warmup_config));
	ServerCommand("mp_unpause_match 1"); //Prevent match is paused.
	ServerCommand("exec %s", warmup_config);
	ServerCommand("mp_warmup_start");
	
	LogAction(client, -1, "\"match_reset\" (player \"%L\")", client);
	
	ServerCommand("hostname %s", g_server);
	
	return Plugin_Handled;
}

public Action CancelKnife(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	if (g_t_knife)
	{
		char event_name[] = "knife_reset";
		LogSimpleEvent(event_name, sizeof(event_name));
		
		g_t_knife = false;
		g_t_had_knife = false;
		ServerCommand("mp_restartgame 1");
		for (int x = 1; x <= 3; x++)
		{
			CPrintToChatAll("%t", "Knife Round Cancelled", CHAT_PREFIX);
		}
		if (client == 0)
		{
			PrintToServer("%t", "Knife Round Cancelled Server", CHAT_PREFIX);
		}
	}
	else
	{
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Knife Round Inactive", client, CHAT_PREFIX);
		}
		else
		{
			PrintToServer("%t", "Knife Round Inactive Server", CHAT_PREFIX);
		}
	}
	
	UpdateStatus();
	
	LogAction(client, -1, "\"knife_reset\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

void ReadySystem(bool enable)
{
	if (enable)
	{
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_ready_system\", \"enabled\": true}");
		}
		else
		{
			LogEvent("{\"event\": \"ready_system\", \"enabled\": true}");
		}

		g_ready_enabled = true;
	}
	else
	{
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_ready_system\", \"enabled\": false}");
		}
		else
		{
			LogEvent("{\"event\": \"ready_system\", \"enabled\": false}");
		}

		g_ready_enabled = false;
	}
}

void ShowInfo(int client, bool enable, bool priv, int time)
{
	if (!IsActive(client, true))
	{
		return;
	}
	
	if (priv && g_cancel_list[client])
	{
		return;
	}
	
	if (!GetConVarBool(wm_show_info))
	{
		return;
	}
	
	if (!enable)
	{
		if (GetConVarBool(wm_ready_panel)) {
			g_m_ready_up = CreatePanel();
			char panel_title[128];
			Format(panel_title, sizeof(panel_title), "%t", "Ready System Disabled", CHAT_PREFIX, client);
			SetPanelTitle(g_m_ready_up, panel_title);
			
			for (int i = 1; i <= MaxClients; i++)
			{
				if (IsClientInGame(i) && !IsFakeClient(i))
				{
					SendPanelToClient(g_m_ready_up, i, Handler_DoNothing, time);
				}
			}
			
			CloseHandle(g_m_ready_up);
			
			UpdateStatus();
		} else {
			CPrintToChatAll("%t", "Ready System Disabled", CHAT_PREFIX);
		}
		
		return;
	}
	
	char players_unready[192];
	char player_name[64];
	char player_temp[192];
	
	for (int i = 1; i <= MaxClients; i++)
	{
		if (g_player_list[i] == PLAYER_UNREADY && IsClientInGame(i) && !IsFakeClient(i))
		{
			GetClientName(i, player_name, sizeof(player_name));
			if (GetConVarBool(wm_ready_panel)) {
				Format(player_temp, sizeof(player_temp), "  %s\n", player_name);
			} else {
				Format(player_temp, sizeof(player_temp), "  %s,", player_name);
			}
			StrCat(players_unready, sizeof(players_unready), player_temp);
			SetTagNotReady(i);
		}
	}
	
	if (priv)
	{
		DispInfo(client, players_unready, time);
	}
	else
	{
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsClientInGame(i) && !IsFakeClient(i) && !g_cancel_list[i])
			{
				if (GetConVarBool(wm_ready_panel)) {
					DispInfo(i, players_unready, time);
				} else if (!(GetConVarBool(wm_ready_panel)) && g_DispInfoLimiter) {
					g_DispInfoLimiter = false;
					DispInfo(i, players_unready, time);
					CreateTimer(30.0, DispInfoLimiterTrue);
				}
			}
		}
	}
	UpdateStatus();
}

void DispInfo(int client, char[] players_unready, int time)
{
	if (GetConVarBool(wm_ready_panel)) 
	{
		char Temp[128];
		SetGlobalTransTarget(client);
		g_m_ready_up = CreatePanel();
		Format(Temp, sizeof(Temp), "%t", "Ready System Panel");
		SetPanelTitle(g_m_ready_up, Temp);
		DrawPanelText(g_m_ready_up, "\n \n");
		Format(Temp, sizeof(Temp), "%t", "Match Begin Msg Panel", GetConVarInt(wm_min_ready));
		DrawPanelItem(g_m_ready_up, Temp);
		DrawPanelText(g_m_ready_up, "\n \n");	
		Format(Temp, sizeof(Temp), "%t", "Info Not Ready Panel");
		DrawPanelItem(g_m_ready_up, Temp);
		DrawPanelText(g_m_ready_up, players_unready);
		DrawPanelText(g_m_ready_up, " \n");
		Format(Temp, sizeof(Temp), "%t", "Exit Panel");
		DrawPanelItem(g_m_ready_up, Temp);
		SendPanelToClient(g_m_ready_up, client, Handler_ReadySystem, time);
		CloseHandle(g_m_ready_up);
	} 
	else if (!g_live) 
	{
		CPrintToChat(client, "%T", "Ready System", client, CHAT_PREFIX);
		CPrintToChat(client, "%T", "Match Begin Msg", client, CHAT_PREFIX, GetConVarInt(wm_min_ready));
		CPrintToChat(client, "%T", "Info Not Ready", client, CHAT_PREFIX, players_unready);
	}
}

void ReadyChangeAll(int client, bool up, bool silent)
{
	if (up)
	{
		char event_name[] = "ready_all";
		LogSimpleEvent(event_name, sizeof(event_name));
		
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && !IsFakeClient(i) &&  GetClientTeam(i) > 1)
			{
				g_player_list[i] = PLAYER_READY;
				SetTagReady(i);
			}
		}
	}
	else
	{
		char event_name[] = "unready_all";
		LogSimpleEvent(event_name, sizeof(event_name));
			
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && !IsFakeClient(i) &&  GetClientTeam(i) > 1)
			{
				g_player_list[i] = PLAYER_UNREADY;
				SetTagNotReady(i);
			}
		}
	}
	if (!silent)
	{
		ShowInfo(client, true, true, 0);
	}
}

bool IsReadyEnabled(int client, bool silent)
{
	if (g_ready_enabled)
	{
		return true;
	}
	else
	{
		if (!silent)
		{
			if (client != 0)
			{
				CPrintToChat(client, "%T", "Ready System Disabled2", client, CHAT_PREFIX);
			}
			else
			{
				PrintToServer("%t", "Ready System Disabled2 Console", CHAT_PREFIX);
			}
		}
	}
	return false;
}

bool IsLive(int client, bool silent)
{
	if (!g_live)
	{
		return false;
	}
	else
	{
		if (!silent)
		{
			if (client != 0)
			{
				CPrintToChat(client, "%T", "Match Is Live", client, CHAT_PREFIX);
			}
			else
			{
				PrintToServer("%t", "Match Is Live Server", CHAT_PREFIX);
			}
		}
	}
	return true;
}

bool IsActive(int client, bool silent)
{
	if (g_active)
	{
		return true;
	}
	else
	{
		if (!silent)
		{
			if (client != 0)
			{
				CPrintToChat(client, "%T", "WarMod Inactive", client, CHAT_PREFIX);
			}
			else
			{
				PrintToServer("%t", "WarMod Inactive Server", CHAT_PREFIX);
			}
		}
	}
	return false;
}

bool IsAdminCmd(int client, bool silent)
{
	if (client == 0 || !GetConVarBool(wm_rcon_only))
	{
		return true;
	}
	else
	{
		if (!silent)
		{
			CPrintToChat(client, "%T", "WarMod Rcon Only", client, CHAT_PREFIX);
		}
	}
	return false;
}

public void OnActiveChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (StringToInt(newVal) != 0)
	{
		g_active = true;
	}
	else
	{
		g_active = false;
	}
}

/*public OnReqNameChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	CheckReady();
}*/

public void OnMinReadyChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	if (!g_live && g_ready_enabled)
	{
		ShowInfo(0, true, false, 0);
	}
	
	if (!g_match && g_ready_enabled)
	{
		CheckReady();
	}
}

public void OnStatsTraceChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (g_stats_trace_timer != INVALID_HANDLE)
	{
		KillTimer(g_stats_trace_timer);
		g_stats_trace_timer = INVALID_HANDLE;
	}
	if (!StrEqual(newVal, "0", false))
	{
		g_stats_trace_timer = CreateTimer(GetConVarFloat(wm_stats_trace_delay), Stats_Trace, _, TIMER_REPEAT|TIMER_FLAG_NO_MAPCHANGE);
	}
}

public void OnStatsTraceDelayChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (g_stats_trace_timer != INVALID_HANDLE)
	{
		KillTimer(g_stats_trace_timer);
		g_stats_trace_timer = INVALID_HANDLE;
	}
	if (GetConVarBool(wm_stats_trace))
	{
		g_stats_trace_timer = CreateTimer(GetConVarFloat(wm_stats_trace_delay), Stats_Trace, _, TIMER_REPEAT|TIMER_FLAG_NO_MAPCHANGE);
	}
}

public void OnAutoReadyChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	if (!g_match && !g_ready_enabled && StrEqual(newVal, "1", false))
	{
		ReadySystem(true);
		ReadyChangeAll(0, false, true);
		SetAllCancelled(false);
		ShowInfo(0, true, false, 0);
	}
}

public void OnTChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (!StrEqual(newVal, ""))
	{
		Format(g_t_name, sizeof(g_t_name), "%s", newVal);
	}
	Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
	EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
	
	CheckReady();
}

public void OnCTChange(Handle cvar, const char[]oldVal, const char[]newVal)
{
	if (!StrEqual(newVal, ""))
	{
		Format(g_ct_name, sizeof(g_ct_name), "%s", newVal);
	}
	Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
	EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
	
	CheckReady();
}

public int Handler_ReadySystem(Handle menu, MenuAction action, int param1, int param2)
{
	if (action == MenuAction_Select)
	{
		if (param2 == 3)
		{
			g_cancel_list[param1] = true;
		}
	}
}

//Knife vote stay
public Action Stay(int client, int args)
{
	if ((g_knife_vote) && GetClientTeam(client) == g_knife_winner)
	{
		if (g_knife_winner == 2)
		{
			CPrintToChatAll("%t", "Knife Stay T", CHAT_PREFIX, g_t_name);
		}
		else
		{
			CPrintToChatAll("%t", "Knife Stay CT", CHAT_PREFIX, g_ct_name);
		}
		
		if (GetConVarBool(wm_knife_auto_start))
		{
			g_knife_winner = 0;
			g_knife_vote = false;
			ShowInfo(0, false, false, 1);
			SetAllCancelled(false);
			ReadySystem(false);
			LiveOn3(true);
		}
		else
		{
			CPrintToChatAll("%t", "Match Begin Msg", CHAT_PREFIX, GetConVarInt(wm_min_ready));
			ReadyChangeAll(0, false, true);
			SetAllCancelled(false);
			ReadySystem(true);
			ShowInfo(0, true, false, 0);
			g_knife_winner = 0;
			g_knife_vote = false;
			UpdateStatus();
		}
	}
	return Plugin_Handled;
}

//Knife vote switch
public Action Switch(int client, int args)
{
	if ((g_knife_vote) && GetClientTeam(client) == g_knife_winner)
	{
		if (g_knife_winner == 2)
		{
			CPrintToChatAll("%t", "Knife Switch T", CHAT_PREFIX, g_t_name);
		}
		else
		{
			CPrintToChatAll("%t", "Knife Switch CT", CHAT_PREFIX, g_ct_name);	
		}
		ServerCommand("mp_swapteams");
		
		char teamflag1[4];
		char teamlogo1[8];
		char teamname1[64];
		char teamflag2[4];
		char teamlogo2[8];
		char teamname2[64];
		
		GetConVarString(mp_teamname_1, teamname1, sizeof(teamname1));
		GetConVarString(mp_teamlogo_1, teamlogo1, sizeof(teamlogo1));
		GetConVarString(mp_teamflag_1, teamflag1, sizeof(teamflag1));
		GetConVarString(mp_teamname_2, teamname2, sizeof(teamname2));
		GetConVarString(mp_teamlogo_2, teamlogo2, sizeof(teamlogo2));
		GetConVarString(mp_teamflag_2, teamflag2, sizeof(teamflag2));
		
		if (GetConVarBool(wm_name_fix)) {
			if (!StrEqual(teamname1, DEFAULT_T_NAME, false) && !StrEqual(teamname1, DEFAULT_CT_NAME, false)) {
				ServerCommand("mp_teamname_2 %s", teamname1);
			} else {
				ServerCommand("mp_teamname_2 \"\"");
			}
			if (!StrEqual(teamflag1, "", false)) {
				ServerCommand("mp_teamflag_2 %s", teamflag1);
			} else {
				ServerCommand("mp_teamflag_2 \"\"");
			}
			if (!StrEqual(teamlogo1, "", false)) {
				ServerCommand("mp_teamlogo_2 %s", teamlogo1);
			} else {
				ServerCommand("mp_teamlogo_2 \"\"");
			}
			
			if (!StrEqual(teamname2, DEFAULT_T_NAME, false) && !StrEqual(teamname2, DEFAULT_CT_NAME, false)) {
				ServerCommand("mp_teamname_1 %s", teamname2);
			} else {
				ServerCommand("mp_teamname_1 \"\"");
			}	
			if (!StrEqual(teamflag2, "", false)) {
				ServerCommand("mp_teamflag_1 %s", teamflag2);
			} else {
				ServerCommand("mp_teamflag_1 \"\"");
			}
			if (!StrEqual(teamlogo2, "", false)) {
				ServerCommand("mp_teamlogo_1 %s", teamlogo2);
			} else {
				ServerCommand("mp_teamlogo_1 \"\"");
			}
		}
		
		if (GetConVarBool(wm_knife_auto_start))
		{
			g_knife_winner = 0;
			g_knife_vote = false;
			ShowInfo(0, false, false, 1);
			ReadySystem(false);
			LiveOn3(true);
		}
		else
		{
			CPrintToChatAll("%t", "Match Begin Msg", CHAT_PREFIX, GetConVarInt(wm_min_ready));
			ReadyChangeAll(0, false, true);
			SetAllCancelled(false);
			ReadySystem(true);
			ShowInfo(0, true, false, 0);
			g_knife_winner = 0;
			g_knife_vote = false;
			UpdateStatus();
		}
	}
	return Plugin_Handled;
}

public int Handler_DoNothing(Handle menu, MenuAction action, int param1, int param2)
{
	/* Do nothing */
}

public void SetAllCancelled(bool cancelled)
{
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && !IsFakeClient(i) && GetClientTeam(i) > 1)
		{
			g_cancel_list[i] = cancelled;
		}
	}
}

public Action ChangeT(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char name[64];
	
	if (GetCmdArgs() > 0)
	{
		GetCmdArgString(name, sizeof(name));
		char name_old[64];
		Format(name_old, sizeof(name_old), "%s", g_t_name);
		Format(g_t_name, sizeof(g_t_name), "%s", name);
		Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
		EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
		LogEvent("{\"event\": \"name_change\", \"team\": 2, \"old\": \"%s\", \"new\": \"%s\"}", name_old, g_t_name);
		ServerCommand("mp_teamname_2 %s", name);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Change T Name", client, CHAT_PREFIX, name);
		}
		else
		{
			PrintToServer("%t", "Change T Name Server", CHAT_PREFIX, name);
		}
		CheckReady();
		LogAction(client, -1, "\"set_t_name\" (player \"%L\") (name \"%s\")", client, name);
	}
	
	return Plugin_Handled;
}

public Action ChangeCT(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	char name[64];
	
	if (GetCmdArgs() > 0)
	{
		GetCmdArgString(name, sizeof(name));
		char name_old[64];
		Format(name_old, sizeof(name_old), "%s", g_ct_name);
		Format(g_ct_name, sizeof(g_ct_name), "%s", name);
		Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
		EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
		LogEvent("{\"event\": \"name_change\", \"team\": 3, \"old\": \"%s\", \"new\": \"%s\"}", name_old, g_ct_name);
		ServerCommand("mp_teamname_1 %s", name);
		if (client != 0)
		{
			CPrintToChat(client, "%T", "Change CT Name", client, CHAT_PREFIX, name);
		}
		else
		{
			PrintToServer("%t", "Change CT Name Server", CHAT_PREFIX, name);
		}
		CheckReady();
		LogAction(client, -1, "\"set_ct_name\" (player \"%L\") (name \"%s\")", client, name);
	}
	
	return Plugin_Handled;
}

/* Chat Commands */

#define ChatAlias(%1,%2) \
if (StrEqual(sArgs[0], %1, false)) { \
    %2 (client, 0); \
}
public Action OnClientSayCommand(int client, const char[]command, const char[]sArgs)
{
	ChatAlias(".ready", ReadyUp)
	ChatAlias(".r", ReadyUp)
	ChatAlias(".rdy", ReadyUp)
	ChatAlias(".unready", ReadyDown)
	ChatAlias(".ur", ReadyDown)
	ChatAlias(".urdy", ReadyDown)
	ChatAlias(".info", ReadyInfoPriv)
	ChatAlias(".i", ReadyInfoPriv)
	ChatAlias(".score", ShowScore)
	ChatAlias(".s", ShowScore)
	ChatAlias(".stay", Stay)
	ChatAlias(".switch", Switch)
	ChatAlias(".swap", Switch)
	ChatAlias(".pause", Pause)
	ChatAlias(".unpause", Unpause)
	ChatAlias(".help", DisplayHelp)
	ChatAlias(".veto", Veto_Setup)
	ChatAlias(".vetoBo1", Veto_Bo1)
	ChatAlias(".vetoBo2", Veto_Bo2)
	ChatAlias(".vetoBo3", Veto_Bo3)
	ChatAlias(".vetoBo5", Veto_Bo5)
	ChatAlias(".veto1", Veto_Bo1)
	ChatAlias(".veto2", Veto_Bo2)
	ChatAlias(".veto3", Veto_Bo3)
	ChatAlias(".veto5", Veto_Bo5)
	ChatAlias(".vetomaps", Veto_Bo3_Maps)
	ChatAlias(".playout", PlayOut_Offer)
	ChatAlias(".pl", PlayOut_Offer)
	ChatAlias(".hardprac", PlayOut_Offer)
	ChatAlias(".hp", PlayOut_Offer)
	ChatAlias(".overtime", OverTime_Offer)
	ChatAlias(".ot", OverTime_Offer)
	ChatAlias(".normal", Default_Offer)
	ChatAlias(".norm", Default_Offer)
	ChatAlias(".default", Default_Offer)
	ChatAlias(".def", Default_Offer)
	ChatAlias(".rws", CMD_RWS)
	ChatAlias(".balance", CMD_Balance)
	ChatAlias(".pug", CMD_Pug)
	
	/* admin pause */
	ChatAlias(".admpause", AdmPause)
	ChatAlias(".admunpause", AdmUnpause)
	ChatAlias(".ap", AdmUnpause)
	ChatAlias(".aup", AdmUnpause)
	
	/* rws */
	ChatAlias(".fb", CMD_ForceBalance)
	ChatAlias(".fp", CMD_ForcePug)
	
	ChatAlias(".call", CMD_CallPlayer)
	
	if (client == 0)
	{
		return Plugin_Continue;
	}
	
	bool teamOnly = false;
	if (StrEqual(command, "say_team", false))
	{
		teamOnly = true;
	}
	
	char message[192];
	strcopy(message, sizeof(message), sArgs);
	StripQuotes(message);
	
	if (StrEqual(message, ""))
	{
		// no message
		return Plugin_Continue;
	}
	
	char log_string[192];
	CS_GetLogString(client, log_string, sizeof(log_string));
	
	EscapeString(message, sizeof(message));
	if (g_t_knife)
	{
		LogEvent("{\"event\": \"knife_player_say\", \"round\": %i, \"player\": %s, \"message\": \"%s\", \"teamOnly\": %d}", g_round, log_string, message, teamOnly);
	}
	else
	{
		LogEvent("{\"event\": \"player_say\", \"round\": %i, \"player\": %s, \"message\": \"%s\", \"teamOnly\": %d}", g_round, log_string, message, teamOnly);
	}
	
	// continue normally
	return Plugin_Continue;
}

void SwitchScores()
{
	int temp;
	
	temp = g_scores[SCORE_T][SCORE_FIRST_HALF];
	g_scores[SCORE_T][SCORE_FIRST_HALF] = g_scores[SCORE_CT][SCORE_FIRST_HALF];
	g_scores[SCORE_CT][SCORE_FIRST_HALF] = temp;
	
	temp = g_scores[SCORE_T][SCORE_SECOND_HALF];
	g_scores[SCORE_T][SCORE_SECOND_HALF] = g_scores[SCORE_CT][SCORE_SECOND_HALF];
	g_scores[SCORE_CT][SCORE_SECOND_HALF] = temp;
	
	for (int i = 0; i <= g_overtime_count; i++)
	{
		temp = g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF];
		g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF] = g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF];
		g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF] = temp;
		
		temp = g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF];
		g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF] = g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF];
		g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF] = temp;
	}
}

void SwitchTeamNames()
{
	char temp[64];
	Format(temp, sizeof(temp), "%s", g_t_name);
	Format(g_t_name, sizeof(g_t_name), "%s", g_ct_name);
	Format(g_ct_name, sizeof(g_ct_name), "%s", temp);
	
	Format(g_t_name_escaped, sizeof(g_t_name_escaped), "%s", g_t_name);
	EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
	Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), "%s", g_ct_name);
	EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
}

public Action SwapAll(int client, int args)
{
	if (!IsActive(client, false))
	{
		// warmod is disabled
		return Plugin_Handled;
	}
	
	if (!IsAdminCmd(client, false))
	{
		// not allowed, rcon only
		return Plugin_Handled;
	}
	
	CS_SwapTeams();
	SwitchScores();
	
	if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) && !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
	{
		SwitchTeamNames();
	}
	else if (!StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
	{
		Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
		SwitchTeamNames();
	}
	else if (!StrEqual(g_t_name, DEFAULT_T_NAME, false))
	{
		Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
		SwitchTeamNames();
	}
	
	LogAction(client, -1, "\"team_swap\" (player \"%L\")", client);
	
	return Plugin_Handled;
}

public Action Timer_DelayedResetConfig(Handle timer) {
	char end_config[128];
	GetConVarString(wm_reset_config, end_config, sizeof(end_config));
	ServerCommand("exec %s", end_config);
}

public Action Swap(Handle timer)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	if (!g_live)
	{
		CS_SwapTeams();
	}
}

public Action DispInfoLimiterTrue(Handle timer)
{
	g_DispInfoLimiter = true;
	ShowInfo(0, true, false, 0);
}

public Action UpdateInfo(Handle timer)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	if (!g_live)
	{
		ShowInfo(0, true, false, 0);
	}
}

public Action StopRecord(Handle timer)
{
	if (!g_match)
	{
		// only stop if another match hasn't started
		if (GetConVarBool(wm_auto_record)) 
		{
			ServerCommand("tv_stoprecord");
		}
		if (g_bRecording && g_MatchComplete)
		{
			Handle hDataPack = CreateDataPack();
			CreateDataTimer(1.0, Timer_UploadDemo, hDataPack);
			WritePackString(hDataPack, g_sDemoPath);
			Format(g_sDemoPath, sizeof(g_sDemoPath), "");
		}
		CreateTimer(2.0, RecordingFalse);
	}
}

public Action RecordingFalse(Handle timer)
{
	g_bRecording = false;
}

/*
public Action LogFileUpload(Handle timer)
{
	if (!g_match)
	{
		if (g_bRecording && g_MatchComplete)
		{
			Handle hDataPackLog = CreateDataPack();
			//CreateDataTimer(1.0, Timer_UploadLog, hDataPackLog);
			WritePackString(hDataPackLog, g_sLogPath);
			Format(g_sLogPath, sizeof(g_sLogPath), "");
		}
	}
}
*/

public Action Timer_UploadDemo(Handle timer, Handle hDataPack)
{
	ResetPack(hDataPack);
	char sDemoPath[PLATFORM_MAX_PATH];
	ReadPackString(hDataPack, sDemoPath, sizeof(sDemoPath));
	Compressionbzip2(sDemoPath);
}

public void Compressionbzip2(char[] sDemoPath)
{
	char sBzipPath[PLATFORM_MAX_PATH];
	Format(sBzipPath, sizeof(sBzipPath), "%s.bz2", sDemoPath);
	BZ2_CompressFile(sDemoPath, sBzipPath, 9, CompressionComplete);
}

public int CompressionComplete(BZ_Error iError, char[] inFile, char[] outFile, any data)
{
	if (iError == BZ_OK)
	{
		LogMessage("%s compressed to %s", inFile, outFile);
		if(save_db)	UploadFile(outFile);
	}
	else
	{
		LogBZ2Error(iError);
		if(save_db)	UploadFile(inFile);
	}
}

void UploadFile(char [] filepath)
{
	Handle curl = curl_easy_init();

	if(curl == INVALID_HANDLE)
	{
		LogMessage("Unable to initalize curl!");
		return;
	}

	curl_easy_setopt_int(curl, CURLOPT_NOSIGNAL, 1);
	curl_easy_setopt_int(curl, CURLOPT_NOPROGRESS, 1);
	curl_easy_setopt_int(curl, CURLOPT_TIMEOUT, 90);
	curl_easy_setopt_int(curl, CURLOPT_CONNECTTIMEOUT, 45);
	curl_easy_setopt_int(curl, CURLOPT_VERBOSE, 0);
	curl_easy_setopt_int(curl, CURLOPT_UPLOAD, 1);
	curl_easy_setopt_function(curl, CURLOPT_READFUNCTION, FTP_ReadFunction);
	curl_easy_setopt_int(curl, CURLOPT_FTP_CREATE_MISSING_DIRS, CURLFTP_CREATE_DIR);

	if(!FileExists(filepath))
	{
		LogError("Demo doesn't exist to upload! File: %s", filepath);
		return;
	}

	if(g_hProccessingFile != INVALID_HANDLE)
	{
		delete g_hProccessingFile;
		g_hProccessingFile = INVALID_HANDLE;
	}

	g_hProccessingFile = OpenFile(filepath, "rb");

	char ftp_url[PLATFORM_MAX_PATH];
	Format(ftp_url, sizeof(ftp_url), "ftp://%s:%s@%s/%s", ftp_a, ftp_p, demoftp, filepath);
		
	DataPack pack = new DataPack();
	pack.WriteString(filepath);
	
	curl_easy_setopt_string(curl, CURLOPT_URL, ftp_url);
	curl_easy_perform_thread(curl, FTPUpload_Finished, pack);

	LogMessage("Start uploading demo to ftp");

	return;
}

public int FTP_ReadFunction(Handle curl, const int bytes, const int nmemb)
{
	if((bytes*nmemb) < 1)	return 0;

	if(IsEndOfFile(g_hProccessingFile))
	{
		delete g_hProccessingFile;
		g_hProccessingFile = INVALID_HANDLE;
		return 0;
	}

	int iBytesToRead = (bytes * nmemb);

	// This is slow as hell, but ReadFile always read 4 byte blocks, even though
	// it was told explicitely to read 'bytes' * 'nmemb' bytes.
	// XXX: Revisit this and try to do it right...
	char[] items = new char[iBytesToRead];
	int pos = 0;
	int cell = 0;
	for(; pos < iBytesToRead && ReadFileCell(g_hProccessingFile, cell, 1) == 1; pos++)
	{
		items[pos] = cell;
	}

	curl_set_send_buffer(curl, items, pos);

	return pos;
}

public int FTPUpload_Finished(Handle curl, CURLcode code, DataPack data)
{
	char filepath[512];
	data.Reset();
	data.ReadString(filepath, sizeof(filepath));
	
	if(code == CURLE_OK)
	{
		//DeleteFile(sDemoPath);
		LogMessage("Successfully uploaded demo to ftp.", filepath);
	}
	else
	{
		char error[256];
		curl_easy_strerror(code, error, sizeof(error));
		LogMessage("Failed to upload file %s, CURL error: %s", filepath, error);
		return 0;
	}

	delete g_hProccessingFile;

	return 0;
}

public void Cvar_Changed(Handle convar, const char[]oldValue, const char[]newValue)
{
	OnConfigsExecuted();
}

public int GetConVarValueInt(const char[]sConVar)
{
	Handle hConVar = FindConVar(sConVar);
	int iResult = GetConVarInt(hConVar);
	CloseHandle(hConVar);
	return iResult;
}

public Action HalfTime(Handle timer)
{
	// starts warmup for second half
	ServerCommand("mp_warmuptime 5000");
	ServerCommand("mp_warmup_start");
	ReadySystem(true);
	ShowInfo(0, true, false, 0);
}

stock void LogSimpleEvent(char[] event_name, int size)
{
	char json[384];
	EscapeString(event_name, size);
	
	Format(json, sizeof(json), "{\"event\": \"%s\"}", event_name);
	LogEvent("%s", json);
}

stock void LogEvent(const char[]format, any:...)
{
	char event[1024];
	VFormat(event, sizeof(event), format, 2);
	int stats_method = GetConVarInt(wm_stats_method);
	
	// inject timestamp into JSON object, hacky but quite simple
	char timestamp[64];
	FormatTime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S");
	
	// remove leading '{' from the event and add the timestamp in, including new '{'
	Format(event, sizeof(event), "{\"timestamp\": \"%s\", %s", timestamp, event[1]);
	
	if (g_log_live && (stats_method == 0 || stats_method == 2))
	{
		// standard server log files + udp stream
		LogToGame("[Warmod+] %s", event);
	}
	
	if ((stats_method == 1 || stats_method == 2) && g_log_file != INVALID_HANDLE)
	{
		WriteFileLine(g_log_file, event);
	}
	
	if (LibraryExists("livewire"))
	{
		Call_StartForward(g_f_livewire_log_event);
		Call_PushString(event);
		Call_Finish();
	}
}

void LogPlayers()
{
	char ip_address[32];
	char country[2];
	char log_string[384];
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i))
		{
			GetClientIP(i, ip_address, sizeof(ip_address));
			GeoipCode2(ip_address, country);
			CS_GetLogString(i, log_string, sizeof(log_string));
			
			EscapeString(ip_address, sizeof(ip_address));
			EscapeString(country, sizeof(country));
			
			LogEvent("{\"event\": \"player_status\", \"player\": %s, \"address\": \"%s\", \"country\": \"%s\"}", log_string, ip_address, country);
		}
	}
}

public Action Stats_Trace(Handle timer)
{
	char log_string[384];
	char  weapon[64];
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && GetClientTeam(i) > 1 && IsPlayerAlive(i))
		{
			CS_GetAdvLogString(i, log_string, sizeof(log_string));
			GetClientWeapon(i, weapon, 64);
			ReplaceString(weapon, 64, "weapon_", "");
			if (StrEqual(weapon, "m4a1"))
			{
				int iWeapon = GetPlayerWeaponSlot(i, CS_SLOT_PRIMARY);
				int pWeapon = GetEntProp(iWeapon, Prop_Send, "m_iItemDefinitionIndex");
				if (pWeapon == 60)
				{
					weapon = "m4a1_silencer";
				}
			}
			else if (StrEqual(weapon, "hkp2000") || StrEqual(weapon, "p250") || StrEqual(weapon, "fiveseven") || StrEqual(weapon, "tec9") || StrEqual(weapon, "deagle"))
			{
				int iWeapon = GetPlayerWeaponSlot(i, CS_SLOT_SECONDARY);
				int pWeapon = GetEntProp(iWeapon, Prop_Send, "m_iItemDefinitionIndex");
				if (pWeapon == 61)
				{
					weapon = "usp_silencer";
				}
				else if (pWeapon == 63)
				{
					weapon = "cz75a";
				}
				else if (pWeapon == 64)
				{
					weapon = "revolver";
				}
			} else if (StrEqual(weapon, "knife_t") || StrEqual(weapon, "knife_default_ct") || StrEqual(weapon, "knife_flip") || StrEqual(weapon, "knife_gut") || StrEqual(weapon, "knife_karambit") || StrEqual(weapon, "knife_m9_bayonet") || StrEqual(weapon, "knife_tactical") || StrEqual(weapon, "knife_falchion") || StrEqual(weapon, "knife_butterfly") || StrEqual(weapon, "knife_push") || StrEqual(weapon, "bayonet") || StrEqual(weapon, "knifegg")) {
				int iWeapon = GetPlayerWeaponSlot(i, CS_SLOT_KNIFE);
				int pWeapon = GetEntProp(iWeapon, Prop_Send, "m_iItemDefinitionIndex");
				if (pWeapon == 500 || pWeapon == 505 || pWeapon == 506 || pWeapon == 507 || pWeapon == 508 || pWeapon == 509 || pWeapon == 512 || pWeapon == 515 || pWeapon == 516 || pWeapon == 59 || pWeapon == 42)
				{
					weapon = "knife";
				}
			}
			
			if (StrEqual(weapon, "m4a1_silencer_off")) {
				weapon = "m4a1_silencer";
			} else if (StrEqual(weapon, "usp_silencer_off")) {
				weapon = "usp_silencer";
			} else if (StrEqual(weapon, "knife_t") || StrEqual(weapon, "knife_default_ct") || StrEqual(weapon, "knife_flip") || StrEqual(weapon, "knife_gut") || StrEqual(weapon, "knife_karambit") || StrEqual(weapon, "knife_m9_bayonet") || StrEqual(weapon, "knife_tactical") || StrEqual(weapon, "knife_falchion") || StrEqual(weapon, "knife_butterfly") || StrEqual(weapon, "knife_push") || StrEqual(weapon, "bayonet") || StrEqual(weapon, "knifegg")) {
				weapon = "knife";
			}
				
			if (g_t_knife)
			{
				LogEvent("{\"event\": \"knife_player_trace\", \"round\": %i, \"player\": %s, \"weapon\": \"%s\"}", g_round, log_string, weapon);
			}
			else
			{
				LogEvent("{\"event\": \"player_trace\", \"round\": %i, \"player\": %s, \"weapon\": \"%s\"}", g_round, log_string, weapon);
			}
		}
	}
}

public Action RenameLogs(Handle timer)
{
	char save_dir[128];
	GetConVarString(wm_save_dir, save_dir, sizeof(save_dir));
	if (g_log_file != INVALID_HANDLE)
	{
		FlushFile(g_log_file);
		CloseHandle(g_log_file);
		g_log_file = INVALID_HANDLE;
		char old_log_filename[128];
		char new_log_filename[128];
		if (g_log_warmod_dir)
		{
			Format(old_log_filename, sizeof(old_log_filename), "%s/_%s.log", save_dir, g_log_filename);
			Format(new_log_filename, sizeof(new_log_filename), "%s/%s.log", save_dir, g_log_filename);
		}
		else if (DirExists("logs"))
		{
			Format(old_log_filename, sizeof(old_log_filename), "logs/_%s.log", g_log_filename);
			Format(new_log_filename, sizeof(new_log_filename), "logs/%s.log", g_log_filename);
		}
		else
		{
			Format(old_log_filename, sizeof(old_log_filename), "_%s.log", g_log_filename);
			Format(new_log_filename, sizeof(new_log_filename), "%s.log", g_log_filename);
		}
		
		RenameFile(new_log_filename, old_log_filename);
		Format(g_sLogPath, sizeof(g_sLogPath), new_log_filename);
	}
	CreateTimer(15.0, RenameDemos);
}

public Action RenameDemos(Handle timer)
{
	char save_dir[128];
	GetConVarString(wm_save_dir, save_dir, sizeof(save_dir));
	char old_demo_filename[128];
	char new_demo_filename[128];
	if (g_log_warmod_dir)
	{
		Format(old_demo_filename, sizeof(old_demo_filename), "%s/_%s.dem", save_dir, g_log_filename);
		Format(new_demo_filename, sizeof(new_demo_filename), "%s/%s.dem", save_dir, g_log_filename);
	}
	else
	{
		Format(old_demo_filename, sizeof(old_demo_filename), "_%s.dem", g_log_filename);
		Format(new_demo_filename, sizeof(new_demo_filename), "%s.dem", g_log_filename);	
	}
	RenameFile(new_demo_filename, old_demo_filename);
	Format(g_sDemoPath, sizeof(g_sDemoPath), new_demo_filename);
}

void ResetPlayerStats(int client)
{
	for (int i = 0; i < NUM_WEAPONS; i++)
	{
		for (int x = 0; x < LOG_HIT_NUM; x++)
		{
			weapon_stats[client][i][x] = 0;
		}
	}
	for (int z = 0; z < ASSIST_NUM; z++)
	{
		assist_stats[client][z] = 0;
	}
}

void LogClutchStats(int client)
{
	if (WasClientInGame[client] && WasClientTeam[client] > 1)
	{
		if (clutch_stats[client][CLUTCH_LAST] == 1)
		{
			match_stats[client][MATCH_LAST]++;
		}
	}
}

void ResetClutchStats()
{
	for (int i = 1; i <= MaxClients; i++)
	{
		for (int z = 0; z < CLUTCH_NUM; z++)
		{
			clutch_stats[i][z] = 0;
			round_health[i] = 100;
		}
		
		for (int j = 1; j <= MaxClients; j++) 
		{
            g_DamageDone[i][j] = 0;
            g_DamageDoneHits[i][j] = 0;
            g_GotKill[i][j] = false;
        }
	}
}

void LogPlayerStats(int client)
{
	if (!IsActive(0, true))	return;
	
	if (IsClientInGame(client) && GetClientTeam(client) > 1)
	{
		if (WasClientInGame[client] && WasClientTeam[client] > 1)
		{
			if (round_kills[client] == 1)
			{
				match_stats[client][MATCH_1K]++;
			}
			else if (round_kills[client] == 2)
			{
				match_stats[client][MATCH_2K]++;
			}
			else if (round_kills[client] == 3)
			{
				match_stats[client][MATCH_3K]++;
			}
			else if (round_kills[client] == 4)
			{
				match_stats[client][MATCH_4K]++;
			}
			else if (round_kills[client] == 5)
			{
				match_stats[client][MATCH_5K]++;
			}
		}
		
		char log_string[384];
		CS_GetLogString(client, log_string, sizeof(log_string));
		
		char player_name[64];
		char authid[20];
		GetClientName(client, player_name, sizeof(player_name));
		GetClientAuthId(client, AuthId_SteamID64, authid, sizeof(authid));
		for (int i = 0; i < NUM_WEAPONS; i++)
		{
			if (weapon_stats[client][i][LOG_HIT_SHOTS] > 0 || weapon_stats[client][i][LOG_HIT_DEATHS] > 0)
			{
				if (g_t_knife)
				{
					LogEvent("{\"event\": \"knife_weapon_stats\", \"round\": %i, \"player\": %s, \"weapon\": \"%s\", \"shots\": %d, \"hits\": %d, \"kills\": %d, \"headshots\": %d, \"tks\": %d, \"damage\": %d, \"deaths\": %d, \"head\": %d, \"chest\": %d, \"stomach\": %d, \"leftArm\": %d, \"rightArm\": %d, \"leftLeg\": %d, \"rightLeg\": %d, \"generic\": %d}", g_round, log_string, weapon_list[i], weapon_stats[client][i][LOG_HIT_SHOTS], weapon_stats[client][i][LOG_HIT_HITS], weapon_stats[client][i][LOG_HIT_KILLS], weapon_stats[client][i][LOG_HIT_HEADSHOTS], weapon_stats[client][i][LOG_HIT_TEAMKILLS], weapon_stats[client][i][LOG_HIT_DAMAGE], weapon_stats[client][i][LOG_HIT_DEATHS], weapon_stats[client][i][LOG_HIT_HEAD], weapon_stats[client][i][LOG_HIT_CHEST], weapon_stats[client][i][LOG_HIT_STOMACH], weapon_stats[client][i][LOG_HIT_LEFTARM], weapon_stats[client][i][LOG_HIT_RIGHTARM], weapon_stats[client][i][LOG_HIT_LEFTLEG], weapon_stats[client][i][LOG_HIT_RIGHTLEG], weapon_stats[client][i][LOG_HIT_GENERIC]);
				}
				else
				{
					LogEvent("{\"event\": \"weapon_stats\", \"round\": %i, \"player\": %s, \"weapon\": \"%s\", \"shots\": %d, \"hits\": %d, \"kills\": %d, \"headshots\": %d, \"tks\": %d, \"damage\": %d, \"deaths\": %d, \"head\": %d, \"chest\": %d, \"stomach\": %d, \"leftArm\": %d, \"rightArm\": %d, \"leftLeg\": %d, \"rightLeg\": %d, \"generic\": %d}", g_round, log_string, weapon_list[i], weapon_stats[client][i][LOG_HIT_SHOTS], weapon_stats[client][i][LOG_HIT_HITS], weapon_stats[client][i][LOG_HIT_KILLS], weapon_stats[client][i][LOG_HIT_HEADSHOTS], weapon_stats[client][i][LOG_HIT_TEAMKILLS], weapon_stats[client][i][LOG_HIT_DAMAGE], weapon_stats[client][i][LOG_HIT_DEATHS], weapon_stats[client][i][LOG_HIT_HEAD], weapon_stats[client][i][LOG_HIT_CHEST], weapon_stats[client][i][LOG_HIT_STOMACH], weapon_stats[client][i][LOG_HIT_LEFTARM], weapon_stats[client][i][LOG_HIT_RIGHTARM], weapon_stats[client][i][LOG_HIT_LEFTLEG], weapon_stats[client][i][LOG_HIT_RIGHTLEG], weapon_stats[client][i][LOG_HIT_GENERIC]);
				}
			}
		}
		int round_stats[LOG_HIT_NUM];
		for (int i = 0; i < NUM_WEAPONS; i++)
		{
			for (int x = 0; x < LOG_HIT_NUM; x++)
			{
				round_stats[x] += weapon_stats[client][i][x];
			}
		}
		if (g_t_knife)
		{
			LogEvent("{\"event\": \"knife_round_stats\", \"round\": %i, \"player\": %s, \"shots\": %d, \"hits\": %d, \"kills\": %d, \"headshots\": %d, \"tks\": %d, \"damage\": %d, \"assists\": %d, \"assists_tk\": %d, \"deaths\": %d, \"head\": %d, \"chest\": %d, \"stomach\": %d, \"leftArm\": %d, \"rightArm\": %d, \"leftLeg\": %d, \"rightLeg\": %d, \"generic\": %d}", g_round, log_string, round_stats[LOG_HIT_SHOTS], round_stats[LOG_HIT_HITS], round_stats[LOG_HIT_KILLS], round_stats[LOG_HIT_HEADSHOTS], round_stats[LOG_HIT_TEAMKILLS], round_stats[LOG_HIT_DAMAGE], assist_stats[client][ASSIST_COUNT],  assist_stats[client][ASSIST_COUNT_TK], round_stats[LOG_HIT_DEATHS], round_stats[LOG_HIT_HEAD], round_stats[LOG_HIT_CHEST], round_stats[LOG_HIT_STOMACH], round_stats[LOG_HIT_LEFTARM], round_stats[LOG_HIT_RIGHTARM], round_stats[LOG_HIT_LEFTLEG], round_stats[LOG_HIT_RIGHTLEG], round_stats[LOG_HIT_GENERIC]);
		}
		else
		{
			LogEvent("{\"event\": \"round_stats\", \"round\": %i, \"player\": %s, \"shots\": %d, \"hits\": %d, \"kills\": %d, \"headshots\": %d, \"tks\": %d, \"damage\": %d, \"assists\": %d, \"assists_tk\": %d, \"deaths\": %d, \"head\": %d, \"chest\": %d, \"stomach\": %d, \"leftArm\": %d, \"rightArm\": %d, \"leftLeg\": %d, \"rightLeg\": %d, \"generic\": %d}", g_round, log_string, round_stats[LOG_HIT_SHOTS], round_stats[LOG_HIT_HITS], round_stats[LOG_HIT_KILLS], round_stats[LOG_HIT_HEADSHOTS], round_stats[LOG_HIT_TEAMKILLS], round_stats[LOG_HIT_DAMAGE], assist_stats[client][ASSIST_COUNT],  assist_stats[client][ASSIST_COUNT_TK], round_stats[LOG_HIT_DEATHS], round_stats[LOG_HIT_HEAD], round_stats[LOG_HIT_CHEST], round_stats[LOG_HIT_STOMACH], round_stats[LOG_HIT_LEFTARM], round_stats[LOG_HIT_RIGHTARM], round_stats[LOG_HIT_LEFTLEG], round_stats[LOG_HIT_RIGHTLEG], round_stats[LOG_HIT_GENERIC]);
		}
		if (clutch_stats[client][CLUTCH_LAST] == 1)
		{
			if (g_t_knife)
			{
				LogEvent("{\"event\": \"knife_player_clutch\", \"round\": %i, \"player\": %s, \"versus\": %d, \"frags\": %d, \"bombPlanted\": %d, \"won\": %d}", g_round, log_string, clutch_stats[client][CLUTCH_VERSUS], clutch_stats[client][CLUTCH_FRAGS], g_planted, clutch_stats[client][CLUTCH_WON]);
			}
			else
			{
				LogEvent("{\"event\": \"player_clutch\", \"round\": %i, \"player\": %s, \"versus\": %d, \"frags\": %d, \"bombPlanted\": %d, \"won\": %d}", g_round, log_string, clutch_stats[client][CLUTCH_VERSUS], clutch_stats[client][CLUTCH_FRAGS], g_planted, clutch_stats[client][CLUTCH_WON]);
			}	
		}
		
		ResetPlayerStats(client);
	}
}

int GetWeaponIndex(const char[]weapon)
{
	for (int i = 0; i < NUM_WEAPONS; i++)
	{
		if (StrEqual(weapon, weapon_list[i], false))
		{
			return i;
		}
	}
	return -1;
}

public int MenuHandler(Handle topmenu, TopMenuAction action, TopMenuObject object_id, int param, char[] buffer, int maxlength)
{
	char menu_name[256];
	GetTopMenuObjName(topmenu, object_id, menu_name, sizeof(menu_name));
	SetGlobalTransTarget(param);
	
	if (StrEqual(menu_name, "WarModCommands"))
	{
		if (action == TopMenuAction_DisplayTitle)
		{
			Format(buffer, maxlength, "%t:", "Admin_Menu WarMod Commands");
		}
		else if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu WarMod Commands");
		}
	}
	else if (StrEqual(menu_name, "forcestart"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Force Start");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ForceStart(param, 0);
		}
	}
	else if (StrEqual(menu_name, "readyup"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			if (g_ready_enabled)
			{
				Format(buffer, maxlength, "%t", "Admin_Menu Disable ReadyUp");
			}
			else
			{
				Format(buffer, maxlength, "%t", "Admin_Menu Enable ReadyUp");
			}
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ReadyToggle(param, 0);
		}
	}
	else if (StrEqual(menu_name, "knife"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Knife");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			KnifeOn3(param, 0);
		}
	}
	else if (StrEqual(menu_name, "cancelhalf"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Cancel Half");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			NotLive(param, 0);
		}
	}
	else if (StrEqual(menu_name, "cancelmatch"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Cancel Match");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			CancelMatch(param, 0);
		}
	}
	else if (StrEqual(menu_name, "forceallready"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu ForceAllReady");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ForceAllReady(param, 0);
		}
	}
	else if (StrEqual(menu_name, "forceallunready"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu ForceAllUnready");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ForceAllUnready(param, 0);
		}
	}
	else if (StrEqual(menu_name, "forceallspectate"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu ForceAllSpectate");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ForceAllSpectate(param, 0);
		}
	}
	else if (StrEqual(menu_name, "toggleactive"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			if (GetConVarBool(wm_active))
			{
				Format(buffer, maxlength, "%t", "Admin_Menu Deactivate WarMod");
			}
			else
			{
				Format(buffer, maxlength, "%t", "Admin_Menu Activate WarMod");
			}
		}
		else if (action == TopMenuAction_SelectOption)
		{
			ActiveToggle(param, 0);
		}
	}
	
	else if (StrEqual(menu_name, "admpause"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Pause");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			AdmPause(param, 0);
		}
	}
	
	else if (StrEqual(menu_name, "admunpause"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Unpause");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			AdmUnpause(param, 0);
		}
	}
	
	else if (StrEqual(menu_name, "fb"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Force Balance");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			CMD_ForceBalance(param, 0);
		}
	}
	else if (StrEqual(menu_name, "fp"))
	{
		if (action == TopMenuAction_DisplayOption)
		{
			Format(buffer, maxlength, "%t", "Admin_Menu Force Pug");
		}
		else if (action == TopMenuAction_SelectOption)
		{
			CMD_ForcePug(param, 0);
		}
	}
}

public Action RestartRound(Handle timer, any delay)
{
	ServerCommand("mp_restartgame %d", delay);
}

//PrintToChatDelayed???
public Action PrintToChatDelayed(Handle timer, Handle datapack)
{
	char text[128];
	ResetPack(datapack);
	ReadPackString(datapack, text, sizeof(text));
	ServerCommand("say %s", text);
}

public Action RespawnPlayer(Handle timer, int client)
{
	if (IsClientInGame(client))
	{
		int team = GetClientTeam(client);
		if (IsClientInGame(client) && (team == CS_TEAM_CT || team == CS_TEAM_T))
		{
			CS_RespawnPlayer(client);
			SetEntData(client, g_iAccount, GetConVarInt(mp_startmoney));
		}
	}
}

public Action HelpText(Handle timer, int client)
{
	if (!IsActive(0, true))
	{
		return Plugin_Handled;
	}
	
	if (!g_live && g_ready_enabled)
	{
		DisplayHelp(client, 0);
	}
	
	return Plugin_Handled;
}

public Action DisplayHelp(int client, int args)
{
	if (client == 0)
	{
		PrintHintTextToAll("%t", "Available Commands");
	}
	else
	{
		if (IsClientConnected(client) && IsClientInGame(client))
		{
			PrintHintText(client, "%t", "Available Commands");
		}
	}
	return Plugin_Handled;
}

/*
public Action Advert(Handle timer, int client)
{
	if (!IsActive(0, true))
	{
		return Plugin_Handled;
	}
	
	if (!g_live)
	{
		switch (GetRandomInt(1, 2))
		{
			case 1:
			{
				CPrintToChatAll("%t", "Open Beta 1", CHAT_PREFIX);
			}
			case 2:
			{
				CPrintToChatAll("%t", "Open Beta 2", CHAT_PREFIX);
			}
		}
	}
	return Plugin_Handled;
}
*/

public Action ShowPluginInfo(Handle timer, int client)
{
	if (client != 0 && IsClientConnected(client) && IsClientInGame(client))
	{
		char max_rounds[64];
		GetConVarName(mp_maxrounds, max_rounds, sizeof(max_rounds));
		char min_ready[64];
		GetConVarName(wm_min_ready, min_ready, sizeof(min_ready));
		PrintToConsole(client, "===============================================================================");
		PrintToConsole(client, "%t","Console Info 1" , WM_VERSION);
		PrintToConsole(client, "");
		PrintToConsole(client, "%t","Console Info 2");
		PrintToConsole(client, "");
		PrintToConsole(client, "%t","Console Info 3");
		PrintToConsole(client, "%t","Console Info 4");
		PrintToConsole(client, "%t","Console Info 5");
		PrintToConsole(client, "%t","Console Info 6");
		PrintToConsole(client, "%t","Console Info 7");
		PrintToConsole(client, "%t","Console Info 8");
		PrintToConsole(client, "%t","Console Info 9");
		PrintToConsole(client, "%t","Console Info 10");
		PrintToConsole(client, "%t","Console Info 11");
		PrintToConsole(client, "");
		PrintToConsole(client, "%t","Console Info 12", max_rounds, GetConVarInt(mp_maxrounds), min_ready, GetConVarInt(wm_min_ready), GetConVarInt(mp_match_can_clinch));
		PrintToConsole(client, "===============================================================================");
	}
}

public Action WMVersion(int client, int args)
{
	//這個有需要翻譯嗎@@
	if (client == 0)
	{
		PrintToServer("\"wm_version\" = \"%s\"\n - [Warmod+] %s", WM_VERSION, WM_DESCRIPTION);
	}
	else
	{
		PrintToConsole(client, "\"wm_version\" = \"%s\"\n - [Warmod+] %s", WM_VERSION, WM_DESCRIPTION);
	}
	
	return Plugin_Handled;
}

int UpdateStatus()
{
	int value;
	if (!g_match)
	{
		if (!g_t_knife)
		{
			if (!g_ready_enabled)
			{
				if (!g_t_had_knife)
				{
					value = 0;
				}
				else
				{
					value = 3;
				}
			}
			else
			{
				if (!g_t_had_knife && GetConVarBool(wm_auto_knife))
				{
					value = 1;
				}
				else
				{
					value = 4;
				}
			}
		}
		else
		{
			value = 2;
		}
	}
	else
	{
		if (!g_overtime)
		{
			if (!g_live)
			{
				if (!g_ready_enabled)
				{
					if (g_first_half)
					{
						value = 3;
					}
					else
					{
						value = 6;
					}
				}
				else
				{
					if (g_first_half)
					{
						value = 4;
					}
					else
					{
						value = 7;
					}
				}
			}
			else
			{
				if (g_first_half)
				{
					value = 5;
				}
				else
				{
					value = 8;
				}
			}
		}
		else
		{
			if (!g_live)
			{
				if (!g_ready_enabled)
				{
					value = 9;
				}
				else
				{
					value = 10;
				}
			}
			else
			{
				if (g_first_half)
				{
					value = 11 + (g_overtime_count * 2);
				}
				else
				{
					value = 12 + (g_overtime_count * 2);
				}
			}
		}
	}
	//	SetConVarIntHidden(g_h_status, value);
	return value;
}

bool isNumeric(char[] argstring)
{
	int argeLength = strlen(argstring);
	for (int i = 0; i < argeLength; i++)
	{
		if (!IsCharNumeric(argstring[i]))
		{
			return false;
		}
	}
	return true;
}

/* Veto Code */
public Action Veto_Bo3_Maps(int client, int args)
{
	if (g_veto_bo3_active) // Bo3
	{
		if (g_ChosenMapBo3[0] == -1)
		{
			int bo = 3;
			CPrintToChatAll("%t", "Veto No Maps", CHAT_PREFIX, bo);
			g_veto_bo3_active = false;
			return Plugin_Handled;
		}
		char map[3][PLATFORM_MAX_PATH];
		for (int i = 0; i <= 2; i++)
		{
			GetArrayString(g_MapNames, g_ChosenMapBo3[i], map[i], PLATFORM_MAX_PATH);
		}
		CPrintToChatAll("%t", "Veto Bo3 Map List", CHAT_PREFIX, map[0], map[1], map[2]);
	}
	else if (g_veto_bo2_active) // Bo2
	{
		if (g_ChosenMapBo2[0] == -1)
		{
			int bo = 2;
			CPrintToChatAll("%t", "Veto No Maps", CHAT_PREFIX, bo);
			g_veto_bo2_active = false;
			return Plugin_Handled;
		}
		char map[2][PLATFORM_MAX_PATH];
		for (int i = 0; i <= 1; i++)
		{
			GetArrayString(g_MapNames, g_ChosenMapBo2[i], map[i], PLATFORM_MAX_PATH);
		}
		CPrintToChatAll("%t", "Veto Bo2 Map List", CHAT_PREFIX, map[0], map[1]);
	}
	else if (g_veto_bo5_active) // Bo5
	{
		if (g_ChosenMapBo5[0] == -1)
		{
			int bo = 5;
			CPrintToChatAll("%t", "Veto No Maps", CHAT_PREFIX, bo);
			g_veto_bo5_active = false;
			return Plugin_Handled;
		}
		char map[5][PLATFORM_MAX_PATH];
		for (int i = 0; i <= 4; i++)
		{
			GetArrayString(g_MapNames, g_ChosenMapBo5[i], map[i], PLATFORM_MAX_PATH);
		}
		CPrintToChatAll("%t", "Veto Bo5 Map List", CHAT_PREFIX, map[0], map[1], map[2], map[3], map[4]);
	}
	return Plugin_Handled;
}

public Action VetoTimeout(Handle timer)
{
	g_h_stored_timer_v = INVALID_HANDLE;
	CPrintToChatAll("%t", "Veto Offer Not Confirmed", CHAT_PREFIX);
	veto_offer_t = false;
	veto_offer_ct = false;
}

public Action Veto_Bo1(int client, int args)
{
	if (client == 0)
	{
		SetConVarInt(wm_veto, 1);
		Veto_Admin_Setup(client, args);
		return Plugin_Handled;
	}
	
	if (GetConVarInt(wm_veto) == 0)
	{
		CPrintToChat(client, "%T", "Veto Disabled", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_veto_active)
	{
		CPrintToChat(client, "%T", "Veto Active Already", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Veto", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	SetConVarInt(wm_veto, 1);
	Veto_Setup(client, args);
	return Plugin_Handled;
}

public Action Veto_Bo2(int client, int args)
{
	if (client == 0)
	{
		SetConVarInt(wm_veto, 2);
		Veto_Admin_Setup(client, args);
		return Plugin_Handled;
	}
	
	if (GetConVarInt(wm_veto) == 0)
	{
		CPrintToChat(client, "%T", "Veto Disabled", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_veto_active)
	{
		CPrintToChat(client, "%T", "Veto Active Already", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Veto", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	SetConVarInt(wm_veto, 2);
	Veto_Setup(client, args);
	return Plugin_Handled;
}

public Action Veto_Bo3(int client, int args)
{
	if (client == 0)
	{
		SetConVarInt(wm_veto, 3);
		Veto_Admin_Setup(client, args);
		return Plugin_Handled;
	}
	
	if (GetConVarInt(wm_veto) == 0)
	{
		CPrintToChat(client, "%T", "Veto Disabled", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_veto_active)
	{
		CPrintToChat(client, "%T", "Veto Active Already", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Veto", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	SetConVarInt(wm_veto, 3);
	Veto_Setup(client, args);
	return Plugin_Handled;
}

public Action Veto_Bo5(int client, int args)
{
	if (client == 0)
	{
		SetConVarInt(wm_veto, 5);
		Veto_Admin_Setup(client, args);
		return Plugin_Handled;
	}
	
	if (GetConVarInt(wm_veto) == 0)
	{
		CPrintToChat(client, "%T", "Veto Disabled", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_veto_active)
	{
		CPrintToChat(client, "%T", "Veto Active Already", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Veto", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	SetConVarInt(wm_veto, 5);
	Veto_Setup(client, args);
	return Plugin_Handled;
}

public Action Veto_Setup(int client, int args)
{
	if (client == 0)
	{
		Veto_Admin_Setup(client, args);
		return Plugin_Handled;
	}
	
	if (GetConVarInt(wm_veto) == 0)
	{
		CPrintToChat(client, "%T", "Veto Disabled", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_veto_active)
	{
		CPrintToChat(client, "%T", "Veto Active Already", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Veto", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (GetClientTeam(client) == 2)
	{
		if (veto_offer_t)
		{
			return Plugin_Handled;
		}
		veto_offer_t = true;
	}
	else if (GetClientTeam(client) == 3)
	{
		if (veto_offer_ct)
		{
			return Plugin_Handled;
		}
		veto_offer_ct = true;
	}
	else
	{
		CPrintToChat(client, "%T", "Veto Non-player", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	int vetonum = GetConVarInt(wm_veto);
	
	if (veto_offer_ct && veto_offer_t)
	{
		if (g_h_stored_timer_v != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer_v);
			g_h_stored_timer_v = INVALID_HANDLE;
		}
		g_veto_bo2_active = false;
		g_veto_bo3_active = false;
		g_veto_bo5_active = false;
		g_bo3_count = -1;
		g_bo5_count = -1;
		g_ChosenMapBo5[0] = -1;
		g_ChosenMapBo3[0] = -1;
		g_ChosenMapBo2[0] = -1;
		g_veto_map_number = 0;
		veto_offer_ct = false;
		veto_offer_t = false;
		g_veto_active = true;
		
		if (GetConVarBool(wm_veto_knife))
		{
			char veto_config[64];
			GetConVarString(wm_veto_config, veto_config, sizeof(veto_config));
	
			ServerCommand("exec gamemode_competive_server");
			ServerCommand("exec %s", veto_config);
			
			g_t_knife = true;
			g_t_veto = true;
			CPrintToChatAll("%t", "Veto Knife", CHAT_PREFIX);
			KnifeOn3Override();
		}
		else
		{
			ServerCommand("mp_pause_match 1");
			SetCaptains();
			CreateMapVeto(3);
		}
	}
	else if (veto_offer_ct && !veto_offer_t)
	{
		CPrintToChatAll("%t", "Veto Offer CT", CHAT_PREFIX, vetonum);
		DisplayVetoOffer(CS_TEAM_CT);
		g_h_stored_timer_v = CreateTimer(30.0, VetoTimeout);
		return Plugin_Handled;
	}
	else if (!veto_offer_ct && veto_offer_t)
	{
		CPrintToChatAll("%t", "Veto Offer T", CHAT_PREFIX, vetonum);
		DisplayVetoOffer(CS_TEAM_T);
		g_h_stored_timer_v = CreateTimer(30.0, VetoTimeout);
		return Plugin_Handled;
	}
	return Plugin_Handled;
}

public void DisplayVetoOffer(int team)
{
	int vetonum = GetConVarInt(wm_veto);
	if (team == CS_TEAM_T)
	{
		PrintHintTextToAll("%t", "Veto Offer Hint T", vetonum);
	}
	else if (team == CS_TEAM_CT)
	{
		PrintHintTextToAll("%t", "Veto Offer Hint CT", vetonum);
	}
}

public Action Veto_Admin_Setup(int client, int args)
{
	if (g_veto_active)
	{
		PrintToServer("%t", "Veto Active Already Server", CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if (g_h_stored_timer_v != INVALID_HANDLE)
	{
		KillTimer(g_h_stored_timer_v);
		g_h_stored_timer_v = INVALID_HANDLE;
	}
	g_veto_bo2_active = false;
	g_veto_bo3_active = false;
	g_veto_bo5_active = false;
	g_bo3_count = -1;
	g_bo5_count = -1;
	g_ChosenMapBo5[0] = -1;
	g_ChosenMapBo3[0] = -1;
	g_ChosenMapBo2[0] = -1;
	g_veto_map_number = 0;
	veto_offer_ct = false;
	veto_offer_t = false;
	g_veto_active = true;
	
	if (GetConVarBool(wm_veto_knife))
	{
		char veto_config[64];
		GetConVarString(wm_veto_config, veto_config, sizeof(veto_config));
	
		ServerCommand("exec gamemode_competive_server");
		ServerCommand("exec %s", veto_config);

		g_t_knife = true;
		g_t_veto = true;
		CPrintToChatAll("%t", "Veto Knife", CHAT_PREFIX);
		KnifeOn3Override();
	}
	else
	{
		ServerCommand("mp_pause_match 1");
		SetCaptains();
		CreateMapVeto(3);
	}
	return Plugin_Handled;
}

public int OtherCaptain(int captain)
{
	if (captain == g_capt1)
	return g_capt2;
	else
	return g_capt1;
}

public void SetCaptains()
{
	if (g_capt1 == -1)
	{
		/* RWS */
		ArrayList players = new ArrayList();
		for (int i = 1; i <= MaxClients; i++) 
		{
			if (IsValidClient(i) && !IsFakeClient(i) && GetClientTeam(i) == TR)	PushArrayCell(players, i);
		}
		SortADTArrayCustom(players, rwsSortFunction);
		
		if (players.Length >= 1)	SetCapt1(GetArrayCell(players, 0));
		
		delete players;
	}
	else if(g_capt2 == -1)
	{
		/* RWS */
		ArrayList players = new ArrayList();
		for (int i = 1; i <= MaxClients; i++) 
		{
			if (IsValidClient(i) && !IsFakeClient(i) && GetClientTeam(i) == CT)	PushArrayCell(players, i);
		}
		SortADTArrayCustom(players, rwsSortFunction);
		
		if (players.Length >= 1)	SetCapt2(GetArrayCell(players, 0));
		
		delete players;
	}
}

public void SetCapt1(int client)
{
	if (IsValidClient(client) && !IsFakeClient(client))
	{
		g_capt1 = client;
		CPrintToChatAll("%t", "Veto Captain T", CHAT_PREFIX, g_capt1, f_PlayerRWS[client]);
	}
	else 
	{
		/*
		int number = 0;
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && GetClientTeam(i) == 2)
			{
				number++;
			}
		}
		if (number > 0)
		{
			int c1 = -1;
			CPrintToChatAll("%t", "Getting a random Terrorists Captain", CHAT_PREFIX);
			c1 = Client_GetRandom(CS_TEAM_T);
			SetCapt1(c1);
		}
		else
		{
			CPrintToChatAll("%t", "Could not find a Terrorists Captain", CHAT_PREFIX);
			CreateTimer(5.0, CancelMatchTimer);
		}
		*/
		
		CPrintToChatAll("%t", "Could not find a Terrorists Captain", CHAT_PREFIX);
		CreateTimer(5.0, CancelMatchTimer);
	}
}

public void SetCapt2(int client)
{
	if (IsValidClient(client) && !IsFakeClient(client))
	{
		g_capt2 = client;
		CPrintToChatAll("%t", "Veto Captain CT", CHAT_PREFIX, g_capt2, f_PlayerRWS[client]);
	}
	else 
	{
		/*	
		int number = 0;
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && GetClientTeam(i) == 3)
			{
				number++;
			}
		}
		if (number > 0)
		{
			int c2 = -1;
			CPrintToChatAll("%t", "Getting a random Counter-Terrorists Captain", CHAT_PREFIX);
			c2 = Client_GetRandom(CS_TEAM_CT);
			SetCapt2(c2);
		}
		else
		{
			CPrintToChatAll("%t", "Could not find a Counter-Terrorists Captain", CHAT_PREFIX);
			CreateTimer(5.0, CancelMatchTimer);
		}
		*/
		
		CPrintToChatAll("%t", "Could not find a Counter-Terrorists Captain", CHAT_PREFIX);
		CreateTimer(5.0, CancelMatchTimer);
	}
}

public Action CancelMatchTimer(Handle timer)
{
	CancelMatch(0, 0);
}


stock void LogVetoEvent(const char[]format, any:...)
{
	char event[1024];
	VFormat(event, sizeof(event), format, 2);
	int stats_method = GetConVarInt(wm_stats_method);
	
	// inject timestamp into JSON object, hacky but quite simple
	char timestamp[64];
	FormatTime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S");
	
	// remove leading '{' from the event and add the timestamp in, including new '{'
	Format(event, sizeof(event), "{\"timestamp\": \"%s\", %s", timestamp, event[1]);
	
	if (stats_method == 0 || stats_method == 2)
	{
		// standard server log files + udp stream
		LogToGame("[Warmod+] %s", event);
	}
	
	if ((stats_method == 1 || stats_method == 2) && g_log_veto_file != INVALID_HANDLE)
	{
		WriteFileLine(g_log_veto_file, event);
	}
	
	if (LibraryExists("livewire"))
	{
		Call_StartForward(g_f_livewire_log_event);
		Call_PushString(event);
		Call_Finish();
	}
}

void VetoLogFileCreate()
{
	FormatTime(date, sizeof(date), "%Y-%m-%d");
	FormatTime(startHour, sizeof(startHour), "%H");
	FormatTime(startMin, sizeof(startMin), "%M");
	
	char t_name[64];
	char ct_name[64];
	Format(t_name, sizeof(t_name), g_t_name);
	Format(ct_name, sizeof(ct_name), g_ct_name);
	
	StripFilename(t_name, sizeof(t_name));
	StripFilename(ct_name, sizeof(ct_name));
	ReplaceString(t_name, sizeof(t_name), ".", "");
	ReplaceString(ct_name, sizeof(ct_name), ".", "");
	StringToLower(t_name, sizeof(t_name));
	StringToLower(ct_name, sizeof(ct_name));
	
/*	if (!GetConVarBool(wm_warmod_safemode))
	{
		if (StrEqual(t_name, DEFAULT_T_NAME, false) || StrEqual(t_name, "", false) || StrEqual(t_name, "_", false))
		{
			getTerroristTeamName();
		}
		
		if (StrEqual(ct_name, DEFAULT_CT_NAME, false) || StrEqual(ct_name, "", false) || StrEqual(ct_name, "_", false))
		{
			getCounterTerroristTeamName();
		}
	}*/
	
	Format(t_name, sizeof(t_name), g_t_name);
	Format(ct_name, sizeof(ct_name), g_ct_name);
	int num = GetConVarInt(wm_veto);
	if (!StrEqual(g_t_name, DEFAULT_T_NAME, false) || !StrEqual(g_ct_name, DEFAULT_CT_NAME, false))
	{
		StripFilename(t_name, sizeof(t_name));
		StripFilename(ct_name, sizeof(ct_name));
		ReplaceString(t_name, sizeof(t_name), ".", "");
		ReplaceString(ct_name, sizeof(ct_name), ".", "");
		StringToLower(t_name, sizeof(t_name));
		StringToLower(ct_name, sizeof(ct_name));
		Format(g_log_veto_filename, sizeof(g_log_veto_filename), "%s-%s%s-%04x-veto_Bo%i-%s-vs-%s", date, startHour, startMin, GetConVarInt(FindConVar("hostport")), num, t_name, ct_name);
		if (StrEqual(t_name, "rrorists", false)) 
		{
			Format(t_name, sizeof(t_name), "terroists");
		}	
		if (StrEqual(ct_name, "unterterrorists", false)) 
		{
				Format(ct_name, sizeof(ct_name), "counterterroists");
		}
	}
	else
	{
		Format(g_log_veto_filename, sizeof(g_log_veto_filename), "%s-%s%s-%04x-veto_Bo%i", date, startHour, startMin, GetConVarInt(FindConVar("hostport")), num);
	}
	
	char save_dir[128];
	GetConVarString(wm_save_dir, save_dir, sizeof(save_dir));
	if (!StrEqual(save_dir, ""))
	{
		if (!DirExists(save_dir))
		{
			CreateDirectory(save_dir, 511);
		}
	}		
	
	char filepath[128];
	if (!StrEqual(save_dir, ""))
	{
		if (DirExists(save_dir))
		{
			Format(filepath, sizeof(filepath), "%s/%s.log", save_dir, g_log_veto_filename);
			Format(g_sLogPath, sizeof(g_sLogPath), "%s/%s.log", save_dir, g_log_veto_filename);
			g_log_veto_file = OpenFile(filepath, "w");
			g_log_warmod_dir = true;
		}
		else if (DirExists("logs"))
		{
			Format(filepath, sizeof(filepath), "logs/%s.log", g_log_veto_filename);
			Format(g_sLogPath, sizeof(g_sLogPath), "logs/%s.log", g_log_veto_filename);
			g_log_veto_file = OpenFile(filepath, "w");
			g_log_warmod_dir = false;
		}
		else
		{
			Format(filepath, sizeof(filepath), "%s.log", g_log_veto_filename);
			Format(g_sLogPath, sizeof(g_sLogPath), "%s.log", g_log_veto_filename);
			g_log_veto_file = OpenFile(filepath, "w");
			g_log_warmod_dir = false;
		}
	}
	else if (DirExists("logs"))
	{
		Format(filepath, sizeof(filepath), "logs/%s.log", g_log_veto_filename);
		Format(g_sLogPath, sizeof(g_sLogPath), "logs/%s.log", g_log_veto_filename);
		g_log_veto_file = OpenFile(filepath, "w");
		g_log_warmod_dir = false;
	}
	else
	{
		Format(filepath, sizeof(filepath), "%s.log", g_log_veto_filename);
		Format(g_sLogPath, sizeof(g_sLogPath), "%s.log", g_log_veto_filename);
		g_log_veto_file = OpenFile(filepath, "w");
		g_log_warmod_dir = false;
	}
	
	LogEvent("{\"event\": \"log_start\", \"unixTime\": %d}", GetTime());
	LogPlayers();
}

/**
	* Map vetoing functions
*/
public void CreateMapVeto(int team)
{
	if (g_capt1 == -1 || g_capt2 == -1)
	{
		CPrintToChatAll("%t", "Veto Cancelled", CHAT_PREFIX);
		g_veto_active = false;
		return;
	}
	
	if (team == 2)
	{
		GiveVetoPickMenu(g_capt1);
		CPrintToChatAll("%t", "Veto First Second T", CHAT_PREFIX, g_capt1);
	}
	else
	{
		GiveVetoPickMenu(g_capt2);
		CPrintToChatAll("%t", "Veto First Second CT", CHAT_PREFIX, g_capt2);
	}
}

public Action GiveVetoPickMenu(int client) {
	VetoLogFileCreate();
	Menu menu = new Menu(VetoPickHandler);
	SetMenuExitButton(menu, false);
	SetMenuTitle(menu, "%t", "Select to Vote first or second");
	
	char First[512];
	Format(First, sizeof(First), "%t", "First");
	AddMenuItem(menu, "First", First);

	char Second[512];
	Format(Second, sizeof(Second), "%t", "Second");
	AddMenuItem(menu, "Second", Second);
	
	DisplayMenu(menu, client, MENU_TIME_FOREVER);
}

public int VetoPickHandler(Menu menu, MenuAction action, int param1, int param2)
{
	if (action == MenuAction_Select)
	{
		GetMapList();
		int client = param1;
		char info[32];
		GetMenuItem(menu, param2, info, sizeof(info));
		
		LogVetoEvent("{\"event\": \"veto_first_second\", \"player\": \"%N\", \"team\": %d, \"selection\": \"%s\"}", client, GetClientTeam(client), info);
		if (StrEqual(info, "Second"))
		{
			client = OtherCaptain(client);
		}
		
		GiveVetoMenu(client);
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i) && !IsFakeClient(i) && i != client)
			{
				VetoStatusDisplay(i);
			}
		}
		
	}
	else if (action == MenuAction_End)
	{
		CloseHandle(menu);
	}
}

public void GiveVetoMenu(int client) {
	Menu menu = new Menu(VetoHandler);
	SetMenuExitButton(menu, false);
	char vetomenutitle[32];
	Format(vetomenutitle, sizeof(vetomenutitle), "%t", "Select a map to veto");
	SetMenuTitle(menu, vetomenutitle);
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i)) {
			char map[PLATFORM_MAX_PATH];
			GetArrayString(g_MapNames, i, map, sizeof(map));
			AddMenuInt(menu, i, map);
		}
	}
	DisplayMenu(menu, client, MENU_TIME_FOREVER);
}

public int GiveVetoMenuSelect(int client) {
	g_veto_s = true;
	
	Menu menu = new Menu(VetoHandler);
	SetMenuExitButton(menu, false);
	char vetomenutitle2[32];
	Format(vetomenutitle2, sizeof(vetomenutitle2), "%t", "Select a map to play");
	SetMenuTitle(menu, vetomenutitle2);
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i)) {
			char map[PLATFORM_MAX_PATH];
			GetArrayString(g_MapNames, i, map, sizeof(map));
			AddMenuInt(menu, i, map);
		}
	}
	DisplayMenu(menu, client, MENU_TIME_FOREVER);
}

static int GetNumMapsLeft() {
	int count = 0;
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i))
		count++;
	}
	return count;
}

static int GetFirstMapLeft() {
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i))
		return i;
	}
	return -1;
}

static int GetRandomMapLeft() {
	int max = GetNumMapsLeft();
	int random = Math_GetRandomInt(0, max);
	int count = -1;
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i))
		{
			count++;
			if (count == random)
			{
				return i;
			}
		}
		
	}
	return -1;
}

public int VetoHandler(Handle menu, MenuAction action, int param1, int param2)
{
	if (action == MenuAction_Select)
	{
		int client = param1;
		int index = GetMenuInt(menu, param2);
		char map[PLATFORM_MAX_PATH];
		GetArrayString(g_MapNames, index, map, PLATFORM_MAX_PATH);
		g_veto_number = g_veto_number + 1;
		SetArrayCell(g_MapVetoed, index, true);
		
		if (!g_veto_s)
		{
			LogVetoEvent("{\"event\": \"veto_remove\", \"player\": \"%N\", \"team\": %d, \"selection\": \"%s\"}", client, GetClientTeam(client), map);
			if (client == g_capt1)
			{
				CPrintToChatAll("%t", "Vetoed 1", CHAT_PREFIX, client, map);
			}
			else
			{
				CPrintToChatAll("%t", "Vetoed 1", CHAT_PREFIX, client, map);
			}
		}
		else
		{
			LogVetoEvent("{\"event\": \"veto_select\", \"player\": \"%N\", \"team\": %d, \"selection\": \"%s\"}", client, GetClientTeam(client), map);
			if (client == g_capt1)
			{
				CPrintToChatAll("%t", "Vetoed 2", CHAT_PREFIX, client, map);
			}
			else
			{
				CPrintToChatAll("%t", "Vetoed 2", CHAT_PREFIX, client, map);
			}
		}
		
		if (GetConVarInt(wm_veto) == 1) // Bo1
		{
			if (!g_veto_s) {
				if (g_MapListCount == (g_veto_number + 1)) {
					g_ChosenMap = GetFirstMapLeft();
					ChangeMap();
					g_veto_s = false;
					g_veto_number = 0;
				} else if (g_veto_number == (g_MapListCount - 2)) {
					if (GetConVarBool(wm_veto_random))
					{
						g_ChosenMap = GetRandomMapLeft();
						ChangeMap();
					}
					else
					{
						int other = OtherCaptain(client);
						if (GetConVarBool(wm_veto_select))
						{
							GiveVetoMenuSelect(other);
						}
						else
						{
							GiveVetoMenu(other);
						}
						DisplayVeto(other);
					}
				} else {
					int other = OtherCaptain(client);
					GiveVetoMenu(other);
					DisplayVeto(other);
				}
			} else {
				g_ChosenMap = index;
				ChangeMap();
				g_veto_s = false;
				g_veto_number = 0;
			}
		}
		else if (GetConVarInt(wm_veto) == 2) // Bo2
		{
			if (!g_veto_s)
			{
				if (g_veto_number == 2)
				{
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
				}
				else
				{
					int other = OtherCaptain(client);
					GiveVetoMenu(other);
					DisplayVeto(other);
				}
			}
			else
			{
				if (g_veto_number == 3)
				{
					g_ChosenMapBo2[1] = index;
					g_ChosenMap = index;
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
					g_veto_s = false;
				}
				else
				{
					g_ChosenMapBo2[1] = index;
					g_veto_bo2_active = true;
					ChangeMap();
					g_veto_s = false;
					g_veto_number = 0;
				}
			}
		}
		else if (GetConVarInt(wm_veto) == 3) // Bo3
		{
			if (!g_veto_s)
			{
				if (GetConVarBool(wm_veto_bo3) && g_veto_number == 2)
				{
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
				}
				else if (GetConVarBool(wm_veto_bo3) && g_veto_number == (g_MapListCount - 2)) //need to fix this?
				{
					g_ChosenMapBo3[g_bo3_count+1] = GetFirstMapLeft();
					GetArrayString(g_MapNames, g_ChosenMapBo3[g_bo3_count+1], map, PLATFORM_MAX_PATH);
					LogVetoEvent("{\"event\": \"veto_last_map\", \"selection\": %s}", map);
					g_ChosenMap = g_ChosenMapBo3[g_bo3_count-1];
					g_veto_map_number = 1;
					g_veto_bo3_active = true;
					ChangeMap();
					g_veto_number = 0;
				}
				else if (g_veto_number == (g_MapListCount - 3))
				{
					if (GetConVarBool(wm_veto_random))
					{
						g_ChosenMap = GetRandomMapLeft();
						g_veto_map_number = 1;
						g_veto_bo3_active = true;
						ChangeMap();
					}
					else
					{
						int other = OtherCaptain(client);
						GiveVetoMenuSelect(other);
						DisplayVeto(other);
					}
				}
				else if (g_veto_number == (g_MapListCount - 2))
				{
					if (GetConVarBool(wm_veto_random))
					{
						g_ChosenMap = GetRandomMapLeft();
						g_veto_map_number = 1;
						g_veto_bo3_active = true;
						ChangeMap();
					}
					else
					{
						int other = OtherCaptain(client);
						GiveVetoMenuSelect(other);
						DisplayVeto(other);
					}
				}
				else
				{
					int other = OtherCaptain(client);
					GiveVetoMenu(other);
					DisplayVeto(other);
				}
			}
			else
			{
				g_bo3_count = g_bo3_count + 1;
				g_ChosenMapBo3[g_bo3_count] = index;
				if (g_bo3_count == 1)
				{
					g_veto_s = false;
					
					if (!GetConVarBool(wm_veto_bo3))
					{
						g_ChosenMapBo3[g_bo3_count+1] = GetFirstMapLeft();
						GetArrayString(g_MapNames, g_ChosenMapBo3[g_bo3_count+1], map, PLATFORM_MAX_PATH);
						LogVetoEvent("{\"event\": \"veto_last_map\", \"selection\": %s}", map);
						g_ChosenMap = g_ChosenMapBo3[g_bo3_count-1];
						g_veto_map_number = 1;
						g_veto_bo3_active = true;
						ChangeMap();
						g_veto_number = 0;
					}
					else
					{
						int other = OtherCaptain(client);
						GiveVetoMenu(other);
						DisplayVeto(other);
					}
				}
				else
				{
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
				}
			}
		}
		else if (GetConVarInt(wm_veto) == 5) // Bo5
		{
			if (!g_veto_s)
			{
				if (g_veto_number < 2)
				{
					int other = OtherCaptain(client);
					GiveVetoMenu(other);
					DisplayVeto(other);
				}
				else
				{
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
				}
			}
			else
			{
				g_bo5_count++;
				g_ChosenMapBo5[g_bo5_count] = index;
				if (g_bo5_count == 2 && g_veto_number < 5)
				{
					g_veto_s = false;
					int other = OtherCaptain(client);
					GiveVetoMenu(other);
					DisplayVeto(other);
				}
				else if (g_veto_number == 6)
				{
					g_ChosenMapBo5[g_bo5_count+1] = GetFirstMapLeft();
					GetArrayString(g_MapNames, g_ChosenMapBo5[g_bo5_count+1], map, PLATFORM_MAX_PATH);
					LogVetoEvent("{\"event\": \"veto_last_map\", \"selection\": %s}", map);
					g_ChosenMap = g_ChosenMapBo5[0];
					g_veto_map_number = 1;
					g_veto_bo5_active = true;
					ChangeMap();
					g_veto_number = 0;
				}
				else
				{
					int other = OtherCaptain(client);
					GiveVetoMenuSelect(other);
					DisplayVeto(other);
				}
			}
		}
	}
	else if (action == MenuAction_End)
	{
		CloseHandle(menu);
	}
}

public void DisplayVeto(int other)
{
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsValidClient(i) && !IsFakeClient(i) && i != other)
		{
			VetoStatusDisplay(i);
			if (GetClientTeam(other) == GetClientTeam(i))
			{
				PrintHintText(i, "%t", "Your Team is voting");
			}
			else if (GetClientTeam(i) > 1)
			{
				PrintHintText(i, "%t", "Other Team is voting");
			}
			else
			{
				char team[20];
				if (GetClientTeam(other) == 2)
				{
					team = "Terrorists";
				}
				else
				{
					team = "Counter-Terrorists";
				}
				PrintHintText(i, "%s are voting.", team);
			}
		}
	}
}

static void VetoStatusDisplay(int client)
{
	char Temp[128];
	SetGlobalTransTarget(client);
	Handle g_m_maps_left = CreatePanel();
	Format(Temp, sizeof(Temp), "%t", "Veto Maps Left");
	SetPanelTitle(g_m_maps_left, Temp);
	DrawPanelText(g_m_maps_left, "\n \n");
	for (int i = 0; i < GetArraySize(g_MapNames); i++) {
		if (!GetArrayCell(g_MapVetoed, i)) {
			char map[PLATFORM_MAX_PATH];
			GetArrayString(g_MapNames, i, map, sizeof(map));
			Format(Temp, sizeof(Temp), "%s\n", map);
			DrawPanelItem(g_m_maps_left, Temp);
		}
	}
	DrawPanelText(g_m_maps_left, " \n");
	Format(Temp, sizeof(Temp), "%t", "Exit Panel");
	DrawPanelItem(g_m_maps_left, Temp);
	SendPanelToClient(g_m_maps_left, client, Handler_DoNothing, 30);
	CloseHandle(g_m_maps_left);
}

public void ChangeMap() {
	if (g_log_veto_file != INVALID_HANDLE) {
		// close log file
		FlushFile(g_log_veto_file);
		CloseHandle(g_log_veto_file);
		g_log_veto_file = INVALID_HANDLE;
		//CreateTimer(5.0, LogFileUpload);
	}
	
	if (g_ChosenMap != -1) 
	{	
		char map[PLATFORM_MAX_PATH];
		GetArrayString(g_MapNames, g_ChosenMap, map, sizeof(map));
		g_veto_active = false;
		if (GetConVarBool(tv_delaymapchange))
		{
			if ((GetConVarFloat(tv_delay)) < 10)
			{
				CPrintToChatAll("%t", "Waiting for GOTV delay", CHAT_PREFIX);
				CPrintToChatAll("%t", "Map will change to", CHAT_PREFIX, map, GetConVarInt(tv_delay));
				CreateTimer(10.0, Timer_DelayedChangeMap);
			}
			
			if ((GetConVarFloat(tv_delay)) >= 10)
			{
				CPrintToChatAll("%t", "Waiting for GOTV delay", CHAT_PREFIX);
				CPrintToChatAll("%t", "Map will change to", CHAT_PREFIX, map, GetConVarInt(tv_delay));
				CreateTimer(GetConVarFloat(tv_delay), Timer_DelayedChangeMap);
			}
		}
		if (!GetConVarBool(tv_delaymapchange))
		{
			CPrintToChatAll("%t", "Changing map to 10", CHAT_PREFIX, map);
			CreateTimer(10.0, Timer_DelayedChangeMap);
		}
	} 
	else 
	{
		CPrintToChatAll("%t", "Something went wrong", CHAT_PREFIX);
		LogError("Veto ChangeMap Error: g_ChosenMap = -1");
	}
}

//veto
public Action Timer_DelayedChangeMap(Handle timer) {
	char map[PLATFORM_MAX_PATH];
	GetArrayString(g_MapNames, g_ChosenMap, map, sizeof(map));
	
	ServerCommand("changelevel %s", map);
	return Plugin_Handled;
}

void VetoMapChange()
{
	if (g_veto_bo3_active)
	{
		g_ChosenMap = g_ChosenMapBo3[g_veto_map_number];
	}
	else if (g_veto_bo5_active)
	{
		g_ChosenMap = g_ChosenMapBo5[g_veto_map_number];
	}
	else
	{
		g_ChosenMap = g_ChosenMapBo2[g_veto_map_number];
	}
	
	g_veto_map_number++;
	
	char map[PLATFORM_MAX_PATH];
	GetArrayString(g_MapNames, g_ChosenMap, map, sizeof(map));
	
	if (GetConVarBool(tv_delaymapchange))
	{	
		if ((GetConVarFloat(tv_delay)) < 10)
		{
			CPrintToChatAll("%t", "Waiting for GOTV delay", CHAT_PREFIX);
			CPrintToChatAll("%t", "Map will change to", CHAT_PREFIX, map, GetConVarInt(tv_delay));
			CreateTimer(10.0, Timer_DelayedChangeMap);
		}
			
		if ((GetConVarFloat(tv_delay)) >= 10)
		{
			CPrintToChatAll("%t", "Waiting for GOTV delay", CHAT_PREFIX);
			CPrintToChatAll("%t", "Map will change to", CHAT_PREFIX, map, GetConVarInt(tv_delay));
			CreateTimer(GetConVarFloat(tv_delay), Timer_DelayedChangeMap);
		}
	}
	if (!GetConVarBool(tv_delaymapchange))
	{
		CPrintToChatAll("%t", "Changing map to 10", CHAT_PREFIX, map);
		CreateTimer(10.0, Timer_DelayedChangeMap);
	}
}

public void GetMapList() {
	ClearArray(g_MapNames);
	ClearArray(g_MapVetoed);
	
	// full file path
	char mapCvar[PLATFORM_MAX_PATH];
	GetConVarString(wm_pugsetup_maplist_file, mapCvar, sizeof(mapCvar));
	
	char mapFile[PLATFORM_MAX_PATH];
	Format(mapFile, sizeof(mapFile), "cfg/%s", mapCvar);
	
	if (!FileExists(mapFile)) {
		CreateDefaultMapFile();
	}
	
	Handle mfile = OpenFile(mapFile, "r");
	char mapName[PLATFORM_MAX_PATH];
	while (!IsEndOfFile(mfile) && ReadFileLine(mfile, mapName, sizeof(mapName))) {
		TrimString(mapName);
		AddMap(mapName);
	}
	CloseHandle(mfile);
	
	if (GetArraySize(g_MapNames) < 1) {
		LogError("The map file was empty: %s", mapFile);
		AddMap("de_inferno");
		AddMap("de_mirage");
		AddMap("de_train");
		AddMap("de_overpass");
		AddMap("de_cache");
		AddMap("de_cbble");
		AddMap("de_nuke");
	}
	
	if (GetConVarBool(wm_pugsetup_randomize_maps)) {
		RandomizeMaps();
	}
	
	g_MapListCount = GetNumMapsLeft();
}

static void AddMap(const char[]mapName) {
	if (IsMapValid(mapName))
	{
		PushArrayString(g_MapNames, mapName);
		PushArrayCell(g_MapVetoed, false);
	}
	else if (strlen(mapName) >= 1)
	{
		// don't print errors on empty
		LogMessage("Invalid map name in mapfile: %s", mapName);
	}
}

static void CreateDefaultMapFile()
{
	LogError("No map list was found, autogenerating one.");
	// full file path
	char mapCvar[PLATFORM_MAX_PATH];
	GetConVarString(wm_pugsetup_maplist_file, mapCvar, sizeof(mapCvar));
	
	if (StrContains(mapCvar, "/") || StrContains(mapCvar, "\\"))
	{
		char g_FolderSplit[2][64];
		if (StrContains(mapCvar, "/"))
		{
			ExplodeString(mapCvar, "/", g_FolderSplit, 2, 64);
		}
		else
		{
			ExplodeString(mapCvar, "\\", g_FolderSplit, 2, 64);
		}
		char folderTest[PLATFORM_MAX_PATH];
		Format(folderTest, sizeof(folderTest), "cfg/%s", g_FolderSplit[0]);
		if (!DirExists(folderTest))
		{
			CreateDirectory(folderTest, 511);
		}
	}
	
	char mapFile[PLATFORM_MAX_PATH];
	Format(mapFile, sizeof(mapFile), "cfg/%s", mapCvar);
	
	Handle mfile = OpenFile(mapFile, "w");
	WriteFileLine(mfile, "de_dust2");
	WriteFileLine(mfile, "de_inferno");
	WriteFileLine(mfile, "de_mirage");
	WriteFileLine(mfile, "de_train");
	WriteFileLine(mfile, "de_overpass");
	WriteFileLine(mfile, "de_nuke");
	WriteFileLine(mfile, "de_cache");
	WriteFileLine(mfile, "de_cbble", false); // no newline at the end
	CloseHandle(mfile);
}

static void RandomizeMaps() {
	int n = GetArraySize(g_MapNames);
	for (int i = 0; i < n; i++) {
		int choice = GetRandomInt(0, n - 1);
		SwapArrayItems(g_MapNames, i, choice);
	}
}

/**
	* Adds an integer to a menu as a string choice.
*/
public void AddMenuInt(Handle menu, any value, char[] display) {
	char buffer[8];
	IntToString(value, buffer, sizeof(buffer));
	AddMenuItem(menu, buffer, display);
}

/**
	* Adds an integer to a menu, named by the integer itself.
*/
public void AddMenuInt2(Handle menu, any value) {
	char buffer[8];
	IntToString(value, buffer, sizeof(buffer));
	AddMenuItem(menu, buffer, buffer);
}

/**
	* Gets an integer to a menu from a string choice.
*/
public int GetMenuInt(Handle menu, any param2) {
	char choice[8];
	GetMenuItem(menu, param2, choice, sizeof(choice));
	return StringToInt(choice);
}

/**
	* Returns a random, uniform Integer number in the specified (inclusive) range.
	* This is safe to use multiple times in a function.
	* The seed is set automatically for each plugin.
	* Rewritten by MatthiasVance, thanks.
	*
	* @param min Min value used as lower border
	* @param max Max value used as upper border
	* @return Random Integer number between min and max
*/
#define SIZE_OF_INT 2147483647 // without 0

stock int Math_GetRandomInt(int min, int max)
{
	int random = GetURandomInt();
	if (random == 0) {
		random++;
	}
	return RoundToCeil(float(random) / (float(SIZE_OF_INT) / float(max - min + 1))) + min - 1;
}

/**
	* Gets all clients matching the specified flags filter.
	*
	* @param client Client Array, size should be MaxClients or MAXPLAYERS
	* @param flags Client Filter Flags (Use the CLIENTFILTER_ constants).
	* @return The number of clients stored in the array
*/
stock int Client_Get(int[] clients, int team)
{
	int x=0;
	for (int client = 1; client <= MaxClients; client++)
	{
		if (!IsValidClient(client) || IsFakeClient(client))
		{
			continue;
		}
		if	(GetClientTeam(client) == team)
		{
			clients[x++] = client;
		}
	}
	return x;
}
/**
	* Gets a random client matching the specified flags filter.
	*
	* @param flags Client Filter Flags (Use the CLIENTFILTER_ constants).
	* @return Client Index or -1 if no client was found
*/
stock int Client_GetRandom(int team)
{
	int[] clients = new int[MaxClients];
	int num = Client_Get(clients, team);
	if (num == 0)
	{
		return -1;
	}
	else if (num == 1)
	{
		return clients[0];
	}
	int random = Math_GetRandomInt(0, num-1);
	return clients[random];
}

/* Restore Match Backup */
public Action MatchRestore(int client, const char[]command, int args)
{
	char arg[128];
	if (GetCmdArg(1, arg, sizeof(arg)) < 1)
	{
		return Plugin_Handled;
	}
	
	if (strcmp(arg, g_c_backup, false) == 0)
	{
		Format(g_c_backup, sizeof(g_c_backup), "");
		return Plugin_Continue;
	}

	if (!StrEqual(arg[strlen(arg)-4], ".txt"))
	{
		ServerCommand("mp_backup_restore_load_file %s.txt", arg);
		return Plugin_Handled;
	}
	
	MatchRestoreCMD(arg);
	return Plugin_Handled;
}

public void MatchRestoreCMD(char[] arg)
{
	Handle kv = CreateKeyValues("SaveFile");
	FileToKeyValues(kv, arg);
	Format(g_c_backup, sizeof(g_c_backup), arg);

	KvJumpToKey(kv, "FirstHalfScore", false);
	g_scores[SCORE_CT][SCORE_FIRST_HALF] = KvGetNum(kv, "team1", 0);
	g_scores[SCORE_T][SCORE_FIRST_HALF] = KvGetNum(kv, "team2", 0);
	
	KvGoBack(kv);
	
	if (!KvJumpToKey(kv, "SecondHalfScore", false))
	{
		g_live = false;
		g_restore = true;
		ReadySystem(true);
		ReadyChangeAll(0, false, true);
		ShowInfo(0, true, false, 0);
		CPrintToChatAll("%t", "Restore Match 1", CHAT_PREFIX, GetCTTotalScore(), GetTTotalScore());
		CPrintToChatAll("%t", "Restore Match 2", CHAT_PREFIX);
	
		CloseHandle(kv);
		return;
	}
	SwitchScores();
	
	g_scores[SCORE_T][SCORE_SECOND_HALF] = KvGetNum(kv, "team1", 0);
	g_scores[SCORE_CT][SCORE_SECOND_HALF] = KvGetNum(kv, "team2", 0);
	
	KvGoBack(kv);
	
	if (!KvJumpToKey(kv, "OvertimeScore", false))
	{
		g_first_half = false;
		g_live = false;
		g_restore = true;
		ReadySystem(true);
		ReadyChangeAll(0, false, true);
		ShowInfo(0, true, false, 0);
		CPrintToChatAll("%t", "Restore Match 1", CHAT_PREFIX, GetCTTotalScore(), GetTTotalScore());
		CPrintToChatAll("%t", "Restore Match 2", CHAT_PREFIX);
	
		CloseHandle(kv);
		return;
	}
	
	int team1_ot = KvGetNum(kv, "team1", 0);
	int team2_ot = KvGetNum(kv, "team2", 0);
	int ot_count = KvGetNum(kv, "OvertimeID", 0);
	
	if (ot_count > 1)
	{
		for (int i = 0; i < (ot_count - 1); i++)
		{
			g_scores_overtime[SCORE_T][i][SCORE_FIRST_HALF] = ((GetConVarInt(mp_overtime_maxrounds)/2)/2);
			g_scores_overtime[SCORE_CT][i][SCORE_FIRST_HALF] = ((GetConVarInt(mp_overtime_maxrounds)/2)/2+1);
			SwitchScores();
			g_scores_overtime[SCORE_T][i][SCORE_SECOND_HALF] = ((GetConVarInt(mp_overtime_maxrounds)/2)/2+1);
			g_scores_overtime[SCORE_CT][i][SCORE_SECOND_HALF] = ((GetConVarInt(mp_overtime_maxrounds)/2)/2);
			team1_ot = team1_ot - (GetConVarInt(mp_overtime_maxrounds)/2);
			team2_ot = team2_ot - (GetConVarInt(mp_overtime_maxrounds)/2);
			g_overtime_count++;
		}
	}
	
	g_scores_overtime[SCORE_T][(ot_count - 1)][SCORE_FIRST_HALF] = team2_ot;
	g_scores_overtime[SCORE_CT][(ot_count - 1)][SCORE_FIRST_HALF] = team1_ot;
	
	if (team1_ot + team2_ot >= (GetConVarInt(mp_overtime_maxrounds)/2))
	{
		g_first_half = false;
	}
	
	CPrintToChatAll("%t", "Restore Match 1", CHAT_PREFIX, GetCTTotalScore(), GetTTotalScore());
	CPrintToChatAll("%t", "Restore Match 2", CHAT_PREFIX);
	
	CloseHandle(kv);
	
	g_live = false;
	g_overtime = true;
	g_restore = true;
	ReadySystem(true);
	ReadyChangeAll(0, false, true);
	ShowInfo(0, true, false, 0);
}

/* Warmod ForceTeam Command */
public Action ForceTeam(int args) {
	if (args < 2) {
		PrintToServer("%t", "Usage wm_forceteam", CHAT_PREFIX);
		return Plugin_Handled;
	}
	char steam_id[64];
	char arg2[20];
	GetCmdArg(1, steam_id, 64);
	GetCmdArg(2, arg2, 20);
	int team;
	
	if (StrEqual(arg2, "2", false) || StrEqual(arg2, "Terrorist", false) || StrEqual(arg2, "t", false) || StrEqual(arg2, "CS_TEAM_T", false)) {
		team = CS_TEAM_T;
	} else if (StrEqual(arg2, "3", false) || StrEqual(arg2, "Counter-Terrorist", false) || StrEqual(arg2, "CounterTerrorist", false) || StrEqual(arg2, "Counter Terrorist", false) || StrEqual(arg2, "ct", false) || StrEqual(arg2, "CS_TEAM_CT", false)) {
		team = CS_TEAM_CT;
	} else {
		PrintToServer("%t", "Usage wm_forceteam", CHAT_PREFIX);
		PrintToServer("%t", "Must be Terrorist or Counter-Terrorist teams", CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	int uindex = GetUseridBySteamId(steam_id);
	int count;
	int countfix;
	if (uindex == -1) {
		PrintToServer("%t", "No client could be found matching the given SteamID64", CHAT_PREFIX);
		if (team == CS_TEAM_CT && force_team_ct_count < 10) {
			for (int i = 0; i < 10; i++) {
				if (strcmp(force_team_t[i], steam_id, false) == 0) {
					Format(force_team_t[i], 64, "");
					countfix = i;
				}
				
				if (strcmp(force_team_ct[i], steam_id, false) == 0) {
					count++;
				}
			}
			
			if (count == 0) {
				PrintToServer("%t", "Added to force team array CS_TEAM_CT", CHAT_PREFIX);
				Format(force_team_ct[force_team_ct_count], 64, steam_id);
				force_team_ct_count++;
			} else {
				PrintToServer("%t", "Already added to force team array CS_TEAM_CT", CHAT_PREFIX);
			}
			
			if (countfix != 0) {
				for (int o = countfix; o < 9; o++) {
					Format(force_team_t[o], 64, force_team_t[o+1]);
				}
				force_team_t_count = force_team_t_count - 1;
			}
		} else if (team == CS_TEAM_T && force_team_t_count < 10) {
			for (int i = 0; i < 10; i++) {
				if (strcmp(force_team_ct[i], steam_id, false) == 0) {
					Format(force_team_ct[i], 64, "");
					countfix = i;
				}
				
				if (strcmp(force_team_t[i], steam_id, false) == 0) {
					count++;
				}
			}
			
			if (count == 0) {
				PrintToServer("%t", "Added to force team array CS_TEAM_T", CHAT_PREFIX);
				Format(force_team_t[force_team_t_count], 64, steam_id);
				force_team_t_count++;
			} else {
				PrintToServer("%t", "Already added to force team array CS_TEAM_T", CHAT_PREFIX);
			}
			
			if (countfix != 0) {
				for (int o = countfix; o < 9; o++) {
					Format(force_team_ct[o], 64, force_team_ct[o+1]);
				}
				force_team_ct_count = force_team_ct_count - 1;
			}
		} else if (team == CS_TEAM_T || team == CS_TEAM_CT) {
			PrintToServer("%t", "No room in array", CHAT_PREFIX, team);
		}
		return Plugin_Handled;
	}
	if (GetClientTeam(uindex) != team) {
		ChangeClientTeam(uindex, team);
	}
	return Plugin_Handled;
}

void ClearForceTeamList (int team) {
	if (team == CS_TEAM_T || team == 4) {
		for (int i = 0; i < 10; i++) {
			Format(force_team_t[i], 64, "");
			}
		force_team_t_count = 0;
		PrintToServer("%t", "Cleared Force team CS_TEAM_T list", CHAT_PREFIX);
	}
	
	if (team == CS_TEAM_CT || team == 4) {
		for (int o = 0; o < 10; o++) {
			Format(force_team_ct[o], 64, "");
		}
		force_team_ct_count = 0;
		PrintToServer("%t", "Cleared Force team CS_TEAM_CT list", CHAT_PREFIX);
	}
}

public Action ClearForceTeamAll(int args) {
	PrintToServer("%t", "Clearing Force teams list", CHAT_PREFIX);
	ClearForceTeamList(4);
	return Plugin_Handled;
}

public Action ClearForceTeamT(int args) {
	PrintToServer("%t", "Clearing Force team CS_TEAM_T list", CHAT_PREFIX);
	ClearForceTeamList(CS_TEAM_T);
	return Plugin_Handled;
}

public Action ClearForceTeamCT(int args) {
	PrintToServer("%t", "Clearing Force team CS_TEAM_CT list", CHAT_PREFIX);
	ClearForceTeamList(CS_TEAM_CT);
	return Plugin_Handled;
}

/* Warmod ForceClientName Command */
public Action ForceClientName(int args)
{
	if (args < 2)
	{
		PrintToServer("%t", "Usage wm_forcename", CHAT_PREFIX);
		return Plugin_Handled;
	}
	char steam_id[64];
	char name[16];
	GetCmdArg(1, steam_id, 64);
	GetCmdArg(2, name, 16);

	int uindex = GetUseridBySteamId(steam_id);
	if (uindex == -1)
	{
		PrintToServer("%t", "No client could be found matching the given SteamID64", CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	SetClientName(uindex, name);
	return Plugin_Handled;
}

stock int GetUseridBySteamId(char[] authid)
{
	int clientIndex = -1;
	char CommunityID[18];
	
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientConnected(i))
		{
			GetClientAuthId(i, AuthId_SteamID64, CommunityID, sizeof(CommunityID));
			if (strcmp(authid, CommunityID, false) == 0)
			{
				return i;
			}
		}
	}
	return clientIndex;
}
/* WarMod Status Command */
public Action WarMod_Status(int args)
{
	char log_string[384];
	int z = 0;
	LogToGame("{\"WarMod_Status\":");
	LogToGame("{\"Counter-Terrorists\":[");
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && GetClientTeam(i) == CS_TEAM_CT)
		{
			CS_GetStatuString(i, log_string, sizeof(log_string));
			if (z == 0)
			{
				LogToGame("{\"player\": %s}", log_string);
				z++;
			}
			else
			{
				LogToGame(", {\"player\": %s}", log_string);
			}
		}
	}
	LogToGame("], \"Terrorists\":[");
	z = 0;
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && GetClientTeam(i) == CS_TEAM_T)
		{
			CS_GetStatuString(i, log_string, sizeof(log_string));
			
			if (z == 0)
			{
				LogToGame("{\"player\": %s}", log_string);
				z++;
			}
			else
			{
				LogToGame(", {\"player\": %s}", log_string);
			}
		}
	}
	LogToGame("], \"SPECTATOR\":[");
	z = 0;
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsClientInGame(i) && (GetClientTeam(i) == CS_TEAM_SPECTATOR || GetClientTeam(i) == CS_TEAM_NONE))
		{
			CS_GetStatuString(i, log_string, sizeof(log_string));
			
			if (z == 0)
			{
				LogToGame("{\"player\": %s}", log_string);
				z++;
			}
			else
			{
				LogToGame(", {\"player\": %s}", log_string);
			}
		}
	}
	LogToGame("]}}");
	return Plugin_Handled;
}

stock int CS_GetStatuString(int client, char[] LogString, int size)
{
	if (client == 0)
	{
		strcopy(LogString, size, "{\"name\": \"Console\", \"userId\": 0, \"uniqueId\": \"Console\", \"team\": 0}");
		return client;
	}
	
	if (!IsClientInGame(client))
	{
		Format(LogString, size, "null");
		return -1;
	}
	
	char player_name[64];
	char authid[32];
	
	GetClientName(client, player_name, sizeof(player_name));
	GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
	
	EscapeString(player_name, sizeof(player_name));
	EscapeString(authid, sizeof(authid));
	if (GetClientTeam(client) == CS_TEAM_SPECTATOR || GetClientTeam(client) == CS_TEAM_NONE)
	{
		Format(LogString, size, "{\"steamId\": \"%s\", \"name\": \"%s\"}", authid, player_name);
	}
	else
	{
		Format(LogString, size, "{\"steamId\": \"%s\", \"name\": \"%s\", \"kills\": %d, \"assists\": %d, \"deaths\": %d, \"score\": %d, \"money\": %d}", authid, player_name, GetClientFrags(client), CS_GetClientAssists(client), GetEntProp(client, Prop_Data, "m_iDeaths"), CS_GetClientContributionScore(client), GetEntData(client, g_iAccount));
	}
	
	return client;
}

void LogMoney()
{
	char log_string[384];
	for(int i = 1; i < MAXPLAYERS; i++)
	{
		if (IsClientInGame(i) && (GetClientTeam(i) == CS_TEAM_T))
		{
			CS_GetAdvLogString(i, log_string, sizeof(log_string));
			LogEvent("{\"event\": \"round_money\", \"round\": %i, \"player\": %s, \"kills\": %d, \"assists\": %d, \"deaths\": %d, \"score\": %d, \"money\": %d}", g_round, log_string, GetClientFrags(i), CS_GetClientAssists(i), GetEntProp(i, Prop_Data, "m_iDeaths"), CS_GetClientContributionScore(i), GetEntData(i, g_iAccount));
		}	
	}
	
	for(int o = 1; o < MAXPLAYERS; o++)
	{
		if (IsClientInGame(o) && (GetClientTeam(o) == CS_TEAM_CT))
		{
			CS_GetAdvLogString(o, log_string, sizeof(log_string));
			LogEvent("{\"event\": \"round_money\", \"round\": %i, \"player\": %s, \"kills\": %d, \"assists\": %d, \"deaths\": %d, \"score\": %d, \"money\": %d}", g_round, log_string, GetClientFrags(o), CS_GetClientAssists(o), GetEntProp(o, Prop_Data, "m_iDeaths"), CS_GetClientContributionScore(o), GetEntData(o, g_iAccount));
		}	
	}
}

// Returns count for players in game and not spectators
stock int CS_GetPlayerListCount()
{
	int clients = 0;
	for(int i = 1; i < MAXPLAYERS; i++)
	{
		if (IsClientInGame(i) && IsClientConnected(i) && !IsFakeClient(i))
		{
			if (GetClientTeam(i) > 1)
			{
				clients++;
			}
		}	
	}
	return clients;
}

stock bool IsEven(int num)
{
    return (num & 1) == 0;
}

public Action SetTagClientDefault(Handle timer)
{
	if (!GetConVarBool(wm_warmod_safemode) && GetConVarBool(wm_ready_tag))
	{
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsValidClient(i))
			{
				if (StrEqual(g_clanTags[i], "NOT READY", false) || StrEqual(g_clanTags[i], "READY", false) || StrEqual(g_clanTags[i], "", false))
				{
					CS_SetClientClanTag(i, " ");
				}
				else
				{
					CS_SetClientClanTag(i, g_clanTags[i]);
				}
			}
		}
	}
}

public void SetTagNotReady(int client)
{
	if (!GetConVarBool(wm_warmod_safemode) && IsValidClient(client) && GetConVarBool(wm_ready_tag))
	{
		if (!g_clanTagsChecked[client])
		{
			CS_GetClientClanTag(client, g_clanTags[client], sizeof(g_clanTags[]));
			g_clanTagsChecked[client] = true;
			if (StrEqual(g_clanTags[client], "NOT READY", false) || StrEqual(g_clanTags[client], "READY", false) || StrEqual(g_clanTags[client], "", false))
			{
				Format(g_clanTags[client], sizeof(g_clanTags[]), " ");
			}
		}
		CS_SetClientClanTag(client, "NOT READY");
	}
}

public void SetTagReady(int client)
{
	if (!GetConVarBool(wm_warmod_safemode) && IsValidClient(client) && GetConVarBool(wm_ready_tag))
	{
		if (!g_clanTagsChecked[client])
		{
			CS_GetClientClanTag(client, g_clanTags[client], sizeof(g_clanTags[]));
			g_clanTagsChecked[client] = true;
			if (StrEqual(g_clanTags[client], "NOT READY", false) || StrEqual(g_clanTags[client], "READY", false) || StrEqual(g_clanTags[client], "", false))
			{
				Format(g_clanTags[client], sizeof(g_clanTags[]), " ");
			}
		}
		CS_SetClientClanTag(client, "READY");
	}
}

public Action SetTeam(int client, int args)
{
	if (!IsValidClient(client))
	{
		return Plugin_Handled;
	}
	
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Name", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	if(!player_loaded[client])	
	{
		CPrintToChat(client, "%T", "Profile not ready", client, CHAT_PREFIX, f_PlayerRWS[client]);
		return Plugin_Handled;
	}
	
	if(player_team_id[client] > -1)
	{
		if(!player_team_use[client])	CPrintToChat(client, "%T", "Team not ready", client, CHAT_PREFIX);
		else
		{
			if(!player_team_leader[client])	CPrintToChat(client, "%T", "Team not allowed", client, CHAT_PREFIX, player_team_name[client]);
			else
			{
				if(GetClientTeam(client) == TR)
				{
					if(g_capt1 != client)
					{
						if(!CheckTeamMember(client))
						{
							CPrintToChat(client, "%T", "Team not team", client, CHAT_PREFIX);
							return Plugin_Handled;
						}
						
						Format(g_t_name, sizeof(g_t_name), "%s", player_team_name[client]);
						Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
						g_t_id = player_team_id[client];
						EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
						ServerCommand("mp_teamname_2 %s", player_team_name[client]);
						ServerCommand("mp_teamlogo_2 %s", player_team_logo[client]);
						//ServerCommand("mp_teamflag_2 %s", player_team_flag[client]);
						
						g_capt1 = client;
						
						CPrintToChat(client, "%T", "Setting team complete", client, CHAT_PREFIX);
						CPrintToChatAll("%t", "Terrorists are called", CHAT_PREFIX, g_t_name);
						CPrintToChatAll("%t", "Veto Captain T", CHAT_PREFIX, g_capt1, f_PlayerRWS[client]);
					}
					else
					{
						Format(g_t_name, sizeof(g_t_name), DEFAULT_T_NAME);
						Format(g_t_name_escaped, sizeof(g_t_name_escaped), g_t_name);
						EscapeString(g_t_name_escaped, sizeof(g_t_name_escaped));
						g_capt1 = -1;
						g_t_id = 0;
						ServerCommand("mp_teamname_2 %s", DEFAULT_T_NAME);
						ServerCommand("mp_teamlogo_2 \"\"");
						ServerCommand("mp_teamflag_2 \"\"");
						CPrintToChatAll("%t", "T Captain Reset", CHAT_PREFIX);
					}
				}
				else if(GetClientTeam(client) == CT)
				{
					if(g_capt2 != client)
					{
						if(!CheckTeamMember(client))
						{
							CPrintToChat(client, "%T", "Team not team", client, CHAT_PREFIX);
							return Plugin_Handled;
						}
						
						Format(g_ct_name, sizeof(g_ct_name), "%s", player_team_name[client]);
						Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
						g_ct_id = player_team_id[client];
						EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
						ServerCommand("mp_teamname_1 %s", player_team_name[client]);
						ServerCommand("mp_teamlogo_1 %s", player_team_logo[client]);
						//ServerCommand("mp_teamflag_1 %s", player_team_flag[client]);
						
						g_capt2 = client;
					
						CPrintToChat(client, "%T", "Setting team complete", client, CHAT_PREFIX);
						CPrintToChatAll("%t", "Counter Terrorists are called", CHAT_PREFIX, g_ct_name);
						CPrintToChatAll("%t", "Veto Captain CT", CHAT_PREFIX, g_capt2, f_PlayerRWS[client]);
					}
					else
					{
						Format(g_ct_name, sizeof(g_ct_name), DEFAULT_CT_NAME);
						Format(g_ct_name_escaped, sizeof(g_ct_name_escaped), g_ct_name);
						EscapeString(g_ct_name_escaped, sizeof(g_ct_name_escaped));
						g_capt2 = -1;
						g_ct_id = 0;
						ServerCommand("mp_teamname_1 %s", DEFAULT_CT_NAME);
						ServerCommand("mp_teamlogo_1 \"\"");
						ServerCommand("mp_teamflag_1 \"\"");
						CPrintToChatAll("%t", "CT Captain Reset", CHAT_PREFIX);
					}
				}
			}
		}
	}
	else CPrintToChat(client, "%T", "Not In Team", client, CHAT_PREFIX);
	
	CheckReady();
	return Plugin_Handled;
}

bool CheckTeamMember(int client)
{
	// don't have 5 member
	if(GetTeamClientCount(client) < 5)	return false;
	
	int count;
	// check each player team
	for (int i = 1; i <= MaxClients; i++)
	{
		if(IsValidClient(i) && GetClientTeam(i) == GetClientTeam(client) && player_team_id[client] == player_team_id[i])
		{
			count++;
		}
	}
	if(count == 5)	return true;
	else return false;
}

void SetLastMatchScores() {
	lt_match_length = RoundFloat(GetEngineTime() - g_match_start);
	Format(lt_map, sizeof(lt_map), g_map);
	lt_max_rounds = (GetConVarInt(mp_maxrounds)/2);
	lt_overtime_max_rounds = 0;
	if (GetConVarBool(mp_overtime_enable)) {
		lt_overtime_max_rounds = (GetConVarInt(mp_overtime_maxrounds)/2);
	}
	lt_overtime_count = g_overtime_count;
	if (GetConVarBool(mp_match_can_clinch)) {
		lt_played_out = 0;
	} else {
		lt_played_out = 1;
	}
	Format(lt_t_name, sizeof(lt_t_name), g_t_name);
	lt_t_overall_score = GetTTotalScore();
	lt_t_first_half_score = g_scores[SCORE_T][SCORE_FIRST_HALF];
	lt_t_second_half_score = g_scores[SCORE_T][SCORE_SECOND_HALF];
	lt_t_overtime_score = GetTOTTotalScore();
	Format(lt_ct_name, sizeof(lt_ct_name), g_ct_name);
	lt_ct_overall_score = GetCTTotalScore();
	lt_ct_first_half_score = g_scores[SCORE_CT][SCORE_FIRST_HALF];
	lt_ct_second_half_score = g_scores[SCORE_CT][SCORE_SECOND_HALF];
	lt_ct_overtime_score = GetCTOTTotalScore();
	Format(lt_log_file_name, sizeof(lt_log_file_name), g_log_filename);
}

/* Print Damage Info */
/* Edit From https://github.com/benscobie/sourcemod-damagereport*/

stock bool IsClientPlaying(int player)
{
	int team = GetClientTeam(player);
	return (team == CS_TEAM_T || team == CS_TEAM_CT);
}

static void PrintDamageInfo(int client)
{
	CPrintToChat(client, "%T", "Damage Report", client, CHAT_PREFIX);

	if(!Hasdamagereport[client])	CPrintToChat(client, "%T", "No Damage Report", client, CHAT_PREFIX);
	
	else if (Hasdamagereport[client])
	{
		for (int i = 1; i <= MaxClients; i++)
		{
			if (IsClientConnected(i) && IsClientInGame(i) && i != client) 
			{
				// Only show spectators in report if they have given or taken damage
				if (!IsClientPlaying(i) && g_DamageDoneHits[i][client] == 0 && g_DamageDoneHits[client][i] == 0) 
				{
					continue;
				}
			
				//No need to show 0 hit players
				else if (g_DamageDoneHits[i][client] == 0 && g_DamageDoneHits[client][i] == 0) 
				{
					continue;
				}
		
				int health = IsPlayerAlive(i) ? GetClientHealth(i) : 0;
				char name[64];
				GetClientName(i, name, sizeof(name));

				CPrintToChat(client, "%T", "Damage Report 2", client, g_DamageDone[client][i], g_DamageDoneHits[client][i], g_DamageDone[i][client], g_DamageDoneHits[i][client], name, health);
			}
		}
	}
}

//MVP
public Action Event_Round_MVP(Handle event, const char[] name, bool dontBroadcast)
{
	if (!IsActive(0, true))
	{
		return;
	}
	
	// stats
	int client = GetClientOfUserId(GetEventInt(event, "userid"));
	if (client > 0)
	{
		// Log mvp
		char log_string[384];
		CS_GetAdvLogString(GetClientOfUserId(GetEventInt(event, "userid")), log_string, sizeof(log_string));
		LogEvent("{\"event\": \"round_mvp\", \"round\": %i, \"player\": %s}", g_round, log_string);
		
		match_stats[client][MATCH_MVP]++;
	}
}

// Match restore
public Action Command_MatchRestore (int client, int args)
{
	if (IsValidClient(client))
	{
		char arg[20];
		
		if (args < 1)
		{
			CPrintToChat(client, "%T", "Usage sm_backup", client, CHAT_PREFIX);
			return Plugin_Handled;
		}
			
		GetCmdArg(1, arg, sizeof(arg));
		
		if (StrEqual(arg, ""))
		{
			CPrintToChat(client, "%T", "sm_backup Can not be empty", client, CHAT_PREFIX);
			return Plugin_Handled;
		}
		
		ServerCommand("mp_backup_restore_load_file %s", arg);
	}
	return Plugin_Handled;
}

// admin pause
public Action AdmPause(int client, int args)
{
	if(!admpause && g_start)
	{
		ServerCommand("mp_pause_match 1");	
		
		CPrintToChatAll("%t", "Admin Pause", CHAT_PREFIX);
		
		if(GameRules_GetProp("m_bFreezePeriod") == 0)	CPrintToChatAll("%t", "Pause Freeze Time", CHAT_PREFIX);
		
		admpause = true;
		
		char player_name[64];
		char authid[32];
			
		GetClientName(client, player_name, sizeof(player_name));
		GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
			
		EscapeString(player_name, sizeof(player_name));
		EscapeString(authid, sizeof(authid));
		
		LogEvent("{\"event\": \"match_paused\", \"round\": %i, \"team\": admin, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
		
		if (g_h_stored_timer != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer);
			g_h_stored_timer = INVALID_HANDLE;
		}		
	}
	return Plugin_Handled;
}

public Action AdmUnpause(int client, int args)
{
	if(g_start)
	{
		ServerCommand("mp_unpause_match 1");	
		CPrintToChatAll("%t", "Admin Unpause", CHAT_PREFIX);
		admpause = false;
		
		char player_name[64];
		char authid[32];
			
		GetClientName(client, player_name, sizeof(player_name));
		GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
			
		EscapeString(player_name, sizeof(player_name));
		EscapeString(authid, sizeof(authid));
		
		LogEvent("{\"event\": \"match_resumed\", \"round\": %i, \"team\": admin, \"name\": \"%s\", \"steamId\": \"%s\"}", g_round, player_name, authid);
		
		if (g_h_stored_timer != INVALID_HANDLE)
		{
			KillTimer(g_h_stored_timer);
			g_h_stored_timer = INVALID_HANDLE;
		}
		
		g_pause_offered_ct = false;
		g_pause_offered_t = false;
	}
	return Plugin_Handled;
}

/* Natives */
public int Native_IsMatchLive(Handle plugin, int numParams)
{
	if(g_live || g_match)  return true;
	else return false;
}

static void RWSUpdate(int client, bool winner) 
{
	if(!save_db)	return;
	
	float rws = 0.0;
	float oldrws = f_PlayerRWS[client];
	
	if (winner) 
	{
		int playerCount = 0;
		int sum = 0;
		for (int i = 1; i <= MaxClients; i++) 
		{
			if (IsValidClient(i) && !IsFakeClient(i)) 
			{
				if (GetClientTeam(i) == GetClientTeam(client)) 
				{
					sum += i_RoundPoints[i];
					playerCount++;
				}
			}
		}
		if (sum != 0) 
		{
			// scaled so it's always considered "out of 5 players" so different team sizes
			// don't give inflated rws
			rws = 100.0 * float(playerCount) / 5.0 * float(i_RoundPoints[client]) / float(sum);
		} 
		else return;
	} 
	else rws = 0.0;

	float alpha = GetAlphaFactor(client);
	f_PlayerRWS[client] = (1.0 - alpha) * f_PlayerRWS[client] + alpha * rws;
	i_PlayerRounds[client]++;
	char authid[32];
	GetClientAuthId(client, AuthId_Steam2, authid, sizeof(authid));
	LogEvent("{\"event\": \"rws_update\", \"name\": \"%N\", \"old_rws\": %f, \"alpha\": %f, \"round_points\": %f, \"round_rws\": %f, \"new_rws\": \"%f\", \"steamId\": \"%s\"}",
	client, oldrws, alpha, i_RoundPoints[client], rws, f_PlayerRWS[client], authid);
	CPrintToChat(client, "%T", "RWS Update", client, CHAT_PREFIX, oldrws, f_PlayerRWS[client]);
}

static float GetAlphaFactor(int client) 
{
	float rounds = float(i_PlayerRounds[client]);
	if (rounds < ROUNDS_FINAL) return ALPHA_INIT + (ALPHA_INIT - ALPHA_FINAL) / (-ROUNDS_FINAL) * rounds;
	else return ALPHA_FINAL;
}

public Action CMD_RWS(int client, int args)
{
	if (IsValidClient(client) && !IsFakeClient(client))
	{
		if(player_loaded[client])	CPrintToChat(client, "%T", "RWS", client, CHAT_PREFIX, f_PlayerRWS[client]);
		else	CPrintToChat(client, "%T", "Profile not ready", client, CHAT_PREFIX, f_PlayerRWS[client]);
	}
	return Plugin_Handled;
}

public void RWSBalance(ArrayList players) 
{
	// force all players to unready
	ReadyChangeAll(0, false, true);
	CheckReady();
	CPrintToChatAll("%t", "Forced Not Ready", CHAT_PREFIX);
	PrintToServer("%t", "Forced Not Ready Server", CHAT_PREFIX);
	
	CPrintToChatAll("%t", "Balance Team", CHAT_PREFIX);
	
	Handle pq = PQ_Init();

	for (int i = 0; i < players.Length; i++) 
	{
		int client = players.Get(i);
		PQ_Enqueue(pq, client, f_PlayerRWS[client]);
		LogEvent("PQ_Enqueue(%L, %f)", client, f_PlayerRWS[client]);
	}
	
	int count = 0;
	
	while (!PQ_IsEmpty(pq) && count < 10) 
	{
		int p1 = PQ_Dequeue(pq);
		int p2 = PQ_Dequeue(pq);
	
		if (IsValidClient(p1) && !IsFakeClient(p1)) 
		{
			SwitchPlayerTeam(p1, CS_TEAM_CT);
			LogEvent("CT: PQ_Dequeue() = %L, rws=%f", p1, f_PlayerRWS[p1]);
	    }
	
		if (IsValidClient(p2) && !IsFakeClient(p2)) 
		{
			SwitchPlayerTeam(p2, CS_TEAM_T);
			LogEvent("T : PQ_Dequeue() = %L, rws=%f", p2, f_PlayerRWS[p2]);
		}
		count += 2;
	}
	
	while(!PQ_IsEmpty(pq)) 
	{
		int client = PQ_Dequeue(pq);
		if (IsValidClient(client) && !IsFakeClient(client))	SwitchPlayerTeam(client, CS_TEAM_SPECTATOR);
	}
	
	CloseHandle(pq);
	
	// reset team
	ResetTeams();
	
	CPrintToChatAll("%t", "Finish Balance Team", CHAT_PREFIX);
	CPrintToChatAll("%t", "Team Captain Reset", CHAT_PREFIX);
}

public int rwsSortFunction(int index1, int index2, Handle array, Handle hndl) {
  int client1 = GetArrayCell(array, index1);
  int client2 = GetArrayCell(array, index2);
  return f_PlayerRWS[client1] < f_PlayerRWS[client2];
}

stock void SwitchPlayerTeam(int client, int team)
{
	if (GetClientTeam(client) == team)	return;

	if (team > CS_TEAM_SPECTATOR) 
	{
		CS_SwitchTeam(client, team);
		CS_UpdateClientModel(client);
		CS_RespawnPlayer(client);
	} 
	else	ChangeClientTeam(client, team);
}

public Action CMD_Balance (int client, int args)
{
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Balance", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	else if (IsVoteInProgress())
	{
		CPrintToChat(client, "%T", "Vote in Progress", client);
		return Plugin_Handled;
	}
	BalanceVote();
	return Plugin_Handled;
}

public Action CMD_ForceBalance (int client, int args)
{
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Balance", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	CPrintToChatAll("%t", "Admin Balance", CHAT_PREFIX);
	
	ArrayList players = new ArrayList();
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i) && GetClientTeam(client) != SPEC) players.Push(i);
	}
        
	RWSBalance(players);
	delete players;
	
	return Plugin_Handled;
}

public void BalanceVote()
{
	// hide ready system
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i))	ShowInfo(0, false, false, 1);
	}
	ServerCommand("wm_show_info 0");
			
	char menutitle[255], menuyes[255], menuno[255];
	Format(menutitle, sizeof(menutitle), "%t", "Vote Balance Titile");	
	Format(menuyes, sizeof(menuyes), "%t", "Yes");
	Format(menuno, sizeof(menuno), "%t", "No");
			
	Menu menu = new Menu(Handle_BalanceVoteMenu);

	menu.SetTitle(menutitle);
	menu.AddItem("yes", menuyes);
	menu.AddItem("no", menuno);
	menu.ExitButton = false;
	menu.DisplayVoteToAll(20);
}

public int Handle_BalanceVoteMenu(Menu menu, MenuAction action, int param1,int param2)
{
	if (action == MenuAction_End)
	{
		delete menu;
	}
	else if (action == MenuAction_VoteEnd) 
	{
		ServerCommand("wm_show_info 1");
		
		// show ready syeam
		for (int i = 1; i <= MaxClients; i++) 
		{
			if (IsValidClient(i) && !IsFakeClient(i) && (GetClientTeam(i) == TR || GetClientTeam(i) == CT))	ShowInfo(i, true, false, 0);
		}

		if (param1 == 0)
		{
			CPrintToChatAll("%t", "Vote Balance Success", CHAT_PREFIX);
			ArrayList players = new ArrayList();
			for (int i = 1; i <= MaxClients; i++) 
			{
				if (IsValidClient(i) && !IsFakeClient(i) && GetClientTeam(i) != SPEC) players.Push(i);
			}
			RWSBalance(players);
			
			delete players;
		}
		else if (param1 == 1)
		{
			CPrintToChatAll("%t", "Vote Balance Failed", CHAT_PREFIX);
		}
	}
}

/* Captain Pick */
public Action CMD_Pug (int client, int args)
{
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Pug", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	else if (IsVoteInProgress())
	{
		CPrintToChat(client, "%T", "Vote in Progress", client);
		return Plugin_Handled;
	}
	PugVote();
	return Plugin_Handled;
}

public Action CMD_ForcePug (int client, int args)
{
	if (g_live)
	{
		CPrintToChat(client, "%T", "Can Not Pug", client, CHAT_PREFIX);
		return Plugin_Handled;
	}
	
	StartPug();
	CPrintToChatAll("%t", "Admin Pug", CHAT_PREFIX);
	return Plugin_Handled;
}

public void PugVote()
{
	char menutitle[255], menuyes[255], menuno[255];
	Format(menutitle, sizeof(menutitle), "%t", "Pug Vote Titile");
	Format(menuyes, sizeof(menuyes), "%t", "Yes");
	Format(menuno, sizeof(menuno), "%t", "No");
			
	Menu menu = new Menu(Handle_PugVoteMenu);

	menu.SetTitle(menutitle);
	menu.AddItem("yes", menuyes);
	menu.AddItem("no", menuno);
	menu.ExitButton = false;
	menu.DisplayVoteToAll(20);
}

public int Handle_PugVoteMenu(Menu menu, MenuAction action, int param1,int param2)
{
	if (action == MenuAction_End)
	{
		delete menu;
	}
	else if (action == MenuAction_VoteEnd) 
	{
		if (param1 == 0)
		{
			CPrintToChatAll("%t", "Vote Pug Success", CHAT_PREFIX);
			StartPug();
		}
		else if (param1 == 1)
		{
			CPrintToChatAll("%t", "Vote Pug Failed", CHAT_PREFIX);
			// show ready syeam
			for (int i = 1; i <= MaxClients; i++) 
			{
				if (IsValidClient(i) && !IsFakeClient(i) && (GetClientTeam(i) == TR || GetClientTeam(i) == CT))	ShowInfo(i, true, false, 0);
			}
			ServerCommand("wm_show_info 1");
		}
	}
}

public void StartPug()
{
	if (g_live)	return;
	
	IsPug = true;
	
	// hide ready system
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i))	ShowInfo(0, false, false, 1);
	}
	ServerCommand("wm_show_info 0");
	
	// force all players to unready
	ReadyChangeAll(0, false, true);
	CheckReady();
	CPrintToChatAll("%t", "Forced Not Ready", CHAT_PREFIX);
	PrintToServer("%t", "Forced Not Ready Server", CHAT_PREFIX);
	
	CPrintToChatAll("%t", "Choosing PUG Captain", CHAT_PREFIX);
	
	ArrayList players = new ArrayList();

	for (int j = 1; j <= MaxClients; j++) 
	{
		if (IsValidClient(j) && !IsFakeClient(j))
		{		
			// Move all players to spec
			SwitchPlayerTeam(j, CS_TEAM_SPECTATOR);
			b_waitpick[j] = true;
			PushArrayCell(players, j);
		}
	}

	SortADTArrayCustom(players, rwsSortFunction);
	
	// Captains are top 2 players in server
	if (players.Length >= 1)	
	{
		g_capt1 = GetArrayCell(players, 0);
		SwitchPlayerTeam(g_capt1, CS_TEAM_T);
		CPrintToChatAll("%t", "Veto Captain T", CHAT_PREFIX, g_capt1, f_PlayerRWS[g_capt1]);
	}
	if (players.Length >= 2)
	{
		g_capt2 = GetArrayCell(players, 1);
		SwitchPlayerTeam(g_capt2, CS_TEAM_CT);
		CPrintToChatAll("%t", "Veto Captain CT", CHAT_PREFIX, g_capt2, f_PlayerRWS[g_capt2]);
	}
	
	// Start pick player.
	CreateTimer(1.0, GivePlayerSelectionMenu, GetClientSerial(g_capt1));
}

public Action GivePlayerSelectionMenu(Handle timer, int serial) 
{
	if (g_live)	return Plugin_Handled;
	
	int client = GetClientFromSerial(serial);
	if (IsPickingFinished()) 
	{
		CreateTimer(1.0, FinishPicking);
	}
	else
	{
		if (IsValidClient(client) && !IsFakeClient(client)) 
		{
			Menu menu = new Menu(PlayerMenuHandler);
			SetMenuTitle(menu, "%T", "PlayerPickTitle", client);
			SetMenuExitButton(menu, false);
			if (AddPlayersToMenu(menu) > 0) 
			{
				DisplayMenu(menu, client, MENU_TIME_FOREVER);
			} 
			else 
			{
 				CPrintToChatAll("%t", "Pug Not enough players", CHAT_PREFIX);
 				
 				ArrayList players = new ArrayList();
				for (int i = 1; i <= MaxClients; i++) 
				{
					if (IsValidClient(i) && !IsFakeClient(i))
					{
						b_waitpick[i] = false;
						players.Push(i);
					}
				}
			        
				RWSBalance(players);
				delete players;
				delete menu;
				
				IsPug = false;
      		}
		} 
		else 
		{
			CPrintToChatAll("%t", "Pug captain is missing", CHAT_PREFIX);
			
			ArrayList players = new ArrayList();
			for (int i = 1; i <= MaxClients; i++) 
			{
				if (IsValidClient(i) && !IsFakeClient(i))
				{
					b_waitpick[i] = false;
					players.Push(i);
				}
			}
		        
			RWSBalance(players);
			delete players;
			IsPug = false;
		}
	}
	return Plugin_Handled;
}

static int AddPlayersToMenu(Menu menu) 
{
	char displayString[128];
	int count = 0;
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i) && b_waitpick[i] && i != g_capt1 && i != g_capt2)
        {
			Format(displayString, sizeof(displayString), "%N - RWS: %.2f", i, f_PlayerRWS[i]);
			AddMenuInt(menu, GetClientSerial(i), displayString);
			count++;
		}
	}
	return count;
}

public int PlayerMenuHandler(Menu menu, MenuAction action, int param1, int param2) 
{
	if (action == MenuAction_Select) 
	{
		int selected = GetClientFromSerial(GetMenuInt(menu, param2));

		if (IsValidClient(selected) && !IsFakeClient(selected)) 
		{
			SwitchPlayerTeam(selected, GetClientTeam(param1));
			
			b_waitpick[selected] = false;

			// wtf is dis?
			//FormatPlayerName(client, client, captName);
			//FormatPlayerName(client, selected, selectedName);
			
			if(GetClientTeam(param1) == CS_TEAM_T)	CPrintToChatAll("%t", "PlayerPickChoice T", CHAT_PREFIX, param1, selected, f_PlayerRWS[selected]);
			else if(GetClientTeam(param1) == CS_TEAM_T)	CPrintToChatAll("%t", "PlayerPickChoice CT", CHAT_PREFIX, param1, selected, f_PlayerRWS[selected]);
	
			if (!IsPickingFinished()) 
			{
				MoreMenuPicks(GetNextCaptain(param1));
			} 
			else 
			{
				CreateTimer(1.0, FinishPicking);
			}
		} 
		else 
		{
			MoreMenuPicks(param1);
	    }
	} 
	else if (action == MenuAction_Cancel) 
	{
		if(GetClientTeam(param1) == CS_TEAM_T)	CPrintToChatAll("%t", "Pug failed to get captain pick T", CHAT_PREFIX, param1);
		else if(GetClientTeam(param1) == CS_TEAM_T)	CPrintToChatAll("%t", "Pug failed to get captain pick CT", CHAT_PREFIX, param1);
		
		ArrayList players = new ArrayList();
		for (int i = 1; i <= MaxClients; i++) 
		{
			if (IsValidClient(i) && !IsFakeClient(i))
			{
				b_waitpick[i] = false;
				players.Push(i);
			}
		}
		        
		RWSBalance(players);
		delete players;
		IsPug = false;
	}
	else if (action == MenuAction_End) 
	{
		delete menu;
		IsPug = false;
	}
}

static int GetNextCaptain(int captain) 
{
	return OtherCaptain(captain);
	
	/* ABBAABBA
	else 
	{
		if (g_PickCounter == 0) 
		{
			g_PickCounter = 1;
			return OtherCaptain(captain);
		} 
		else 
		{
			g_PickCounter--;
			return captain;
		}
	}
	*/
}

static void MoreMenuPicks(int client) 
{
	if (IsPickingFinished() || !IsValidClient(client) || IsFakeClient(client)) 
	{
		CreateTimer(5.0, FinishPicking);
		return;
	}
	CreateTimer(1.0, GivePlayerSelectionMenu, GetClientSerial(client));
}

static bool IsPickingFinished() 
{
	int numSelected = 0;
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i) && !b_waitpick[i])	numSelected++;
	}
	
	if(numSelected >= GetConVarInt(wm_max_players)) return true;
	else return false;
}

public Action FinishPicking(Handle timer) 
{
	for (int i = 1; i <= MaxClients; i++) 
	{
		if (IsValidClient(i) && !IsFakeClient(i)) 
		{
			if (GetClientTeam(i) == CS_TEAM_NONE || GetClientTeam(i) == CS_TEAM_SPECTATOR) 
			{
				SwitchPlayerTeam(i, CS_TEAM_SPECTATOR);
			} 
			else SwitchPlayerTeam(i, GetClientTeam(i));
		}
	}
	CPrintToChatAll("%t", "Pug Pick Finished", CHAT_PREFIX);
	IsPug = false;
	
	// show ready syeam
	for (int j = 1; j <= MaxClients; j++) 
	{
		if (IsValidClient(j) && !IsFakeClient(j) && (GetClientTeam(j) == TR || GetClientTeam(j) == CT))	ShowInfo(j, true, false, 0);
		CheckReady();
	}
	ServerCommand("wm_show_info 1");
	
	return Plugin_Handled;
}

/* Discord */
void SendToDiscord(int type)
{
	// test
	if(type == 0)
	{
		
	}
	// call player
	else if(type == 1)
	{
		int port = GetConVarInt(FindConVar("hostport"));
		int ip[4]; SteamWorks_GetPublicIP(ip);
		
		char server[512];
		Format(server, sizeof(server), "steam://connect/%d.%d.%d.%d:%d", ip[0], ip[1], ip[2], ip[3], port);
		
		int player_need = GetConVarInt(wm_max_players);
		player_need -= GetInTeamPlayer();
		
		char content[512];
		Format(content, sizeof(content), "@here We need %d more player!", player_need);
			
		DiscordWebHook hook = new DiscordWebHook(dsicord_webhook);
		hook.SlackMode = true;	
		hook.SetContent(content);
		
		MessageEmbed Embed = new MessageEmbed();
		
		Embed.SetColor("#63b8ff");		//blue
		Embed.SetTitle("Need more player!");
		Embed.AddField(g_server, server, true);
		Embed.AddField("Map", g_map, true);
		hook.Embed(Embed);
	
		hook.Send();
		delete hook;
	}
	// match end
	else if(type == 2)
	{
		char result[512];
		Format(result, sizeof(result), "%s - %d VS %d - %s", g_ct_name, GetCTTotalScore(), GetTTotalScore(), g_t_name);
		char view[512];
		Format(view, sizeof(view), "%sshowmatch.php?id=%d", siteLocation, match_id);
		
		char id[10];
		IntToString(match_id, id, sizeof(id));
		
		DiscordWebHook hook = new DiscordWebHook(dsicord_webhook);
		hook.SlackMode = true;
		
		MessageEmbed Embed = new MessageEmbed();
		Embed.SetTitle("Match End");
		Embed.SetColor("#12D400");				//green
		Embed.AddField("Match ID", id, true);
		Embed.AddField("Result", result, true);
		Embed.AddField("View Stats", view, true);
		hook.Embed(Embed);
		
		hook.Send();
		delete hook;
	}
	// cancel
	else if(type == 3)
	{
		char result[512];
		Format(result, sizeof(result), "%s - %d VS %d - %s", g_ct_name, GetCTTotalScore(), GetTTotalScore(), g_t_name);
		char view[512];
		Format(view, sizeof(view), "%sshowmatch.php?id=%d", siteLocation, match_id);
		
		char id[10];
		IntToString(match_id, id, sizeof(id));
		
		DiscordWebHook hook = new DiscordWebHook(dsicord_webhook);
		hook.SlackMode = true;
	
		MessageEmbed Embed = new MessageEmbed();
		Embed.SetColor("#ff2222");				//red
		Embed.SetTitle("Match Canceled");
		Embed.AddField("Match ID", id, true);
		Embed.AddField("Result", result, true);
		Embed.AddField("View Stats", view, true);
		hook.Embed(Embed);
		
		hook.Send();
		delete hook;
	}
	// start
	else if(type == 4)
	{
		char result[512];
		Format(result, sizeof(result), "%s VS %s", g_ct_name, g_t_name);
		
		char tvport[PLATFORM_MAX_PATH];
		GetConVarString(FindConVar("tv_port"), tvport, sizeof(tvport));
		int ip[4]; SteamWorks_GetPublicIP(ip);
		
		char server[512];
		Format(server, sizeof(server), "steam://connect/%d.%d.%d.%d:%s", ip[0], ip[1], ip[2], ip[3], tvport);
		
		char id[10];
		IntToString(match_id, id, sizeof(id));
		
		DiscordWebHook hook = new DiscordWebHook(dsicord_webhook);
		hook.SlackMode = true;
		
		MessageEmbed Embed = new MessageEmbed();
		Embed.SetColor("#FFA500");				//orange
		Embed.SetTitle("Match Start");
		Embed.AddField("Match ID", id, true);
		Embed.AddField("Teams", result, true);
		Embed.AddField("GOTV", server, true);
		hook.Embed(Embed);
		
		hook.Send();
		delete hook;
	}
}

int GetInTeamPlayer ()
{
	int players = 0;
	for (int i = 1; i <= MaxClients; i++)
	{
		if (IsValidClient(i) && !IsFakeClient(i) && (GetClientTeam(i) == TR || GetClientTeam(i) == CT)) 
		{
			players++;
		}
	}
	
	return players;
}

public Action CMD_CallPlayer(int client, int args)
{
	if(IsValidClient(client) && !IsFakeClient(client))
	{
		if(g_live)	CPrintToChat(client, "%T", "Can Not Call", client, CHAT_PREFIX);
		else
		{
			if(CanCall)
			{
				SendToDiscord(1);
				CPrintToChat(client, "%T", "Call Discord", client, CHAT_PREFIX);
				CanCall = false;
				Call_Cooldown = 500;
				h_CallTimer = CreateTimer(1.0, Timer_Call, _, TIMER_REPEAT|TIMER_FLAG_NO_MAPCHANGE);
			}
			else CPrintToChat(client, "%T", "Can Not Call Time", client, CHAT_PREFIX, Call_Cooldown);
		}
	}
	return Plugin_Handled;
}

public Action Timer_Call(Handle timer)
{
	--Call_Cooldown;
	
	if (Call_Cooldown <= 0)
	{
		CanCall = true;	
		if(h_CallTimer != INVALID_HANDLE)
		{
			KillTimer(h_CallTimer);
			h_CallTimer = INVALID_HANDLE;
		}
	}
}

/*************************************************************************************************/
public Action CMD_Test(int client, int args)
{

}