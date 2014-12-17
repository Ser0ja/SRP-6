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

            $("#data").append("Data calculated: <br />" +
                "a: " + srp.a + "<br />" +
                "A: " + srp.A + "<br /><br/>" +
                "Data to be sent to server: <br />" +
                "A, <br/>" +
                "Username: " + "carol <br/><br/>");

            //DEBUG
            $('#submit').trigger("click");

            $('button[type=submit]').on('click', function(){
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

                        // Generate the key
                        var key = srp.calculateKey("carols-password", 
                            data.salt, data.B, data.u);

                        // Prepare to send the verification of the key, to 
                        // the server
                        var verificationHash = srp.generateVerification(data.B);

                        $("data").append("Computing: <br/>" +
                            "CLient key: " + key + "<br/>" +
                            "verificationHash: " + verificationHash + "<br/><br/>");

                        $("data").append("Transmitting to server: <br/>" + 
                            "verificationHash");

                        $.ajax({
                            url: "server.php",
                            type: "POST",
                            data: {action: "Verification", verificationHash: verificationHash},
                            success: function(json){
                                var data = JSON.parse(json);

                                $("data").append("Response from server: <br />");
                            }
                        })

                    }
                });
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

            <div class="debug" id="data"></div>
            <img src="ajax-loader.gif" id="img-ajax" style="visibility: hidden" />
        </div>
   </body>
</html>