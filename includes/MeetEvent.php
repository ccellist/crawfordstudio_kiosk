<?php

class MeetEvent extends AppObject {
    private $id;
    private $meetId;
    private $sessionId;
    private $eventId;
    private $rotation;
    private $sessionName;
    private $eventName;
    private $meetName;
    private $eventPhotos;
    
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

