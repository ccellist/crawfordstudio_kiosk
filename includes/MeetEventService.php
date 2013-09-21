<?php

class MeetEventService {

    private $meetEventDao;

    public function __construct() {
        $this->meetEventDao = new MeetEventDao();
    }

    public function getRotationsBySessionEventAndMeetId($sessionId, $eventId, $meetId) {
        $meetEvents = $this->meetEventDao->getRotationsBySessionEventAndMeetId($sessionId, $eventId, $meetId);
        if ($meetEvents != null) {
            foreach ($meetEvents as $meetEvent) {
                $this->loadRotations($meetEvent);
                //$this->loadEventPhotos($meetEvent);
            }
            return $meetEvents;
        } else {
            return null;
        }
    }

    public function getEventsForMeetByIdAndSessionId($meetId, $sessionId) {
        $meetEvents = $this->meetEventDao->getMeetEventsByMeetAndSessionId($meetId, $sessionId);
        if ($meetEvents != null) {
            return $meetEvents;
        } else {
            return null;
        }
    }

    public function getSessionsForMeetById($meetId) {
        $meetEvents = $this->meetEventDao->getMeetSessionsByMeetId($meetId);
        if ($meetEvents != null) {
            return $meetEvents;
        } else {
            return null;
        }
    }

    public function getMeetEventById($meetEventId) {
        $meetEvent = $this->meetEventDao->getMeetEventById($meetEventId);
        if ($meetEvent != null) {
            $this->loadEventPhotos($meetEvent);
        }
        return $meetEvent;
    }

    public function getEventsForMeet(GymMeet $meet) {
        $meetEvents = $this->meetEventDao->getEventsByMeetId($meet->meetId);
        foreach ($meetEvents as $meetEvent) {
            $this->loadEventPhotos($meetEvent);
        }
        return $meetEvents;
    }

    public function generateMeetSessionDropdown($meetId) {
        $events = $this->getSessionsForMeetById($meetId);
        $output = "";
        $count = 1;
        foreach ($events as $event) {
            $output .= sprintf("<input type=\"radio\" id=\"sessionId\" name=\"sessionGrp\" value=\"%s\" onchange=\"getMeetEvents(this.value, %s)\">%s\n", $event->meetSession->getSessionId(), $event->meetId, $event->meetSession->getSessionName());
            $output .= "<br>\n";
            $count++;
        }
        return $output;
    }

    public function generateMeetEventDropdown($meetId, $sessionId) {
        $events = $this->getEventsForMeetByIdAndSessionId($meetId, $sessionId);
        $output = "";
        $count = 1;
        foreach ($events as $event) {
            $output .= sprintf("<input type=\"radio\" id=\"eventId\" name=\"eventGrp\" value=\"%s\" onchange=\"getRotations(this.value, %s, %s)\">%s\n", $event->eventId, $event->meetId, $event->meetSession->sessionId, $event->eventName);
            $output .= "<br>\n";
            $count++;
        }
        return $output;
    }

    public function generateRotationDropdown($eventId, $sessionId, $meetId) {
        $events = $this->getRotationsBySessionEventAndMeetId($sessionId, $eventId, $meetId);
        $output = "";
        $count = 1;
        foreach ($events as $event) {
            $rotation = $event->rotation;
            $output .= sprintf("<input type=\"radio\" id=\"eventId\" name=\"rotationGrp\" value=\"%s\" onchange=\"getThumbnails(this.value)\">%s\n", $event->id, $rotation->getRotationName());
            $output .= "<br>\n";
            $count++;
        }
        return $output;
    }

    private function loadEventPhotos(MeetEvent $meetEvent) {
        $photoDao = new PhotoDao();
        $meetPhotos = $photoDao->getPhotosByMeetEventId($meetEvent->id);
        $meetEvent->eventPhotos = $meetPhotos;
    }

    private function loadRotations(MeetEvent $meetEvent) {
        $rotationDao = new RotationDao();
        $rotation = $rotationDao->getRotationById($meetEvent->rotation->getRotationId());
        $meetEvent->rotation = $rotation;
    }

}

?>
