<?php
/**
 * Description of GymMeet
 *
 * @author arturo
 */
class GymMeet extends AppObject{
    private $meetId;
    private $meetName;
    private $meetEvents;
    
    public function __construct($meetName){
        $this->meetName = $meetName;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

