<?php

include 'src/Srp6.class.php';
require_once('src/BigInteger.php');

class Srp6Test extends \PHPUnit_Framework_TestCase
{
	public function testGenerateVlargeN()
    {
    	// hex for 5
    	$x = '05';
        // 2^5
        $expectedResult = new Math_BigInteger(32);

        $srp6 = new Srp6();
        $result = $srp6->generateV($x);
        $this->assertEquals($result, $expectedResult);
    }

	public function testGenerateVsmallN()
    {
    	// hex for 100
    	$x = '64';
        // 2^100 mod 240
        $expectedResult = new Math_BigInteger(16);

        // g = 2, n = 240
        $srp6 = new Srp6("02", "F0");
        $result = $srp6->generateV($x);
        $this->assertEquals($result, $expectedResult);
    }


}

?>