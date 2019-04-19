$(document).ready(function() {
		$('.ReadNotify').on( 'click', function () {
			var data = $(this).closest('tr').find('.notifydata').text();
			wm.readNotify(data);
			$(this).closest('tr').remove();						var num = +$("#notify_count").html() - 1;						if(num == "0")							{								$('#notify_count').remove();							}						else							{								$('#notify_count').html(num);								$('#NotifyTable').append("<tr><td colspan='2'>You don't have any unread notification.</td></tr>");						}
		} );
		$('.TeamAccept').on( 'click', function () {
			var data = $(this).closest('tr').find('.notifydata').text();
			var data2 = $(this).closest('tr').find('.notifydata2').text();
			wm.showSwal("accept-team", data, data2, '');						var num = +$("#notify_count").html() - 1;						if(num == "0")							{								$('#notify_count').remove();							}						else							{								$('#notify_count').html(num);								$('#NotifyTable').append("<tr><td colspan='2'>You don't have any unread notification.</td></tr>");						}
		} );
		$('.TeamIgnore').on( 'click', function () {
			var data = $(this).closest('tr').find('.notifydata').text();
			wm.ignoreinvite(data);
			$(this).closest('tr').remove();						var num = +$("#notify_count").html() - 1;						if(num == "0")							{								$('#notify_count').remove();							}						else							{								$('#notify_count').html(num);								$('#NotifyTable').append("<tr><td colspan='2'>You don't have any unread notification.</td></tr>");						}
		} );
    });