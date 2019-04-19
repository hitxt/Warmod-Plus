<?php
require_once("config.php");

if(isset($_SESSION['steamid']))
{
	$result=mysqli_query($link, "SELECT * FROM ".$invite_table." WHERE receive = '".$steamprofile['steamid']."' AND status = ''");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		$notify_count = 1;
		while ($row = mysqli_fetch_array($result))
		{
			$notify_send = $row['send'];
			$notify_team = $row['team'];
			$notify_type = $row['type'];
			$notify_count++;
		}
		$notify_count-=1;
	}
	else $notify_count = 0;
	
	echo"
	<ul class='nav navbar-nav navbar-right'>
		<li class='dropdown'>
			<a href='#' class='dropdown-toggle' data-toggle='dropdown'>
				<i class='material-icons' id='notify_icon'>notifications</i>";
	
	if($notify_count>0)	echo "<span class='notification' id='notify_count'>".$notify_count."</span>";
	
	echo"
				<p class='hidden-lg hidden-md'>
					Notifications
					<b class='caret'></b>
				</p>
			</a>
			<ul class='dropdown-menu'>
				<li>
					<a href='./settings.php?page=notifications'>You have <span id='unread_count'>".$notify_count."</span> new messages.</a>
				</li>
			</ul>
		</li>
		<li class='separator hidden-lg hidden-md'></li>
	</ul>";
}
?>
<form class="navbar-form navbar-right" role="search" action="./assets/inc/search.php" method="get">
		<div class="form-group form-search is-empty">
			<input type="text" class="form-control" placeholder="Match ID or Steam64 ID" id="match" name="id">
			<span class="material-input"></span>
		</div>
		<button type="submit" class="btn btn-white btn-round btn-just-icon">
			<i class="material-icons">call_made</i>
			<div class="ripple-container"></div>
		</button>
</form>