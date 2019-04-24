<?php
	$playerSQL = "SELECT rank, steam_id_64, rws, team, kills, deaths, hits, shots FROM
		(
		SELECT
			a.rank AS 'Rank',
		   a.steam_id_64,
				a.rws,
				a.team,
				a.kills,
				a.deaths,
				a.hits,
				a.shots
		FROM
			(
			SELECT
				stats.steam_id_64,
				stats.rws,
				stats.team,
				stats.kills,
				stats.deaths,
				stats.hits,
				stats.shots,
			@prev := @curr,
			@curr := stats.rws,
		@rank := IF(@prev = @curr, @rank, @rank + 1) AS rank
	FROM
		(
		SELECT
			@curr := NULL,
			@prev := NULL,
			@rank := 0
	) s,
	(
		SELECT
			".$player_table.".steam_id_64,
			".$player_table.".rws,
			".$player_table.".team,
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".kills,
					0
				)
			) AS 'kills',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".deaths,
					0
				)
			) AS 'deaths',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".hits,
					0
				)
			) AS 'hits',
			SUM(
				IF(
					".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64,
					".$stats_table.".shots,
					0
				)
			) AS 'shots'
		FROM
			`".$stats_table."`,
			`".$player_table."`
		WHERE
			".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR' AND `".$player_table."`.rws > 0
		GROUP BY
			".$player_table.".steam_id_64
		ORDER BY
			".$player_table.".rws
		DESC
	) stats
	ORDER BY
		rws
	DESC
		) a
	) b
	ORDER BY
		rws
	DESC";

	class Player{
		protected $cardColor = array("", "rose", "info", "success", "warning");
		public function __construct(Array $data = array())
		{
			foreach($data as $key => $value){
				if($key == "1k")	$key = "k1";
				elseif($key == "2k")	$key = "k2";
				elseif($key == "3k")	$key = "k3";
				elseif($key == "4k")	$key = "k4";
				elseif($key == "5k")	$key = "k5";
			  $this->{$key} = $value;
			}

			$this->color = "warning";
			if(!empty($this->rank) && $this->rank < 4)	$this->color = $this->cardColor[$this->rank];
		}
		public function Card($name, $avatar, $index)
		{
			if($this->shots == 0) $this->ac = 0;
			else	$this->ac = round($this->hits/$this->shots, 2);
				
			if($this->deaths == 0)	$this->kdr = round($this->kills/1, 2);
			else	$this->kdr = round($this->kills/$this->deaths, 2);
			?>
				<div class="col-lg-6 col-xl-4 col-md-6 col-12 <?=($index)?"d-xl-last-none":""?>">
					<div class="card card-stats">
						<div class="card-header card-header-icon card-header-<?=$this->color?>">
							<div class="card-icon" data-header-animation='true'>
								<?=($this->rank <= 4)?"<div class='ribbon'><span>".ordinal($this->rank)."</span></div>":""?>
								<img src='<?=$avatar?>' width="90px">
							</div>
							<p class="card-category text-danger">RWS: <?=$this->rws?></p>
							<h3 class="card-title font-bold"><?=$name?></h3>
						</div>
						<div class="card-footer">
							<div class="w-100 stats text-info2 justify-content-end">
								Kill: <?=$this->kills?>&emsp;Deaths:<?=$this->deaths?>&emsp;KDR:<?=$this->kdr?>&emsp;AC: <?=$this->ac?>%
							</div>
						</div>
					</div>
				</div>
			<?php
		}

		static function emptyCard(){
			?>
				<div class="col-lg-6 col-xl-4 col-md-6 col-12">
					<div class="card card-stats">
						<div class="card-header card-header-rose card-header-icon">
							<div class="card-icon" data-header-animation='true'>
								<img src='./assets/img/default-avatar.png' width="90px">
							</div>
							<p class="card-category text-danger">RWS: 0.00</p>
							<h3 class="card-title font-bold">No Name</h3>
						</div>
						<div class="card-footer">
							<div class="w-100 stats text-info2 justify-content-end">
								Kill: 0&emsp;Deaths: 0&emsp;KDR: 0.00&emsp;AC: 0%
							</div>
						</div>
					</div>
				</div>
			<?php
		}

		public function matchStats($name, $cc){
			$this->rws2 = substr($this->rws, 0, 1);
			$this->rws = floor_dec($this->rws,2);
				
			if($this->deaths == 0)	$this->kdr = round($this->kills/1, 2);
			else	$this->kdr = round($this->kills/$this->deaths, 2);
				
			$this->kpr = round($this->kills/$this->rounds_played, 2);
			$this->dpr = round($this->damage/$this->rounds_played, 2);
			
			if($this->shots == 0) $this->ac = 0;
			else	$this->ac = round($this->hits/$this->shots,2);
			
			$this->la2 = round($this->clutch_won/$this->rounds_played, 2);
			
			if($this->last_alive == 0)	$this->clutch_won = 0;
			else	$this->clutch_won = round($this->clutch_won/$this->last_alive, 2);
			?>
				<tr>
					<td class='icon' data-th="Country"><img src='./assets/img/flags/<?=$cc?>.png' width='25'/></td>
					<td class='text-left text-match-name font-weight-bold text-<?=($this->team == 1)?"ct":"t"?>' data-th="Name"><?=$name?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="Kills"><?=$this->kills?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="Deaths"><?=$this->deaths?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="Assists"><?=$this->assists?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="HS"><?=$this->head_shots?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="TK"><?=$this->team_kills?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="ATA"><?=$this->assists_team_attack?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="KDR"><?=$this->kdr?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="KPR"><?=$this->kpr?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="DPR"><?=$this->dpr?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="1K"><?=$this->k1?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="2K"><?=$this->k2?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="3K"><?=$this->k3?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="4K"><?=$this->k4?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="ACE"><?=$this->k5?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="MVP"><?=$this->mvp?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="LA"><?=$this->la2?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="CW"><?=$this->clutch_won?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="AC"><?=$this->ac?></td>
					<td class="<?=($this->rws >=0)?"text-positive":"text-negative"?>" data-th="RWS"><?=($this->rws >=0)?"+":""?><?=sprintf("%01.2f",$this->rws);?></td>
				</tr>
			<?php
		}
	}

	class SteamData{
		static function GetData($key, $players){
			$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$key	."&steamids=";
			for ($i=0;$i<count($players);$i++) 
			{
				if($i == 0)	$url.="".$players[$i]."";
				else $url.=",".$players[$i]."";
			}
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $url);
			curl_setopt($cURL, CURLOPT_HTTPGET, true);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
			$result = curl_exec($cURL);
			curl_close($cURL);
			$json = json_decode($result, true);
			$data["name"] = array();
			$data["avatar"] = array();
			foreach($json['response']['players'] as $item)
			{
				$data["name"][$item['steamid']] = $item['personaname'];
				$data["avatar"][$item['steamid']] = $item['avatarfull'];
			}
			return $data;
		}
	}
?>