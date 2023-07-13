var socket = io.connect( 'http://htdocs.local:8080' );
$( "#messageForm" ).submit( function() {
	var nameVal = $( "#nameInput" ).val();
	var msg = CKEDITOR.instances.messageInput.getData();
	CKEDITOR.instances.messageInput.setData('');

	socket.emit( 'message to master', { name: nameVal, message: msg }  );

	// Ajax call for saving datas
	$.ajax({
		url: "./ajax/insertNewMessage.php",
		type: "POST",
		data: { name: nameVal, message: msg },
		success: function(data) {
		}
	});

	return false;
});
socket.emit( 'I\'m the master');

socket.on( 'message', function( data ) {
	if(data.name == 'Jason') {
		var actualContent = $( "#messages" ).html();
		var newMsgContent = '<li> <strong>' + data.name + '</strong> : ' + data.message + '</li>';
		var content = newMsgContent + actualContent;

		$( "#messages" ).html( content );
	}
});