<html>
  <head>
        <title>Example</title>
        <script type="text/javascript" src="js/MochiKit/MochiKit.js"></script>
        <script type="text/javascript" src="js/Clipperz/ByteArray.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/SHA.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/BigInt.js"></script>
        <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript">

        $(document).ready(function(){

            var srp = new Srp6();
            var A = srp.A;

            $.ajax({
                url: "server.php",
                type: "POST",
                data: {action: "Initial", A: d2h(A), username: "carol"},
                success: function(json){
                    var data = JSON.parse(json);

                    var key = srp.calculateKey("carols-password", 
                        data.salt, data.B, data.u);



                    console.log("Client key: " + key);
                    console.log("server key: " + data.serverKey);
                    console.log("b: " + data._b);
                }
            });
        });
        
        function d2h(d) {return d.toString(16);}
        function h2d(h) {return parseInt(h,16);} 
        function sha265hash(string)
        {
            var hash;
            var byteArray = new Clipperz.ByteArray(string);
            hash = Clipperz.Crypto.SHA.sha256(byteArray);
            var hex = hash.toHexString();
            var digest_sha256 = hex.substring(2);
            return digest_sha256;
        }

        var Srp6 = function(){
            // Setup constants
            var n_hex = "00:f2:fe:02:b5:a8:a8:62:96:68:da:92:b6:99:59:4f:ce:5d:3f:70:ba:bd:52:4f:bd:7a:56:d4:c6:57:45:dc:72:00:47:92:a2:a7:fc:e6:97:83:d3:1a:45:f0:c1:59:57:7d:3e:b5:b9:6e:3a:c0:26:4a:75:18:75:54:3b:3d:17:97:6e:5c:f7:64:75:5d:6d:0f:f9:10:29:8e:73:ec:b9:78:27:ae:87:61:44:0a:f3:2c:a0:71:02:86:ff:e0:b2:b0:2c:0a:2e:3f:e4:66:90:9c:a8:84:3b:6c:a4:65:d6:b6:a8:c8:53:00:99:8b:75:6e:01:e3:d2:70:3b:ce:33";
            n_hex = n_hex.replace(/:/g, ""); // Remove all :
            var g_hex = "02";

            this.g = new Clipperz.Crypto.BigInt(g_hex, 16);
            this.n = new Clipperz.Crypto.BigInt(n_hex, 16);

            var k_hex = sha265hash(this.n + this.g);
            this.k = new Clipperz.Crypto.BigInt(k_hex, 16);

            // TODO: Find a random a
            this.a = new Clipperz.Crypto.BigInt("797efabd9c8996a32cf7d2a8c145a321e9afda799bb1d5e3a127f5eb2e4ff737a1a768844f6f28987d56aea3022437a8fd8e234342d4a81fcd586cdf33387db689b82ea9e07539e14c854062e13ff6190897b3639d106c7051c3b65ea635fdabdcf0a31af933e5acf6e73f3680f0ebbd3e1852c37d867602a10147d125b28f72",16);
            this.A = this.g.powerModule(this.a, this.n);
        }

        Srp6.prototype.calculateKey = function(password, salt, B_hex, u_hex) {
            var k = this.k;
            var g = this.g;
            var n = this.n;
            var a = this.a;


            var B = new Clipperz.Crypto.BigInt(B_hex, 16);
            console.log("B: " + B.asString(16));

            var u = new Clipperz.Crypto.BigInt(u_hex, 16);
            console.log("u: " + u.asString(16));

            // Calculate x
            var hashHex = sha265hash(salt + password);
            var x = new Clipperz.Crypto.BigInt(hashHex, 16);

            // kg^x
            var kgx = k.multiply(g.powerModule(x, n));
            kgx = kgx.module(n);
            console.log("kgx:" + kgx.asString(16));

            // B - kg^x
            var Bkgx = B.subtract(kgx);

            // If result Bkgx is negative, then Bkgx is 
            // represented as 2s complement, meaning
            // that module doesn't work as expected
            var zero = new Clipperz.Crypto.BigInt(0, 10);
            if (Bkgx.compare(zero) > 0) {
                // If negative, the modulus must also be 
                // negative
                var negativeN = zero.subtract(n); 
                Bkgx = Bkgx.module(negativeN);
            } else {
                Bkgx = Bkgx.module(n);
            };
            console.log("Bkgx:" + Bkgx.asString(16));

            // a + ux
            var aux = a.add(u.multiply(x));
            aux = aux.module(n);
            console.log("aux:" + aux.asString(16));

            // (B - kg^x)^{a+ux}
            var S = Bkgx.powerModule(aux, n);
            console.log("S: " + S.asString(16));
            var key = sha265hash(S.asString(10));
            return key;
        }
        </script>
  </head>
  <body>
        <input type="text" name="username" /><br />
        <input type="text" name="password" /><br />
        <button name="submit">Login</button><br />
   </body>
</html>