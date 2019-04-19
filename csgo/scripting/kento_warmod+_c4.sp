#pragma semicolon 1

#include <sourcemod>
#include <sdktools>
#include <cstrike>
#include <kento_csgocolors>

#define PLUGIN_VERSION "Warmod+.1"

#pragma newdecls required

#define SPEC 1
#define TR 2
#define CT 3

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

Handle g_hTimer_Countdown = INVALID_HANDLE;

float g_fExplosionTime, g_fCounter, g_fTimer;
char CHAT_PREFIX[64];

bool g_bAll;

ConVar Cvar_All;
ConVar g_hCvarTimer;
ConVar wm_chat_prefix;

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

#include <warmod_plus/c4msg>

//* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

public Plugin myinfo = 
{
	name = "Warmod+ C4 Addons",
	author = "Kento",
	description = "Warmod+ C4 Timer And Bomb Defuse Messages for Specs.",
	version = PLUGIN_VERSION,
	url = "http://steamcommunity.com/id/kentomatoryoshika/"
};

public void OnPluginStart()
{
	LoadTranslations("kento.warmod+.phrases");
	
	Cvar_All = CreateConVar("wm_c4_all", "0", "Show c4 message to all? 1=all 0=gotv & spec.", _, true, 0.0, true, 1.0);
	wm_chat_prefix = FindConVar("wm_chat_prefix");
	Cvar_All.AddChangeHook(OnConVarChanged);
	wm_chat_prefix.AddChangeHook(OnConVarChanged);
	
	//cfg moved into warmod
	//AutoExecConfig(true, "c4timer");

	g_hCvarTimer = FindConVar("mp_c4timer");
	g_hCvarTimer.AddChangeHook(OnConVarChanged);
	
	HookEvent("bomb_planted", EventBombPlanted, EventHookMode_Pre);
	HookEvent("round_start", EventRoundStart, EventHookMode_PostNoCopy);
	HookEvent("bomb_exploded", EventBombExploded, EventHookMode_PostNoCopy);
	HookEvent("bomb_defused", EventBombDefused, EventHookMode_Post);
	HookEvent( "player_death", Event_PlayerDeath, EventHookMode_Pre );
	
	// for bomb messages from 
	// https://github.com/Metapyziks/csgo-retakes-ziksallocator
	HookEvent("bomb_planted", Event_Bomb_Planted_Post, EventHookMode_Post);
	HookEvent("bomb_begindefuse", Event_Bomb_Defuse_Begin_Post, EventHookMode_Post);
	HookEvent("bomb_abortdefuse", Event_Bomb_Defuse_Abort_Post, EventHookMode_Post);
	HookEvent("bomb_exploded", Event_Bomb_Exploded_Post, EventHookMode_Post);
}

public void OnConfigsExecuted()
{
	g_fTimer = g_hCvarTimer.FloatValue;
	g_bAll = g_hCvarTimer.BoolValue;
	wm_chat_prefix.GetString(CHAT_PREFIX, sizeof(CHAT_PREFIX));
}

public void OnConVarChanged(ConVar convar, const char[] oldValue, const char[] newValue)
{
	if(convar == g_hCvarTimer)	g_fTimer = g_hCvarTimer.FloatValue;
	else if(convar == Cvar_All)	g_bAll = g_hCvarTimer.BoolValue;
	else if(convar == wm_chat_prefix)	wm_chat_prefix.GetString(CHAT_PREFIX, sizeof(CHAT_PREFIX));
}

public Action Event_Bomb_Planted_Post(Event event, const char[]name, bool dontBroadcast)
{
	BombTime_BombPlanted();
}

public Action Event_Bomb_Defuse_Begin_Post(Event event, const char[]name, bool dontBroadcast)
{
	BombTime_BombBeginDefuse(event);
}

public Action Event_Bomb_Defuse_Abort_Post(Event event, const char[]name, bool dontBroadcast)
{
	BombTime_BombAbortDefuse(event);
}

public Action Event_Bomb_Exploded_Post(Event event, const char[]name, bool dontBroadcast)
{
	BombTime_BombExploded();
}

public Action Event_PlayerDeath( Event event, const char[] name, bool dontBroadcast )
{
	BombTime_PlayerDeath( event );
}

public Action EventRoundStart(Handle event, const char[]name, bool dontBroadcast)
{
	if(g_hTimer_Countdown != INVALID_HANDLE && CloseHandle(g_hTimer_Countdown))
		g_hTimer_Countdown = INVALID_HANDLE;
		
	g_DetonateTime = 0.0;
	g_DefuseEndTime = 0.0;
	g_DefusingClient = -1;
	g_CurrentlyDefusing = false;
}

public Action EventBombPlanted(Handle event, const char[]name, bool dontBroadcast)
{
	g_fCounter = g_fTimer - 1.0;
	g_fExplosionTime = GetEngineTime() + g_fTimer;
		
	g_hTimer_Countdown = CreateTimer(((g_fExplosionTime - g_fCounter) - GetEngineTime()), TimerCountdown, _, TIMER_REPEAT);

	return Plugin_Continue;
}


public Action EventBombDefused(Event event, const char[]name, bool dontBroadcast)
{
	if(g_hTimer_Countdown != INVALID_HANDLE && CloseHandle(g_hTimer_Countdown))
		g_hTimer_Countdown = INVALID_HANDLE;
		
	BombTime_BombDefused(event);
}

public Action EventBombExploded(Handle event, const char[]name, bool dontBroadcast)
{
	if(g_hTimer_Countdown != INVALID_HANDLE && CloseHandle(g_hTimer_Countdown))
		g_hTimer_Countdown = INVALID_HANDLE;
}

public Action TimerCountdown(Handle timer, any data)
{
	BombMessage(RoundToFloor(g_fCounter));
	
	g_fCounter--;
	if(g_fCounter <= 0)
	{
		g_hTimer_Countdown = INVALID_HANDLE;
		return Plugin_Stop;
	}
	
	return Plugin_Continue;
}


public void BombMessage(int count)
{
	char sBuffer[192];
	
	if(count <= 10)
	{	
		for(int i = 1; i <= MaxClients; i++)
		{
			Format(sBuffer, sizeof(sBuffer), "countdown %i", count);
			Format(sBuffer, sizeof(sBuffer), "%T", sBuffer, i, count);
	
			if(!g_bAll)
			{
				if(IsClientInGame(i) && GetClientTeam(i) != TR && GetClientTeam(i) != CT)
				{
					PrintHintText(i, sBuffer);
				}
			}
			
			else if(g_bAll)
			{
				if(IsClientInGame(i))
				{
					PrintHintText(i, sBuffer);
				}
			}
		}
	}
}

stock bool IsValidClient(int client)
{
	if (client <= 0 || client > MaxClients) 
	{
		return false;
	}
	
	if (!IsClientInGame(client)) 
	{
		return false;
	}
	
	if (IsFakeClient(client))
	{
		return false;
	}
	
	if (!IsClientConnected(client))
	{
		return false;
	}
	
	if (IsClientSourceTV(client) || IsClientReplay(client)) 
	{
		return false;
	}
	
	return true;
}