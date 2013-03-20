<?php
class EventDao extends Dao{
    public function __construct(){
        parent::__construct();
    }
    
    public function getEventById($eventId){
        $this->updsql = "select * from event_lookup where uid = $eventId";
        $res = $this->retrieve();
        
        if ($res != null){
            $event = new Event($res[0]['event_name']);
            $event->eventId = $eventId;
            return $event;
        } else {
            return null;
        }
    }
    
    public function getEventByName($name){
        $this->updsql = "select * from event_lookup where event_name = '$name'";
        $res = $this->retrieve();
        if ($res != null){
            $event = new Event($res[0]['event_name']);
            $event->eventId = $res[0]['uid'];
            return $event;
        } else {
            return null;
        }
    }
    
    public function insertEvent(Event $event){
        $this->updsql = sprintf("insert into event_lookup(event_name) values ('%s')", $event->eventName);
        $this->commit();
    }
    
    public function updateEvent(Event $event){
        $this->updsql = sprintf("update event_lookup set event_name = '%s'", $event->eventName);
        $this->commit();
    }
    
    public function deleteEvent(Event $event){
        $this->updsql = sprintf("delete from event_lookup where event_name ='%s' and uid = %s", 
                $event->eventName, $event->eventId);
        $this->commit();
    }
}

?>

