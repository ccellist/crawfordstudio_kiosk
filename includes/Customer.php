<?php


/**
 * Description of Customer
 *
 * @author arturo
 */
class Customer extends AppObject {
    protected $customerId;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $primaryPhone;
    
    const LAST_NAME = " order by last_name";
    const FIRST_NAME = " order by first_name";
    const EMAIL = " order by email_address";
    
    public function __construct($firstName, $lastName, $email, $primaryPhone = "") {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->primaryPhone = $primaryPhone;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

