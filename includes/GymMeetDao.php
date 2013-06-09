<?php

class GymMeetDao extends Dao{
    public function __construct(){
        parent::__construct();
    }
    
    public function getMeetById($meetId){
        $this->updsql = "select * from gym_meets where uid = $meetId";
        $res = $this->retrieve();
        if ($res != null){
            $gymMeet = new GymMeet($res[0]['meet_name']);
            $gymMeet->meetId = $res[0]['uid'];
            return $gymMeet;
        } else {
            return null;
        }
    }
    
    public function getMeetByName($meetName){
        $this->updsql = "select * from gym_meets where meet_name = '$meetName'";
        $res = $this->retrieve();
        if ($res != null){
            $gymMeet = new GymMeet($res[0]['meet_name']);
            $gymMeet->meetId = $res[0]['uid'];
            return $gymMeet;
        } else {
            return null;
        }
    }
    
    public function getAllMeets(){
        $this->updsql = "select * from gym_meets order by meet_name";
        $res = $this->retrieve();
        $meets = array();
        
        if ($res != null){
            foreach ($res as $rec){
                $meet = new GymMeet($rec['meet_name']);
                $meet->meetId = $rec['uid'];
                $meets[] = $meet;
            }
        }
        return $meets;
    }
}

?>
