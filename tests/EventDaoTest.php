<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class EventDaoTest extends PHPUnit_Framework_Testcase{
    private $eventDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->eventDao = new Mock_EventDao();
    }
    
    public function testGetEventById(){
        $event = $this->eventDao->getEventById(1);
        $this->assertTrue($event instanceof Event);
        $this->assertEquals("Vault", $event->eventName);
    }
    
    public function testInsertEvent(){
        $sql = "insert into event_lookup(event_name) values ('Test')";
        $this->assertNull($this->eventDao->getEventByName("Test"));
        $this->assertEquals($sql, $this->eventDao->insertEvent(new Event("Test")));
    }
    
    public function testUpdateEvent(){
        $event = $this->eventDao->getEventById(2);
        $event->eventName = "bARS";
        $sql = "update event_lookup set event_name = 'bARS'";
        $this->assertEquals($sql, $this->eventDao->updateEvent($event));
    }
    
    public function testDeleteEvent(){
        $event = $this->eventDao->getEventById(2);
        $sql = "delete from event_lookup where event_name ='Bars' and uid = 2";
        $this->assertEquals($sql, $this->eventDao->deleteEvent($event));
    }
}