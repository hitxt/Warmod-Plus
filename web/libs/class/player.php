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
	) b";

	$playerStatsSQL = "SELECT 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".kills, 0)) AS 'kills', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".deaths, 0)) AS 'deaths', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".head_shots, 0)) AS 'head_shots',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hits, 0)) AS 'hits',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".shots, 0)) AS 'shots',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".5k, 0)) AS 'ace', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mvp, 0)) AS 'mvp',
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".knife ,0)) AS  'knife', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".glock ,0)) AS  'glock', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hkp2000 ,0)) AS  'hkp2000', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".usp_silencer ,0)) AS  'usp_silencer', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".p250 ,0)) AS  'p250', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".deagle ,0)) AS  'deagle', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".elite ,0)) AS  'elite', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".fiveseven ,0)) AS  'fiveseven', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".tec9 ,0)) AS  'tec9', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".cz75a ,0)) AS  'cz75a', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".revolver ,0)) AS  'revolver', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".nova ,0)) AS  'nova', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".xm1014 ,0)) AS  'xm1014', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mag7 ,0)) AS  'mag7', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".sawedoff ,0)) AS  'sawedoff', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".bizon ,0)) AS  'bizon', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mac10 ,0)) AS  'mac10', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp9 ,0)) AS  'mp9', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp7 ,0)) AS  'mp7', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".mp5sd ,0)) AS  'mp5sd', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ump45 ,0)) AS  'ump45', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".p90 ,0)) AS  'p90', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".galilar ,0)) AS  'galilar', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ak47 ,0)) AS  'ak47', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".scar20 ,0)) AS  'scar20', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".famas ,0)) AS  'famas', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m4a1 ,0)) AS  'm4a1', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m4a1_silencer ,0)) AS  'm4a1_silencer', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".aug ,0)) AS  'aug', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".ssg08 ,0)) AS  'ssg08', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".sg556 ,0)) AS  'sg556', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".awp ,0)) AS  'awp', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".g3sg1 ,0)) AS  'g3sg1', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".m249 ,0)) AS  'm249', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".negev ,0)) AS  'negev', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hegrenade ,0)) AS  'hegrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".flashbang ,0)) AS  'flashbang', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".smokegrenade ,0)) AS  'smokegrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".inferno ,0)) AS  'inferno', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".incgrenade ,0)) AS  'incgrenade', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".molotov ,0)) AS  'molotov', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".decoy ,0)) AS  'decoy', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".taser ,0)) AS  'taser', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".generic ,0)) AS  'generic', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".head ,0)) AS  'head', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".chest ,0)) AS  'chest', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".stomach ,0)) AS  'stomach', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".left_arm ,0)) AS  'left_arm', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".right_arm ,0)) AS  'right_arm', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".left_leg ,0)) AS  'left_leg', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".right_leg ,0)) AS  'right_leg', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_planted ,0)) AS  'c4_planted', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_exploded ,0)) AS  'c4_exploded', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".c4_defused ,0)) AS  'c4_defused', 
	SUM(IF(".$player_table.".steam_id_64=".$stats_table.".steam_id_64, ".$stats_table.".hostages_rescued ,0)) AS  'hostages_rescued'
	FROM ".$stats_table.", ".$player_table;

	$playerRankSQL = "
	SELECT steam_id_64, b.Rank AS rank 
	FROM (SELECT a.rank AS 'Rank', a.rws, a.steam_id_64 
		FROM (SELECT steam_id_64, rws, @prev := @curr, @curr := rws, @rank := IF(@prev = @curr, @rank, @rank+1) AS rank
			FROM ".$player_table.", (SELECT @curr := null, @prev := null, @rank := 0) s
			ORDER BY rws DESC) a) b";
	
	$playerLoseSQL = "
		SELECT DISTINCT ".$result_table.".id 
		FROM ".$stats_table.", ".$result_table." 
		WHERE (".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND 
			".$stats_table.".team = 1 AND 
			".$result_table.".id = ".$stats_table.".id AND 
			".$stats_table.".steam_id_64 = ?)
			 OR (".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND
			  ".$stats_table.".team = 2 AND 
			  ".$result_table.".id = ".$stats_table.".id AND
			   ".$stats_table.".steam_id_64 = ?)";

	$playerWinSQL = "
		SELECT DISTINCT ".$result_table.".id 
		FROM ".$stats_table.", ".$result_table." 
		WHERE (".$result_table.".t_overall_score > ".$result_table.".ct_overall_score AND
			 ".$stats_table.".team = 2 AND
			  ".$result_table.".id = ".$stats_table.".id AND
			   ".$stats_table.".steam_id_64 = ?) 
			   OR (".$result_table.".ct_overall_score > ".$result_table.".t_overall_score AND
				".$stats_table.".team = 1 AND
				 ".$result_table.".id = ".$stats_table.".id AND
				  ".$stats_table.".steam_id_64 = ?)";

	$playerDrawSQL = "
		SELECT DISTINCT ".$result_table.".id 
		FROM ".$stats_table.", ".$result_table." 
		WHERE ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score AND ".$stats_table.".steam_id_64 = :id";

	$playerMatchSQL = "
	select
		".$result_table.".*,
		(
		  CASE
			 WHEN
				(
	(". $result_table.".t_overall_score > ". $result_table.".ct_overall_score 
				   AND ".$stats_table.".team = 1) 
				   OR 
				   (
					  ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
					  AND ".$stats_table.".team = 2
				   )
				)
			 THEN
				'lose' 
			 WHEN
				(
	(".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
				   AND ".$stats_table.".team = 2) 
				   OR 
				   (
					  ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
					  AND ".$stats_table.".team = 1
				   )
				)
			 THEN
				'win' 
			 ELSE
				'draw' 
		  END
	   )
	   AS 'result',
	IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
	IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
	from
	   ".$result_table.", ".$stats_table." 
	where
	   ".$result_table.".id = ".$stats_table.".match_id 
	   and ".$stats_table.".steam_id_64 = :id  
	order by
	   ".$result_table.".id desc
	";

	class Player{
		protected $cardColor = array("", "rose", "info", "warning", "success");

		public function __construct(Array $data = array())
		{
			foreach($data as $key => $value){
				if($key === "1k")	$key = "k1";
				elseif($key === "2k")	$key = "k2";
				elseif($key === "3k")	$key = "k3";
				elseif($key === "4k")	$key = "k4";
				elseif($key === "5k")	$key = "k5";
				$this->{$key} = $value;
			}

			$this->color = "success";
			if(isset($this->rank) && $this->rank < 4)	$this->color = $this->cardColor[$this->rank];

			if(isset($this->deaths) && isset($this->kills)){
				if($this->deaths == 0)	$this->kdr = round($this->kills/1, 2);
				else	$this->kdr = round($this->kills/$this->deaths, 2);
			}

			if(isset($this->shots) && isset($this->hits)){
				if($this->shots == 0) $this->ac = 0;
				else	$this->ac = round($this->hits/$this->shots,2);
			}
		}

		public function Card($name, $avatar, $index)
		{
			if($this->shots == 0) $this->ac = 0;
			else	$this->ac = round($this->hits/$this->shots, 2);
				
			if($this->deaths == 0)	$this->kdr = round($this->kills/1, 2);
			else	$this->kdr = round($this->kills/$this->deaths, 2);
			?>
				<div class="col-lg-6 col-xl-4 col-md-6 col-12 <?=($index)?"d-xl-last-none":""?>">
					<a href="./showplayer.php?id=<?=$this->steam_id_64?>">
						<div class="card card-stats">
							<div class="card-header card-header-icon card-header-<?=$this->color?>">
								<div class="card-icon" data-header-animation='true'>
									<?=(($index && $this->rank <= 4) || (!$index && $this->rank < 4))?"<div class='ribbon'><span>".ordinal($this->rank)."</span></div>":""?>
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
					</a>
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

		public function matchStats($name){
			$this->rws2 = substr($this->rws, 0, 1);
			$this->rws = floor_dec($this->rws,2);
				
			$this->kpr = round($this->kills/$this->rounds_played, 2);
			$this->dpr = round($this->damage/$this->rounds_played, 2);
			
			$this->la2 = round($this->clutch_won/$this->rounds_played, 2);
			
			if($this->last_alive == 0)	$this->clutch_won = 0;
			else	$this->clutch_won = round($this->clutch_won/$this->last_alive, 2);
			?>
				<tr>
					<td class='icon' data-th="Country"><img src='./assets/img/flags/<?=$this->cc?>.png' width='25'/></td>
					<td class='text-left text-match-name font-weight-bold' data-th="Name">
						<a href="./showplayer.php?id=<?=$this->steam_id_64?>" class="text-<?=($this->team == 1)?"ct":"t"?>"><?=$name?></a>
					</td>
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
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="LA"><?=sprintf("%01.2f",$this->la2)?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="CW"><?=sprintf("%01.2f",$this->clutch_won)?></td>
					<td class="text-match-<?=($this->team == 1)?"ct":"t"?>" data-th="AC"><?=sprintf("%01.2f",$this->ac)?></td>
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