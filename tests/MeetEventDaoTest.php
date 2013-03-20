<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class MeetEventDaoTest extends PHPUnit_Framework_Testcase{
    private $meetEventDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->meetEventDao = new MeetEventDao();
    }
    
    public function testGetMeetEventById(){
        $meetEvent = $this->meetEventDao->getMeetEventById(6);
        $this->assertEquals(1, $meetEvent->meetId);
        $this->assertEquals(3, $meetEvent->eventId);
        $this->assertEquals(3, $meetEvent->rotation->getRotationId());
        $this->assertEquals("Test Meet 1", $meetEvent->meetName);
        $this->assertEquals("Floor", $meetEvent->eventName);
    }
    
    public function testGetEventsByMeetId(){
        $meetEvents = $this->meetEventDao->getEventsByMeetId(3);
        $this->assertEquals(4, count($meetEvents));
        $this->assertEquals("Test Meet 3", $meetEvents[0]->meetName);
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals(1, $meetEvents[0]->eventId);
        $this->assertEquals(1, $meetEvents[0]->rotation->getRotationId());
        
        $this->assertEquals("Test Meet 3", $meetEvents[1]->meetName);
        $this->assertEquals("Bars", $meetEvents[1]->eventName);
        $this->assertEquals(2, $meetEvents[1]->eventId);
        $this->assertEquals(1, $meetEvents[1]->rotation->getRotationId());
    }
    
    public function testGetMeetEventsByMeetId(){
        $meetEvents = $this->meetEventDao->getMeetEventsByMeetId(1);
        $this->assertEquals(4, count($meetEvents));        
        
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals(1, $meetEvents[0]->eventId);
        
        $this->assertEquals("Bars", $meetEvents[1]->eventName);
        $this->assertEquals(2, $meetEvents[1]->eventId);
        
        $this->assertEquals("Floor", $meetEvents[2]->eventName);
        $this->assertEquals(3, $meetEvents[2]->eventId);
        
        $this->assertEquals("Beam", $meetEvents[3]->eventName);
        $this->assertEquals(4, $meetEvents[3]->eventId);
    }
    
    public function testGetEventsByMeetIdMultipleRotations(){
        $meetEvents = $this->meetEventDao->getEventsByMeetId(1);
        $this->assertEquals(10, count($meetEvents));
        $this->assertEquals("Test Meet 1", $meetEvents[0]->meetName);
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals(1, $meetEvents[0]->eventId);
        $this->assertEquals(2, $meetEvents[0]->rotation->getRotationId());
        
        $this->assertEquals("Test Meet 1", $meetEvents[1]->meetName);
        $this->assertEquals("Vault", $meetEvents[1]->eventName);
        $this->assertEquals(1, $meetEvents[1]->eventId);
        $this->assertEquals(1, $meetEvents[1]->rotation->getRotationId());
    }
    
    public function testGetEventsByMeetIdNone(){
        $meetEvents = $this->meetEventDao->getEventsByMeetId(33);
        $this->assertEquals(0, count($meetEvents));
    }
    
    public function testGetMeetEvent(){
        $meetEvent = $this->meetEventDao->getMeetEvent(1, 1);
        $this->assertNotNull($meetEvent);
        $this->assertTrue($meetEvent instanceof MeetEvent);
        $this->assertEquals("Test Meet 1", $meetEvent->meetName);
        $this->assertEquals("Vault", $meetEvent->eventName);
        $this->assertEquals(1, $meetEvent->id);
        $this->assertEquals(1, $meetEvent->rotation->getRotationId());
    }
    
    public function testGetMeetEventNone(){
        $meetEvent = $this->meetEventDao->getMeetEvent(10, 10);
        $this->assertNull($meetEvent);
    }
}