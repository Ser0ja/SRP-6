<html>
  <head>
        <title>Example</title>
        <link rel="stylesheet" type="text/css" href="style.css">
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

            $('button[type=submit]').on('click', function(){
                $("#img-ajax").css("visibility", "visible");
                $("#data").css("visibility", "visible");
                $("#data").html("");



                var username = $('input[type="text"]').val();
                var password = $('input[type="password"]').val();

                $("#data").append("<b>Data calculated by client</b>: <br />" +
                    "a: " + srp.a + "<br />" +
                    "A: " + srp.A + "<br /><br/>" +
                    "<b>Transmitting to server</b>: " +
                    "A, " + 
                    "Username: " + username + " <br/><br/>");

                $.ajax({
                    url: "server.php",
                    type: "POST",
                    data: {action: "Initial", A: A, username: username},
                    success: function(json){
                        $("#img-ajax").css("visibility", "hidden");

                        var data = JSON.parse(json);

                        $("#data").append("<b>Data received from server</b>: <br/>" +
                            "Salt: " + data.salt + "<br/>" +
                            "B: " + data.B + "<br/><br />");

                        // Generate the key
                        var key = srp.calculateKey(username, password, 
                            data.salt, data.B);

                        // Prepare to send the verification of the key, to 
                        // the server
                        var verificationHash = srp.generateVerification(data.B);

                        $("#data").append("<b>Data calculated by client</b>: <br/>" +
                            "Client key: " + key + "<br/>" +
                            "verificationHash: " + verificationHash + "<br/><br/>");

                        $("#data").append("<b>Transmitting to server</b>: <br/>" + 
                            "verificationHash<br /><br />");


                        $("#img-ajax").css("visibility", "visible");
                        $.ajax({
                            url: "server.php",
                            type: "POST",
                            data: {action: "Verification", verificationHash: verificationHash},
                            success: function(json){
                                $("#img-ajax").css("visibility", "hidden");
                                var data = JSON.parse(json);
                                $("#data").append("<b>Data received from server</b>: <br />" + 
                                    "Status: " + data.status);
                            }
                        })

                    }
                });
            });


            // If enter is clicked, try to submit
            $(document).keypress(function(e) {
                if(e.which == 13) {
                    $('button[type=submit]').click();
              }
            });
        });


        </script>
  </head>
  <body>
        <div class="login">
            <h1>Login</h1>
            <input type="text" name="u" placeholder="Username" required="required" />
            <input type="password" name="p" placeholder="Password" required="required" />
            <button type="submit" class="btn btn-primary btn-block btn-large">Let me in.</button>

        </div>
            <div class="debug" id="data" style="visibility: hidden;"></div>
            <img src="ajax-loader.gif" id="img-ajax" style="visibility: hidden" />
   </body>
</html>