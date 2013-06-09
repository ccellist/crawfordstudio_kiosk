<?php

class MeetEventService {

    private $meetEventDao;

    public function __construct() {
        $this->meetEventDao = new MeetEventDao();
    }

    public function getMeetEvent($meetId, $eventId) {
        $meetEvent = $this->meetEventDao->getMeetEvent($meetId, $eventId);
        if ($meetEvent != null) {
            $this->loadRotations($meetEvent);
            $this->loadEventPhotos($meetEvent);
            return $meetEvent;
        } else {
            return null;
        }
    }

    public function getEventsForMeetById($meetId) {
        $meetEvents = $this->meetEventDao->getMeetEventsByMeetId($meetId);
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

    public function getEventsAllRotationsForMeetById($eventId, $meetId) {
        $meetEvents = $this->meetEventDao->getEventsByEventAndMeetId($eventId, $meetId);
        if ($meetEvents != null) {
            foreach ($meetEvents as $meetEvent) {
                $this->loadRotations($meetEvent);
                $this->loadEventPhotos($meetEvent);
            }
            return $meetEvents;
        } else {
            return array();
        }
    }

    public function getEventsForMeet(GymMeet $meet) {
        $meetEvents = $this->meetEventDao->getEventsByMeetId($meet->meetId);
        foreach ($meetEvents as $meetEvent) {
            $this->loadEventPhotos($meetEvent);
        }
        return $meetEvents;
    }

    public function generateMeetEventDropdown($meetId) {
        $events = $this->getEventsForMeetById($meetId);
        $output = "";
        $count = 1;
        foreach ($events as $event) {
            //$output .= sprintf("<input type=\"radio\" id=\"eventId\" name=\"eventId\" value=\"%s\" onchange=\"getThumbnails(this.value)\">%s\n", $event->id, $event->eventName);
            $output .= sprintf("<input type=\"radio\" id=\"eventId\" name=\"eventGrp\" value=\"%s\" onchange=\"getRotations(this.value, %s)\">%s\n", $event->eventId, $event->meetId, $event->eventName);
            $output .= "<br>\n";
            $count++;
        }
        return $output;
    }

    public function generateRotationDropdown($eventId, $meetId) {
        $events = $this->getEventsAllRotationsForMeetById($eventId, $meetId);
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
