var SrpProtocol = function(){
    // Setup constants
    var n_hex = "00:f2:fe:02:b5:a8:a8:62:96:68:da:92:b6:99:59:4f:ce:5d:3f:70:ba:bd:52:4f:bd:7a:56:d4:c6:57:45:dc:72:00:47:92:a2:a7:fc:e6:97:83:d3:1a:45:f0:c1:59:57:7d:3e:b5:b9:6e:3a:c0:26:4a:75:18:75:54:3b:3d:17:97:6e:5c:f7:64:75:5d:6d:0f:f9:10:29:8e:73:ec:b9:78:27:ae:87:61:44:0a:f3:2c:a0:71:02:86:ff:e0:b2:b0:2c:0a:2e:3f:e4:66:90:9c:a8:84:3b:6c:a4:65:d6:b6:a8:c8:53:00:99:8b:75:6e:01:e3:d2:70:3b:ce:33";
    n_hex = n_hex.replace(/:/g, ""); // Remove all :
    var g_hex = "02";

    this.g = new Clipperz.Crypto.BigInt(g_hex, 16);
    this.n = new Clipperz.Crypto.BigInt(n_hex, 16);

    var k_hex = this.hash(this.n + this.g);
    this.k = new Clipperz.Crypto.BigInt(k_hex, 16);

    // TODO: Find a random a

    // Must generate a value in the interval log_g(n) to n
    this.a = Clipperz.Crypto.BigInt.randomPrime(256);
    this.A = this.g.powerModule(this.a, this.n);

}

SrpProtocol.prototype.getA = function(){
    return this.A.toString(16);
}

SrpProtocol.prototype.generateUserInfo = function(username, password){
    // TODO: Make salt random
    var salt = "carols-salt";
    var x = this.generateX(salt, username, password);

    // Generate v
    var v = this.g.powerModule(x, this.n);
    v = v.module(this.n);

    var ret = {
        salt: salt,
        v: v
    }
    return ret;
}

SrpProtocol.prototype.generateX = function(salt, username, password){
    // Calculate x
    var hashHex = this.hash(salt + username +  password);
    //console.log("hash: " + hashHex);
    var x = new Clipperz.Crypto.BigInt(hashHex, 16);

    return x;
}

SrpProtocol.prototype.calculateKey = function(username, password, salt, B_hex) {
    var k = this.k;
    var g = this.g;
    var n = this.n;
    var a = this.a;


    var B = new Clipperz.Crypto.BigInt(B_hex, 16);
    //console.log("B: " + B.asString(16));

    var u_hex = this.hash(this.A.asString(16) + B.asString(16));
    this.u = new Clipperz.Crypto.BigInt(u_hex, 16);
    //console.log("u: " + u.asString(16));

    // Calculate x
    this.x = this.generateX(salt, username, password);

    // kg^x
    var kgx = k.multiply(g.powerModule(this.x, n));
    kgx = kgx.module(n);
    //console.log("kgx:" + kgx.asString(16));

    // B - kg^x
    var Bkgx = B.subtract(kgx);

    // If result Bkgx is negative, then Bkgx is 
    // represented as 2s complement, meaning
    // that module doesn't work as expected
    var zero = new Clipperz.Crypto.BigInt(0, 10);
    if (Bkgx.compare(zero) > 0) {
        // if negative, the modulus must also be 
        // negative
        var negativen = zero.subtract(n); 
        Bkgx = Bkgx.module(negativen);
    } else {
        Bkgx = Bkgx.module(n);
    };
    //console.log("Bkgx:" + Bkgx.asString(16));

    // a + ux
    var aux = a.add(this.u.multiply(this.x));
    aux = aux.module(n);
    //console.log("aux:" + aux.asString(16));

    // (B - kg^x)^{a+ux}
    this.S = Bkgx.powerModule(aux, n);
    //console.log("S: " + this.S.asString(16));
    this.key = this.hash(this.S.asString(10));
    return this.key;
}

SrpProtocol.prototype.generateVerification = function(B_hex)
{
    return this.hash(this.A.asString(16) + B_hex + this.S.asString(16));
    //console.log("A: " + this.A.asString(16));
    //console.log("B: " + B_hex);
    //console.log("S: " + this.S.asString(16));
}

SrpProtocol.prototype.hash = function(string){
    var hash;
    var byteArray = new Clipperz.ByteArray(string);
    hash = Clipperz.Crypto.SHA.sha256(byteArray);
    var hex = hash.toHexString();
    var digest_sha256 = hex.substring(2);
    return digest_sha256;
}