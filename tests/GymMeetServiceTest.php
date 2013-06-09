<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class GymMeetServiceTest extends PHPUnit_Framework_Testcase{
    private $gymMeetService;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->gymMeetService = new GymMeetService();
    }
    
    public function testGetMeetById(){
        $meet = $this->gymMeetService->getMeetById(1);
        $this->assertTrue($meet instanceof GymMeet);
        $this->assertEquals("Test Meet 1", $meet->meetName);
        $this->assertEquals(1, $meet->meetId);
        $this->assertEquals(4, count($meet->meetEvents));
    }
    
    public function testGetMeetByIdNull(){
        $meet = $this->gymMeetService->getMeetById(11);
        $this->assertNull($meet);
    }
    
    public function testGetMeetByName(){
        $meet = $this->gymMeetService->getMeetByName("Test Meet 2");
        $this->assertEquals(2, $meet->meetId);
        $this->assertEquals(4, count($meet->meetEvents));
    }
    
    public function testGetMeetByNameNull(){
        $meet = $this->gymMeetService->getMeetByName("Test Meet 22");
        $this->assertNull($meet);
    }
    
    public function testGetMeetEventsByGymMeetId(){
        $meetEvents = $this->gymMeetService->getMeetEventsByMeetId(1);
        $this->assertEquals(4, count($meetEvents));
        $this->assertEquals(1, $meetEvents[0]->eventId);
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals(2, $meetEvents[1]->eventId);
        $this->assertEquals("Bars", $meetEvents[1]->eventName);
    }
    
    public function testGetMeetEventsByGymMeetIdNone(){
        $meetEvents = $this->gymMeetService->getMeetEventsByMeetId(199);
        $this->assertEquals(0, count($meetEvents));
    }
    
    public function testGetAvailableMeetEventsByGymMeetName(){
        $meetEvents = $this->gymMeetService->getAvailableMeetEventsByGymMeetName("Test Meet 2");
        $this->assertEquals(4, count($meetEvents));
        $this->assertEquals(1, $meetEvents[0]->eventId);
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals(2, $meetEvents[1]->eventId);
        $this->assertEquals("Bars", $meetEvents[1]->eventName);        
    }
    
    public function testGetAvailableMeetEventsByGymMeetNameNone(){ 
        $meetEvents = $this->gymMeetService->getAvailableMeetEventsByGymMeetName("Test Meet 22");
        $this->assertEquals(0, count($meetEvents));
    }
}

?>
