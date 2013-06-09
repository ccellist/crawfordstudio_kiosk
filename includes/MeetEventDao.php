<?php

class MeetEventDao extends Dao {

    public function __construct() {
        parent::__construct();
    }

    public function getMeetEventById($meetEventId) {
        $this->updsql = "select * from events_by_meet_view where uid = $meetEventId";
        $res = $this->retrieve();
        if ($res != null) {
            $rec = $res[0];
            $meetEvent = new MeetEvent($rec['event_name']);
            $meetEvent->meetId = $rec['meet_id'];
            $meetEvent->eventId = $rec['event_id'];
            $meetEvent->rotation = new Rotation($rec['rotation_id'], null);
            $meetEvent->eventName = $rec['event_name'];
            $meetEvent->meetName = $rec['meet_name'];
            $meetEvent->id = $rec['uid'];
            return $meetEvent;
        } else {
            return null;
        }
    }

    public function getEventsByEventAndMeetId($eventId, $meetId) {
        $this->updsql = "select * from events_by_meet_view where event_id = $eventId and meet_id = $meetId order by meet_id, event_id";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEventList = array();
            foreach ($res as $record) {
                $meetEvent = new MeetEvent($record['event_name']);
                $meetEvent->id = $record['uid'];
                $meetEvent->rotation = new Rotation($record['rotation_id'], null);
                $meetEvent->eventId = $record['event_id'];
                $meetEvent->meetName = $record['meet_name'];
                $meetEventList[] = $meetEvent;
            }
            return $meetEventList;
        } else {
            return null;
        }
    }

    public function getEventsByMeetId($meetId) {
        $meetEvents = array();
        $this->updsql = "select * from events_by_meet_view where meet_id = $meetId order by meet_id, event_id";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $rec) {
                $meetEvent = new MeetEvent($rec['event_name']);
                $meetEvent->meetId = $rec['meet_id'];
                $meetEvent->eventId = $rec['event_id'];
                $meetEvent->rotation = new Rotation($rec['rotation_id'], null);
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

    public function getMeetEventsByMeetId($meetId) {
        $this->updsql = "select uid, event_id, event_name, meet_id from events_by_meet_view where meet_id = $meetId" .
                " group by event_id, event_name";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEventList = array();
            foreach ($res as $record) {
                $meetEvent = new MeetEvent($record['event_name']);
                $meetEvent->id = $record['uid'];
                $meetEvent->eventId = $record['event_id'];
                $meetEvent->meetId = $record['meet_id'];
                $meetEventList[] = $meetEvent;
            }
            return $meetEventList;
        } else {
            return null;
        }
    }

    public function getMeetEvent($meetId, $eventId) {
        $this->updsql = "select * from events_by_meet_view " .
                "where meet_id = $meetId and event_id = $eventId order by meet_id, event_id";
        $res = $this->retrieve();
        if ($res != null) {
            $meetEvent = new MeetEvent($res[0]['event_name']);
            $meetEvent->eventId = $res[0]['event_id'];
            $meetEvent->eventName = $res[0]['event_name'];
            $meetEvent->meetId = $res[0]['meet_id'];
            $meetEvent->meetName = $res[0]['meet_name'];
            $meetEvent->rotation = new Rotation($res[0]['rotation_id'], null);
            $meetEvent->id = $res[0]['uid'];
            return $meetEvent;
        } else {
            return null;
        }
    }

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
