<div class="card">
										<div class="card-header card-header-text" data-background-color="blue">
											<h4 class="card-title">Notifications</h4>
										</div>
										<div class="card-content table-responsive">
										<?php
											$notify_result=mysqli_query($link, "
											SELECT ".$team_table.".name AS teamname, a.* 
											FROM ".$team_table.", (SELECT * FROM ".$invite_table." WHERE receive = '".$steamprofile['steamid']."' AND status = '')a 
											WHERE id = a.team");
											$notify_numb=mysqli_num_rows($notify_result); 
											
											echo "<table class='table table-hover' style='width:80%' align='center' id='NotifyTable'><tbody>";
												
											if (!empty($notify_numb)) 
											{
												$i=1;

												while ($notify_row = mysqli_fetch_array($notify_result))
												{
													$notify_ty[$i] = $notify_row['type'];
													$notify_s[$i] = $notify_row['send'];
													$notify_t[$i] = $notify_row['team'];
													$notify_tn[$i] = $notify_row['teamname'];
													$notify_id[$i] = $notify_row['key_id'];
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
															if($l == 1)	$url[$j].="".$notify_s[$l]."";
															else $url[$j].=",".$notify_s[$l]."";
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
																if($l == 1)	$url[$j].="".$notify_s[$l]."";
																else $url[$j].=",".$notify_s[$l]."";
															}
														}
														// 最後一次查詢
														elseif($j == $query_count)
														{
															$k= 1;
															for($l=($j*100)-99;$l<=$i;$l++) 
															{
																if($k == 1)	$url[$j].="".$notify_s[$l]."";
																else $url[$j].=",".$notify_s[$l]."";
																$k++;
															}
														}
														// 中間查詢
														else
														{ 
															$k=1;
															for($l=($j*100)-99;$l<=($j*200);$l++) 
															{
																if($k == 1)	$url[$j].="".$notify_s[$l]."";
																else $url[$j].=",".$notify_s[$l]."";
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
													if($notify_ty[$j] == "invite")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has invited you to join <a href='./showteam.php?id=".$notify_t[$j]."'>".$notify_tn[$j]."</a>
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple TeamAccept' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='Accept'>
																<i class='material-icons'>done</i>
																</button>
																<button type='button' class='btn btn-danger btn-simple TeamIgnore' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='Ignore'>
																<i class='material-icons'>close</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_t[$j]."</td>
															<td style='display:none' class='notifydata2'>".$notify_id[$j]."</td>
														</tr>";
													elseif($notify_ty[$j] == "deleteteam")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has disbanded your team <a href='./showteam.php?id=".$notify_t[$j]."'>".$notify_tn[$j]."</a>
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";
													elseif($notify_ty[$j] == "kickteam")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has removed you from <a href='./showteam.php?id=".$notify_t[$j]."'>".$notify_tn[$j]."</a>
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";	
													elseif($notify_ty[$j] == "leave")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has left <a href='./showteam.php?id=".$notify_t[$j]."'>your team</a>
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";	
													elseif($notify_ty[$j] == "newleader")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has left <a href='./showteam.php?id=".$notify_t[$j]."'>your team</a>, you are the new team leader now
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";
													elseif($notify_ty[$j] == "ignoreinvite")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has ignored your team invite.
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";
													elseif($notify_ty[$j] == "joinedteam")
														echo "
														<tr>
															<td style='width:70%'>
															<a href='./showplayer.php?id=".$notify_s[$j]."'>".$personname[$notify_s[$j]]."</a> has joined your team.
															</td>
															<td class='td-actions text-right' style='width:30%'>
																<button type='button' class='btn btn-success btn-simple ReadNotify' onclick='javascript:void(0);' rel='tooltip' data-placement='left' title='OK'>
																<i class='material-icons'>done</i>
																</button>
															</td>
															<td style='display:none' class='notifydata'>".$notify_id[$j]."</td>
															<td style='display:none' class='notifydata2'></td>
														</tr>";
												}
												
												
											}
											else echo "<tr><td colspan='2'>You don't have any unread notification.</td></tr>";
											
											echo "</tbody></table>";
										?>
										</div>
									</div>