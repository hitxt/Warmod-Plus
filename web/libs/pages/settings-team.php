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
        </div>
    </div>
</div>