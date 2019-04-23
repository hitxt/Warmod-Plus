	$(document).ready(function() {
        wm.initMaterialWizard();
		$('.createmember0').rules("add", { 
			required: true,
			number: true,
			remote: function(element) {
				return {
					url : "./assets/inc/validate-steam.php",
					type:'post',
					data: {
						steam: $(element).val()
					}               
				}
			}
		});
		
		var counter = 1;
		$('#create-add-member').on( 'click', function (e) {
			e.preventDefault();
			var inputclass = 'createmember'+counter;
			var newinput = '<div class="row">' +
				'<div class="col-lg-9 col-lg-offset-1">' +
					 '<div class="input-group">' +
						'<span class="input-group-addon">' +
							'<i class="material-icons">email</i>' +
						'</span>' +
						'<div class="form-group label-floating is-empty">' +
							'<label class="control-label">Enter Steam64 ID' +
							'</label>' +
							'<input name="createmember[]" type="text" class="form-control '+inputclass+'" id="createmember">' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="col-lg-1 label-floating">' +
					'<div class="input-group">' +
						'<button type="button" class="create-remove-member btn btn-danger btn-simple" onclick="javascript:void(0);">' +
							'<i class="material-icons">close</i>' +
						'</button>' +
					'</div>' +
				'</div>' +
			'</div>';
			$(newinput).insertAfter('.create-member-info');
			
			$.validator.addClassRules(inputclass, {
				number: true,
				remote: function(element) {
					return {
						url : "./assets/inc/validate-steam.php",
						type:'post',
						data: {
							steam: $(element).val()
						}               
					}
				}
			});
			counter+=1;
		});
		
		$('form').on('click', '.create-remove-member', function(e){
			e.preventDefault();
			$(this).closest('.row').remove()
		});
		
		var createform = $('#create-team-wizard');
		$('#create-finish').on( 'click', function () {
			if(createform.valid())	{
				var fd3 = new FormData(this);
				fd3.append('name',$('#createname').val());
				fd3.append('facebook',$('#createfb').val());
				fd3.append('twitter',$('#createtwitter').val());
				fd3.append('youtube',$('#createyoutube').val());
				fd3.append('twitch',$('#createtwitch').val());
				fd3.append('steam',$('#createsteam').val());
				if ($('#wizard-picture').get(0).files.length != 0){
					fd3.append('file',$('#wizard-picture')[0].files[0]);
				}
				
				if ($('#wizard-picture2').get(0).files.length != 0){
					fd3.append('file2',$('#wizard-picture2')[0].files[0]);
				}
				
				$('input[name="createmember[]"]').each(function (index, member) {
					if($(member).val())
					{
						var value = $(member).val()
						fd3.append('member[]', value);
					}
				});
				
				wm.showSwal("team-create", fd3, '', '');
			}
		});
    });