<?php
    function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. ' th';
        else
            return $number." ".$ends[$number % 10];
    }

    function ceil_dec($v, $precision)
    {
        $c = pow(10, $precision);
        return ceil($v*$c)/$c;
    }
    
    function floor_dec($v, $precision)
    {
        $c = pow(10, $precision);
        return floor($v*$c)/$c;
    }

    function random_password($length) 
    {     
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";    
        $password = substr(str_shuffle($str), 0, $length);    
        return $password;
	}
	
	function getAvatarFull($commid) 
	{
		global $SteamAPI_Key;
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=".$commid."";
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		$result = curl_exec($cURL);
		$json = json_decode($result, true);
		foreach ($json['response']['players'] as $item) {
			return $item['avatarfull'];
		}
	}

	function getAvatarMedium($commid) 
	{
		global $SteamAPI_Key;
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=".$commid."";
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		$result = curl_exec($cURL);
		$json = json_decode($result, true);
		foreach ($json['response']['players'] as $item) {
			return $item['avatarmedium'];
		}
	}

	function getName($commid) 
	{
		global $SteamAPI_Key;
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=".$commid."";
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		$result = curl_exec($cURL);
		$json = json_decode($result, true);
		foreach ($json['response']['players'] as $item) {
			return $item['personaname'];
		}
	}

	// Edit from
	// https://github.com/revunix/SteamGroup-Widgets-XML-/blob/master/widget.php
	function getGroupAvatar($groupname)
	{
		$xml = simplexml_load_file("http://steamcommunity.com/groups/".$groupname."/memberslistxml/?xml=1");
		foreach ($xml->groupDetails->avatarFull as $icon)
		{ 
			return $icon;
		}
	}
?>