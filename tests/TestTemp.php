<?php

require("../../../../wp-load.php");

class TestTemp extends PHPUnit_Framework_TestCase
{
    
    public function testMinimumViableTest()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue(false, "true didn't end up being false!");
    }

}
