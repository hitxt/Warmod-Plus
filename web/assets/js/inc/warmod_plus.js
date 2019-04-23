$(document).ready(function () {
	wm = {
		showSwal: function (type, data1, data2, data3) {
			if (type == 'leave-team') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to leave the team?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/leaveteam.php",
						data: {	data1: "member", data2: data1 },
						success: swal({
							title: 'Success!',
							text: 'You have left the team.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							window.setTimeout(function () {
								window.location.reload()
							}, 1000);
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'You are still in the team. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'leave-team-leader') {
				swal({
					title: 'Are you sure?',
					text: 'Random player in your team will be the new leader.<br>If you are the last member, team will be disbanded.',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/leaveteam.php",
						data: {	data1: "leader", data2: data1 },
						success: swal({
							title: 'Success!',
							text: 'You have left the team.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							window.setTimeout(function () {
								window.location.reload()
							}, 1000);
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'You are still in the team. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'disband-team') {
				swal({
					title: 'Are you sure?',
					text: 'All players will be removed from the team.<br> Stats will not be deleted.',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/deleteteam.php",
						data: { data1: data1 },
						success: swal({
							title: 'Success!',
							text: 'You have disbanded the team.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							window.setTimeout(function () {
								window.location.reload()
							}, 1000);
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'Your team is still there. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'player-save') {
				$.ajax({
					type: "post",
					url: "./assets/inc/saveplayer.php",
					data: data1,
					success: swal({
						title: 'Success!',
						text: 'Your settings have been saved!',
						type: 'success',
						confirmButtonClass: "btn btn-success",
						buttonsStyling: false
					}).then(),
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
				return false;
			} else if (type == 'team-save') {
				$.ajax({
					type: "post",
					url: "./assets/inc/saveteam.php",
					data: data1,
					dataType: "json",
					contentType: false,
					processData: false,
					cache: false,
					success: function (json) {
						if (json["responce"] == "success") {
							swal({
								title: 'Successed',
								text: "Your settings has been saved!",
								type: 'success',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "upload-logo-error") {
							swal({
								title: 'Error',
								text: json["responce2"],
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "name-duplicate") {
							swal({
								title: 'Error',
								text: "Team name duplicated",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "large") {
							swal({
								title: 'Error',
								text: "File must smaller than 1MB.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "empty") {
							swal({
								title: 'Error',
								text: "Team name can not be empty.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
				return false;
			} else if (type == 'gamelogo-save') {
				$.ajax({
					type: "post",
					url: "./assets/inc/savegamelogo.php",
					data: data1,
					dataType: "json",
					contentType: false,
					processData: false,
					cache: false,
					success: function (json) {
						if (json["responce"] == "success") {
							swal({
								title: 'Successed',
								text: "Your settings has been saved!",
								type: 'success',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "upload-logo-error") {
							swal({
								title: 'Error',
								text: json["responce2"],
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "file-large") {
							swal({
								title: 'Error',
								text: "File must smaller than 1 MB.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "size-large") {
							swal({
								title: 'Error',
								text: "Image size must equal 64x64.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "error") {
							swal({
								title: 'Error',
								text: "Please contact developer for help.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						} else if (json["responce"] == "svg-only") {
							swal({
								title: 'Error',
								text: "SVG file only.",
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
				return false;
			} else if (type == 'delete-member') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to remove your teammate?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/deletemember.php",
						data: {
							data2: data2,
							data3: data3
						},
						success: swal({
							title: 'Success!',
							text: 'Your teammate will miss you :(',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							var table = $('#TeamMemberTable').DataTable();
							table.row(data1).remove().draw();
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'Your teammate is still there. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'delete-invite') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to cancel your invitation?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/deleteinvite.php",
						data: {
							data2: data2
						},
						success: swal({
							title: 'Success!',
							text: 'You have cancelled your invitation',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							var table = $('#TeamMemberTable').DataTable();
							table.row(data1).remove().draw();
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'Your invitation is still there. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'add-member') {
				swal({
					title: 'Input Steam64 ID',
					html: '<div class="form-group">' + '<input id="input-field" type="text" class="form-control" />' + '</div>',
					showCancelButton: true,
					confirmButtonClass: 'btn btn-success',
					cancelButtonClass: 'btn btn-danger',
					buttonsStyling: false
				}).then(function (result) {
					var val = $('#input-field').val();
					if (val) {
						$.ajax({
							type: "post",
							url: "./assets/inc/addmember.php",
							data: {
								val: val,
								data1: data1
							},
							dataType: "json",
							success: function (json) {
								if (json["responce"] == "invalid") {
									swal({
										title: 'Cancelled',
										text: 'Invalid Steam64 ID',
										type: 'error',
										confirmButtonClass: "btn btn-info",
										buttonsStyling: false
									})
								} else if (json["responce"] == "already-in") {
									swal({
										title: 'Cancelled',
										text: 'This player is already in your team.',
										type: 'error',
										confirmButtonClass: "btn btn-info",
										buttonsStyling: false
									})
								} else if (json["responce"] == "already-have") {
									swal({
										title: 'Cancelled',
										text: 'This player is already in a team.',
										type: 'error',
										confirmButtonClass: "btn btn-info",
										buttonsStyling: false
									})
								} else if (json["responce"] == "already-invite") {
									swal({
										title: 'Cancelled',
										text: 'You have already invited this player.',
										type: 'error',
										confirmButtonClass: "btn btn-info",
										buttonsStyling: false
									})
								} else if (json["responce"] == "self") {
									swal({
										title: 'Cancelled',
										text: 'You can not invite yourself.',
										type: 'error',
										confirmButtonClass: "btn btn-info",
										buttonsStyling: false
									})
								} else {
									swal({
										title: 'Success!',
										text: 'You have invited ' + json["name"] + '!',
										type: 'success',
										confirmButtonClass: "btn btn-success",
										buttonsStyling: false
									}).then(function () {
										var table = $('#TeamMemberTable').DataTable();
										var rowNode = table.row.add(["Invinting", json["rank"], json["name"], json["rws"], json["k"], json["d"], json["kdr"], json["ac"], '<a href="./showplayer.php?id=' + json['profile'] + '">View</a>', '<button type="button" class="DeleteInvite btn btn-danger btn-simple" onclick="javascript:void(0);"><i class="material-icons">close</i></button>']).draw().node();
										$(rowNode).find('td').eq(9).addClass('td-actions text-left');
									});
								}
							},
							error: function (xhr, ajaxOptions, thrownError) {
								alert(xhr.status);
								alert(thrownError);
							}
						});
					} else {
						swal({
							title: 'Cancelled',
							text: 'Steam64 ID can not be empty',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				}).catch(swal.noop)
			} else if (type == 'disbanded-team') {
				swal({
					title: 'This team is disbanded by leader!',
					text: 'But you still can still check team stats.',
					type: 'warning',
					buttonsStyling: false,
					confirmButtonClass: "btn btn-success"
				});
			} else if (type == 'accept-team') {
				swal({
					title: 'Are you sure?',
					text: 'You will leave current team if you are in a team.',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/acceptinvite.php",
						data: {
							data1: data1,
							data2: data2
						},
						success: swal({
							title: 'Success!',
							text: 'You have joined the team!',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							window.setTimeout(function () {
								window.location.reload()
							}, 1000);
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: "",
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			} else if (type == 'team-create') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to create a team?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/createteam.php",
						data: data1,
						dataType: "json",
						contentType: false,
						processData: false,
						cache: false,
						success: function (json) {
							if (json["responce"] == "success") {
								swal({
									title: 'Success!',
									text: 'Your team has been created!',
									type: 'success',
									confirmButtonClass: "btn btn-success",
									buttonsStyling: false
								}).then(function () {
									window.setTimeout(function () {
										window.location.reload()
									}, 1000);
								})
							} else if (json["responce"] == "upload-logo-error") {
								swal({
									title: 'Error',
									text: json["responce2"],
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "file-large") {
								swal({
									title: 'Error',
									text: "File must smaller than 1 MB.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "size-large") {
								swal({
									title: 'Error',
									text: "Image size must equal 64x64.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "error") {
								swal({
									title: 'Error',
									text: "Please contact developer for help.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "svg-only") {
								swal({
									title: 'Error',
									text: "SVG file only.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "name-duplicate") {
								swal({
									title: 'Error',
									text: "Team name duplicated",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "large") {
								swal({
									title: 'Error',
									text: "File must smaller than 1MB.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							} else if (json["responce"] == "empty") {
								swal({
									title: 'Error',
									text: "Team name can not be empty.",
									type: 'error',
									confirmButtonClass: "btn btn-info",
									buttonsStyling: false
								})
							}
						},
						error: function (xhr, ajaxOptions, thrownError) {alert(xhr.status);alert(thrownError);}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: "",
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			}
			else if (type == 'buy') 
			{				
				swal({
					title: 'Notice',	
					text: "Please contact developer in official Warmod+ discord server!",
					type: 'warning',
					confirmButtonClass: "btn btn-info",	
					buttonsStyling: false
				})
			} 
			else if (type == 'source') 
			{				
				swal({
					title: 'Notice',	
					text: "Please contact developer in official Warmod+ discord server!",
					type: 'warning',
					confirmButtonClass: "btn btn-info",	
					buttonsStyling: false
				})
			} 
			else if (type == 'add-license') 
			{
				$.ajax({
					type: "post",
					url: "./assets/inc/addlicense.php",
					dataType: "json",
					data: data1,
					contentType: false,
					processData: false,
					cache: false,
					success :function(json){
						if (json["responce"] == "invalid") {
							swal({
								title: 'Cancelled',
								text: 'Invalid Steam64 ID',
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
						else if (json["responce"] == "success") {
							swal({
								title: 'Success!',
								text: 'Successfully gave token '+ json["responce3"] + '\n to '+json["responce2"]+ ' !',
								type: 'success',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							}).then(function () {
								var table = $('#LicenseTable').DataTable();
								var rowNode = table.row.add([json["responce2"], json["responce3"], json["responce4"], json["responce5"], json["responce6"], '<button type="button" class="DeleteLicense btn btn-danger btn-simple" onclick="javascript:void(0);"><i class="material-icons">close</i></button><button type="button" class="EditLicense btn btn-success btn-simple" onclick="javascript:void(0);"><i class="material-icons">edit</i></button>', json["responce7"]]).draw().node();
								$(rowNode).find('td').eq(5).addClass('td-actions text-left');
								$(rowNode).find('td').eq(6).hide();
							})
						}
						else if (json["responce"] == "already") {
							swal({
								title: 'Cancelled',
								text: json["responce2"] + ' is already have token!',
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			}
			else if (type == 'delete-license') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to remove token?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/deletelicense.php",
						data: {
							data2: data2
						},
						success: swal({
							title: 'Success!',
							text: 'Your have removed token.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							var table = $('#LicenseTable').DataTable();
							table.row(data1).remove().draw();
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'Your token is still there. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			}
			else if (type == 'update-license') 
			{				
				$.ajax({
					type: "post",
					url: "./assets/inc/updatelicense.php",
					dataType: "text",
					data: data1,
					contentType: false,
					processData: false,
					cache: false,
					success :function(json){
						swal({
							title: 'Success!',
							text: 'Successfully update license!',
							type: 'success',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						}).then(function () {
							var table = $('#LicenseTable').dataTable();
							table.fnUpdate($('#editdate2').val(), data2, 2);
							table.fnUpdate($('#editftpa').val(), data2, 3);
							table.fnUpdate($('#editftpp').val(), data2, 4);
							table.fnUpdate('<button type="button" class="DeleteLicense btn btn-danger btn-simple" onclick="javascript:void(0);"><i class="material-icons">close</i></button>', data2, 5);
							$('#modal_form').modal('hide');
							$('#SaveLicense').text('Save');
							$('#SaveLicense').attr('disabled',false);
							$('#SaveLicenseCancel').attr('disabled',false);
						})
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			}
			else if (type == 'add-server') 
			{
				$.ajax({
					type: "post",
					url: "./assets/inc/addserver.php",
					dataType: "json",
					data: data1,
					contentType: false,
					processData: false,
					cache: false,
					success :function(json){
						if (json["responce"] == "success") {
							swal({
								title: 'Success!',
								text: 'Successfully add server to database!',
								type: 'success',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							}).then(function () {
								var table = $('#ServerTable').DataTable();
								var rowNode = table.row.add([json["responce2"], json["responce3"], json["responce4"], json["responce5"], json["responce6"], json["responce7"], json["responce8"], json["responce9"], '<button type="button" class="DeleteServer btn btn-danger btn-simple" onclick="javascript:void(0);"><i class="material-icons">close</i></button>']).draw().node();
								$(rowNode).find('td').eq(8).addClass('td-actions text-left');
							})
						}
						else if (json["responce"] == "already") {
							swal({
								title: 'Cancelled',
								text: 'This server is already in server list!',
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			}
			else if (type == 'delete-server') {
				swal({
					title: 'Are you sure?',
					text: 'Do you really want to remove server?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No',
					confirmButtonClass: "btn btn-success",
					cancelButtonClass: "btn btn-danger",
					buttonsStyling: false
				}).then(function () {
					$.ajax({
						type: "post",
						url: "./assets/inc/deleteserver.php",
						data: {
							data2: data2, data3:data3
						},
						success: swal({
							title: 'Success!',
							text: 'Your have removed server.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							var table = $('#ServerTable').DataTable();
							table.row(data1).remove().draw();
						}),
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					return false;
				}, function (dismiss) {
					if (dismiss === 'cancel') {
						swal({
							title: 'Cancelled',
							text: 'Your server is still there. :)',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
				})
			}
		},
		ignoreinvite: function (data1) {
			$.ajax({
				type: "post",
				url: "./assets/inc/ignoreinvite.php",
				data: {
					data1: data1
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
			return false;
		},
		readNotify: function (value1) {
			$.ajax({
				type: "post",
				url: "./assets/inc/readnotify.php",
				data: {
					value1: value1
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
			return false;
		},
		showNotification: function (from, align, style, text) {
			$.notify({
				icon: "notifications",
				message: text,
			}, {
				type: style,
				timer: 3000,
				placement: {
					from: from,
					align: align
				}
			});
		},
		initMaterialWizard: function () {
			var $validator = $('.wizard-card form').validate({
					rules: {
						createname: {
							required: true,
							remote: {
								url: "./assets/inc/validate-team.php",
								type: "post",
							}
						},
					},
					errorPlacement: function (error, element) {
						$(element).parent('div').addClass('has-error');
					},
					success: function (error, element) {
						$(element).parent('div').removeClass('has-error');
					}
				});
			$('.wizard-card').bootstrapWizard({
				'tabClass': 'nav nav-pills',
				'nextSelector': '.btn-next',
				'previousSelector': '.btn-previous',
				onNext: function (tab, navigation, index) {
					var $valid = $('.wizard-card form').valid();
					if (!$valid) {
						$validator.focusInvalid();
						return false;
					}
				},
				onInit: function (tab, navigation, index) {
					var $total = navigation.find('li').length;
					var $wizard = navigation.closest('.wizard-card');
					$first_li = navigation.find('li:first-child a').html();
					$moving_div = $('<div class="moving-tab">' + $first_li + '</div>');
					$('.wizard-card .wizard-navigation').append($moving_div);
					refreshAnimation($wizard, index);
					$('.moving-tab').css('transition', 'transform 0s');
				},
				onTabClick: function (tab, navigation, index) {
					var $valid = $('.wizard-card form').valid();
					if (!$valid) {
						return false;
					} else {
						return true;
					}
				},
				onTabShow: function (tab, navigation, index) {
					var $total = navigation.find('li').length;
					var $current = index + 1;
					var $wizard = navigation.closest('.wizard-card');
					if ($current >= $total) {
						$($wizard).find('.btn-next').hide();
						$($wizard).find('.btn-finish').show();
					} else {
						$($wizard).find('.btn-next').show();
						$($wizard).find('.btn-finish').hide();
					}
					button_text = navigation.find('li:nth-child(' + $current + ') a').html();
					setTimeout(function () {
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
			$("#wizard-picture").change(function () {
				readURL(this);
			});
			$("#wizard-picture2").change(function () {
				readURL2(this);
			});
			$('[data-toggle="wizard-radio"]').click(function () {
				wizard = $(this).closest('.wizard-card');
				wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
				$(this).addClass('active');
				$(wizard).find('[type="radio"]').removeAttr('checked');
				$(this).find('[type="radio"]').attr('checked', 'true');
			});
			$('[data-toggle="wizard-checkbox"]').click(function () {
				if ($(this).hasClass('active')) {
					$(this).removeClass('active');
					$(this).find('[type="checkbox"]').removeAttr('checked');
				} else {
					$(this).addClass('active');
					$(this).find('[type="checkbox"]').attr('checked', 'true');
				}
			});
			$('.set-full-height').css('height', 'auto');
			function readURL(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						$('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
					}
					reader.readAsDataURL(input.files[0]);
				}
			}
			function readURL2(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						$('#wizardPicturePreview2').attr('src', e.target.result).fadeIn('slow');
					}
					reader.readAsDataURL(input.files[0]);
				}
			}
			$(window).resize(function () {
				$('.wizard-card').each(function () {
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
		},
	}
})
