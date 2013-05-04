<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="http://cdn.socket.io/stable/socket.io.js"></script>
    <script>
        $(function(){
            var socket= new io.Socket('localhost',{ 
              port: 8080 
            }); 
            socket.connect(); 
            $('#outgoingChatMessage').keypress(function(event) {
                if(event.which == 13) {
                    event.preventDefault();
                    iosocket.send($('#outgoingChatMessage').val());
                    $('#incomingChatMessages').append($('<li></li>').text($('#outgoingChatMessage').val()));
                    $('#outgoingChatMessage').val('');
                }
            });
        });
    </script>
</head>
<body>
Incoming Chat:&nbsp;<ul id="incomingChatMessages"></ul>
<br />
<input type="text" id="outgoingChatMessage">
</body>
</html>