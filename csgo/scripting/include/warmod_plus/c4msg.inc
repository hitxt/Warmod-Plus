float g_DetonateTime = 0.0;
float g_DefuseEndTime = 0.0;
int g_DefusingClient = -1;
bool g_CurrentlyDefusing = false;

void BombTime_PlayerDeath( Handle event )
{
	int victim = GetClientOfUserId(GetEventInt(event, "userid" ));
	int attacker = GetClientOfUserId(GetEventInt(event, "attacker"));

	if ( g_DefusingClient != victim || !g_CurrentlyDefusing ) return;
	if ( !IsValidClient( victim ) ) return;
	if ( !IsValidClient( attacker ) ) return;

	float timeRemaining = g_DefuseEndTime - GetGameTime();
	if ( timeRemaining > 0.0 )
	{
		char defuserName[64];
		GetClientName( victim, defuserName, sizeof(defuserName) );

		char timeString[32];
		FloatToStringFixedPoint( timeRemaining, 2, timeString, sizeof(timeString) );
		
		for(int i = 1; i <= MaxClients; i++)
		{
			if(!g_bAll)
			{
				if(IsClientInGame(i) && GetClientTeam(i) != TR && GetClientTeam(i) != CT)
				{
					CPrintToChat(i,  "%T", "DefuserDiedTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
				}
			}
			else if(g_bAll)
			{
				if(IsClientInGame(i))
				{
					CPrintToChat(i,  "%T", "DefuserDiedTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
				}
			}
		}
	}
	else
	{
		char attackerName[64];
		GetClientName( attacker, attackerName, sizeof(attackerName) );

		char timeString[32];
		FloatToStringFixedPoint( -timeRemaining, 2, timeString, sizeof(timeString) );
		
		for(int i = 1; i <= MaxClients; i++)
		{
			if(!g_bAll)
			{
				if(IsClientInGame(i) && GetClientTeam(i) != TR && GetClientTeam(i) != CT)
				{
					CPrintToChat(i, "%T", "PostDefuseKillTimeMessage", i, CHAT_PREFIX, attackerName, timeString );
				}
			}
			else if(g_bAll)
			{
				if(IsClientInGame(i))
				{
					CPrintToChat(i, "%T", "PostDefuseKillTimeMessage", i, CHAT_PREFIX, attackerName, timeString );
				}
			}
		}
	}
}

void BombTime_BombPlanted()
{
	g_DetonateTime = GetGameTime() + GetConVarInt(FindConVar( "mp_c4timer" ));
	g_DefusingClient = -1;
	g_CurrentlyDefusing = false;
}

void BombTime_BombDefused( Event event )
{
	int defuser = GetClientOfUserId( event.GetInt( "userid" ) );

	if ( !IsValidClient( defuser ) ) return;

	float timeRemaining = g_DetonateTime - GetGameTime();

	char defuserName[64];
	GetClientName( defuser, defuserName, sizeof(defuserName) );

	char timeString[32];
	FloatToStringFixedPoint( timeRemaining, 2, timeString, sizeof(timeString) );

	for(int i = 1; i <= MaxClients; i++)
	{
		if(!g_bAll)
		{
			if(IsClientInGame(i) && GetClientTeam(i) != TR && GetClientTeam(i) != CT)
			{
				CPrintToChat(i, "%T", "SuccessfulDefuseTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
			}
		}
		else if(g_bAll)
		{
			if(IsClientInGame(i))
			{
				CPrintToChat(i, "%T", "SuccessfulDefuseTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
			}
		}
	}
}

void BombTime_BombBeginDefuse( Event event )
{
	int defuser = GetClientOfUserId( event.GetInt( "userid" ) );
	bool hasKit = event.GetBool( "haskit" );

	float endTime = GetGameTime() + (hasKit ? 5.0 : 10.0);
	
	g_CurrentlyDefusing = true;

	if ( g_DefusingClient == -1 || g_DefuseEndTime < g_DetonateTime )
	{
		g_DefuseEndTime = endTime;
		g_DefusingClient = defuser;

		int bomb = FindEntityByClassname( -1, "weapon_c4" );
		if ( !BombTime_AnyLivingTerrorists() && bomb != -1 )
		{   
			float defuseLength = GetEntPropFloat( bomb, Prop_Send, "m_flDefuseLength", 0 );
			SetEntPropFloat( bomb, Prop_Send, "m_flDefuseLength", defuseLength - 4, 0 );
		} 
	}
}

void BombTime_BombAbortDefuse( Event event )
{
	int defuser = GetClientOfUserId( event.GetInt( "userid" ) );

	if ( g_DefusingClient == defuser )
	{
		g_CurrentlyDefusing = false;
	}
}

void BombTime_BombExploded()
{
	float timeRemaining = g_DefuseEndTime - g_DetonateTime;

	if ( IsValidClient( g_DefusingClient ) && timeRemaining >= 0.0 )
	{
		char defuserName[64];
		GetClientName( g_DefusingClient, defuserName, sizeof(defuserName) );

		char timeString[32];
		FloatToStringFixedPoint( timeRemaining, 2, timeString, sizeof(timeString) );

		for(int i = 1; i <= MaxClients; i++)
		{
			if(!g_bAll)
			{
				if(IsClientInGame(i) && GetClientTeam(i) != TR && GetClientTeam(i) != CT)
				{
					CPrintToChat(i, "%T", "BombExplodedTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
				}
			}
			else if(g_bAll)
			{
				if(IsClientInGame(i))
				{
					CPrintToChat(i, "%T", "BombExplodedTimeLeftMessage", i, CHAT_PREFIX, defuserName, timeString );
				}
			}
		}
	}
}

bool BombTime_AnyLivingTerrorists()
{
	for ( int client = 1; client <= MaxClients; ++client )
	{
		if ( !IsValidClient( client )) continue;

		int team = GetClientTeam( client );
		if ( team != CS_TEAM_T ) continue;

		if ( IsPlayerAlive( client ) ) return true;    
	}
	return false;
}

int FloatToStringFixedPoint( float value, int fractionalDigits, char[] buffer, int maxLength )
{
	if ( fractionalDigits == 0 )
	{
		return IntToString( RoundFloat( value ), buffer, maxLength );
	}

	int scale = RoundFloat( Pow( 10.0, fractionalDigits * 1.0 ) );
	int valueInt = view_as<int>( RoundFloat( value * scale ) );

	int offset = IntToString( valueInt / scale, buffer, maxLength );
	if ( offset >= maxLength - 2 ) return offset;

	buffer[offset++] = '.';

	for ( int i = 0; i < fractionalDigits && offset < maxLength - 1; ++i, ++offset )
	{
		scale /= 10;
		buffer[offset] = '0' + ((valueInt / scale) % 10);
	}

	buffer[offset] = 0;

	return offset;
}