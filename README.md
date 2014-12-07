SRP-6
=====

A small proof of concept of the implementation of the SRP-6 protocol. 

It's a protocol that can authenticate a user with a server without sending the plaintext password to the server. 
For more information take a look at the authors papers from [srp.stanford.edu](http://srp.stanford.edu/doc.html#papers).

I'm using phpseclib's [BigInteger](https://github.com/phpseclib/phpseclib/blob/master/phpseclib/Math/BigInteger.php) for the server arithmetic implementation, 
and Clipperz.io's [Javascript crypto library](https://github.com/clipperz/javascript-crypto-library) for the clients crypto.

Warning
--------

This is just a proof of concept implementation. Do not use in a production environment. 
