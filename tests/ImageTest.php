<?php

require_once dirname(__FILE__) . '/testPrep.php';
require_once BASE_PATH . "/includes/config.php";


class ImageTest extends PHPUnit_Framework_Testcase{
    
    
    public function setUp() {
        spl_autoload_register('autoload');
        
    }
    public function testLoadPhotoByUri(){
        $testName = "test1.jpg";
        $testPath = "/home/arturo/testpics";
        $testImage = new Mock_Image($testPath . "/" . $testName);
        $this->assertEquals("/home/arturo/testpics/test1.jpg", $testImage->saveImg());
    }
}