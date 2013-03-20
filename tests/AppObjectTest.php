<?php

require_once dirname(__FILE__) . '/testPrep.php';
require_once BASE_PATH . "/includes/config.php";


class AppObjectTest extends PHPUnit_Framework_Testcase{
    
    
    public function setUp() {
        spl_autoload_register('autoload');
        
    }
    
    public function testFilterArrayByValue(){
        $array = array(1,2,3,4,5,6);
        $newArray = AppObject::filterValue($array, 2);
        $this->assertEquals(5, count($newArray));
        $this->assertEquals(1, $newArray[0]);
        $this->assertEquals(3, $newArray[2]);
        $this->assertEquals(4, $newArray[3]);
    }
}