<?php

/**
 * Description of KioskJob
 *
 * @author arturo
 */
class KioskJob extends AppObject{
    private $createTime; //DateTime
    private $isPending;
    private $command;
    
    public function __construct(){
        
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
    
    public function __get($name){
        return $this->$name;
    }
}

?>
