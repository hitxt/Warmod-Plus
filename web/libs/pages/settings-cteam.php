<div class="tab-pane" id="create">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="wizard-container">
                    <div class="card card-wizard active" data-color="rose" id="wizardProfile">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="card-header text-center">
                                <h3 class="card-title">
                                    You are not in any team.
                                </h3>
                                <h5 class="card-description">How about create your own team?</h5>
                            </div>
                            <div class="wizard-navigation">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#team-profile" data-toggle="tab" role="tab">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#team-logo" data-toggle="tab" role="tab">
                                            Logo
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#team-member" data-toggle="tab" role="tab">
                                            Member
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="team-profile">
                                        <h5 class="info-text"> Let's start with the basic information.</h5>
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-id-card"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control" name="name" required id="team-name" placeholder="Team Name (Required)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fab fa-steam-symbol"></i>
                                                            </span>
                                                        </div>
                                                        <input type="url" class="form-control" name="steamurl" id="team-steam" placeholder="Steam Group">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fab fa-facebook-f text-dark"></i>
                                                            </span>
                                                        </div>
                                                        <input type="url" class="form-control" name="facebook" id="team-facebook" placeholder="Facebook">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fab fa-twitter text-dark"></i>
                                                            </span>
                                                        </div>
                                                        <input type="url" class="form-control" name="twitter" id="team-twitter" placeholder="Twitter">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fab fa-twitch text-dark"></i>
                                                            </span>
                                                        </div>
                                                        <input type="url" class="form-control" name="twitch" id="team-twitch" placeholder="Twitch">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fab fa-youtube text-dark"></i>
                                                            </span>
                                                        </div>
                                                        <input type="url" class="form-control" name="youtube" id="team-youtube" placeholder="Youtube">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="team-logo">
                                        <h5 class="info-text"> You can also customize your team logo. </h5>
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-6 ">
                                                <div class="fileinput fileinput-new text-center w-100"
                                                    data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail img-raised">
                                                        <img src="./assets/img/default-avatar.png"
                                                            alt="...">
                                                    </div>
                                                    <div
                                                        class="fileinput-preview fileinput-exists thumbnail img-raised">
                                                    </div>
                                                    <div>
                                                        <p class="text-rose mt-2 mb-2">Web Logo (JPG/ PNG, 1MB)</p>
                                                        <span class="btn btn-raised btn-round btn-rose btn-file">
                                                            <span class="fileinput-new">Select image</span>
                                                            <span class="fileinput-exists">Change</span>
                                                            <input type="file" name="weblogo" accept='image/jpeg, image/png' />
                                                        </span>
                                                        <a href="#pablo"
                                                            class="btn btn-danger btn-round fileinput-exists"
                                                            data-dismiss="fileinput">
                                                            <i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="fileinput fileinput-new text-center w-100"
                                                    data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail img-raised">
                                                        <img src="./assets/img/default-avatar.png"
                                                            alt="...">
                                                    </div>
                                                    <div
                                                        class="fileinput-preview fileinput-exists thumbnail img-raised">
                                                    </div>
                                                    <div>
                                                        <p class="text-rose mt-2 mb-2">
                                                            In-game Logo (SVG, Custom Format) 
                                                            <a href="https://gist.github.com/rogeraabbccdd/13c7503f7d6add3062c57bc2bb409b5e" target="_blank" class="btn btn-rose btn-link btn-fab btn-fab-mini btn-round token-refresh">
                                                                <i class="material-icons">help</i>
                                                            </a>
                                                        </p>
                                                        <span class="btn btn-raised btn-round btn-rose btn-file">
                                                            <span class="fileinput-new">Select image</span>
                                                            <span class="fileinput-exists">Change</span>
                                                            <input type='file' name='teamlogo' accept='image/svg+xml' />
                                                        </span>
                                                        <a href="#pablo"
                                                            class="btn btn-danger btn-round fileinput-exists"
                                                            data-dismiss="fileinput">
                                                            <i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="team-member">
                                        <h5 class="info-text"> You can invite your friend. </h5>
                                        <div class="row justify-content-center">
                                            <div class="col-12 members">
                                                <div class="form-group">
                                                    <div class="input-group mt-1">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="material-icons">email</i>
                                                            </span>
                                                        </div>
                                                        <input type="number" class="form-control createmember" name="steam[]" id="" placeholder="Steam64 ID">
                                                        <button class="btn btn-link btn-del">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <input type="button" value="add" class="btn btn-success" id="append-member">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="mr-auto">
                                    <input type="button" class="btn btn-previous btn-fill btn-default btn-wd disabled"
                                        name="previous" value="Previous">
                                </div>
                                <div class="ml-auto">
                                    <input type="button" class="btn btn-next btn-fill btn-rose btn-wd" name="next"
                                        value="Next">
                                    <input type="submit" class="btn btn-finish btn-fill btn-rose btn-wd" name="finish"
                                        value="Finish" style="display: none;">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>