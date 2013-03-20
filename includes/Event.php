<?php

class Event extends AppObject {
    private $eventId;
    private $eventName;
    
    public function __construct($name){
        $this->eventName = $name;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

?>
