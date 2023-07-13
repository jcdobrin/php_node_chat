<?php
	include_once( dirname( __FILE__ ) . '/class/Database.class.php' );
	$pdo = Database::getInstance()->getPdoObject();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport">

		<title>NodeJS + PHP</title>

		<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css" />
		<style type="text/css">body { padding-top: 60px; }</style>
		<link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-responsive.css" />

		<link rel="stylesheet" href="css/index.css" />
		<script src="node_modules/jquery/jquery.min.js" ></script>
		<script src="node_modules/ckeditor5/build/ckeditor5-dll.js"></script>
	</head>

	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="index.php">NodeJS_PHP</a>

				</div>
			</div>
		</div>

		<div class="container">
			<h1>Integration test NodeJS + PHP</h1>
			<p>
				This is a simple application, showing integration between nodeJS and PHP.
			</p>

			<form class="form-inline" id="messageForm">
				<input id="nameInput" type="text" class="input-medium" placeholder="Name" />
				<textarea id="messageInput" type="text" class="input-xxlarge" placeHolder="Message"></TextArea>
				<input type="submit" value="Send" />
			</form>

			<div>
				<ul id="messages">
					<?php
						$query = $pdo->prepare( 'SELECT * FROM messages ORDER BY id DESC' );
						$query->execute();

						$messages = $query->fetchAll( PDO::FETCH_OBJ );
						foreach( $messages as $message ):
					?>
						<li> <strong><?php echo $message->name; ?></strong> : <?php echo $message->message; ?> </li>
					<?php endforeach; ?>
				</ul>
			</div>
			<!-- End #messages -->
		</div>
		<script>
			CKEDITOR.config.toolbar='Comment';
			CKEDITOR.config.removePlugins = 'elementspath';
			CKEDITOR.replace('messageInput', {width:400,height:60});
		</script>
		<script src="js/bootstrap.js"></script>
		<script src="js/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script>
		<!--<script src="js/nodeClient.js"></script>-->
		<script>
			var socket = io.connect( 'http://htdocs.local:8080', <?php echo json_encode($_SESSION); ?>);
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
		</script>

	</body>
</html>