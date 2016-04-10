<?php

class GeneralTest extends \Codeception\TestCase\Test {

    public function test_cut_string() {
        $this->assertEquals('aaa', cut_string('aaaaaa', 3));
    }

    public function test_cut_string_sameLength() {
        $this->assertEquals('aaa', cut_string('aaa', 3));
    }

    public function test_cut_string_empty() {
        $this->assertEquals('', cut_string('', 3));
    }

    public function test_short_description() {
        $this->assertEquals('aa ...', short_description('aaaa', 2));
    }

    public function test_short_description_noCut() {
        $this->assertEquals('aa', short_description('aa', 2));
    }

    public function test_permalink() {
        $string = ' This Is A permalink!!  &&?? ## alright!';
        $expected = 'this-is-a-permalink-alright';

        $this->assertEquals(
            to_permalink($string),
            $expected
        );
    }
}