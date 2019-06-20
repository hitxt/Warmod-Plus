function initMaterialWizard () {
	// Code for the Validator
	var $validator = $('.card-wizard form').validate({
		rules: {
			name: {
				required: true
			},
			"steam[]": {
				remote: function(element){
					return{
						url: "./libs/api.php?action=valid-steam",
						type:'post',
						data: {
							steam: $(element).val()
						}               
					}
				}
			}
		},
		messages: {
			"steam[]": {
				remote:"Invalid Steam64 ID",
			}
		},
		ignore: [],
		onkeyup: function(element) {
			$(element).valid();
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
			$(element).closest('.form-group').find(".error").remove();
		},
		errorPlacement: function(error, element) {
			$(element).closest('.form-group').append(error);
		},
		submitHandler:function(form){        
			let fd =  new FormData(form);
			
			$.ajax({
				url : "./libs/api.php?action=team-create",
				data: fd, 
				processData: false,
				contentType: false,
				cache: false,
				type: "POST",
				dataType:"json",
				success: function(r){
					if(r.error.length == 0){
						swal({
							type: "success",
							title: "Success!",
							text: "Your team is created successfully.",
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1"
						}).then(function () {
							window.setTimeout(function () {
								window.location.reload()
							}, 750);
						})
					}
					else{
						swal({
							type: "error",
							title: "Error",
							text: r.error,
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1"
						})
					}
				}
			})
			return false;
		}
	});

	// Wizard Initialization
	$('.card-wizard').bootstrapWizard({
		'tabClass': 'nav nav-pills',
		'nextSelector': '.btn-next',
		'previousSelector': '.btn-previous',

		onNext: function(tab, navigation, index) {
			var $valid = $('.card-wizard form').valid();
			if (!$valid) {
				$validator.focusInvalid();
				return false;
			}
		},

		onInit: function(tab, navigation, index) {
			//check number of tabs and fill the entire row
			var $total = navigation.find('li').length;
			var $wizard = navigation.closest('.card-wizard');

			$first_li = navigation.find('li:first-child a').html();
			$moving_div = $('<div class="moving-tab">' + $first_li + '</div>');
			$('.card-wizard .wizard-navigation').append($moving_div);

			refreshAnimation($wizard, index);

			$('.moving-tab').css('transition', 'transform 0s');
		},

		onTabClick: function(tab, navigation, index) {
			var $valid = $('.card-wizard form').valid();
			if (!$valid) {
				$validator.focusInvalid();
				return false;
			} else {
				return true;
			}
		},

		onTabShow: function(tab, navigation, index) {
			var $total = navigation.find('li').length;
			var $current = index + 1;

			var $wizard = navigation.closest('.card-wizard');

			// If it's the last tab then hide the last button and show the finish instead
			if ($current >= $total) {
				$($wizard).find('.btn-next').hide();
				$($wizard).find('.btn-finish').show();
			} else {
				$($wizard).find('.btn-next').show();
				$($wizard).find('.btn-finish').hide();
			}

			button_text = navigation.find('li:nth-child(' + $current + ') a').html();

			setTimeout(function() {
				$('.moving-tab').text(button_text);
			}, 150);

			var checkbox = $('.footer-checkbox');

			if (!index == 0) {
				$(checkbox).css({
					'opacity': '0',
					'visibility': 'hidden',
					'position': 'absolute'
				});
			} else {
				$(checkbox).css({
					'opacity': '1',
					'visibility': 'visible'
				});
			}

			refreshAnimation($wizard, index);
		}
	});


	// Prepare the preview for profile picture
	$("#wizard-picture").change(function() {
		readURL(this);
	});

	$('[data-toggle="wizard-radio"]').click(function() {
		wizard = $(this).closest('.card-wizard');
		wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
		$(this).addClass('active');
		$(wizard).find('[type="radio"]').removeAttr('checked');
		$(this).find('[type="radio"]').attr('checked', 'true');
	});

	$('[data-toggle="wizard-checkbox"]').click(function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).find('[type="checkbox"]').removeAttr('checked');
		} else {
			$(this).addClass('active');
			$(this).find('[type="checkbox"]').attr('checked', 'true');
		}
	});

	$('.set-full-height').css('height', 'auto');

	//Function to show image before upload
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	$(window).resize(function() {
		$('.card-wizard').each(function() {
			$wizard = $(this);

			index = $wizard.bootstrapWizard('currentIndex');
			refreshAnimation($wizard, index);

			$('.moving-tab').css({
				'transition': 'transform 0s'
			});
		});
	});

	function refreshAnimation($wizard, index) {
		$total = $wizard.find('.nav li').length;
		$li_width = 100 / $total;

		total_steps = $wizard.find('.nav li').length;
		move_distance = $wizard.width() / total_steps;
		index_temp = index;
		vertical_level = 0;

		mobile_device = $(document).width() < 600 && $total > 3;

		if (mobile_device) {
			move_distance = $wizard.width() / 2;
			index_temp = index % 2;
			$li_width = 50;
		}

		$wizard.find('.nav li').css('width', $li_width + '%');

		step_width = move_distance;
		move_distance = move_distance * index_temp;

		$current = index + 1;

		if ($current == 1 || (mobile_device == true && (index % 2 == 0))) {
			move_distance -= 8;
		} else if ($current == total_steps || (mobile_device == true && (index % 2 == 1))) {
			move_distance += 8;
		}

		if (mobile_device) {
			vertical_level = parseInt(index / 2);
			vertical_level = vertical_level * 38;
		}

		$wizard.find('.moving-tab').css('width', step_width);
		$('.moving-tab').css({
			'transform': 'translate3d(' + move_distance + 'px, ' + vertical_level + 'px, 0)',
			'transition': 'all 0.5s cubic-bezier(0.29, 1.42, 0.79, 1)'

		});
	}
}

// remove click event
$('#modal').on('hide.bs.modal', function (e) {
	$("#modal").off("click", ".btn-modal-save");
})

/* Token */
var tokenTable = $("#token-table").DataTable({
	"columnDefs": [ 
		{
			"targets": 5,
			"searchable": false,
			"orderable": false
		}, 
	]
});

$("#token-table").on("click", ".btn-token-edit", function(e){
	e.preventDefault();
	tokenModal(true, $(this));
	return false;
});

$("#btn-token-add").on("click", function(e){
	e.preventDefault();
	tokenModal(false, "");
	return false;
});

$("#token-table").on("click", ".btn-token-del", function(){
	swal({
		type: "warning",
		title: "Are you sure?",
		text: "Do you really want to delete this token?",
		buttonsStyling: false,
		confirmButtonClass: "btn btn-success mx-1",
		cancelButtonClass: "btn btn-danger mx-1",
		showCancelButton: true,
	}).then((result) => {
		if (result.value) {
			let _this = $(this);
			let id = _this.attr("data-id");
			$.ajax({
				method: "POST",
				url: "./libs/api.php?action=token-del",
				data: {id},
				dataType: "json",
				success: function(r){
					if(r.success){
						swal({
							type: "success",
							title: "Success!",
							text: "The token has been deleted.",
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1"
						})
						tokenTable.row(_this.parents('tr')).remove().draw();
					}
					else{
						swal({
							type: "error",
							title: "Error!",
							text: "Error occurred when deleting the token.",
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1",
						})
					}
				}
			})
		}
	})
});

function tokenModal(edit, el){
	let url;
	if(edit){
		$("#modal .modal-title").text("Edit Token");
		url = "./libs/api.php?action=token-edit";
	}
	else{
		$("#modal .modal-title").text("New Token");
		url = "./libs/api.php?action=token-new";
	}
	
	let steam = "", token = "", exp = "", ftpu = "", ftpp = "", id = "";
	if(edit){
		steam = el.parents("tr").find(".token-steam").attr("data-steam");
		token = el.parents("tr").find(".token-token").text();
		exp = el.parents("tr").find(".token-exp").text();
		ftpu = el.parents("tr").find(".token-ftpu").text();
		ftpp = el.parents("tr").find(".token-ftpp").text();
		id = el.attr("data-id");
	}

	const html = /*html*/`
	<form action="" id="form-modal">
		<input type="hidden" name="id" value="${id}">	
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				Steam64 ID
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control" type="number" name="steam" value="${steam}" required>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				Token
			</label>
			<div class="col-7">
				<div class="form-group">
					<input class="form-control" type="text" name="token" value="${token}" required>
				</div>
			</div>
			<div class="col-2">
				<button class="btn btn-primary btn-fab btn-fab-mini btn-round token-refresh" type="button">
					<i class="material-icons">cached</i>
				</button>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				FTP User
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control" type="text" name="ftpu" value="${ftpu}" required>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				FTP Password
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control" type="text" name="ftpp" value="${ftpp}" required>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				Expire Date
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control datetimepicker" type="text" name="exp" value="${exp}" required>
				</div>
			</div>
		</div>
	</form>`;

	$("#modal .modal-body").html(html);

	$("#modal").on("click", ".token-refresh", function(e){
		e.preventDefault();
		$("#modal input[name=token]").val( Math.random().toString(36).substr(2) );
		return false;
	})

	let $validator = $('.modal form').validate({
		rules: {
			steam: {
				required: true,
				remote: function(element){
					return{
						url : "./libs/api.php?action=valid-steam",
						type:'post',
						data: {
							steam: $(element).val()
						}
					}
				}
			}
		},
		messages: {
			steam: {
				remote:"Invalid Steam64 ID"
			}
		},
		onkeyup: function(element) {
			$(element).valid();
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
			$(element).remove();
		},
		errorPlacement : function(error, element) {
			error.insertAfter($(element));
		}
	});

	$(".datetimepicker").datetimepicker({
		format: 'YYYY-MM-DD',
		icons: {
			time: "fa fa-clock-o",
			date: "fa fa-calendar",
			up: "fa fa-chevron-up",
			down: "fa fa-chevron-down",
			previous: 'fa fa-chevron-left',
			next: 'fa fa-chevron-right',
			today: 'fa fa-screenshot',
			clear: 'fa fa-trash',
			close: 'fa fa-remove'
		}
	});
	$("#modal .modal-footer").html(/*html*/`
		<button type="button" class="btn btn-success mx-1 btn-modal-save">Save changes</button>
		<button type="button" class="btn btn-danger mx-1" data-dismiss="modal">Close</button>
	`);

	$("#modal").on("click", ".btn-modal-save", function(e){
		e.preventDefault();
		var $valid = $('.modal form').valid();
		if (!$valid) {
			$validator.focusInvalid();
			return false;
		}
		$(this).attr("disabled", true);
		$(this).text("Saving...");
		let fd =  new FormData( $("#form-modal")[0] );
		$.ajax({
			method: "POST",
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			cache: false,
			dataType: "json",
			success: function(r){
				if(r.success){
					swal({
						type: "success",
						title: "Success!",
						text: "Token has been saved.",
						buttonsStyling: false,
						confirmButtonClass: "btn btn-success mx-1",
						cancelButtonClass: "btn btn-danger mx-1",
						showCancelButton: true,
					}).then(function(){
						let rowData = [`
							<a href="https://steamcommunity.com/profiles/${fd.get("steam")}">${r.name}</a>`,
							fd.get("token"), fd.get("ftpu"), fd.get("ftpp"), fd.get("exp"),
							`<button type="button" class="btn btn-link btn-success px-2 btn-token-edit" data-id="${fd.get("id")}">
								<i class="fas fa-pen"></i>
							</button>
							<button type="button" class="btn btn-link btn-danger px-2 btn-token-del" data-id="${fd.get("id")}">
								<i class="fas fa-times"></i>
							</button>`
						]

						if(edit){
							tokenTable.row(el.parents("tr")).data(rowData);
						}
						else{
							tokenTable.row.add(rowData).draw().node();
						}
						$("#modal").modal('hide');
					})
					
				}
				else{
					swal({
						type: "error",
						title: "Error!",
						text: "Error occurred when saving your token.",
						buttonsStyling: false,
						confirmButtonClass: "btn btn-success mx-1",
					})
				}
			}
		})
		return false;
	})
	$("#modal").modal('show');
}

function serverModal(edit, el){
	let url;
	if(edit){
		$("#modal .modal-title").text("Edit Server");
		url = "./libs/api.php?action=server-edit";
	}
	else{
		$("#modal .modal-title").text("New Server");
		url = "./libs/api.php?action=server-new";
	}
	
	let ip = "", port = "", enable = "", id="";
	if(edit){
		ip = el.parents("tr").find("td").eq(1).text();
		port = el.parents("tr").find("td").eq(2).text();
		id = el.attr("data-id");
		enable = el.siblings("button.btn-server-enable").attr("data-enable");
	}

	const html = /*html*/`
	<form action="" id="form-modal">
		<input type="hidden" name="id" value="${id}">	
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				IP
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control" type="text" name="ip" value="${ip}" required>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				Port
			</label>
			<div class="col-9">
				<div class="form-group">
					<input class="form-control" type="text" name="port" value="${port}" required>
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-2 col-form-label" style="font-size: .875rem;">
				Enable
			</label>
			<div class="col-9">
				<div class="togglebutton">
					<label>
						<input type="checkbox" ${enable == 1 ? "checked" : ""} value="1" name="enable">
							<span class="toggle"></span>
					</label>
				</div>
			</div>
		</div>
	</form>`;

	$("#modal .modal-body").html(html);

	let $validator = $('.modal form').validate({
		onkeyup: function(element) {
			$(element).valid();
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
			$(element).remove();
		},
		errorPlacement : function(error, element) {
			error.insertAfter($(element));
		}
	});

	$("#modal .modal-footer").html(/*html*/`
		<button type="button" class="btn btn-success mx-1 btn-modal-save">Save changes</button>
		<button type="button" class="btn btn-danger mx-1" data-dismiss="modal">Close</button>
	`);

	$("#modal").on("click", ".btn-modal-save", function(e){
		e.preventDefault();
		var $valid = $('.modal form').valid();
		if (!$valid) {
			$validator.focusInvalid();
			return false;
		}
		$(this).attr("disabled", true);
		$(this).text("Saving...");
		let fd =  new FormData( $("#form-modal")[0] );
		$.ajax({
			method: "POST",
			url: url,
			data: fd,
			processData: false,
			contentType: false,
			cache: false,
			dataType: "json",
			success: function(r){
				if(r.success){
					swal({
						type: "success",
						title: "Success!",
						text: "Token has been saved.",
						buttonsStyling: false,
						confirmButtonClass: "btn btn-success mx-1",
						cancelButtonClass: "btn btn-danger mx-1",
						showCancelButton: true,
					}).then(function(){
						let ip =  fd.get("ip");
						let port = fd.get("port");

						if(edit)	id = fd.get("id");
						else id = r.id;

						let rowData = [r.name, ip, port, r.map, `${r.players}/${r.places}`,
						`<a href ='steam://connect/`+ip+`:`+port+`'>CONNECT</a>`,
						`<button type="button" class="btn btn-link btn-info px-2 btn-server-enable" data-id="${id}" data-enable="${fd.get("enable") == undefined ? 0 : 1}">
							<i class="far ${fd.get("enable") == undefined ? "fa-eye" : "fa-eye-slash"}"></i>
						</button>
						<button type="button" class="btn btn-link btn-success px-2 btn-server-edit" data-id="${id}">
							<i class="fas fa-pen"></i>
						</button>
						<button type="button" class="btn btn-link btn-danger px-2 btn-server-del" data-id="${id}">
							<i class="fas fa-times"></i>
						</button>`];

						if(edit)	serverTable.row(el.parents("tr")).data(rowData);
						else	serverTable.row.add(rowData).draw().node();

						$("#modal").modal('hide');
					})
					
				}
				else{
					swal({
						type: "error",
						title: "Error!",
						text: "Error occurred when saving your server.",
						buttonsStyling: false,
						confirmButtonClass: "btn btn-success mx-1",
					})
				}
			}
		})
		return false;
	})
	$("#modal").modal('show');
}

var serverTable = $("#server-table").DataTable({
	"columnDefs": [ 
		{
			"targets": 5,
			"searchable": false,
			"orderable": false
		},
		{
			"targets": 6,
			"searchable": false,
			"orderable": false
		}, 
	]
});

$("#server-table").on("click", ".btn-server-edit", function(e){
	e.preventDefault();
	serverModal(true, $(this));
	return false;
});

$("#btn-server-add").on("click", function(e){
	e.preventDefault();
	serverModal(false, "");
	return false;
});

$("#server-table").on("click", ".btn-server-del", function(){
	swal({
		type: "warning",
		title: "Are you sure?",
		text: "Do you really want to delete this server?",
		buttonsStyling: false,
		confirmButtonClass: "btn btn-success mx-1",
		cancelButtonClass: "btn btn-danger mx-1",
		showCancelButton: true,
	}).then((result) => {
		if (result.value) {
			let _this = $(this);
			let id = _this.attr("data-id");
			$.ajax({
				method: "POST",
				url: "./libs/api.php?action=server-del",
				data: {id},
				dataType: "json",
				success: function(r){
					if(r.success){
						swal({
							type: "success",
							title: "Success!",
							text: "The server has been deleted.",
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1"
						})
						serverTable.row(_this.parents('tr')).remove().draw();
					}
					else{
						swal({
							type: "error",
							title: "Error!",
							text: "Error occurred when deleting the server.",
							buttonsStyling: false,
							confirmButtonClass: "btn btn-success mx-1",
						})
					}
				}
			})
		}
	})
});

$("#server-table").on("click", ".btn-server-enable", function(){
	let enable = $(this).attr("data-enable");
	let id =  $(this).attr("data-id");
	let _this = $(this);
	$.ajax({
		method: "POST",
		url: "./libs/api.php?action=server-enable",
		data: {id, enable},
		dataType: "json",
		cache: false,
		success: function(r){
			if(r.success){
				if(enable == 1){
					_this.html(`<i class="far fa-eye-slash"></i>`);
					_this.attr("data-enable", "0");
				}
				else{
					_this.html(`<i class="far fa-eye"></i>`);
					_this.attr("data-enable", "1");
				}
			}
			else{
				swal({
					type: "error",
					title: "Error!",
					text: "Error occurred when saving the setting.",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1",
				})
			}
		}
	})
});

$("#form-profile").submit((e) => {
	e.preventDefault();
	$.ajax({
		url: "./libs/api.php?action=profile-save",
		method: "POST",
		data: new FormData($('#form-profile')[0]),
		cache: false,
		processData: false,
		contentType: false,
		dataType: "json",
		success: (r)=>{
			if(r.responce == true){
				swal({
					type: "success",
					title: "Success!",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1",
					text: "Your settings has been saved!"
				})
			}
			else{
				swal({
					type: "error",
					title: "Error!",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1",
					text: "Please contact server admin for help."
				})
			}
		},
	})
})

/* Create Team */
initMaterialWizard();

// trigger resize event for tab
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	if(e.target.id == "teamtab"){
		window.dispatchEvent(new Event('resize'));
		$(".card-wizard .btn-next").removeClass("disabled");
	}
})

$(".card-wizard").on("click", "#append-member", function(){
	$("#team-member .members").append(`
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
	`);
	addSteamValidRule();
})

$(".card-wizard").on("click", ".btn-del", function(e){
	e.preventDefault();
	$(this).parents(".form-group").remove();
	return false;
})

let url = document.location.toString();
if (url.match('#')) {
	$('.nav-pills a[href="#' + url.split('#')[1] + '"]').tab('show');
} 

$("#form-team").submit((e) => {
	e.preventDefault();
	$.ajax({
		url: "./libs/api.php?action=team-save",
		method: "POST",
		data: new FormData($('#form-team')[0]),
		cache: false,
		processData: false,
		contentType: false,
		dataType: "json",
		success: (r)=>{
			if(r.responce == true){
				swal({
					type: "success",
					title: "Success!",
					text: "Your settings has been saved!",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1"
				})
			}
			else{
				swal({
					type: "error",
					title: "Error!",
					text: "Error occurred when saving your data",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1"
				})
			}
		},
	})
})

$("#form-team-logo").submit((e) => {
	e.preventDefault();
	$.ajax({
		url: "./libs/api.php?action=team-logo",
		method: "POST",
		data: new FormData($('#form-team-logo')[0]),
		processData: false,
		contentType: false,
		cache: false,
		type: "POST",
		dataType:"json",
		success: function(r){
			if(r.error.length == 0){
				swal({
					type: "success",
					title: "Success!",
					text: "Your team is created successfully.",
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1"
				}).then(function () {
					window.setTimeout(function () {
						window.location.reload()
					}, 750);
				})
			}
			else{
				swal({
					type: "error",
					title: "Error",
					text: r.error,
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success mx-1"
				})
			}
		}
	})
})

