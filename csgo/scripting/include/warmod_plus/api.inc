/* License */
void CheckLicense()
{
	char url[512];
	Format(url, sizeof(url), "license.php?token=%s", user_token);
	PrintToServer("OnLicenseUrl: %s", url);
	HTTPClient httpClient;
	httpClient = new HTTPClient(APIURL);
	httpClient.Get(url, OnLicenseReceived);
}

public void OnLicenseReceived(HTTPResponse response, any value, const char[] error){
	PrintToServer("OnLicenseReceived: %s", error);
	if (response.Status != HTTPStatus_OK) {
		SetFailState("Failed to verify Warmod+ License. Error: %s", response.Status);
		return;
	}
	if (response.Data == null) {
		SetFailState("Failed to verify Warmod+ License. Error: Wrong token", response);
		return;
	}

	char version[32];

	JSONObject license = view_as<JSONObject>(response.Data);

	if(!license.HasKey("time_exp")) {
		SetFailState("Failed to verify Warmod+ License. Error: Wrong token", response);
	}

	license.GetString("time_exp", time_exp, sizeof(time_exp));
	license.GetString("time_now", time_now, sizeof(time_now));
	license.GetString("ftp_a", ftp_a, sizeof(ftp_a));
	license.GetString("ftp_p", ftp_p, sizeof(ftp_p));
	license.GetString("version", version, sizeof(version));

	LogMessage("******   WARMOD+ LICENSE ******");
	LogMessage("Date Expired: %s", time_exp);
	LogMessage("Date Now: %s", time_now);
	LogMessage("Latest Plugin Version: %s", version);
	LogMessage("******   WARMOD+ LICENSE ******");

	// check license expired or not.
	char expdatas[3][32], nowdatas[3][32];
	ExplodeString(time_exp, "-", expdatas, 3, 32);
				ExplodeString(time_now, "-", nowdatas, 3, 32);
				
				int nowtime = StringToInt(nowdatas[2]) + StringToInt(nowdatas[1]) * 32 + StringToInt(nowdatas[0]) * 367; 
	int exptime = StringToInt(expdatas[2]) + StringToInt(expdatas[1]) * 32 + StringToInt(expdatas[0]) * 367; 
	
	if( exptime - nowtime < 0 )  SetFailState("Your license is expired, please contact plugin author");

	// check version
	if(!StrEqual(version, WM_VERSION))	SetFailState("Your plugin version is not the latest, please contact plugin author.");

	authed = true;
	
	for (int i = 1; i <= MaxClients; i++) 
	{
		if(IsValidClient(i) && !IsFakeClient(i) && !player_loaded[i])	LoadPlayerInfo(i);
	}

	delete license;
}

// load player stats
void LoadPlayerInfo(int client)
{
	if(!IsValidClient(client) || IsFakeClient(client))	return;
	
	char authid[64], sAuth[64];
	GetClientAuthId(client, AuthId_Steam2, sAuth, sizeof(sAuth));
	SteamIDToCommunityID(sAuth, authid, sizeof(authid));
	
	char ip_address[32];
	GetClientIP(client, ip_address, sizeof(ip_address));

	HTTPClient httpClient;
	httpClient = new HTTPClient(APIURL);
	JSONObject player = new JSONObject();
	player.SetString("token", user_token);
	player.SetString("id", authid);
	player.SetString("ip", ip_address);
	httpClient.Post("player.php", player, OnPlayerLoad, client);
	delete player;
}

void OnPlayerLoad(HTTPResponse response, int client) {
	if (response.Status != HTTPStatus_OK) {
		CPrintToChat(client, "%T", "Profile Error", client, CHAT_PREFIX);
		LogError("Get player info failed. Error: %s", response.Status);
		KickClient(client, "%T", "Kick Load", client);
		f_PlayerRWS[client] = 0.00;
		player_team_name[client] = "";
		player_team_logo[client] = "";
		i_PlayerRounds[client] = 0;
		return;
	}
	if (response.Data == null) {
		CPrintToChat(client, "%T", "Profile Error", client, CHAT_PREFIX);
		LogError("Get player info failed. Error: Can not get response from web api.");
		KickClient(client, "%T", "Kick Load", client);
		f_PlayerRWS[client] = 0.00;
		player_team_name[client] = "";
		player_team_logo[client] = "";
		i_PlayerRounds[client] = 0;
		return;
	}

	float temp_rws;

	JSONObject player = view_as<JSONObject>(response.Data);

	if(!player.HasKey("rws")) {
		CPrintToChat(client, "%T", "Profile Error", client, CHAT_PREFIX);
		LogError("Get player info failed. Error: Can not get response from web api.");
		KickClient(client, "%T", "Kick Load", client);
		f_PlayerRWS[client] = 0.00;
		player_team_name[client] = "";
		player_team_logo[client] = "";
		i_PlayerRounds[client] = 0;
		return;
	}

	player.GetString("team_logo", player_team_logo[client], sizeof(player_team_logo[][]));
	player.GetString("team_name", player_team_name[client], sizeof(player_team_name[][]));
	player_team_id[client] = player.GetInt("team_id");
	player_team_leader[client] = player.GetBool("team_leader");
	temp_rws = player.GetFloat("rws");
	i_PlayerRounds[client] = player.GetInt("round");

	f_PlayerRWS[client] = temp_rws;
	f_PlayerRWS_join[client] = temp_rws;
	
	player_loaded[client] = true;
	
	CPrintToChat(client, "%T", "Profile Loaded", client, CHAT_PREFIX, f_PlayerRWS[client]);
					
					if(!StrEqual(player_team_name[client], "") && player_team_leader[client])
					{
						if(!StrEqual(player_team_logo[client], ""))
						{
							char path[1024];
							Format(path, sizeof(path), "materials/panorama/images/tournaments/teams/%s.svg", player_team_logo[client]);
							
							if(!FileExists(path)) GetTeamLogo(client, player_team_logo[client]);
							else
							{
								CPrintToChat(client, "%T", "Team Loaded Leader", client, CHAT_PREFIX, player_team_name[client]);
								player_team_use[client] = true;
							}
						}
						else
						{
							CPrintToChat(client, "%T", "Team Loaded Leader", client, CHAT_PREFIX, player_team_name[client]);
			player_team_use[client] = true;
		}
	}

	delete player;
}

void GetTeamLogo(int client, const char[] file)
{
	char url[128];
	Format(url, sizeof(url), "%s/%s.svg", logo_web, file);
	
	Handle LogoPack = CreateDataPack();
	ResetPack(LogoPack);
	WritePackString(LogoPack, file);
	WritePackCell(LogoPack, GetClientSerial(client));
	
	Handle h_TRequest = SteamWorks_CreateHTTPRequest(k_EHTTPMethodPOST, url);
	SteamWorks_SetHTTPCallbacks(h_TRequest, OnTeamLogoDownload);
	SteamWorks_SetHTTPRequestContextValue(h_TRequest, LogoPack);
	SteamWorks_SendHTTPRequest(h_TRequest);
}

public int OnTeamLogoDownload(Handle h_TRequest, bool bFailure, bool bRequestSuccessful, EHTTPStatusCode eStatusCode, Handle LogoPack) 
{
	char file[1024], path[1024];
	ResetPack(LogoPack);
	ReadPackString(LogoPack, file, sizeof(file));
	int client = GetClientFromSerial(ReadPackCell(LogoPack)); // Validate the client serial
	delete LogoPack;
		
	if (bRequestSuccessful && eStatusCode == k_EHTTPStatusCode200OK) 
	{
		if(IsValidClient(client))
		{
			Format(path, sizeof(path), "materials/panorama/images/tournaments/teams/%s.svg", file);

			if(!SteamWorks_WriteHTTPResponseBodyToFile(h_TRequest, path))
			{
				LogError("Failed to write team logo to server.");
				CPrintToChat(client, "%T", "Logo Download Failed", client, CHAT_PREFIX);
			}
			else
			{
				Handle PathPack = CreateDataPack();
				ResetPack(PathPack);
				WritePackString(PathPack, path);
				CreateTimer(5.0, Timer_DownloadToClients, PathPack);
				
				Handle DecalPack = CreateDataPack();
				ResetPack(DecalPack);
				WritePackCell(DecalPack, GetClientSerial(client));
				CreateTimer(20.0, Timer_DownloadLogo, DecalPack);
			}
		}
	}
	else
	{ 
		LogError("Failed to download team logo to server.");
		CPrintToChat(client, "%T", "Logo Download Failed", client, CHAT_PREFIX);
	}
}

/*
public void OnDownloadSuccess(int client, char[] filename) { 
	if (client > 0)  return; 
	LogMessage("All players successfully downloaded file '%s'!", filename); 
} 

public void OnDownloadFailure(int client, char[] filename) { 
	LogError("%N failed to downloaded file '%s'!", client, filename); 
	//CPrintToChat(client, "%T", "Logo Download Failed Client", client, CHAT_PREFIX);
} 
*/

public Action Timer_DownloadToClients(Handle timer, Handle PathPack)
{
	char path[64];
	ResetPack(PathPack);
	ReadPackString(PathPack, path, sizeof(path));

	AddFileToDownloadsTable(path);
	
	// Engine error "Called RequestFile_OLD"
	// https://github.com/jonatan1024/latedl/issues/1
	// AddLateDownload(path);
	delete PathPack;
}

public Action Timer_DownloadLogo(Handle timer, Handle DecalPack)
{
	ResetPack(DecalPack);
	int client = GetClientFromSerial(ReadPackCell(DecalPack)); // Validate the client serial
	
	if (LogoTimer[client] != INVALID_HANDLE)
    {
        KillTimer(LogoTimer[client]);
        LogoTimer[client] = INVALID_HANDLE;
    }
 
	if (!IsValidClient(client)) return Plugin_Stop;
	
	if(player_team_leader[client] && !StrEqual(player_team_logo[client], ""))	
	{
		CPrintToChat(client, "%T", "Logo Downloaded", client, CHAT_PREFIX);
		CPrintToChat(client, "%T", "Logo Downloaded Next Map", client, CHAT_PREFIX);
	}
	
	player_team_use[client] = true;
	
	delete DecalPack;
	
	return Plugin_Continue;
}

void SaveStats(int client)
{
	if(!save_db)	return;
	
	char mid[32];
	IntToString(match_id, mid, sizeof(mid));
	
	Handle h_StRequest = SteamWorks_CreateHTTPRequest(k_EHTTPMethodPOST, stats_api);
	SteamWorks_SetHTTPRequestHeaderValue(h_StRequest, "Pragma", "no-cache");
	SteamWorks_SetHTTPRequestHeaderValue(h_StRequest, "Cache-Control", "no-cache");
	
	addedtorequest = 0;
	
	// All
	if(client == -1)
	{
		for (int i = 1; i <= MaxClients; i++) 
		{
			if(IsValidClient(i) && !IsFakeClient(i))	
			{
				if(GetClientTeam(i) > 1 && WasClientInGame[i] && WasClientTeam[i] > 1)
				{
					AddDataToStatsRequest(h_StRequest, i);
					addedtorequest++;
				}
			}
		}
	}
	else
	{
		if(IsValidClient(client) &&  GetClientTeam(client) > 1 && WasClientInGame[client] && WasClientTeam[client] > 1)	
			AddDataToStatsRequest(h_StRequest, client);
	}
	
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, "t", user_token);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, "mid", mid);
	
	SteamWorks_SetHTTPCallbacks(h_StRequest, OnStatsHTTPComplete);
	
	if(h_StRequest == INVALID_HANDLE || !SteamWorks_SendHTTPRequest(h_StRequest)) 
	{
		LogError("ERROR Request(%i): Save Stats", h_StRequest);
		return;
	}
}

void AddDataToStatsRequest(Handle h_StRequest, int client)
{
	char player_name[64];
	char authid[20];
	int team = GetClientTeam(client);
	GetClientName(client, player_name, sizeof(player_name));
	StripFilename(player_name, sizeof(player_name));
	
	if(!IsFakeClient(client))
	{
		char sAuth[64];
		GetClientAuthId(client, AuthId_Steam2, sAuth, sizeof(sAuth));
		SteamIDToCommunityID(sAuth, authid, sizeof(authid));
	}
	else
	{
		Format(authid, sizeof(authid), "BOT_%s", player_name);
	}
	
	
	
	if (team_switch) 
	{
		if (team == 2)	team = 1;
		else if (team == 3)	team = 2;
	} 
	else 
	{
		if (team == 2) team = 2;
		else if (team == 3) team = 1;
	}
	
	char steam[32], rws[32], rws2[32], round[32], k[32], d[32], a[32], hs[32], tk[32], ata[32], dmg[32], hit[32], shot[32], last[32], won[32], 
	k1[32], k2[32], k3[32], k4[32], k5[32], mvp[32], defuse[32], plant[32], explode[32], hostage[32];
	FloatToString(f_PlayerRWS[client], rws, sizeof(rws));
	float rws_diff = f_PlayerRWS[client];
	rws_diff -= f_PlayerRWS_join[client];

	FloatToString(rws_diff, rws2, sizeof(rws2));
	IntToString(team, steam, sizeof(steam));
	IntToString(match_stats[client][MATCH_ROUND], round, sizeof(round));
	IntToString(match_stats[client][MATCH_KILLS], k, sizeof(k));
	IntToString(match_stats[client][MATCH_DEATHS], d, sizeof(d));
	IntToString(match_stats[client][MATCH_ASSIST], a, sizeof(a));
	IntToString(match_stats[client][MATCH_HEADSHOTS], hs, sizeof(hs));
	IntToString(match_stats[client][MATCH_TEAMKILLS], tk, sizeof(tk));
	IntToString(match_stats[client][MATCH_ATA], ata, sizeof(ata));
	IntToString(match_stats[client][MATCH_DAMAGE], dmg, sizeof(dmg));
	IntToString(match_stats[client][MATCH_HITS], hit, sizeof(hit));
	IntToString(match_stats[client][MATCH_SHOTS], shot, sizeof(shot));
	IntToString(match_stats[client][MATCH_LAST], last, sizeof(last));
	IntToString(match_stats[client][MATCH_WON], won, sizeof(won));
	IntToString(match_stats[client][MATCH_1K], k1, sizeof(k1));
	IntToString(match_stats[client][MATCH_2K], k2, sizeof(k2));
	IntToString(match_stats[client][MATCH_3K], k3, sizeof(k3));
	IntToString(match_stats[client][MATCH_4K], k4, sizeof(k4));
	IntToString(match_stats[client][MATCH_5K], k5, sizeof(k5));
	IntToString(match_stats[client][MATCH_MVP], mvp, sizeof(mvp));
	IntToString(match_stats[client][MATCH_DEFUSE], defuse, sizeof(defuse));
	IntToString(match_stats[client][MATCH_PLANT], plant, sizeof(plant));
	IntToString(match_stats[client][MATCH_EXPLODE], explode, sizeof(explode));
	IntToString(match_stats[client][MATCH_HOSTAGE], hostage, sizeof(hostage));
	
	char raid[32],rteam[32],rrws[32],rround[32],rk[32],rd[32],ra[32],
	rhs[32],rtk[32],rata[32],rdmg[32],rhit[32],rshot[32],rlast[32],rwon[32],
	rk1[32],rk2[32],rk3[32],rk4[32],rk5[32],rmvp[32],rrws2[32],
	rdefuse[32],rplant[32],rexplode[32],rhostage[32];
	
	Format(raid, sizeof(raid), "aid[%d]", addedtorequest);
	Format(rteam, sizeof(rteam), "team[%d]", addedtorequest);
	Format(rrws, sizeof(rrws), "rws[%d]", addedtorequest);
	Format(rround, sizeof(rround), "round[%d]", addedtorequest);
	Format(rk, sizeof(rk), "k[%d]", addedtorequest);
	Format(rd, sizeof(rd), "d[%d]", addedtorequest);
	Format(ra, sizeof(ra), "a[%d]", addedtorequest);
	Format(rhs, sizeof(rhs), "hs[%d]", addedtorequest);
	Format(rtk, sizeof(rtk), "tk[%d]", addedtorequest);
	Format(rata, sizeof(rata), "ata[%d]", addedtorequest);
	Format(rdmg, sizeof(rdmg), "dmg[%d]", addedtorequest);
	Format(rhit, sizeof(rhit), "hit[%d]", addedtorequest);
	Format(rshot, sizeof(rshot), "shot[%d]", addedtorequest);
	Format(rlast, sizeof(rlast), "last[%d]", addedtorequest);
	Format(rwon, sizeof(rwon), "won[%d]", addedtorequest);
	Format(rk1, sizeof(rk1), "k1[%d]", addedtorequest);
	Format(rk2, sizeof(rk2), "k2[%d]", addedtorequest);
	Format(rk3, sizeof(rk3), "k3[%d]", addedtorequest);
	Format(rk4, sizeof(rk4), "k4[%d]", addedtorequest);
	Format(rk5, sizeof(rk5), "k5[%d]", addedtorequest);
	Format(rmvp, sizeof(rmvp), "mvp[%d]", addedtorequest);
	Format(rrws2, sizeof(rrws2), "rws2[%d]", addedtorequest);
	Format(rdefuse, sizeof(rdefuse), "defuse[%d]", addedtorequest);
	Format(rplant, sizeof(rplant), "plant[%d]", addedtorequest);
	Format(rexplode, sizeof(rexplode), "explode[%d]", addedtorequest);
	Format(rhostage, sizeof(rhostage), "hostage[%d]", addedtorequest);
	
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,raid, authid);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rteam, steam);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rrws, rws);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rround, round);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk, k);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rd, d);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,ra, a);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rhs, hs);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rtk, tk);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rata, ata);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rdmg, dmg);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rhit, hit);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rshot, shot);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rlast, last);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rwon, won);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk1, k1);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk2, k2);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk3, k3);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk4, k4);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rk5, k5);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rmvp, mvp);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rrws2, rws2);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rdefuse, defuse);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rplant, plant);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rexplode, explode);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest,rhostage, hostage);


	// add weapons
	char sWeapon[100], kills[10];
	for (int i = 0; i < NUM_WEAPONS; i++)
	{
		Format(sWeapon, sizeof(sWeapon), "%s", weapon_list[i]);
		ReplaceString(sWeapon, 50, "_projectile", "");
		ReplaceString(sWeapon, 50, "weapon_", "");
		Format(sWeapon, sizeof(sWeapon), "%s[%d]", sWeapon, addedtorequest);
		IntToString(weapon_kills[client][i], kills, sizeof(kills));
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, sWeapon, kills);
	}

	char h0[32], h1[32], h2[32], h3[32], h4[32], h5[32], h6[32], h7[32];
	Format(h0, sizeof(h0), "generic[%d]", addedtorequest);
	Format(h1, sizeof(h1), "head[%d]", addedtorequest);
	Format(h2, sizeof(h2), "chest[%d]", addedtorequest);
	Format(h3, sizeof(h3), "stomach[%d]", addedtorequest);
	Format(h4, sizeof(h4), "left_arm[%d]", addedtorequest);
	Format(h5, sizeof(h5), "right_arm[%d]", addedtorequest);
	Format(h6, sizeof(h6), "left_leg[%d]", addedtorequest);
	Format(h7, sizeof(h7), "right_leg[%d]", addedtorequest);
	
	int ihitbox = 0;
	char shitbox[10];
	for (int i = 0; i <= 7; i++)
	{
		ihitbox = hitbox[client][i];
		IntToString(ihitbox, shitbox, sizeof(shitbox));
		
		if(i == 0)			SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h0, shitbox);
		else if(i == 1)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h1, shitbox);
		else if(i == 2)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h2, shitbox);
		else if(i == 3)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h3, shitbox);
		else if(i == 4)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h4, shitbox);
		else if(i == 5)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h5, shitbox);
		else if(i == 6)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h6, shitbox);
		else if(i == 7)		SteamWorks_SetHTTPRequestGetOrPostParameter(h_StRequest, h7, shitbox);
		
		ihitbox = 0;
		Format(shitbox, sizeof(shitbox), "");
	}
}

public int OnStatsHTTPComplete(Handle h_StRequest, bool bFailure, bool bRequestSuccessful, EHTTPStatusCode eStatusCode, any data) 
{
	if (bRequestSuccessful && eStatusCode == k_EHTTPStatusCode200OK) 
	{
		int length = 2048;
		if(SteamWorks_GetHTTPResponseBodySize(h_StRequest, length) && length > 2048) {
			length = 4096;
		}
		char[] response = new char[length];
		SteamWorks_GetHTTPResponseBodyData(h_StRequest, response, length);
		
		if(StrContains(response, "<br />") != -1) 
		{
			LogError("Failed to update stats to server. Error: %s", response);
			return;
		}
		
		KeyValues tempKV = CreateKeyValues("response");
		if(StringToKeyValues(tempKV, response, "response")) 
		{
			if (tempKV.JumpToKey("response", false)) 
			{
				char temp_response[32];
				tempKV.GetString("response", temp_response, sizeof(temp_response), "Unknown");
				
				if (StrEqual(temp_response, "Unknown")) 
					LogError("Failed to update stats to server. Error: Unable to get response from server.");
			}
			else	LogError("Failed to update stats to server. Error: Unable to get response from server.");
		}
		else LogError("Failed to update stats to server. Error: Unable to process reponse from server.");
		
		delete tempKV;
	} 
	else	LogError("SteamWorks error (status code %i)", view_as<int>(eStatusCode));
	
	delete h_StRequest;
}

void UploadResultsRound() {
	
	if(!save_db)	return;
	
	int g_overtime_rounds;
	int g_played_out;
	
	if (!GetConVarBool(mp_match_can_clinch)) {
		g_played_out = 1;
	}
	if (GetConVarBool(mp_overtime_enable)) {
		g_overtime_rounds = (GetConVarInt(mp_overtime_maxrounds)/2);
	}
	
	char mr[32], otr[32], otc[32], po[32], cts[32], ctots[32], ctfh[32], ctsh[32], ts[32], tots[32], tfh[32], tsh[32];
	IntToString((GetConVarInt(mp_maxrounds) / 2), mr, sizeof(mr));
	IntToString(g_overtime_rounds, otr, sizeof(otr));
	IntToString(g_overtime_count, otc, sizeof(otc));
	IntToString(g_played_out, po, sizeof(po));
	IntToString((GetCTTotalScore()), cts, sizeof(cts));
	IntToString(g_scores[SCORE_CT][SCORE_FIRST_HALF], ctfh, sizeof(ctfh));
	IntToString(g_scores[SCORE_CT][SCORE_SECOND_HALF], ctsh, sizeof(ctsh));
	IntToString((GetCTOTTotalScore()), ctots, sizeof(ctots));
	IntToString((GetTTotalScore()), ts, sizeof(ts));
	IntToString(g_scores[SCORE_T][SCORE_FIRST_HALF], tfh, sizeof(tfh));
	IntToString(g_scores[SCORE_T][SCORE_SECOND_HALF], tsh, sizeof(tsh));
	IntToString((GetTOTTotalScore()), tots, sizeof(tots));
	
	Handle h_SRequest = SteamWorks_CreateHTTPRequest(k_EHTTPMethodPOST, score_api);
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Pragma", "no-cache");
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Cache-Control", "no-cache");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "t", user_token);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "demo", g_log_filename);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "comp", g_competition);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ev", g_event);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "map", g_map);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "mr", mr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otr", otr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otc", otc);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "po", po);
		
	if (match_id != 0) 
	{
		char id[32];
		IntToString(match_id, id, sizeof(id));
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "type", "updater");
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "id", id);
		
		if (team_switch) 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  tots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", ctots);
		} 
		else 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  ctots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", tots);
			
		}
	} 
	else 
	{
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "type", "insertr");
		
		if (team_switch) 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  tots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", ctots);
		} 
		else 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  ctots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", tots);
		}
	}
	
	SteamWorks_SetHTTPCallbacks(h_SRequest, OnScoreHTTPComplete);
		
	if(h_SRequest == INVALID_HANDLE || !SteamWorks_SendHTTPRequest(h_SRequest)) 
	{
		LogError("ERROR Request(%i): Update round score", h_SRequest);
		return;
	}
}

public int OnScoreHTTPComplete(Handle h_SRequest, bool bFailure, bool bRequestSuccessful, EHTTPStatusCode eStatusCode, any data) 
{
	if (bRequestSuccessful && eStatusCode == k_EHTTPStatusCode200OK) 
	{
		int length = 512;
		if(SteamWorks_GetHTTPResponseBodySize(h_SRequest, length) && length > 1024) {
			length = 1024;
		}
		char[] response = new char[length];
		SteamWorks_GetHTTPResponseBodyData(h_SRequest, response, length);
		if(StrContains(response, "<br />") != -1) 
		{
			LogError("Failed to update score to server. Error: %s", response);
			return;
		}
		
		//Convert to KeyValue
		KeyValues tempKV = CreateKeyValues("response");
		if(StringToKeyValues(tempKV, response, "response")) 
		{
			if (tempKV.JumpToKey("response", false)) 
			{
				char temp_response[32], temp_id[32];
				tempKV.GetString("response", temp_response, sizeof(temp_response), "Unknown");
				tempKV.GetString("id", temp_id, sizeof(temp_id), "Unknown");
				
				if (StrEqual(temp_response, "Unknown") || StrEqual(temp_id, "Unknown")) 
					LogError("Failed to update score to server. Error: Unable to get response from server.");
				else
				{
					match_id = StringToInt(temp_id);
					//char g_def_backup[128];
					//GetConVarString(FindConVar("mp_backup_round_file_pattern"), g_def_backup, sizeof(g_def_backup));
					//Format(g_def_backup, sizeof(g_def_backup), "%i_%s", match_id, g_def_backup);
					//ServerCommand("mp_backup_round_file_pattern %s", g_def_backup);
				}
			}
			else	LogError("Failed to update score to server. Error: Unable to get response from server.");
		}
		else LogError("Failed to update score to server. Error: Unable to process reponse from server.");
		
		delete tempKV;
	} 
	else	LogError("SteamWorks error (status code %i)", view_as<int>(eStatusCode));
	
	delete h_SRequest;
}

void UploadResults() 
{
	if(!save_db)	return;
	
	char mr[32], otr[32], otc[32], po[32], cts[32], ctots[32], ctfh[32], ctsh[32], ts[32], tots[32], tfh[32], tsh[32], ml[32];
	IntToString(lt_overtime_max_rounds, mr, sizeof(mr));
	IntToString(lt_overtime_max_rounds, otr, sizeof(otr));
	IntToString(lt_overtime_count, otc, sizeof(otc));
	IntToString(lt_played_out, po, sizeof(po));
	IntToString(lt_ct_overall_score, cts, sizeof(cts));
	IntToString(lt_ct_first_half_score, ctfh, sizeof(ctfh));
	IntToString(lt_ct_second_half_score, ctsh, sizeof(ctsh));
	IntToString(lt_ct_overtime_score, ctots, sizeof(ctots));
	IntToString(lt_t_overall_score, ts, sizeof(ts));
	IntToString(lt_t_first_half_score, tfh, sizeof(tfh));
	IntToString(lt_t_second_half_score, tsh, sizeof(tsh));
	IntToString(lt_t_overtime_score, tots, sizeof(tots));
	IntToString(lt_match_length, ml, sizeof(ml));
	
	Handle h_SRequest = SteamWorks_CreateHTTPRequest(k_EHTTPMethodPOST, score_api);
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Pragma", "no-cache");
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Cache-Control", "no-cache");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "t", user_token);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "demo", g_log_filename);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "comp", g_competition);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ev", g_event);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "map", g_map);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "mr", mr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otr", otr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otc", otc);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "po", po);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ml", ml);
		
	if (match_id != 0) 
	{
		char id[32];
		IntToString(match_id, id, sizeof(id));
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "type", "update");
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "id", id);
		
		if (team_switch) 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  tots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", ctots);
		} 
		else 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  ctots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", tots);
			
		}
	} 
	else 
	{
		SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "type", "insert");
		
		if (team_switch) 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  tots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", ctots);
		} 
		else 
		{
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_ct_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", cts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", ctfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", ctsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  ctots);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_t_id);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", ts);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", tfh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", tsh);
			SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", tots);
		}
	}
	
	SteamWorks_SetHTTPCallbacks(h_SRequest, OnResultHTTPComplete);
		
	if(h_SRequest == INVALID_HANDLE || !SteamWorks_SendHTTPRequest(h_SRequest)) 
	{
		LogError("ERROR Request(%i): Update match result", h_SRequest);
		return;
	}
}

public int OnResultHTTPComplete(Handle h_SRequest, bool bFailure, bool bRequestSuccessful, EHTTPStatusCode eStatusCode, any data) 
{
	if (bRequestSuccessful && eStatusCode == k_EHTTPStatusCode200OK) 
	{
		int length = 512;
		if(SteamWorks_GetHTTPResponseBodySize(h_SRequest, length) && length > 1024) {
			length = 1024;
		}
		char[] response = new char[length];
		SteamWorks_GetHTTPResponseBodyData(h_SRequest, response, length);
		if(StrContains(response, "<br />") != -1) 
		{
			LogError("Failed to update result to server. Error: %s", response);
			return;
		}
		
		//Convert to KeyValue
		KeyValues tempKV = CreateKeyValues("response");
		if(StringToKeyValues(tempKV, response, "response")) 
		{
			if (tempKV.JumpToKey("response", false)) 
			{
				char temp_response[32], temp_id[32];
				tempKV.GetString("response", temp_response, sizeof(temp_response), "Unknown");
				tempKV.GetString("id", temp_id, sizeof(temp_id), "Unknown");
				
				if (StrEqual(temp_response, "Unknown") || StrEqual(temp_id, "Unknown")) 
					LogError("Failed to update result to server. Error: Unable to get response from server.");
				else
				{
					CPrintToChatAll("%t", "MySQL data uploaded", CHAT_PREFIX);
					SendToDiscord(2);
					match_id = StringToInt(temp_id);
					//char g_def_backup[128];
					//GetConVarString(FindConVar("mp_backup_round_file_pattern"), g_def_backup, sizeof(g_def_backup));
					//Format(g_def_backup, sizeof(g_def_backup), "%i_%s", match_id, g_def_backup);
					//ServerCommand("mp_backup_round_file_pattern %s", g_def_backup);
				}
			}
			else	LogError("Failed to update result to server. Error: Unable to get response from server.");
		}
		else LogError("Failed to update result to server. Error: Unable to process reponse from server.");
		
		delete tempKV;
	} 
	else	LogError("SteamWorks error (status code %i)", view_as<int>(eStatusCode));
	
	delete h_SRequest;
}

void CreateResultKey() 
{
	if(!save_db)	return;
	
	int g_overtime_rounds;
	int g_played_out;
	if (!GetConVarBool(mp_match_can_clinch)) {
		g_played_out = 1;
	}
	if (GetConVarBool(mp_overtime_enable)) {
		g_overtime_rounds = (GetConVarInt(mp_overtime_maxrounds)/2);
	}
	
	char mr[32], otr[32], po[32];
	IntToString((GetConVarInt(mp_maxrounds) / 2), mr, sizeof(mr));
	IntToString(g_overtime_rounds, otr, sizeof(otr));
	IntToString(g_played_out, po, sizeof(po));
	
	Handle h_SRequest = SteamWorks_CreateHTTPRequest(k_EHTTPMethodPOST, score_api);
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Pragma", "no-cache");
	SteamWorks_SetHTTPRequestHeaderValue(h_SRequest, "Cache-Control", "no-cache");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "t", user_token);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "demo", g_log_filename);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "comp", g_competition);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ev", g_event);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "map", g_map);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "mr", mr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otr", otr);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "otc", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "po", po);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "type", "create");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctid", g_t_id);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "cts", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctfh", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctsh", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ctots",  "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tid", g_ct_id);
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "ts", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tfh", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tsh", "0");
	SteamWorks_SetHTTPRequestGetOrPostParameter(h_SRequest, "tots", "0");
	SteamWorks_SetHTTPCallbacks(h_SRequest, OnCreateHTTPComplete);		
	
	if(h_SRequest == INVALID_HANDLE || !SteamWorks_SendHTTPRequest(h_SRequest)) 
	{
		SetFailState("ERROR Request(%i): Create Match", h_SRequest);
		return;
	}
}

public int OnCreateHTTPComplete(Handle h_SRequest, bool bFailure, bool bRequestSuccessful, EHTTPStatusCode eStatusCode, any data) 
{
	if (bRequestSuccessful && eStatusCode == k_EHTTPStatusCode200OK) 
	{
		int length = 512;
		if(SteamWorks_GetHTTPResponseBodySize(h_SRequest, length) && length > 1024) {
			length = 1024;
		}
		//char[] response = new char[length];
		char response[4096];
		SteamWorks_GetHTTPResponseBodyData(h_SRequest, response, length);
		if(StrContains(response, "<br />") != -1) 
		{
			LogError("Failed to create match data in server. Error: %s", response);
			return;
		}
		
		//Convert to KeyValue
		KeyValues tempKV = CreateKeyValues("response");
		if(StringToKeyValues(tempKV, response, "response")) 
		{
			if (tempKV.JumpToKey("response", false)) 
			{
				char temp_response[32], temp_id[32];
				tempKV.GetString("response", temp_response, sizeof(temp_response), "Unknown");
				tempKV.GetString("id", temp_id, sizeof(temp_id), "Unknown");
				
				if (StrEqual(temp_response, "Unknown") || StrEqual(temp_id, "Unknown")) 
					LogError("Failed to create match data in server. Error: Unable to get response from server.");
				else
				{
					match_id = StringToInt(temp_id);
					char g_def_backup[128];
					GetConVarString(FindConVar("mp_backup_round_file_pattern"), g_def_backup, sizeof(g_def_backup));
					Format(g_def_backup, sizeof(g_def_backup), "%i_%s", match_id, g_def_backup);
					ServerCommand("mp_backup_round_file_pattern %s", g_def_backup);
					CPrintToChatAll("%t", "MySQL Match_ID", CHAT_PREFIX, match_id);
					ServerCommand("hostname %s (ID = %d)", g_server, match_id);
					
					SendToDiscord(4);
				}
			}
			else	LogError("Failed to create match data in server. Error: Unable to get response from server.");
		}
		else LogError("Failed to create match data in server. Error: Unable to process reponse from server.");
		
		delete tempKV;
	} 
	else	LogError("SteamWorks error (status code %i)", view_as<int>(eStatusCode));
	
	delete h_SRequest;
}

stock bool SteamIDToCommunityID(const char[] sSteamID, char[] sCommunityID, int size)
{
    if(strlen(sSteamID) < 11 || sSteamID[0]!='S' || sSteamID[6]=='I')
    {
        sCommunityID[0] = 0;
        return false;
    }

    int iUpper = 765611979;
    int iFriendID = StringToInt(sSteamID[10])*2 + 60265728 + sSteamID[8]-48;

    int iDiv = iFriendID / 100000000;
    int tt = iDiv / 10 + 1;
    int iIdx = 9 - (iDiv ? tt : 0);
    iUpper += iDiv;
    
    IntToString(iFriendID, sCommunityID[iIdx], size-iIdx);
    iIdx = sCommunityID[9];
    IntToString(iUpper, sCommunityID, size);
    sCommunityID[9] = iIdx;

    return true;
}

stock void DownloadTeamLogo()
{
	Handle dir = OpenDirectory("materials/panorama/images/tournaments/teams/");
	if (dir == null)
	{
		LogError("[SM] Unable to read directory: 'materials/panorama/images/tournaments/teams/'");
		return;
	}
	
	FileType type;
	char filename[PLATFORM_MAX_PATH];
	char fullpath[PLATFORM_MAX_PATH];
	
	while (ReadDirEntry(dir, filename, sizeof(filename), type))
	{
		if(type != FileType_File)	continue;
		
		if(!StrEqual(filename[strlen(filename) - 4], ".svg"))	continue;
		
		PrintToServer("[SM] Loading team logo: %s", filename);
		Format(fullpath, sizeof(fullpath), "materials/panorama/images/tournaments/teams/%s", filename);
		AddFileToDownloadsTable(fullpath);
	}
	
	CloseHandle(dir);
}