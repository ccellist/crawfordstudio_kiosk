<?php

/**
 * Description of CustomerDao
 *
 * @author arturo
 */
class Mock_CustomerDao extends CustomerDao {
    public function __construct() {
        parent::__construct();
    }

    public function updateCustomerDetails($customerId, $customer){
        $fname = $customer->firstName;
        $lname = $customer->lastName;
        $email = $customer->email;
        $phone = $customer->primaryPhone;
        $sql = "update customers set first_name = '$fname', last_name = '$lname', " .
        "email = '$email', primary_phone = '$phone' where uid = $customerId";
        return $sql;
    }
    
    public function saveNewCustomerToDb($customer){
        $fname = $customer->firstName;
        $lname = $customer->lastName;
        $email = $customer->email;
        $phone = $customer->primaryPhone;
        $sql = "insert into customers (first_name, last_name, email_address, primary_phone) " .
                "values ('$fname','$lname','$email', '$phone')";
        return $sql;
    }
    
    public function deleteCustomerFromDb($customerId){
        $sql = "delete from customers where uid = $customerId";
        return $sql;
    }
}

