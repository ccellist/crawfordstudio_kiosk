<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataUpdateException
 *
 * @author arturo
 */
class DataCommitException extends Exception{
    private $exceptionNumber;
    private $failingSql;
    
    public function __construct($exceptionNumber, $sql){
        $this->exceptionNumber = $exceptionNumber;
        $this->failingSql = $sql;
    }
}

