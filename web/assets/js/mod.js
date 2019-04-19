$(document).ready(function() {
		var licensetable = $('#LicenseTable').DataTable();
		var servertable = $('#ServerTable').DataTable();
		$('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove',
                inline: true
            }
         });
		 
		var liform = $('#license-add');
		liform.validate({
			listeam: 'required',
			lidate1: 'required',	
			lidate2: 'required',
			liftpa: 'required',
			liftpp: 'required'
		});
		
		var liform2 = $('#editlicenseform');
		liform2.validate({
			editdate2: 'required',
			editftpa: 'required',	
			editftpp: 'required',
		});
		
		$('#LicenseTable_Add').on( 'click', function () {
			if(liform.valid()){
				var lifd = new FormData(this);
				lifd.append('steam',$('#listeam').val());
				lifd.append('date1',$('#lidate1').val());
				lifd.append('date2',$('#lidate2').val());
				lifd.append('ftpa',$('#liftpa').val());
				lifd.append('ftpp',$('#liftpp').val());
				wm.showSwal("add-license", lifd, '', '');
			}
		});
		
		$('#LicenseTable tbody').on('click', 'button.DeleteLicense', function() {
			var data = licensetable.row( $(this).parents('tr') ).data();
			wm.showSwal("delete-license", $(this).parents('tr'), data[1], '');
		});
		
		$('#LicenseTable tbody').on('click', 'button.EditLicense', function() {
			var row = $(this).parents('tr');
			var data = licensetable.row( row ).data();
			$('#editlicenseform')[0].reset();
			$('.form-group').removeClass('has-error');
			$('.help-block').empty();
			$('#modal_form').modal('show');
			
			$('[name="editrow"]').val(data[6]);
			$('[name="edittoken"]').val(data[1]);
			$('[name="editdate2"]').val(data[2]);
            $('[name="editftpa"]').val(data[3]);
			$('[name="editftpp"]').val(data[4]);
		});
		
		$('#SaveLicense').on( 'click', function () {
			if(liform2.valid()){
				var lifd2 = new FormData(this);
				var row = $('#editrow').val();
				lifd2.append('token',$('#edittoken').val());
				lifd2.append('date2',$('#editdate2').val());
				lifd2.append('ftpa',$('#editftpa').val());
				lifd2.append('ftpp',$('#editftpp').val());
				wm.showSwal("update-license", lifd2, row, '');
				$('#SaveLicense').text('Saving...');
				$('#SaveLicense').attr('disabled',true);
				$('#SaveLicenseCancel').attr('disabled',true);
			}
		});
		
		/************************************************************************************************/
		
		var svform = $('#server-add');
		svform.validate({
			svip: 'required',
			svport: 'required',	
			svtype: 'required',
		});
		
		$('#ServerTable_Add').on( 'click', function () {
			if(svform.valid()){
				var svfd = new FormData(this);
				svfd.append('ip',$('#svip').val());
				svfd.append('port',$('#svport').val());
				svfd.append('type',$('#svtype').val());
				svfd.append('enable',$('[name="enableradio"]:checked').val());
				wm.showSwal("add-server", svfd, '', '');
				$('#ServerTable_Add').text('Saving...');
				$('#ServerTable_Add').attr('disabled',true);
			}
		});
		
		$('#ServerTable tbody').on('click', 'button.DeleteServer', function() {
			var data = servertable.row( $(this).parents('tr') ).data();
			wm.showSwal("delete-server", $(this).parents('tr'), data[1], data[2]);
		});
    });