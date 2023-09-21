<?php 
use PHPUnit\Framework\TestCase;

class MonTest extends TestCase {
    public function testAddition() {
        $resultat = 2 + 2;
        $this->assertEquals(4, $resultat);
    }
}