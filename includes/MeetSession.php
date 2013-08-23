<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MeetSession
 *
 * @author arturo
 */
class MeetSession extends AppObject {

    private $sessionName;
    private $sessionId;

    function __construct($sessionName) {
        $this->sessionName = $sessionName;
    }

    public function getSessionName() {
        return $this->sessionName;
    }

    public function setSessionName($sessionName) {
        $this->sessionName = $sessionName;
    }

    public function getSessionId() {
        return $this->sessionId;
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

}

?>
