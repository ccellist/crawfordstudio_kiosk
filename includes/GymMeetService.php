<?php

class GymMeetService {

    private $gymMeetDao;

    public function __construct() {
        $this->gymMeetDao = new GymMeetDao();
    }

    public function getMeetById($meetId) {
        $meet = $this->gymMeetDao->getMeetById($meetId);
        if ($meet != null) {
            $meetEventService = new MeetEventService();
            $meet->meetEvents = $meetEventService->getEventsForMeet($meet);
            return $meet;
        } else {
            return null;
        }
    }

    public function getMeetByName($meetName) {
        $meet = $this->gymMeetDao->getMeetByName($meetName);
        if ($meet != null) {
            $meetEventService = new MeetEventService();
            $meet->meetEvents = $meetEventService->getEventsForMeet($meet);
            return $meet;
        } else {
            return null;
        }
    }

    public function getMeetEventsByMeetId($meetId) {
        $meetEventService = new MeetEventService();
        return $meetEventService->getEventsForMeetById($meetId);
    }

    public function getAvailableMeetEventsByGymMeetName($meetName) {
        $meetEventService = new MeetEventService();
        $meet = $this->gymMeetDao->getMeetByName($meetName);
        if ($meet != null) {
            return $meetEventService->getEventsForMeet($meet);
        } else {
            return array();
        }
    }

    public function generateMeetDropdown() {
        $meets = $this->gymMeetDao->getAllMeets();
        $output = "<select id=\"meetSelect\" name=\"meetSelect\" onchange=\"getMeetEvents(this.value)\">\n<option>Select a meet...</option>\n";
        foreach ($meets as $meet) {
            $output .= sprintf("<option value=%s>%s\n", $meet->meetId, $meet->meetName);
        }
        $output .= "</select>";
        return $output;
    }

}

?>
