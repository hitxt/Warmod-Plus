<?php
// Edit from
// https://github.com/splewis/csgo-multi-1v1/blob/4e2a6569876bb72f73414e2578b0cb204c96f6bb/web/includes/utils.inc.php

//require_once("../../config.php");

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