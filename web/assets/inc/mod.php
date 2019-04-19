									<div class="card">
                                        <div class="card-header card-header-text" data-background-color="blue">
                                            <h4 class="card-title">Token Management</h4>
                                            <p class="category">
                                                
                                            </p>
                                        </div>
                                        <div class="card-content">
											<div class="material-datatables">
												<table id="LicenseTable" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
													<thead class="thead-inverse">
														<tr>
															<th>Owner</th>
															<th>Token</th>
															<th>Exp Date</th>
															<th>FTP Account</th>
															<th>FTP Password</th>
															<th>Action</th>
															<th style="display:none;">ID</th>
														</tr>
													</thead>
													<tfoot>
														<tr>
															<th>Owner</th>
															<th>Token</th>
															<th>Exp Date</th>
															<th>FTP Account</th>
															<th>FTP Password</th>
															<th>Action</th>
															<th style="display:none;">ID</th>
														</tr>
													</tfoot>
													<tbody>
													<?php
														$result=mysqli_query($link, "SELECT * FROM ".$license_table."");
														mysqli_set_charset ($link , "utf-8");
														$numb=mysqli_num_rows($result); 
														if (!empty($numb)) 
														{
															$i=1;
															while ($row = mysqli_fetch_array($result))
															{
																$license_id[$i] = $row['id'];
																$license_steam[$i] = $row['steam_id_64'];
																$license_token[$i] = $row['token'];
																$license_time_exp[$i] = $row['time_exp'];
																$license_ftp_a[$i] = $row['ftp_a'];
																$license_ftp_p[$i] = $row['ftp_p'];
																$i++;
															}
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
																	if($l == 1)	$url[$j].="".$license_steam[$l]."";
																	else $url[$j].=",".$license_steam[$l]."";
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
																		if($l == 1)	$url[$j].="".$license_steam[$l]."";
																		else $url[$j].=",".$license_steam[$l]."";
																	}
																}
																// 最後一次查詢
																elseif($j == $query_count)
																{
																	$k= 1;
																	for($l=($j*100)-99;$l<=$i;$l++) 
																	{
																		if($k == 1)	$url[$j].="".$license_steam[$l]."";
																		else $url[$j].=",".$license_steam[$l]."";
																		$k++;
																	}
																}
																// 中間查詢
																else
																{ 
																	$k=1;
																	for($l=($j*100)-99;$l<=($j*200);$l++) 
																	{
																		if($k == 1)	$url[$j].="".$license_steam[$l]."";
																		else $url[$j].=",".$license_steam[$l]."";
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
																<td><a href='./showplayer.php?id=".$license_steam[$j]."'>".$personname[$license_steam[$j]]."</td>
																<td>".$license_token[$j]."</td>
																<td>".$license_time_exp[$j]."</td>
																<td>".$license_ftp_a[$j]."</td>
																<td>".$license_ftp_p[$j]."</td>
																<td class='td-actions text-left'>
																	<button type='button' class='DeleteLicense btn btn-danger btn-simple' onclick='javascript:void(0);'>
																		<i class='material-icons'>close</i>
																	</button>
																	<button type='button' class='EditLicense btn btn-success btn-simple' onclick='javascript:void(0);'>
																		<i class='material-icons'>edit</i>
																	</button>
																</td>
																<td style='display:none'>".$j."</td>
															</tr>";
														}
													?>
													</tbody>
												</table>
											</div>
											<br />
											<hr>
											<form id="license-add" method="post" class="form-horizontal">
												<div class="row">
													<label class="col-sm-2 label-on-left">Steam64 ID</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class='form-control' type='text' id='listeam' name='listeam' required='true'/>
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">End Date</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class='form-control datepicker' type='text' name='lidate2' id='lidate2' required='true'/>
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">FTP Account</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class='form-control' type='text' id='liftpa' name='liftpa' required='true'/>
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">FTP Password</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class='form-control' type='text' id='liftpp' name='liftpp' required='true'/>
														</div>
													</div>
												</div>
												<br />
												<div class="col-md-12 text-center">
													<button type='button' class='btn btn-rose btn-fill' id='LicenseTable_Add' onclick='javascript:void(0);'>Add New Token</button>
												</div>
											</form>
										</div>
                                    </div>
									<?php
										if($server_list)
										{
									?>
									<div class="card">
                                        <div class="card-header card-header-text" data-background-color="green">
                                            <h4 class="card-title">Server Management</h4>
                                            <p class="category">
                                                
                                            </p>
                                        </div>
                                        <div class="card-content">
											<div class="material-datatables">
												<table id="ServerTable" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
													<thead class="thead-inverse">
														<tr>
															<th>Enable</th>
															<th>IP</th>
															<th>Port</th>
															<th>Type</th>
															<th>Name</th>
															<th>Map</th>
															<th>Players</th>
															<th>Connect</th>
															<th>Action</th>
														</tr>
													</thead>
													<tfoot>
														<tr>
															<th>Enable</th>
															<th>IP</th>
															<th>Port</th>
															<th>Type</th>
															<th>Name</th>
															<th>Map</th>
															<th>Players</th>
															<th>Connect</th>
															<th>Action</th>
														</tr>
													</tfoot>
													<tbody>
													<?php
														$result=mysqli_query($link, "SELECT * FROM ".$server_table." ORDER BY id DESC");
														mysqli_set_charset ($link , "utf-8"); 
														$numb=mysqli_num_rows($result); 
														while ($row = mysqli_fetch_array($result)) 
														{
															try
															{
																$server = new SourceQuery($row['ip'],$row['port']);
																$infos  = $server->getInfos();
																if(is_null($infos["mod"]))	throw new InvalidArgumentException( "invalid" );
																else	throw new InvalidArgumentException( "valid" );
															}
															catch( InvalidArgumentException $e )
															{
																if( $e->getMessage() == "invalid" )
																{
																	$infos['name'] = "Cannot Connect To Server.";
																	$infos["map"] = "-";
																	$infos["players"] = "-";
																	$infos["places"] = "-";
																}	
															}
															
															if($row['official'] == "1") $type = "Official";
															elseif($row['official'] == "2") $type = "Partner";
															else $type = "";
															
															echo "<tr>";
																
															if($row["enabled"] == "1")	echo "<td>Enable</td>";
															else	echo "<td>Disable</td>";
															
															echo"
															<td>".$row['ip']."</td>
															<td>".$row['port']."</td>
															<td>".$type."</td>
															<td>".$infos['name']."</td>
															<td>".$infos["map"]."</td>
															<td>".$infos["players"]."/".$infos["places"]."</td>
															<td><a href ='steam://connect/".$row['ip'].":".$row['port']."'>CONNECT</a></td>
															<td class='td-actions text-left'>
																<button type='button' class='DeleteServer btn btn-danger btn-simple' onclick='javascript:void(0);'>
																	<i class='material-icons'>close</i>
																</button>
															</td>
															</tr>";
														}
													?>
													</tbody>
												</table>
												<br />
												<hr>
												
												<form id="server-add" method="post" class="form-horizontal" name="server-add">
													<div class="row">
														<label class="col-sm-2 label-on-left">IP</label>
														<div class="col-md-9">
															<div class="form-group label-floating">
																<label class="control-label"></label>
																<input class='form-control' type='text' id='svip' name='svip' required='true'/>
															</div>
														</div>
													</div>
													<div class="row">
														<label class="col-sm-2 label-on-left">Port</label>
														<div class="col-md-9">
															<div class="form-group label-floating">
																<label class="control-label"></label>
																<input class='form-control' type='text' name='svport' id='svport' required='true'/>
															</div>
														</div>
													</div>
													<div class="row">
														<label class="col-sm-2 label-on-left">Type</label>
														<div class="col-md-9">
															<select class="selectpicker" data-style="select-with-transition" title="Choose Type" data-size="4" required='true' name="svtype" id="svtype">
																<option disabled> Choose Type</option>
																<option value="1">Official </option>
																<option value="2">Partner </option>
																<option value="0">Other </option>
															</select>
														</div>
													</div>
													<br />
													<div class="row">
														<div class="radio col-md-12 text-center">
															<label>
																<input type="radio" name="enableradio" checked value="1"> Enable
															</label>
															<label>
																<input type="radio" name="enableradio" value="0"> Disable
															</label>
														</div>
													</div>
													<br />
													<div class="col-md-12 text-center">
														<button type='button' class='btn btn-rose btn-fill' id='ServerTable_Add' onclick='javascript:void(0);'>Add New Server</button>
													</div>
												</form>
											</div>
										</div>
                                    </div>
										<?php } ?>
									<div class="modal fade" id="modal_form" role="dialog">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h3 class="modal-title">Edit License</h3>
												</div>
												<div class="modal-body form">
													<form id="editlicenseform" class="form-horizontal" name="editlicenseform" action="#">
														<input type="hidden" value="" name="edittoken" id="edittoken"/> 
														<input type="hidden" value="" name="editrow" id="editrow"/> 
														<div class="row">
															<label class="col-sm-2 label-on-left">End Date</label>
															<div class="col-md-9">
																<div class="form-group label-floating" style="margin:0px !important">
																	<label class="control-label"></label>
																	<input class='form-control datepicker' type='text' name='editdate2' id='editdate2' required='true'/>
																</div>
															</div>
														</div>
														<div class="row">
															<label class="col-sm-2 label-on-left">FTP Account</label>
															<div class="col-md-9">
																<div class="form-group label-floating"  style="margin:0px !important">
																	<label class="control-label"></label>
																	<input class='form-control' type='text' id='editftpa' name='editftpa' required='true'/>
																</div>
															</div>
														</div>
														<div class="row">
															<label class="col-sm-2 label-on-left">FTP Password</label>
															<div class="col-md-9">
																<div class="form-group label-floating"  style="margin:0px !important">
																	<label class="control-label"></label>
																	<input class='form-control' type='text' id='editftpp' name='editftpp' required='true'/>
																</div>
															</div>
														</div>
														<br />
													</form>
												</div>
												<div class="modal-footer">
													<button type="button" id="SaveLicense" onclick="javascript:void(0);" class="btn btn-primary">Save</button>
													<button type="button" id="SaveLicenseCancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												</div>
											</div>
										</div>
									</div>