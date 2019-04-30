<?php
	$matchSQL = "
	SELECT ".$result_table.".*, 
	IF(".$result_table.".t_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".t_id)) AS t_name,
	IF(".$result_table.".ct_id = '', '', (SELECT ".$team_table.".name FROM ".$team_table." WHERE ".$team_table.".id = ".$result_table.".ct_id)) AS ct_name
	FROM ".$result_table." order by id DESC ";

	class Match
	{
		public function __construct(Array $data = array(), $timezone)
		{
			foreach($data as $key => $value){
			  $this->{$key} = $value;
			}
			
			$this->timestamp = strtotime( $this->match_start );
			$dt = new DateTime("now", new DateTimeZone( $timezone ));
			$dt->setTimestamp($this->timestamp);
			$this->timestamp = $dt->format('Y/m/d g:i:s A');
		}

		static function emptyCard()
		{
			?>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="card card-product">
						<div class="card-header card-header-image" data-header-animation="true">
							<img class='img' src='./assets/img/maps/unknown.jpg' />
						</div>
						<div class="card-body">
							<div class="card-actions text-center"></div>
							<h4 class="card-title">
								<i class='material-icons text-danger'>warning</i> Can not found a match.
							</h4>
						</div>
						<div class="card-footer text-center">
							<div class="stats">
								Please start a match in server first.
							</div>
						</div>
					</div>
				</div>
			<?php
		}

		public function Card($index)
		{
			$mappic = $this->map."-thumb.jpg";
			if(!file_exists("./assets/img/maps/".$mappic))	$mappic = "unknown.jpg";
			
			$comp = $this->competition;
			if(empty($comp))	$comp = "Unknown";

			$event = $this->event;
			if(empty($event))	$event = "Unknown";

			$ct_name = $this->ct_name;
			if(empty($ct_name))	$ct_name = "Unknown";

			$t_name = $this->t_name;
			if(empty($t_name))	$t_name = "Unknown";
			?>
				<div class="col-lg-6 col-xl-4 col-md-6 col-12 <?=($index)?"d-xl-last-none":""?>">
					<a href="./showmatch.php?id=<?=$this->id?>">
						<div class="card card-product">
							<div class="card-header card-header-image" data-header-animation="true">
								<img class="img" src="./assets/img/maps/<?=$mappic?>">
							</div>
							<div class="card-body">
								<div class="card-actions text-center"></div>
								<h4 class="card-title">
									<span class="text-ct"><?=$ct_name?>&nbsp;-&nbsp;<?=$this->ct_overall_score?></span>
									<span class="text-vs">&emsp;VS&emsp;</span>
									<span class="text-t"><?=$this->t_overall_score?>&nbsp;-&nbsp;<?=$t_name?></span>
								</h4>
								<div class="card-description text-dark">
									Event:&nbsp;<?=$comp?>&nbsp;-&nbsp;<?=$event?>&nbsp;&nbsp;&nbsp;<br>
									Match ID:&nbsp;<?=$this->id?>
								</div>
							</div>
							<div class="card-footer text-center">
								<div class="stats">
									<i class='material-icons align-bottom'>access_time</i>&emsp;<?=$this->timestamp?>
								</div>
							</div>
						</div>
					</a>
				</div>
			<?php
		}
	}
?>