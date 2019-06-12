<div class="tab-pane" id="mod">
	<form id="form-profile">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header card-header-text card-header-info">
							<div class="card-text">
							<h4 class="card-title">Token Settings</h4>
							</div>
						</div>
						<div class="card-body">
							<div class="material-datatables">
								<table id="token-table" class="table table-striped table-no-bordered table-hover table-rwd w-100">
									<thead>
										<tr class="tr-only-hide">
											<th>Owner</th>
											<th>Token</th>
											<th>FTP User</th>
											<th>FTP Password</th>
											<th>Expire Date</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql = "SELECT * FROM ".$license_table;
											$sth = $pdo->prepare($sql);
											$sth->execute();
											$result = $sth->fetchAll();

											$sql = "SELECT * FROM ".$license_table;
											$sth = $pdo->prepare($sql);
											$sth->execute();
											$steamids = $sth->fetchAll(PDO::FETCH_COLUMN, 1);
											$data = SteamData::GetData($SteamAPI_Key, $steamids);

											foreach($result as $row){
												?>
													<tr>
														<td class="token-steam" data-th="Owner" data-steam="<?=$row["steam_id_64"]?>">
															<a href="https://steamcommunity.com/profiles/<?=$row["steam_id_64"]?>"><?=$data["name"][ $row["steam_id_64"] ]?></a>
														</td>
														<td class="token-token" data-th="Token"><?=$row["token"]?></td>
														<td class="token-ftpu" data-th="FTP User"><?=$row["ftp_a"]?></td>
														<td class="token-ftpp" data-th="FTP Password"><?=$row["ftp_p"]?></td>
														<td class="token-exp" data-th="Expire Date"><?=$row["time_exp"]?></td>
														<td class="" data-th="Actions">
															<button type="button" class="btn btn-link btn-success px-2 btn-token-edit" data-id="<?=$row["id"]?>">
																<i class="fas fa-pen"></i>
															</button>
															<button type="button" class="btn btn-link btn-danger px-2 btn-token-del" data-id="<?=$row["id"]?>">
																<i class="fas fa-times"></i>
															</button>
														</td>
													</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="card-footer">
							<button class="btn btn-success" id="btn-token-add" type="button">
								<i class="fas fa-plus"></i>&nbsp;Add
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header card-header-text card-header-primary">
							<div class="card-text">
							<h4 class="card-title">Servers</h4>
							</div>
						</div>
						<div class="card-body">
							<div class="material-datatables">
								<table id="server-table" class="table table-striped table-no-bordered table-hover table-rwd w-100">
									<thead>
										<tr class="tr-only-hide">
											<th>Name</th>
											<th>IP</th>
											<th>Port</th>
											<th>Map</th>
											<th>Players</th>
											<th>Connect</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sth = $pdo->prepare("SELECT * FROM ".$server_table);
											$sth->execute();
											$result = $sth->fetchAll();
											foreach($result as $row){
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
														$infos["players"] = "- / -";
														$infos["places"] = "-";
													}	
												}
												?>
													<tr>
														<td><?=$infos['name']?></td>
														<td><?=$row['ip']?></td>
														<td><?=$row['port']?></td>
														<td><?=$infos['map']?></td>
														<td><?=$infos["players"]."/".$infos["places"]?></td>
														<td>
															<a href ='steam://connect/<?=$row['ip']?>:<?=$row['port']?>'>CONNECT</a>
														</td>
														<td>
															<button type="button" class="btn btn-link btn-info px-2 btn-server-enable" data-id="<?=$row["id"]?>" data-enable="<?=$row["enabled"]?>">
																<i class="far <?=($row["enabled"] == "1")?"fa-eye":"fa-eye-slash"?>"></i>
															</button>
															<button type="button" class="btn btn-link btn-success px-2 btn-server-edit" data-id="<?=$row["id"]?>">
																<i class="fas fa-pen"></i>
															</button>
															<button type="button" class="btn btn-link btn-danger px-2 btn-server-del" data-id="<?=$row["id"]?>">
																<i class="fas fa-times"></i>
															</button>
														</td>
													</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="card-footer">
							<button class="btn btn-success" id="btn-server-add" type="button">
								<i class="fas fa-plus"></i>&nbsp;Add
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>