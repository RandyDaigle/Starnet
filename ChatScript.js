$(document).ready(function(){

	$('#ChatBody').hide();
	var refreshMsgs;
	var reload = true;
	
	window.setInterval(function(){
		updateUserOnlineStatus()
	}, 60000);
	
	function get_messages(userID, friendID) {
			$.ajax({
				url: '../control/Get_msg.php',
				method: 'post',
				data: { userID: userID, friendID: friendID },
				success: function(data) {
					$('#PrivateBody').html(data);
				}
			});
	}
	
	$('#ChatBody').on('click', '.Users', function() {
		window.clearInterval(refreshMsgs);
		
		var userID = $(this).children().eq(3).text();	
		var friendName = $(this).children().eq(1).text();
		var friendTagID = "#" + friendName.replace(/\s/g,'');
		var friendID = $(friendTagID).text();
	
		$.ajax({
			url: '../views/friendChatView.php',
			method: 'post',
			data: {friendName: friendName, userID: userID, friendID: friendID },
			success: function(data) {
				$("#PrivateChat").html(data);
				$("#PrivateBody").scrollTop($('#PrivateBody')[0].scrollHeight);
				get_messages(userID, friendID);
				updateReadMessages(userID, friendID);
				refreshMsgs = setInterval(function() {
					get_messages(userID, friendID);
				}, 700);
			}
		});
	});

	$('#PrivateChat').on('click', '#PrivateTop', function() {
		$('#MsgWrap').slideToggle();
	});
	
	$('#ChatTop').click(function(){
		$('#ChatBody').slideToggle();
		refreshFriendStatus = setInterval(function() {
			updateFriendStatus();
		}, 1000);
	});
	
	$('#PrivateChat').on('click', '#Close', function() {
		reload = false;
		$('#PrivateChat').hide();
	});
	
	$('#ChatBody').on('click', '.Users', function() {
		$('#MsgWrap').show();
		$('#PrivateChat').show();
	});
	
	$('#PrivateChat').keypress('textarea', function(e){
		if(e.keyCode == 13) {
			e.preventDefault();
			var userID = $("#UserID").text();
			var chatID = $("#ChatID").text();
			var msg = $('#MsgSend').val();
			
			$('#MsgSend').val("");
			$("<div class='msg_receiver'>" + msg + "</div>").insertAfter('#Messages');
			$("#PrivateBody").scrollTop($('#PrivateBody')[0].scrollHeight);
			add_Message(chatID, userID, msg);
			
		}
	});
	
	$('#FriendRequestsWindow').on('click', '#Confirmation', function() {
		var userID = $(this).parent().children().eq(0).text();
		var friendID = $(this).parent().children().eq(1).text();
		var requestStatus = 1;
		
		$.ajax({
			url: '../control/UpdateFriendRequest.php',
			method: 'post',
			data: { userID, friendID, requestStatus },
			success: function(data) {
				$('#FriendRequestsWindow').html(data);
			}
		});
	});
	
	$('#FriendRequestsWindow').on('click', '#Rejection', function() {
		var userID = $(this).parent().children().eq(0).text();
		var friendID = $(this).parent().children().eq(1).text();
		var requestStatus = 3;
		
		$.ajax({
			url: '../control/UpdateFriendRequest.php',
			method: 'post',
			data: { userID, friendID, requestStatus },
			success: function(data) {
				$('#FriendRequestsWindow').html(data);
			}
		});
	});
	
	$('#SuggestedFriendsWindow').on('click', '#sendFriendRequest', function() {
		var userID = $(this).parent().children().eq(0).text();
		var friendID = $(this).parent().children().eq(1).text();
		
		$.ajax({
			url: '../control/SendFriendRequest.php',
			method: 'post',
			data: { userID, friendID },
			success: function(data) {
				$('#SuggestedFriendsWindow').html(data);
			}
		});
	});
	
	function add_Message(chatID, userID, msg) {
		$.ajax({
			url: '../control/Add_msg.php',
			method: 'post',
			data: { chatID, userID, msg },
			success: function(data) { }
		});
	}
	
	function updateUserOnlineStatus() {
		$.ajax({
			url: '../control/UpdateUserChatStatus.php',
			method: 'post',
			success: function(data) { }
		});
	}
	
	function updateFriendStatus() {
		$.ajax({
			url: '../control/UpdateFriendChatStatus.php',
			method: 'post',
			success: function(data) {
				$('#ChatBody').html(data);
			}
		});
	}
	
	function updateReadMessages(userID, friendID) {
		$.ajax({
			url: '../control/UpdateReadMessages.php',
			method:'post',
			data: { userID, friendID },
			success:function(data) { }
		});
	}
});