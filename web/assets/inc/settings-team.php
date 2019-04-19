<?php
									// Have Team
									if(isset($session_team_id) && !empty($session_team_id))
									{
										// echo "You're currently in ".$session_team_name."";
										
										// Is Leader
										if($steamprofile['steamid'] == $session_team_leader)
										{
											// team profile
											echo "
											<div class='card'>
												<div class='card-header card-header-text' data-background-color='blue'>
													<h4 class='card-title'>Team Profile</h4>
												</div>
												<div class='card-content'>
													<form id='TeamProfile' class='form-horizontal' enctype='multipart/form-data'>
														<div class='row'>
															<div class='col-md-4 col-md-offset-5'>
																<div class='fileinput fileinput-new text-center' data-provides='fileinput' id='uploadteamlogoinput'>
																	<div class='fileinput-new thumbnail'>
																		<img src='".$session_team_logo."' alt='...' style='width:128px !important; height:128px !important'>
																	</div>
																	<div class='fileinput-preview fileinput-exists thumbnail'></div>
																	<div>
																		<span class='btn btn-rose btn-round btn-file'>
																			<span class='fileinput-new'>Select image</span>
																			<span class='fileinput-exists'>Change</span>
																			<input type='file' name='uploadteamlogo' id='uploadteamlogo' class='uploadteamlogo' accept='image/jpeg, image/png' />
																		</span>
																		<a class='btn btn-danger btn-round fileinput-exists' data-dismiss='fileinput'><i class='fas fa-lg fa-times'></i> Remove</a>
																		<br>
																		<div class='stats'><i class='material-icons text-danger'>warning</i> <font color='#FF0000'>Size limit: 1 MB, jpg/png</font></div>
																	</div>
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>Team Name *</label>
															<div class='col-sm-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='text' id='tname' maxLength='15' required='true' value='".$session_team_name."'/>
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>Steam Group</label>
															<div class='col-md-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='url' id='tsteam' url='true' value='".$session_team_steam."'/>
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>Facebook</label>
															<div class='col-md-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='url' id='tfacebook' url='true' value='".$session_team_fb."'/>
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>Twitter</label>
															<div class='col-md-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='url' id='ttwitter' url='true' value='".$session_team_twitter."'/>
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>YouTube</label>
															<div class='col-md-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='url' id='tyoutube' url='true' value='".$session_team_youtube."' />
																</div>
															</div>
														</div>
														<div class='row'>
															<label class='col-sm-2 label-on-left'>Twitch</label>
															<div class='col-md-9'>
																<div class='form-group label-floating'>
																	<label class='control-label'></label>
																	<input class='form-control' type='url' id='ttwitch' url='true' value='".$session_team_twitch."' />
																</div>
															</div>
														</div>
														<div class='card-footer text-center'>
															<button type='button' onclick='javascript:void(0);' id='TeamProfile_Save' class='btn btn-rose btn-fill'>Save</button>
														</div>
													</form>
												</div>
											</div>
											";
											
											// in game logo
											echo "
											<div class='card'>
												<div class='card-header card-header-text' data-background-color='green'>
													<h4 class='card-title'>Game Logo</h4>
												</div>
												<div class='card-content'>
													<form id='TeamLogo' class='form-horizontal' enctype='multipart/form-data'>
														<div class='row'>
															<div class='col-md-4 col-md-offset-5'>
																<div class='fileinput fileinput-new text-center' data-provides='fileinput' id='uploadteamgamelogoinput'>
																	<div class='fileinput-new thumbnail'>
																		<img src='".$session_team_logo_game."' alt='...' style='width:80px !important; height:80px !important'>
																	</div>
																	<div class='fileinput-preview fileinput-exists thumbnail'></div>
																	<div>
																		<span class='btn btn-rose btn-round btn-file'>
																			<span class='fileinput-new'>Select image</span>
																			<span class='fileinput-exists'>Change</span>
																			<input type='file' name='uploadteamgamelogo' id='uploadteamgamelogo' class='uploadteamgamelogo' accept='image/svg+xml' />
																		</span>
																		<a class='btn btn-danger btn-round fileinput-exists' data-dismiss='fileinput'><i class='fas fa-lg fa-times'></i> Remove</a>
																		<br>
																		<div class='stats'><i class='material-icons text-danger'>warning</i> <font color='#FF0000'>1 MB, 64x64, svg</font></div>
																	</div>
																</div>
															</div>
														</div>
														<div class='card-footer text-center'>
															<button type='button' onclick='javascript:void(0);' id='TeamLogo_Save' class='btn btn-rose btn-fill'>Save</button>
														</div>
													</form> 
												</div>
											</div>
												";
											
											
											// team member
											echo "
											<div class='card'>
												<div class='card-header card-header-text' data-background-color='orange'>
													<h4 class='card-title'>Member Settings</h4>
												</div>
												<div class='card-content'>
													<div class='material-datatables'>
														<table id='TeamMemberTable' class='table table-striped table-no-bordered table-hover' cellspacing='0' width='100%' style='width:100%'>
															<thead class='thead-inverse'>
																<tr>
																	<th>Role</th>
																	<th>Rank</th>
																	<th>Name</th>
																	<th>RWS</th>
																	<th>Kills</th>
																	<th>Deaths</th>
																	<th>KDR</th>
																	<th>AC</th>
																	<th>Profile</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tfoot>
																<tr>
																	<th>Role</th>
																	<th>Rank</th>
																	<th>Name</th>
																	<th>RWS</th>
																	<th>Kills</th>
																	<th>Deaths</th>
																	<th>KDR</th>
																	<th>AC</th>
																	<th>Profile</th>
																	<th>Action</th>
																</tr>
															</tfoot>
															<tbody>";
											
											$result=mysqli_query($link, "
											SELECT rank,
												   steam_id_64,
														rws,
														team,
														kills,
														deaths,
														hits,
														shots
											FROM
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
													".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR'
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
											WHERE team='".$session_team_id."'
											ORDER BY
												rws
											DESC"); 

											mysqli_set_charset ($link , "utf-8");
											$numb=mysqli_num_rows($result); 
											if (!empty($numb)) 
											{
												$i=1;
												while ($row = mysqli_fetch_array($result))
												{
													// get steam id and name
													$steam[$i] = $row['steam_id_64'];
													
													if($steam[$i] == $session_team_leader)	$role[$i] = "Leader";
													else $role[$i] = "";
															
													$rws[$i] = $row['rws'];
													if($rws[$i] != 0.00)	$rws[$i] = floor_dec($rws[$i],2);
														
													$kills[$i] = $row['kills'];
													$deaths[$i] = $row['deaths'];
													$hits[$i] = $row['hits'];
													$shots[$i] = $row['shots'];
																
													if($shots[$i] == 0) $ac[$i] = 0;
													else	$ac[$i] = round($hits[$i]/$shots[$i], 2);
														
													if($deaths[$i] == 0)	$kdr[$i] = round($kills[$i]/1, 2);
													else	$kdr[$i] = round($kills[$i]/$deaths[$i], 2);

													if($rws[$i] > 0)	$rank[$i] = $row['rank'];
													else $rank[$i] = "Unranked";
													
													$i++;
												}
												
												$i-=1;
												
												$query_count=ceil($i/100);
												
												// $i = 資料筆數
												// $query_count = api總共要查詢的次數
												// $k=已查詢的次數
												// $j = api查詢次數loop 
												// $l = api查詢url的steamid筆數
												for ($j=1;$j<=$query_count;$j++) 
												{
													$url[$j] = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
													
													// 如果只要查一次
													if($query_count==1)
													{
														for ($l=1;$l<=$i;$l++) 
														{
															if($l == 1)	$url[$j].="".$steam[$l]."";
															else $url[$j].=",".$steam[$l]."";
														}
													}
													// 如果多於一次
													else
													{
														// 第一次查詢
														if($j == 1)
														{
															for($l=1;$l<=100;$l++) 
															{
																if($l == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
															}
														}
														// 最後一次查詢
														elseif($j == $query_count)
														{
															$k= 1;
															for($l=($j*100)-99;$l<=$i;$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
														// 中間查詢
														else
														{ 
															$k=1;
															for($l=($j*100)-99;$l<=($j*200);$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
													}

													$cURL = curl_init();
													curl_setopt($cURL, CURLOPT_URL, $url[$j]);
													curl_setopt($cURL, CURLOPT_HTTPGET, true);
													curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
													$result = curl_exec($cURL);
													curl_close($cURL);
													$json = json_decode($result, true);
													
													foreach($json['response']['players'] as $item)
													{
														$personname[$item['steamid']] = $item['personaname'];
														//$avatarfull[$item['steamid']] = $item['avatarfull'];
													}
												}

												for ($j=1;$j<=$i;$j++) 
												{
													echo "
													<tr height='40px'>
														<td>".$role[$j]."</td>
														<td>".$rank[$j]."</td>
														<td>".$personname[$steam[$j]]."</td>
														<td>".$rws[$j]."</td>
														<td>".$kills[$j]."</td>
														<td>".$deaths[$j]."</td>
														<td>".$kdr[$j]."</td>
														<td>".$ac[$j]."</td>
														<td><a href='./showplayer.php?id=".$steam[$j]."'>View</a></td>";
													
													if($steam[$j] != $session_team_leader)
														echo"<td class='td-actions text-left'>
													<button type='button' class='DeleteMember btn btn-danger btn-simple' onclick='javascript:void(0);'>
													<i class='material-icons'>close</i>
													</button>
													</td>";
													else	echo "<td></td>";
													
													echo "</tr>";
												}
											}
											
											// inviting members
											$result=mysqli_query($link, "
											SELECT
											   rank,
											   steam_id_64,
											   rws,
											   kills,
											   deaths,
											   hits,
											   shots 
											FROM
											   (
												  SELECT
													 a.rank AS 'Rank',
													 a.steam_id_64,
													 a.rws,
													 a.kills,
													 a.deaths,
													 a.hits,
													 a.shots 
												  FROM
													 (
														SELECT
														   stats.steam_id_64,
														   stats.rws,
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
														   )
														   s,
														   (
															  SELECT
																 ".$player_table.".steam_id_64,
																 ".$player_table.".rws,
																 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".kills, 0 ) ) AS 'kills',
																 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".deaths, 0 ) ) AS 'deaths',
																 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".hits, 0 ) ) AS 'hits',
																 SUM( IF( ".$player_table.".steam_id_64 = ".$stats_table.".steam_id_64, ".$stats_table.".shots, 0 ) ) AS 'shots' 
															  FROM
																 `".$stats_table."`,
																 `".$player_table."` 
															  WHERE
																 ".$player_table.".steam_id_64 <> 'STEAM_ID_STOP_IGNOR' 
															  GROUP BY
																 ".$player_table.".steam_id_64 
															  ORDER BY
																 ".$player_table.".rws DESC 
														   )
														   stats 
														ORDER BY
														   rws DESC 
													 )
													 a 
											   )
											   b,  ".$invite_table."
											WHERE
											   steam_id_64 = ".$invite_table.".receive AND ".$invite_table.".status = '' AND ".$invite_table.".team = '".$session_team_id."' AND ".$invite_table.".type = 'invite'
											ORDER BY
											   rws DESC");

											mysqli_set_charset ($link , "utf-8");
											$numb=mysqli_num_rows($result); 
											if (!empty($numb)) 
											{
												$i=1;
												while ($row = mysqli_fetch_array($result))
												{
													// get steam id and name
													$steam[$i] = $row['steam_id_64'];
													
													$role[$i] = "Inviting";
															
													$rws[$i] = $row['rws'];
													if($rws[$i] != 0.00)	$rws[$i] = floor_dec($rws[$i],2);
														
													$kills[$i] = $row['kills'];
													$deaths[$i] = $row['deaths'];
													$hits[$i] = $row['hits'];
													$shots[$i] = $row['shots'];
																
													if($shots[$i] == 0) $ac[$i] = 0;
													else	$ac[$i] = round($hits[$i]/$shots[$i], 2);
														
													if($deaths[$i] == 0)	$kdr[$i] = round($kills[$i]/1, 2);
													else	$kdr[$i] = round($kills[$i]/$deaths[$i], 2);

													$rank[$i] = $row['rank'];
													
													if($rws[$i] > 0)	$rank[$i] = $row['rank'];
													else $rank[$i] = "Unranked";
													
													$i++;
												}
												
												$i-=1;
												
												$query_count=ceil($i/100);
												
												// $i = 資料筆數
												// $query_count = api總共要查詢的次數
												// $k=已查詢的次數
												// $j = api查詢次數loop 
												// $l = api查詢url的steamid筆數
												for ($j=1;$j<=$query_count;$j++) 
												{
													$url[$j] = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
													
													// 如果只要查一次
													if($query_count==1)
													{
														for ($l=1;$l<=$i;$l++) 
														{
															if($l == 1)	$url[$j].="".$steam[$l]."";
															else $url[$j].=",".$steam[$l]."";
														}
													}
													// 如果多於一次
													else
													{
														// 第一次查詢
														if($j == 1)
														{
															for($l=1;$l<=100;$l++) 
															{
																if($l == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
															}
														}
														// 最後一次查詢
														elseif($j == $query_count)
														{
															$k= 1;
															for($l=($j*100)-99;$l<=$i;$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
														// 中間查詢
														else
														{ 
															$k=1;
															for($l=($j*100)-99;$l<=($j*200);$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
													}

													$cURL = curl_init();
													curl_setopt($cURL, CURLOPT_URL, $url[$j]);
													curl_setopt($cURL, CURLOPT_HTTPGET, true);
													curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
													$result = curl_exec($cURL);
													curl_close($cURL);
													$json = json_decode($result, true);
													
													foreach($json['response']['players'] as $item)
													{
														$personname[$item['steamid']] = $item['personaname'];
														//$avatarfull[$item['steamid']] = $item['avatarfull'];
													}
												}

												for ($j=1;$j<=$i;$j++) 
												{
													echo "
													<tr height='40px'>
														<td>".$role[$j]."</td>
														<td>".$rank[$j]."</td>
														<td>".$personname[$steam[$j]]."</td>
														<td>".$rws[$j]."</td>
														<td>".$kills[$j]."</td>
														<td>".$deaths[$j]."</td>
														<td>".$kdr[$j]."</td>
														<td>".$ac[$j]."</td>
														<td><a href='./showplayer.php?id=".$steam[$j]."'>View</a></td>";
													
													if($steam[$j] != $session_team_leader)
														echo"<td class='td-actions text-left'>
													<button type='button' class='DeleteInvite btn btn-danger btn-simple' onclick='javascript:void(0);'>
													<i class='material-icons'>close</i>
													</button>
													</td>";
													else	echo "<td></td>";
													
													echo "</tr>";
												}
											}
											
											// inviting player not in db
											$result=mysqli_query($link, "
											SELECT * FROM ".$invite_table." WHERE receive NOT IN (SELECT steam_id_64 FROM ".$player_table.") AND team = '".$session_team_id."' AND status = '' AND type = 'invite'");
											mysqli_set_charset ($link , "utf-8");
											$numb=mysqli_num_rows($result); 
											if (!empty($numb)) 
											{
												$i=1;
												while ($row = mysqli_fetch_array($result))
												{
													// get steam id and name
													$steam[$i] = $row['receive'];
													
													$role[$i] = "Inviting";
													$i++;
												}
												
												$i-=1;
												
												$query_count=ceil($i/100);
												
												// $i = 資料筆數
												// $query_count = api總共要查詢的次數
												// $k=已查詢的次數
												// $j = api查詢次數loop 
												// $l = api查詢url的steamid筆數
												for ($j=1;$j<=$query_count;$j++) 
												{
													$url[$j] = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
													
													// 如果只要查一次
													if($query_count==1)
													{
														for ($l=1;$l<=$i;$l++) 
														{
															if($l == 1)	$url[$j].="".$steam[$l]."";
															else $url[$j].=",".$steam[$l]."";
														}
													}
													// 如果多於一次
													else
													{
														// 第一次查詢
														if($j == 1)
														{
															for($l=1;$l<=100;$l++) 
															{
																if($l == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
															}
														}
														// 最後一次查詢
														elseif($j == $query_count)
														{
															$k= 1;
															for($l=($j*100)-99;$l<=$i;$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
														// 中間查詢
														else
														{ 
															$k=1;
															for($l=($j*100)-99;$l<=($j*200);$l++) 
															{
																if($k == 1)	$url[$j].="".$steam[$l]."";
																else $url[$j].=",".$steam[$l]."";
																$k++;
															}
														}
													}

													$cURL = curl_init();
													curl_setopt($cURL, CURLOPT_URL, $url[$j]);
													curl_setopt($cURL, CURLOPT_HTTPGET, true);
													curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
													$result = curl_exec($cURL);
													curl_close($cURL);
													$json = json_decode($result, true);
													
													foreach($json['response']['players'] as $item)
													{
														$personname[$item['steamid']] = $item['personaname'];
														//$avatarfull[$item['steamid']] = $item['avatarfull'];
													}
												}

												for ($j=1;$j<=$i;$j++) 
												{
													echo "
													<tr height='40px'>
														<td>".$role[$j]."</td>
														<td>Unranked</td>
														<td>".$personname[$steam[$j]]."</td>
														<td>0</td>
														<td>0</td>
														<td>0</td>
														<td>0</td>
														<td>0</td>
														<td><a href='./showplayer.php?id=".$steam[$j]."'>View</a></td>";
													
													if($steam[$j] != $session_team_leader)
														echo"<td class='td-actions text-left'>
													<button type='button' class='DeleteInvite btn btn-danger btn-simple' onclick='javascript:void(0);'>
													<i class='material-icons'>close</i>
													</button>
													</td>";
													else	echo "<td></td>";
													
													echo "</tr>";
												}
											}
											
											echo "			</tbody>
														</table>
														<button type='button' class='btn btn-rose btn-fill' id='TeamMemberTable_Add' onclick='javascript:void(0);'>Invite New Member</button>
													</div>									
												</div>
											</div>
											";
											
											echo "
											<div class='card'>
												<div class='card-header card-header-text' data-background-color='red'>
													<h4 class='card-title'>Danger Zone</h4>
												</div>
												<div class='card-content text-center'>
													<button type='button' class='btn btn-rose btn-fill' onclick='wm.showSwal(\"leave-team-leader\", \"".$session_team_id."\", \"\", \"\")'>Leave Team</button>
													&emsp;
													<button type='button' class='btn btn-rose btn-fill' onclick='wm.showSwal(\"disband-team\", \"".$session_team_id."\", \"\", \"\")'>Disband Team</button>
												</div>
											</div>";
										}
										// Not Leader
										else
										{
											echo "
											<div class='card'>
												<div class='card-header card-header-text' data-background-color='red'>
													<h4 class='card-title'>Danger Zone</h4>
												</div>
												<div class='card-content'>
													<div class='card-footer text-center'>
													<button type='button' class='btn btn-rose btn-fill' onclick='wm.showSwal(\"leave-team\", \"".$session_team_id."\", \"\", \"\")'>Leave Team</button>
													</div>
												</div>
											</div>";
										}
									}
									else 
									{	
										echo "
										<div class='wizard-container'>
											<div class='card wizard-card' data-color='rose' id='wizardProfile'>
												<form id='create-team-wizard' enctype='multipart/form-data'>
													<div class='wizard-header'>
														<h3 class='wizard-title'>
															You are not in any team.
														</h3>
														<h5>How about create your own team?</h5>
													</div>
													<div class='wizard-navigation'>
														<ul>
															<li>
																<a href='#create_profile' data-toggle='tab'>Profile</a>
															</li>
															<li>
																<a href='#create_logo' data-toggle='tab'>In-Game Logo</a>
															</li>
															<li>
																<a href='#create_member' data-toggle='tab'>Members</a>
															</li>
														</ul>
													</div>
													<div class='tab-content'>
														<div class='tab-pane' id='create_profile'>
															<div class='row'>
																<h4 class='info-text'> Let's start with the basic information</h4>
																<div class='col-sm-4 col-sm-offset-4'>
																	<div class='picture-container'>
																		<div class='picture'>
																			<img src='./assets/img/default-avatar.png' class='picture-src' id='wizardPicturePreview' title='' />
																			<input type='file' id='wizard-picture' name='createlogo' accept='image/jpeg, image/png'>
																		</div>
																		<h6>Choose Team Logo<br>
																		<i class='material-icons text-danger'>warning</i> <font color='#FF0000'>Size limit: 1 MB, jpg/png</font>
																		</h6>
																	</div>
																</div>
															</div>
															<div class='row'>
																<div class='col-lg-5 col-lg-offset-1'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='material-icons'>assistant_photo</i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Team Name
																				<small>(required)</small>
																			</label>
																			<input name='createname' id='createname' type='text' class='form-control' required='true'>
																		</div>
																	</div>
																</div>
																<div class='col-lg-5 label-floating'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='fab fa-2x fa-steam-symbol'></i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Steam
																				<small>(URL)</small>
																			</label>
																			<input name='createsteam' id='createsteam' type='url' class='form-control'>
																		</div>
																	</div>
																</div>
															</div>
															<div class='row'>
																<div class='col-lg-5 col-lg-offset-1'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='fab fa-2x fa-facebook-f'></i>&nbsp;
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Facebook
																				<small>(URL)</small>
																			</label>
																			<input name='createfb' id='createfb' type='url' class='form-control'>
																		</div>
																	</div>
																</div>
																<div class='col-lg-5 label-floating'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='fab fa-2x fa-twitter'></i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Twitter
																				<small>(URL)</small>
																			</label>
																			<input name='createtwitter' id='createtwitter' type='url' class='form-control'>
																		</div>
																	</div>
																</div>
															</div>
															<div class='row'>
																<div class='col-lg-5 col-lg-offset-1'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='fab fa-2x fa-youtube'></i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Youtube
																				<small>(URL)</small>
																			</label>
																			<input name='createyoutube' id='createyoutube' type='url' class='form-control'>
																		</div>
																	</div>
																</div>
																<div class='col-lg-5 label-floating'>
																	<div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='fab fa-2x fa-twitch'></i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Twitch
																				<small>(URL)</small>
																			</label>
																			<input name='createtwitch' id='createtwitch' type='url' class='form-control'>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class='tab-pane' id='create_logo'>
															<h4 class='info-text'>Upload your in-game logo</h4>
															<div class='row'>
																<div class='row'>
																<div class='col-sm-4 col-sm-offset-4'>
																	<div class='picture-container'>
																		<div class='picture'>
																			<img src='./assets/img/default-avatar.png' class='picture-src' id='wizardPicturePreview2' title='' />
																			<input type='file' id='wizard-picture2' name='creategamelogo' accept='image/svg+xml'>
																		</div>
																		<h6>Choose In-Game Team Logo<br>
																		<i class='material-icons text-danger'>warning</i> <font color='#FF0000'>1 MB, 64x64, svg</font>
																		</h6>
																	</div>
																</div>
															</div>
															</div>
														</div>
														<div class='tab-pane' id='create_member'>
															<h4 class='create-member-info info-text'>Invite players to join your team.</h4>
															<div class='row'>
																<div class='col-lg-9 col-lg-offset-1'>
																	 <div class='input-group'>
																		<span class='input-group-addon'>
																			<i class='material-icons'>email</i>
																		</span>
																		<div class='form-group label-floating'>
																			<label class='control-label'>Enter Steam64 ID
																				<small>(required)</small>
																			</label>
																			<input name='createmember[]' id='createmember' type='text' class='form-control createmember0' required='true'>
																		</div>
																	</div>
																</div>
																<div class='col-lg-1 label-floating'>
																	<div class='input-group'>

																	</div>
																</div>
															</div>
															<br>
															<div class='row'>
																<div class='col-xs-4 col-xs-offset-1'>
																	 <div class='input-group'>
																		<button type='button' class='btn btn-rose btn-fill' id='create-add-member' onclick='javascript:void(0);'>Invite New Member</button>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class='wizard-footer'>
														<div class='pull-right'>
															<input type='button' class='btn btn-next btn-fill btn-rose btn-wd' name='next' value='Next' />
															<input type='button' onclick='javascript:void(0);' id='create-finish' class='btn btn-finish btn-fill btn-rose btn-wd' name='finish' value='Finish' />
														</div>
														<div class='pull-left'>
															<input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
														</div>
														<div class='clearfix'></div>
													</div>
												</form>
											</div>
										</div>
										";
									}
?>