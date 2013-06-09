<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class CustomerServiceTest extends PHPUnit_Framework_Testcase{
    private $customerService;
    private $customerId;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->customerService = new CustomerService();
        $this->customerId = 1;
    }
    
    public function testGetCustomerById(){        
        $customer = $this->customerService->getCustomerById($this->customerId);
        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals("Test", $customer->firstName);
        $this->assertEquals("Customer", $customer->lastName);
        $this->assertEquals("test@email.com", $customer->email);
    }
    
    public function testGetCustomerByIdNull(){
        $customer = $this->customerService->getCustomerById(999);
        $this->assertNull($customer);
    }
    
    public function testGetCustomerByEmail(){
        $customer = $this->customerService->getCustomerByEmail("test@email.com");
        $this->assertTrue($customer instanceof Customer);
        $this->assertEquals("Test", $customer->firstName);
        $this->assertEquals("Customer", $customer->lastName);
        $this->assertEquals("test@email.com", $customer->email);
    }
    
    public function testGetCustomerByEmailNull(){
        $customer = $this->customerService->getCustomerByEmail("test9@email.com");
        $this->assertNull($customer);
    }
    
    public function testGetCustomers(){
        $customersArray = $this->customerService->getCustomers();
        $this->assertEquals(3, count($customersArray));
        $this->assertTrue($customersArray[0] instanceof Customer);
        $this->assertEquals(1, $customersArray[0]->customerId);
        $this->assertEquals("Test", $customersArray[0]->firstName);
        $this->assertEquals("Customer", $customersArray[0]->lastName);
        $this->assertEquals("test@email.com", $customersArray[0]->email);
    }
}

?>
