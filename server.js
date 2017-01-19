var app = require('http').createServer(handler)
  , io = require('socket.io').listen(app)
  , fs = require('fs')

app.listen(5602);

function handler (req, res) {
  fs.readFile(__dirname + '/index.html',
  function (err, data) {
    if (err) {
      res.writeHead(500);
      return res.end('Error loading index.html');
    }
    res.writeHead(200);
    res.end(data);
  });

}

io.sockets.on('connection', function (socket) {
  socket.on('reiniciar', function (data) {
    socket.broadcast.emit('reiniciar', data);
  });

 socket.on('update', function (data) {
    socket.broadcast.emit('update', data);
  });

});
