<?php

class OrderItem extends AppObject {
    private $orderId;
    private $photo;
    
    public function __construct($orderId, $photo) {
        $this->orderId = $orderId;
        $this->photo = $photo;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

?>
