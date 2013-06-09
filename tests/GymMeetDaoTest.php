<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class GymMeetDaoTest extends PHPUnit_Framework_Testcase{
    private $gymMeetDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->gymMeetDao = new GymMeetDao();
    }
    
    public function testGetMeetById(){
        $meet = $this->gymMeetDao->getMeetById(1);
        $this->assertTrue($meet instanceof GymMeet);
        $this->assertEquals("Test Meet 1", $meet->meetName);
    }
    
    public function testGetMeetByIdNull(){
        $meet = $this->gymMeetDao->getMeetById(11);
        $this->assertNull($meet);
    }
    
    public function testGetMeetByName(){        
        $meet = $this->gymMeetDao->getMeetByName("Test Meet 2");
        $this->assertTrue($meet instanceof GymMeet);
        $this->assertEquals(2, $meet->meetId);
    }
    
    public function testGetMeetByNameNull(){
        $meet = $this->gymMeetDao->getMeetByName("Test Meet 4");
        $this->assertNull($meet);
    }
    
    public function testGetAllMeets(){
        $meets = $this->gymMeetDao->getAllMeets();
        $this->assertEquals(3, count($meets));
        $this->assertEquals("Test Meet 1", $meets[0]->meetName);
        $this->assertEquals("Test Meet 2", $meets[1]->meetName);
        $this->assertEquals("Test Meet 3", $meets[2]->meetName);
    }
}

?>
