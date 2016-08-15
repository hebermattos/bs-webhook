<?php

use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{
    
    public function testCanBeNegated()
    {
        
        $this->assertEquals(-1, -1);
    }

    
}
