	$(document).ready(function() {
		var teamform = $( '#TeamProfile' );
		var table = $('#TeamMemberTable').DataTable();
		teamform.validate();
		$('#TeamProfile_Save').on( 'click', function () {
			if(teamform.valid())	{
				var fd = new FormData(this);
				fd.append('name',$('#tname').val());
				fd.append('facebook',$('#tfacebook').val());
				fd.append('twitter',$('#ttwitter').val());
				fd.append('youtube',$('#tyoutube').val());
				fd.append('twitch',$('#ttwitch').val());
				fd.append('steam',$('#tsteam').val());
				
				if ($('#uploadteamlogo').get(0).files.length != 0){
					fd.append('file',$('#uploadteamlogo')[0].files[0]);
				}

				wm.showSwal("team-save", fd, '', '');
			}
		});
		
		$('#TeamLogo_Save').on( 'click', function () {
			if ($('#uploadteamgamelogo').get(0).files.length != 0){
				var fd2 = new FormData(this);
				fd2.append('file',$('#uploadteamgamelogo')[0].files[0]);
				wm.showSwal("gamelogo-save", fd2, '', '');
			}
		});
		
		$('#TeamMemberTable tbody').on('click', 'button.DeleteMember', function() {
			var data = table.row( $(this).parents('tr') ).data();
			wm.showSwal("delete-member", $(this).parents('tr'), data[8], '<?=$session_team_id?>');
		});
		
		$('#TeamMemberTable tbody').on('click', 'button.DeleteInvite', function() {
			var data = table.row( $(this).parents('tr') ).data();
			wm.showSwal("delete-invite", $(this).parents('tr'), data[8], '');
		});
		
		$('#TeamMemberTable_Add').on( 'click', function () {
			wm.showSwal("add-member", "<?=$session_team_id?>", '', '');
		});
    });
	
	
	$('#uploadteamlogoinput').fileinput();
	$('#uploadteamgamelogoinput').fileinput();