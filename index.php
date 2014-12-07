<?php

require_once('src/BigInteger.php');
require_once('src/Srp6.class.php');

echo "The index<br />";



$hashAlgo = "sha256";

# A large safe prime (N = 2q+1, where q is prime)
# All arithmetic is done modulo N
# (generated using "openssl dhparam -text 1024")
// Must be a non-smooth-prime (p.12)
$n = "00:f2:fe:02:b5:a8:a8:62:96:68:da:92:b6:99:59:
            4f:ce:5d:3f:70:ba:bd:52:4f:bd:7a:56:d4:c6:57:
            45:dc:72:00:47:92:a2:a7:fc:e6:97:83:d3:1a:45:
            f0:c1:59:57:7d:3e:b5:b9:6e:3a:c0:26:4a:75:18:
            75:54:3b:3d:17:97:6e:5c:f7:64:75:5d:6d:0f:f9:
            10:29:8e:73:ec:b9:78:27:ae:87:61:44:0a:f3:2c:
            a0:71:02:86:ff:e0:b2:b0:2c:0a:2e:3f:e4:66:90:
            9c:a8:84:3b:6c:a4:65:d6:b6:a8:c8:53:00:99:8b:
            75:6e:01:e3:d2:70:3b:ce:33";
$n = str_replace(":","", $n);
$n = preg_replace('/\s+/', '', $n);
$n = new Math_BigInteger('0x' . $n, 16);
// Generator modulus the 
$g = new Math_Biginteger(2); 


/*
 * THE ALL NEW THING
 */

$srp = new Srp6();

// Get the v for Carol

$x = hash("sha256", "carols-salt" . "carols-password");
$v = $srp->generateV($x);
$srp->setV($v->toHex());


/*
 * Client sends username and ephemeral value A to the server
 */

// a must be bigger than log_g(n). 
$min = log(floatval($n->toString()), floatval($g->toString()));
$min = new Math_BigInteger($min);

// Generates a value from log_g(n) to n
$a = $min->random($n);
$a = bigMod($a, $n);
$A = $g->modPow($a, $n);

// Send username and A to the server
$username = "carol";


/*
 * Server sends the user's salt together with the value B, and u to the client
 */

$srp->calculateBandU($A);

// to send
$salt = "carols-salt";
$B = $srp->getB();
$u = $srp->getU();

/*
 * Client computes the session key
 */

$B = hexToBigInt($B);
$u = hexToBigInt($u);

// Generate the secret key x
// Calculate the session key


$x = hexToBigInt($x);

// B - g^x
$Bgx = $B->subtract($g->modPow($x, $n));
$Bgx = bigMod($Bgx, $n);

// a + ux
$aux = $a->add($u->multiply($x));
$aux = bigMod($aux, $n);

// (B - g^x)^{a+ux}
$SClient = $Bgx->modPow($aux, $n);

echo "sessionkey of client <br />";
echo hash($hashAlgo, $SClient->toString());
echo "<br />";

/*
 * Server computes session key
 */

$srp->computeS($A);

echo "sessionkey of server <br />";
echo $srp->getKey();
echo "<br />";


function bigMod($number, $mod)
{
	return $number->modPow(new Math_BigInteger(1), $mod);
}
function hexToBigInt($string)
{
	// Remove : and all whitespace
	$string = str_replace(":","", $string);
	$string = preg_replace('/\s+/', '', $string);
	return new Math_BigInteger('0x' . $string, 16);	
}

?>