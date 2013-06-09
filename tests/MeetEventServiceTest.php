<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";

class MeetEventServiceTest extends PHPUnit_Framework_Testcase {

    private $meetEventService;

    public function setUp() {
        spl_autoload_register('autoload');
        $this->meetEventService = new MeetEventService();
    }

    public function testGetMeetEvent() {
        $meetEvent = $this->meetEventService->getMeetEvent(1, 2);
        $this->assertEquals("Test Meet 1", $meetEvent->meetName);
        $this->assertEquals("Bars", $meetEvent->eventName);
        $this->assertEquals(2, $meetEvent->eventId);
        $this->assertEquals(10, $meetEvent->id);
        $this->assertEquals(2, count($meetEvent->eventPhotos));
        $this->assertNotNull($meetEvent->rotation);
        $this->assertTrue($meetEvent->rotation instanceof Rotation);
        $this->assertEquals("Morning A", $meetEvent->rotation->getRotationName());
    }

    public function testGetMeetEventNone() {
        $meetEvent = $this->meetEventService->getMeetEvent(10, 2);
        $this->assertNull($meetEvent);
    }

    public function testGetEventsForMeetById() {
        $meetEvents = $this->meetEventService->getEventsForMeetById(1);
        $this->assertEquals(4, count($meetEvents));
        $this->assertEquals(1, $meetEvents[0]->eventId);
        $this->assertEquals("Vault", $meetEvents[0]->eventName);

        $this->assertEquals(2, $meetEvents[1]->eventId);
        $this->assertEquals("Bars", $meetEvents[1]->eventName);

        $this->assertEquals(3, $meetEvents[2]->eventId);
        $this->assertEquals("Floor", $meetEvents[2]->eventName);

        $this->assertEquals(4, $meetEvents[3]->eventId);
        $this->assertEquals("Beam", $meetEvents[3]->eventName);
    }

    public function testGetMeetEventById() {
        $meetEvent = $this->meetEventService->getMeetEventById(10);
        //more needed for test.
    }

    public function testGetEventsAllRotationsForMeetById() {
        $meetEvents = $this->meetEventService->getEventsAllRotationsForMeetById(1, 2);
        $this->assertEquals(1, count($meetEvents));
        $this->assertEquals(2, count($meetEvents[0]->eventPhotos));
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals("Test Meet 2", $meetEvents[0]->meetName);
        $this->assertNotNull($meetEvents[0]->rotation);
        $this->assertTrue($meetEvents[0]->rotation instanceof Rotation);
        $this->assertEquals("Morning A", $meetEvents[0]->rotation->getRotationName());
        $this->assertEquals(1, $meetEvents[0]->rotation->getRotationId());
    }

    public function testGetEventsForMeetByIdNone() {
        $meetEvents = $this->meetEventService->getEventsForMeetById(20);
        $this->assertEquals(0, count($meetEvents));
    }

    public function testGetEventsForMeet() {
        $gymMeetService = new GymMeetService();
        $meet = $gymMeetService->getMeetByName("Test Meet 2");
        $meetEvents = $this->meetEventService->getEventsForMeet($meet);
        $this->assertEquals(4, count($meetEvents));
        $this->assertEquals(2, count($meetEvents[0]->eventPhotos));
        $this->assertEquals("Vault", $meetEvents[0]->eventName);
        $this->assertEquals("Test Meet 2", $meetEvents[0]->meetName);
    }

}