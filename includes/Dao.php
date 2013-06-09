<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DTO
 *
 * @author arturo
 */
class Dao extends SQL {
    private $updsql;
    
    public function __construct(){
        parent::__construct();
    }
    
    public function __set($key,$val){
        $this->$key = $val;
    }
    
    public function __get($key){
        return $this->$key;
    }
    
    public function commit() {
        $testsql = $this->updsql;
        $this->Run($testsql);
        if ($this->affected_rows != -1) {
            return true;
        } else {
            throw new DataCommitException(UPDATE_FAILED, $this->updsql);
        }
    }
    
    public function retrieve(){
        $testsql = $this->updsql;
        if (($res = $this->getResult($testsql)) == false){
            return null;
        } else {
            return $res;
        }
    }
    
    public function getLastInsertId($table){
        $this->updsql = "select LAST_INSERT_ID() as last_insert_id from $table where 1=1 limit 1";
        $res =$this->retrieve();
        if ($res != null){
            return $res[0]['last_insert_id'];
        } else {
            return 0;
        }
    }
        
    public function getAvailableId($table){
        $this->updsql = "select MAX(uid) as uid from $table";
        $res = $this->retrieve();
        if ($res != null){
            return $res[0]['uid'] + 1;
        } else {
            return 0;
        }
    }
}

