<?php

class ajax_Customer_deleteCustomer extends presenter implements Iface_Presenter {

    public function __construct($data, $modName, $error) {
        parent::__construct(__CLASS__, $data, $modName, $error);
    }
    
    public function display(){
        parent::display();
    }

}