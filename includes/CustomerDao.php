<?php

/**
 * Description of CustomerDao
 *
 * @author arturo
 */
class CustomerDao extends Dao {
    public function __construct(){
        parent::__construct();
    }
    
    public function getCustomerById($id){
        $this->updsql = "select * from customers where uid = $id";
        $rs = $this->retrieve();
        if ($rs != null){
            $row = $rs[0];
            $uid = $row['uid'];
            $fname = $row['first_name'];
            $lname = $row['last_name'];
            $email = $row['email_address'];
            $phone = $row['primary_phone'];
            $customer = new Customer($fname, $lname, $email, $phone);
            $customer->customerId = $uid;
            return $customer;
        } else {
            return null;
        }
    }
    
    public function getCustomerByEmail($email){
        $this->updsql = "select * from customers where email_address = '$email'";
        $rs = $this->retrieve();
        if ($rs != null){
            $row = $rs[0];
            $uid = $row['uid'];
            $fname = $row['first_name'];
            $lname = $row['last_name'];
            $email = $row['email_address'];
            $phone = $row['primary_phone'];
            $customer = new Customer($fname, $lname, $email, $phone);
            $customer->customerId = $uid;
            return $customer;            
        } else {
            return null;
        }
    }
    
    public function getCustomerByNameAndPhone($fname,$lname,$phone){
        $this->updsql = "select * from customers where primary_phone = '$phone' and first_name = '$fname' and last_name = '$lname'";
        $rs = $this->retrieve();
        if ($rs != null){
            $row = $rs[0];
            $uid = $row['uid'];
            $fname = $row['first_name'];
            $lname = $row['last_name'];
            $email = $row['email_address'];
            $phone = $row['primary_phone'];
            $customer = new Customer($fname, $lname, $email, $phone);
            $customer->customerId = $uid;
            return $customer;            
        } else {
            return null;
        }
    }
    
    public function updateCustomerDetails($customerId, $customer){
        $fname = $customer->firstName;
        $lname = $customer->lastName;
        $email = $customer->email;
        $phone = $customer->primaryPhone;
        $this->updsql = "update customers set first_name = '$fname', last_name = '$lname', " .
        "email = '$email', primary_phone = '$phone' where uid = $customerId";
        return $this->commit();
    }
    
    public function saveNewCustomerToDb($customer){
        $fname = $customer->firstName;
        $lname = $customer->lastName;
        $email = $customer->email;
        $phone = $customer->primaryPhone;
        $this->updsql = "insert into customers (first_name, last_name, email_address, primary_phone) " .
                "values ('$fname','$lname','$email', '$phone')";
        return $this->commit();
    }
    
    public function deleteCustomerFromDb($customerId){
        $this->updsql = "delete from customers where uid = $customerId";
        $this->commit();
        return $this->affected_rows;
    }
    
    public function getAllCustomersFromDb($orderBy = Customer::LAST_NAME){
        $this->updsql = "select * from customers" . $orderBy;
        $rs = $this->retrieve();
        $output = array();
        if ($rs != null){
            foreach ($rs as $row){
                $customer = new Customer($row['first_name'], $row['last_name'], 
                        $row['email_address'], $row['primary_phone']);
                $customer->customerId = $row['uid'];
                $output[] = $customer;
            }
        }
        return $output;
    }
}

