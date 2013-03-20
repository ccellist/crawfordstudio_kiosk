<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rotation
 *
 * @author arturo
 */
class Rotation {
    private $rotationId;
    private $rotationName;
    
    public function __construct($rotationId, $rotationName){
        $this->rotationId = $rotationId;
        $this->rotationName = $rotationName;
    }
    
    public function getRotationName(){
        return $this->rotationName;
    }
    
    public function setRotationName($name){
        $this->rotationName = $name;
    }
    
    public function getRotationId() {
        return $this->rotationId;
    }

    public function setRotationId($rotationId) {
        $this->rotationId = $rotationId;
    }
}

?>
