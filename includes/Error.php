<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Error
 *
 * @author arturo
 */
class Error extends SQL {
    private $errorId;
    private $errorMessage;
    
    public function __construct($pErrorId){
        $this->errorId = $pErrorId;
        $this->errorMessage = $this->getErrorMessageFromDB($pErrorId);
    }
    
    public function getMessage(){
        return $this->errorMessage;
    }
    
    private function getErrorMessageFromDB($errorId){
        $sql = "select message from errors where uid = $errorId";
        $res = $this->getResult($sql);
        if ($this->num_rows > 0){
            return $res[0]['message'];
        } elseif ($errorId == 0) {
            return "";
        } else {
            return "Unspecified error.";
        }
    }
}

