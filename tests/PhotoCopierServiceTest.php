<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class PhotoCopierServiceTest extends PHPUnit_Framework_Testcase{
    private $photoCopierService;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->photoCopierService = new Mock_PhotoCopierService();
    }
    
    public function testDoCopy(){
        $output = "cp -va c:\\testpics\\test1.jpg %homepath%\\Desktop\\Orders\\test@email.com\\20120901\\1\\test1.jpg";
        $testPhotoCopy = new PhotoCopy("test1.jpg", "c:\\testpics", "%homepath%\\Desktop\\Orders\\test@email.com\\20120901\\1");
        $this->assertEquals($output, $this->photoCopierService->doPhotoCopy($testPhotoCopy));
    }
}