<?php

class MeetEventDao extends Dao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get a single meet event by uid.
     * 
     * @param int $meetEventId
     * @return \MeetEvent|null
     */
    public function getMeetEventById($meetEventId) {
        $this->updsql = "select * from events_by_meet_view where uid = $meetEventId";
        $res = $this->retrieve();
        if ($res != null) {
            $rec = $res[0];
            $meetEvent = new MeetEvent($rec['event_name']);
            $meetEvent->meetId = $rec['meet_id'];
            $meetEvent->eventId = $rec['event_id'];
            $meetEvent->rotation = new Rotation($rec['rotation_id'], null);
            $meetEvent->meetSession = new MeetSession($rec['session_name']);
            $meetEvent->meetSession->sessionId = $rec['session_id'];
            $meetEvent->eventName = $rec['event_name'];
            $meetEvent->meetName = $rec['meet_name'];
            $meetEvent->id = $rec['uid'];
            return $meetEvent;
        } else {
            return null;
        }
    }

    /**
     * Retrieves a list of rotations by session, event, and meet id. Used to get a list
     * of all available rotations with the given parameters.
     * 
     * @param int $sessionId
     * @param int $eventId
     * @param int $meetId
     * @return \MeetEvent|null
     */
    public function getRotationsBySessionEventAndMeetId($sessionId, $eventId, $meetId) {
        $this->updsql = "select * from events_by_meet_view where event_id = $eventId and meet_id = $meetId and session_id = $sessionId order by meet_id, event_id";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEventList = array();
            foreach ($res as $record) {
                $meetEvent = new MeetEvent($record['event_name']);
                $meetEvent->id = $record['uid'];
                $meetEvent->rotation = new Rotation($record['rotation_id'], null);
                $meetEvent->eventId = $record['event_id'];
                $meetEvent->sessionId = $record['session_id'];
                $meetEvent->sessionName = $record['session_name'];
                $meetEvent->meetName = $record['meet_name'];
                $meetEventList[] = $meetEvent;
            }
            return $meetEventList;
        } else {
            return null;
        }
    }

    /**
     * Retrieves a list of all events, sessions, and rotations for the meet with the given id.
     * 
     * @param int $meetId
     * @return \MeetEvent array|null
     */
    public function getEventsByMeetId($meetId) {
        $meetEvents = array();
        $this->updsql = "select * from events_by_meet_view where meet_id = $meetId order by meet_id, event_id, rotation_id";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $rec) {
                $meetEvent = new MeetEvent($rec['event_name']);
                $meetEvent->meetId = $rec['meet_id'];
                $meetEvent->eventId = $rec['event_id'];
                $meetEvent->meetSession = new MeetSession($rec['session_name']);
                $meetEvent->meetSession->sessionId = $rec['session_id'];
                $meetEvent->rotation = new Rotation($rec['rotation_id'], null);
                $meetEvent->sessionName = $rec['session_name'];
                $meetEvent->eventName = $rec['event_name'];
                $meetEvent->meetName = $rec['meet_name'];
                $meetEvent->id = $rec['uid'];
                $meetEvents[] = $meetEvent;
            }
            return $meetEvents;
        } else {
            return null;
        }
    }

    /**
     * Retrieves a distinct list of available sessions and events for a given meet.
     * Rotations are not included.
     * 
     * @param type $meetId
     * @return \MeetEvent|null
     */
    public function getMeetSessionsByMeetId($meetId) {
        $this->updsql = "select uid, session_id, session_name, meet_id from events_by_meet_view where meet_id = $meetId" .
                " group by session_id, session_name";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEventList = array();
            foreach ($res as $record) {
                $meetEvent = new MeetEvent(null);
                $meetEvent->meetSession = new MeetSession($record['session_name']);
                $meetEvent->meetSession->sessionId = $record['session_id'];
                $meetEvent->meetId = $record['meet_id'];
                $meetEventList[] = $meetEvent;
            }
            return $meetEventList;
        } else {
            return null;
        }
    }

    /**
     * Retrieves a grouped list of available events for a given session for a given
     * meet and session id.
     * 
     * @param int $meetId
     * @param int $sessionId
     * @return \MeetEvent|null
     */
    public function getMeetEventsByMeetAndSessionId($meetId, $sessionId) {
        $this->updsql = "select uid, session_id, session_name, event_id, event_name, meet_id from events_by_meet_view where meet_id = $meetId" .
                " and session_id = $sessionId group by session_id, session_name, event_id, event_name";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEventList = array();
            foreach ($res as $record) {
                $meetEvent = new MeetEvent($record['event_name']);
                $meetEvent->id = $record['uid'];
                $meetEvent->meetSession = new MeetSession($record['session_name']);
                $meetEvent->meetSession->sessionId = $record['session_id'];
                $meetEvent->eventId = $record['event_id'];
                $meetEvent->meetId = $record['meet_id'];
                $meetEventList[] = $meetEvent;
            }
            return $meetEventList;
        } else {
            return null;
        }
    }

//
//    public function getMeetEvent($meetId, $eventId) {
//        $this->updsql = "select * from events_by_meet_view " .
//                "where meet_id = $meetId and event_id = $eventId order by meet_id, event_id";
//        $res = $this->retrieve();
//        if ($res != null) {
//            $meetEvent = new MeetEvent($res[0]['event_name']);
//            $meetEvent->eventId = $res[0]['event_id'];
//            $meetEvent->sessionId = $res[0]['session_id'];
//            $meetEvent->sessionName = $res[0]['session_name'];
//            $meetEvent->eventName = $res[0]['event_name'];
//            $meetEvent->meetId = $res[0]['meet_id'];
//            $meetEvent->meetName = $res[0]['meet_name'];
//            $meetEvent->rotation = new Rotation($res[0]['rotation_id'], null);
//            $meetEvent->id = $res[0]['uid'];
//            return $meetEvent;
//        } else {
//            return null;
//        }
//    }

    public function getMeetIdFromMeetEventId($meetEventId) {
        $this->updsql = "select meet_id from events_by_meet_view where uid = $meetEventId";
        $res = $this->retrieve();
        if ($res != null) {
            return $res[0]['meet_id'];
        } else {
            return null;
        }
    }

}

?>
