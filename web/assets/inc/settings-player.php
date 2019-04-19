									<div class="card">
                                        <div class="card-header card-header-text" data-background-color="blue">
                                            <h4 class="card-title">Profile Settings</h4>
                                            <p class="category">
                                                
                                            </p>
                                        </div>
                                        <div class="card-content">
                                            <form id="PlayerProfile" class="form-horizontal">
												<div class="row">
													<label class="col-sm-2 label-on-left">Facebook</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class="form-control" type="url" name="facebook" url="true" value="<?=$fb?>"/>
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">Twitter</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class="form-control" type="url" name="twitter" url="true" value="<?=$twitter?>"/>
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">YouTube</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class="form-control" type="url" name="youtube" url="true" value="<?=$youtube?>" />
														</div>
													</div>
												</div>
												<div class="row">
													<label class="col-sm-2 label-on-left">Twitch</label>
													<div class="col-md-9">
														<div class="form-group label-floating">
															<label class="control-label"></label>
															<input class="form-control" type="url" name="twitch" url="true" value="<?=$twitch?>" />
														</div>
													</div>
												</div>
												<div class="card-footer text-center">
													<button type="button" onclick="javascript:void(0);" id="PlayerProfile_Save" class="btn btn-rose btn-fill">Save</button>
												</div>
											</form>
										</div>
                                    </div>