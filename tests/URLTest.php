<?php

include 'src/URL.class.php';

class URLTest extends \PHPUnit_Framework_TestCase
{
	public function testSluggifyReturnsSluggifiedString()
    {
        $originalString = 'This string will be sluggified';
        $expectedResult = 'this-string-will-be-sluggified';

        $URL = new URL();

        $result = $URL->sluggify($originalString);

        $this->assertEquals($result, $expectedResult);
    }
}