<html>
  <head>
        <title>Example</title>
        <script type="text/javascript" src="js/MochiKit/MochiKit.js"></script>
        <script type="text/javascript" src="js/Clipperz/ByteArray.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/SHA.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/BigInt.js"></script>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="js/SrpProtocol.js"></script>
        <script type="text/javascript">

        $(document).ready(function(){

            var srp = new SrpProtocol();
            var A = srp.getA();

            $("#data").append("Data calculated: <br />" +
                "a: " + srp.a + "<br />" +
                "A: " + srp.A + "<br /><br/>" +
                "Data sent to server: <br />" +
                "A, <br/>" +
                "Username: " + "carol <br/><br/>");

            $('#submit').on('click', function(){
                $("#img-ajax").css("visibility", "visible");

                $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: {action: "Initial", A: A, username: "carol"},
                    success: function(json){
                        $("#img-ajax").css("visibility", "hidden");

                        var data = JSON.parse(json);

                        $("#data").append("Data received: <br/>" +
                            "Salt: " + data.salt + "<br/>" +
                            "B: " + data.B + "<br/>" +
                            "u: " + data.u + "<br/><br />");

                        var key = srp.calculateKey("carols-password", 
                            data.salt, data.B, data.u);

                        console.log("Client key: " + key);
                        console.log("server key: " + data.serverKey);

                        $("#data").append("client key: <br/>" + key + "<br/>");
                        $("#data").append("server key: <br/>" + key + "<br />");
                    }
                });
            });
        });


        </script>
  </head>
  <body>
        <input type="text" name="username" /><br />
        <input type="text" name="password" /><br />
        <button id="submit">Login</button><br />

        <div id="data"></div>
        <img src="ajax-loader.gif" id="img-ajax" style="visibility: hidden" />
   </body>
</html>