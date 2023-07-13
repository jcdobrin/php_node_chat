var sio = require( 'socket.io' );
var express = require( 'express' );
var http = require( 'http' );
var redis = require("redis");

var client = redis.createClient();
var app = express();
var server = http.createServer( app );
var io = sio.listen(server);

io.set("store", new sio.RedisStore);

io.of('/').authorization(function (handshakeData, callback) {
	var cookies = parse_cookies(handshakeData.headers.cookie);

	client.get(cookies.PHPSESSID, function (err, reply) {
			handshakeData.identity = reply;
			callback(false, reply !== null);
	});
}).on( 'connection', function( socket ) {

	socket.on("I'm the master", function() {
		// Save the socket id to Redis so that all processes can access it.
		client.set("mastersocket", socket.id, function(err) {
			if (err) throw err;
			console.log("Master socket is now" + socket.id);
		});
	});

	socket.on("message to master", function(data) {
		// Fetch the socket id from Redis
		client.get("mastersocket", function(err, socketId) {
			if (err) throw err;
			//io.sockets.socket(socketId).emit( 'message', { name: data.name, message: data.message } );
			io.sockets.emit( 'message', { name: data.name, message: data.message } );
		});
	});

//	client.on( 'message', function( data ) {
//		console.log( 'Message received ' + data.name + ":" + data.message );

		//client.broadcast.emit( 'message', { name: data.name, message: data.message } );
//		io.sockets.emit( 'message', { name: data.name, message: data.message } );
//	});

});

server.listen( 8080 );