<div class="tab-pane" id="team">
    <div class="container">
        <div class="row">
            <!-- Team Profile -->
            <div class="col-12">
                <form id="form-team" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header card-header-text card-header-info">
                            <div class="card-text">
                                <h4 class="card-title">Team Settings</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">
                                    <i class="fab fa-facebook-f text-dark"></i>&emsp;Facebook
                                </label>
                                <div class="col-sm-9">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="url" name="facebook" value="<?=$team->fb?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">
                                    <i class="fab fa-twitter text-dark"></i>&emsp;Twitter
                                </label>
                                <div class="col-sm-9">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="url" name="twitter"
                                            value="<?=$team->twitter?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">
                                    <i class="fab fa-twitch text-dark"></i>&emsp;Twitch
                                </label>
                                <div class="col-sm-9">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="url" name="twitch" value="<?=$team->twitch?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">
                                    <i class="fab fa-youtube text-dark"></i>&emsp;Youtube
                                </label>
                                <div class="col-sm-9">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="url" name="youtube"
                                            value="<?=$team->youtube?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-success">
                                Save
                                <div class="ripple-container"></div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Team Logo -->
            <div class="col-12">
                <form id="form-team-logo" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header card-header-text card-header-primary">
                            <div class="card-text">
                                <h4 class="card-title">Logo Settings</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="fileinput fileinput-new text-center w-100" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail img-raised">
                                            <img src="<?=$team->logo?>"
                                                alt="...">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail img-raised"></div>
                                        <h4 class="text-info font-bold mt-3">Web Logo</h4>
                                        <div>
                                            <span class="btn btn-raised btn-round btn-rose btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="weblogo" accept='image/jpeg, image/png' />
                                            </span>
                                            <a href="#" class="btn btn-danger btn-round fileinput-exists"
                                                data-dismiss="fileinput">
                                                <i class="fa fa-times"></i> Remove
                                            </a>
                                        </div>
                                        <p class="text-danger font-bold">JPG/ PNG, 1MB</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="fileinput fileinput-new text-center w-100" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail img-raised">
                                            <img src="<?=$team->gamelogo?>"
                                                alt="...">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail img-raised"></div>
                                        <h4 class="text-info font-bold mt-3">In-game Logo</h4>
                                        <div>
                                            <span class="btn btn-raised btn-round btn-rose btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type='file' name='teamlogo' accept='image/svg+xml' />
                                            </span>
                                            <a href="#" class="btn btn-danger btn-round fileinput-exists"
                                                data-dismiss="fileinput">
                                                <i class="fa fa-times"></i> Remove
                                            </a>
                                        </div>
                                        <p class="text-danger font-bold">SVG, Custom Format
                                            <a href="https://gist.github.com/rogeraabbccdd/13c7503f7d6add3062c57bc2bb409b5e" target="_blank" class="btn btn-rose btn-link btn-fab btn-fab-mini btn-round token-refresh">
                                                <i class="material-icons">help</i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-success">
                                Save
                                <div class="ripple-container"></div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>