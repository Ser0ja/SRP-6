<html>
  <head>
        <title>Example</title>
        <script type="text/javascript" src="js/MochiKit/MochiKit.js"></script>
        <script type="text/javascript" src="js/Clipperz/Base.js"></script>
        <script type="text/javascript" src="js/Clipperz/ByteArray.js"></script>
        <script type="text/javascript" src="js/Clipperz/Crypto/SHA.js"></script>
        <script type="text/javascript">

        //var result;
        //var stringResult;
        //stringResult = Clipperz.Crypto.Base.computeHashValue("ab".asString());
        //result = new Clipperz.ByteArray("0x" + stringResult);
        //alert(result);


        //var cl = new Clipperz.Crypto.SHA

        var hash;
        var byteArray = new Clipperz.ByteArray("message");

        hash = Clipperz.Crypto.SHA.sha256(byteArray);
        var hex = hash.toHexString();
        var digest_sha256 = hex.substring(2);
        alert(digest_sha256);

        /*
        var testSHA = function (aValue, anExpectedResult) {
          var byteArrayValue;
          
          byteArrayValue = new Clipperz.ByteArray(aValue);
          hash = Clipperz.Crypto.SHA.sha256(byteArrayValue);
          is(hash.toHexString(), anExpectedResult, "sha256(' " + byteArrayValue.toHexString() + "')");
        }
        */
        </script>
  </head>
  <body>
        My test
   </body>
</html>