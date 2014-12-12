<html>
  <head>
        <title>Example</title>
        <script type="text/javascript" src="js/MochiKit/MochiKit.js"></script>
        <script type="text/javascript" src="js/Clipperz/ByteArray.js"></script>
        <script type="text/javascript" src="js/Clipperz/Base.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/Base.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/SHA.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/BigInt.js"></script>
        <script type="text/javascript">

        //var g = new Clipperz.ByteArray(d2h(2));

        var n_he = "00:f2:fe:02:b5:a8:a8:62:96:68:da:92:b6:99:59:4f:ce:5d:3f:70:ba:bd:52:4f:bd:7a:56:d4:c6:57:45:dc:72:00:47:92:a2:a7:fc:e6:97:83:d3:1a:45:f0:c1:59:57:7d:3e:b5:b9:6e:3a:c0:26:4a:75:18:75:54:3b:3d:17:97:6e:5c:f7:64:75:5d:6d:0f:f9:10:29:8e:73:ec:b9:78:27:ae:87:61:44:0a:f3:2c:a0:71:02:86:ff:e0:b2:b0:2c:0a:2e:3f:e4:66:90:9c:a8:84:3b:6c:a4:65:d6:b6:a8:c8:53:00:99:8b:75:6e:01:e3:d2:70:3b:ce:33";
        n_hex = n_he.replace(/:/g, ""); // Remove all :
        var g_hex = "02";

        var g = new Clipperz.Crypto.BigInt(g_hex, 16);
        var n = new Clipperz.Crypto.BigInt(n_hex, 16);

        console.log(n.asString());

        //result = g.powerModule(bigInt_2, n);

        //alert(result.asString());

        // Hash a string
        var hash;
        var byteArray = new Clipperz.ByteArray("abab");
        hash = Clipperz.Crypto.SHA.sha256(byteArray);
        var hex = hash.toHexString();
        var digest_sha256 = hex.substring(2);

        </script>
  </head>
  <body>
        Login form to be added
   </body>
</html>