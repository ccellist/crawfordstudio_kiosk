<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionDao
 *
 * @author arturo
 */
class SessionDao extends Dao {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getSessions($sessionId = null, $sessionName = "") {
        $output = array();
        $sql = "select * from sessions";
        if ($sessionId != null){
            $sql .= " and uid = $sessionId";
        }
        if ($sessionName != ""){
            $sql .= " and session_name = '$sessionName'";
        }
        $this->updsql = preg_replace("/sessions and/","sessions where", $sql);
        $this->updsql .= " order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $key => $val) {
                $session = new MeetSession($val['session_name']);
                $session->sessionId = $val['uid'];
                $output[] = $session;
            }
            return $output;
        } else {
            return null;
        }
    }
}

?>
