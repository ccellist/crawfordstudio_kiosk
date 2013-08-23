<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";

class CustomerDaoTest extends PHPUnit_Framework_Testcase {

    private $customerDao;
    private $customerId;

    public function setUp() {
        spl_autoload_register('autoload');
        $this->customerDao = new Mock_CustomerDao();
        $this->customerId = 1;
    }

    public function testGetCustomerById() {
        $customer = $this->customerDao->getCustomerById($this->customerId);
        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals("Test", $customer->firstName);
        $this->assertEquals("Customer", $customer->lastName);
        $this->assertEquals("DSC000@email.com", $customer->email);
    }

    public function testGetCustomerByIdNull() {
        $customer = $this->customerDao->getCustomerById(99);
        $this->assertNull($customer);
    }

    public function testGetCustomerByEmail() {
        $customer = $this->customerDao->getCustomerByEmail("DSC000@email.com");
        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals("Test", $customer->firstName);
        $this->assertEquals("Customer", $customer->lastName);
        $this->assertEquals(1, $customer->customerId);
    }

    public function testGetCustomerByEmailNull() {
        $customer = $this->customerDao->getCustomerByEmail("test5@email.com");
        $this->assertNull($customer);
    }

    public function testUpdateCustomerDetails() {
        $sql = "update customers set first_name = 'Test', last_name = 'Customer', " .
                "email = 'newemail@email.com', primary_phone = '513-555-1234' where uid = 1";
        $customer = new Customer("Test", "Customer", "newemail@email.com", "513-555-1234");
        $newSql = $this->customerDao->updateCustomerDetails(1, $customer);
        $this->assertEquals($sql, $newSql);
    }

    public function testSaveNewCustomerToDb() {
        $sql = "insert into customers (first_name, last_name, email_address, primary_phone) " .
                "values ('Test4','Customer4','test4@email.com', '513-555-1212')";
        $newCustomer = new Customer("Test4", "Customer4", "test4@email.com", "513-555-1212");
        $this->assertEquals($sql, $this->customerDao->saveNewCustomerToDb($newCustomer));
    }

    public function testDeleteCustomerFromDb() {
        $sql = "delete from customers where uid = 2";
        $this->assertEquals($sql, $this->customerDao->deleteCustomerFromDb(2));
    }

    public function testGetAllCustomersFromDb() {
        $newCustomer0 = new Customer("Test", "Customer", "DSC000@email.com");
        $newCustomer1 = new Customer("Test2", "Customer2", "DSC0002@email.com");
        $newCustomer2 = new Customer("Test3", "Customer3", "DSC0003@email.com");
        $newCustomer3 = new Customer("Test4", "Customer4", "DSC0004@email.com");
        $newCustomer4 = new Customer("Test5", "Customer5", "DSC0005@email.com");
        $newCustomer5 = new Customer("Test6", "Customer6", "DSC0006@email.com");
        $newCustomer6 = new Customer("Test7", "Customer6", "DSC0007@email.com");
        $testCustomersArray = array($newCustomer0, $newCustomer1, $newCustomer2, $newCustomer3, $newCustomer4, $newCustomer5, $newCustomer6);
        $customersArray = $this->customerDao->getAllCustomersFromDb();
        $this->assertEquals(count($testCustomersArray), count($customersArray));
        $this->assertTrue($customersArray[0] instanceof Customer);
        $this->assertEquals($newCustomer0->firstName, $customersArray[0]->firstName);
        $this->assertEquals($newCustomer0->lastName, $customersArray[0]->lastName);
        $this->assertEquals($newCustomer0->email, $customersArray[0]->email);
    }

}

?>
