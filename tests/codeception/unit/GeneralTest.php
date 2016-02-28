<?php

class GeneralTest extends \Codeception\TestCase\Test {

    public function testCutString() {
        $this->assertEquals('aaa', cut_string('aaaaaa', 3));
    }

    public function testCutString_sameLength() {
        $this->assertEquals('aaa', cut_string('aaa', 3));
    }

    public function testCutString_empty() {
        $this->assertEquals('', cut_string('', 3));
    }
}