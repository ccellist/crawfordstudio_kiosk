<?php


class Mock_EventDao extends EventDao{
    public function __construct(){
        parent::__construct();
    }
    
    public function insertEvent(Event $event){
        $this->updsql = sprintf("insert into event_lookup(event_name) values ('%s')", $event->eventName);
        return $this->updsql;
    }
    
    public function updateEvent(Event $event){
        $this->updsql = sprintf("update event_lookup set event_name = '%s'", $event->eventName);
        return $this->updsql;
    }
    
    public function deleteEvent(Event $event){
        $this->updsql = sprintf("delete from event_lookup where event_name ='%s' and uid = %s", 
                $event->eventName, $event->eventId);
        return $this->updsql;
    }
}

?>
