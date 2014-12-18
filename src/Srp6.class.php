<?php

require_once('BigInteger.php');

class Srp6
{
	private $hashAlgo = "sha256";

	# A large safe prime (N = 2q+1, where q is prime)
	# All aithmetic is done modulo N
	# (generated using "openssl dhparam -text 1024")
	// Must be a non-smooth-prime (p.12)
	private $n;
	private $g;
	private $k;
	private $b;
	private $B;
	private $u;
	private $v;
	private $S;
	private $key;

	function __construct(
		$g = "02", 
		$n =
	     "00:f2:fe:02:b5:a8:a8:62:96:68:da:92:b6:99:59:
	      4f:ce:5d:3f:70:ba:bd:52:4f:bd:7a:56:d4:c6:57:
	      45:dc:72:00:47:92:a2:a7:fc:e6:97:83:d3:1a:45:
	      f0:c1:59:57:7d:3e:b5:b9:6e:3a:c0:26:4a:75:18:
	      75:54:3b:3d:17:97:6e:5c:f7:64:75:5d:6d:0f:f9:
	      10:29:8e:73:ec:b9:78:27:ae:87:61:44:0a:f3:2c:
	      a0:71:02:86:ff:e0:b2:b0:2c:0a:2e:3f:e4:66:90:
	      9c:a8:84:3b:6c:a4:65:d6:b6:a8:c8:53:00:99:8b:
	      75:6e:01:e3:d2:70:3b:ce:33" )
	{
		$this->n = $this->hexToBigInt($n);
		$this->g = $this->hexToBigInt($g);
		$this->k = $this->hexToBigInt(
			hash($this->hashAlgo, $this->n->toString() . $this->g->toString()));

	}

	/*
	 * Converts a hexstring to a big-integer
	 */
	private function hexToBigInt($string)
	{
		// Remove : and all whitespace
		$string = str_replace(":","", $string);
		$string = preg_replace('/\s+/', '', $string);
		return new Math_BigInteger('0x' . $string, 16);	
	}

	public function generateV($x)
	{
		$x = $this->hexToBigInt($x);
		$v = $this->g->modPow($x, $this->n);
		$v = $this->modN($v);	
		return $v;

	}

	private function modN($number)
	{
		return $number->modPow(new Math_BigInteger(1), $this->n);
	}


	/*
	 * Take an A as hex, and calculates B and u
	 */
	public function calculateB($A)
	{
		$A = new Math_BigInteger('0x' . $A, 16);

		/*
		 * Generate b
		 */
		// b must be bigger than log_g(n). 
		$min = log(
			floatval($this->n->toString()), 
			floatval($this->g->toString()));
		$min = new Math_BigInteger($min);

		// Generates a value in the interval log_g(n) to n
		$this->b = $min->random($this->n);

		/*
		 * Calculate B
		 */
		// kv
		$kv 	 = $this->modN($this->k->multiply($this->v));
		// g^b
		$this->B = $this->g->modPow($this->b, $this->n);
		// kv + g^b
		$this->B = $kv->add($this->B);
		$this->B = $this->modN($this->B);

		$this->A = $A;
	}

	/*
	 * Take the A, and computes S for the session
	 */
	public function computeS($A)
	{
		$A = $this->A;

		/*
		 * Generate u, the srp-6 way
		 */
		$uHash = hash($this->hashAlgo, 
			$this->A->toHex() . $this->B->toHex());
		$this->u = $this->hexToBigInt($uHash);

		// A*v^u
		$Avu = $A->multiply($this->v->modPow($this->u, $this->n));
		$Avu = $this->modN($Avu, $this->n);

		// (A*v^u)^b
		$this->S = $Avu->modPow($this->b, $this->n);
		$this->key = hash($this->hashAlgo, $this->S->toString());
	}


	public function generateServerHash()
	{
		if ($this->S == null) {
			$this->computeS($this->A);

		}

		return hash($this->hashAlgo,
			$this->A->toHex() .
			$this->B->toHex() .
			$this->S->toHex());

	}

	public function getKey()
	{
		return $this->key;
	}

	/*
	 * Return the hex of the B parameter
	 */
	public function getB()
	{
		return $this->B->toHex();
	}

	public function getb_()
	{
		return $this->b->toHex();
	}

	public function getS()
	{
		return $this->S;
	}

	/*
	 * Set the v. 
	 */
	public function setV($v)
	{
		$this->v = $this->hexToBigInt($v);
	}

}
?>