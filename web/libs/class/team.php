<?php
	$teamSQL = "
	SELECT name, leader, steam_url,
	   b.rank AS rank,
	   wlr,
	   win,
	   lose,
	   draw,
	   id, logo
	FROM
	   (
		  SELECT
			 a.rank AS 'Rank',
			 a.wlr,
			 a.name,
			 a.leader,
			 a.steam_url,
			 a.win,
			 a.lose,
			 a.draw,
			 a.id,
			 a.logo
		  FROM
			 (
				SELECT
				   stats.name,
				   stats.steam_url,
				   stats.leader,
				   stats.win,
				   stats.lose,
				   stats.draw,
				   stats.id,
				   stats.logo,
				   Ifnull(TRUNCATE (stats.win / stats.win + stats.lose + stats.draw, 2), stats.win) AS 'wlr',
				   @prev := @curr,
				   @curr := Ifnull(TRUNCATE (stats.win / stats.win + stats.lose + stats.draw, 2), stats.win),
				   @rank := IF(@prev = @curr, @rank, @rank + 1) AS rank 
				FROM
				   (
					  SELECT
						 @curr := NULL,
						 @prev := NULL,
						 @rank := 0 
				   )
				   s,
				   (
					  SELECT
						 ".$team_table.".name,
						 ".$team_table.".id,
						 ".$team_table.".steam_url,
						 ".$team_table.".leader,
						 ".$team_table.".logo,
						 Count( 
						 CASE
							WHEN
							   (
								  ".$result_table.".t_id = ".$team_table.".id 
								  AND ".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".ct_id = ".$team_table.".id 
								  AND ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'win', Count( 
						 CASE
							WHEN
							   (
								  ".$result_table.".ct_id = ".$team_table.".id 
								  AND ".$result_table.".t_overall_score > ".$result_table.".ct_overall_score 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".t_id = ".$team_table.".id
								  AND ".$result_table.".ct_overall_score > ".$result_table.".t_overall_score 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'lose', COUNT( 
						 CASE
							WHEN
							   (
								  ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score 
								  AND ".$result_table.".ct_id = ".$team_table.".id 
							   )
							THEN
							   1 
							WHEN
							   (
								  ".$result_table.".t_overall_score = ".$result_table.".ct_overall_score 
								  AND ".$result_table.".t_id = ".$team_table.".id 
							   )
							THEN
							   1 
							ELSE
							   NULL 
						 END
	) AS 'draw' 
					  FROM
						 ".$result_table.", ".$team_table." 
					  GROUP BY
						 ".$team_table.".name 
				   )
				   stats 
				ORDER BY
				   wlr DESC 
			 )
			 a 
	   )
	   b
	ORDER BY wlr DESC";

	class Team
	{
		protected $cardColor = array("", "rose", "info", "warning", "success");

		public function __construct(Array $data = array())
		{
			foreach($data as $key => $value){
			  $this->{$key} = $value;
			}

			if(!empty($this->rank)){
					$this->color = "success";
				if($this->rank < 4)	$this->color = $this->cardColor[$this->rank];
			}
	
			$this->logo = "./assets/img/teams/unknown.png";
			if(!empty($this->logo) && file_exists("./assets/img/teams/".$this->logo))	$this->logo = "./assets/img/teams/".$this->logo;
		}

		public function Card($name, $index)
		{
			?>
				<div class="col-lg-6 col-xl-4 col-md-6 col-12 <?=($index)?"d-xl-last-none":""?>">
					<div class="card card-stats">
						<div class="card-header card-header-icon card-header-<?=$this->color?>">
							<div class="card-icon" data-header-animation='true'>
								<?=($this->rank <= 4)?"<div class='ribbon'><span>".ordinal($this->rank)."</span></div>":""?>
								<img src='<?=$this->logo?>' width="90px">
							</div>
							<p class="card-category text-danger">Wins / Matches : <?=$this->wlr?></p>
							<h3 class="card-title font-bold"><?=$this->name?></h3>
						</div>
						<div class="card-footer">
							<div class="w-100 stats text-info2 justify-content-end">
								<?=$this->win?> W / <?=$this->lose?> L / <?=$this->draw?> D &emsp; Leader: <?=$name?>
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
							<p class="card-category text-danger">Wins / Matches : 0></p>
							<h3 class="card-title font-bold">No Name</h3>
						</div>
						<div class="card-footer">
							<div class="w-100 stats text-info2 justify-content-end">
								0 W / 0 L / 0 D &emsp; Leader: Unknown
							</div>
						</div>
					</div>
				</div>
			<?php
		}
	}
?>