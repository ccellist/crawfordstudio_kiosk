<?php

/**
 * Description of Module
 *
 * @author arturo
 */
class Mod_Customer extends AuthPublic {

    protected $destUrl;
    protected $customerService;

    public function __construct($modName, $qry = "") {
        parent::__construct($modName, $qry);
    }

    public function _default() {
        
    }

    public function registerNew() {
        $this->customerService = new CustomerService();
        $this->destUrl = $this->qryString;
        $orderService = new OrderService();
        $email = $_POST['email'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $primaryPhone = $_POST['phone'];
        $customer = $this->customerService->getCustomerByEmail($email);
        if ($customer == null) {
            $customer = new Customer($firstName, $lastName, $email, $primaryPhone);
            $this->customerService->saveCustomer($customer);
            $newCustomer = $this->customerService->getCustomerByEmail($email);
            $order = $orderService->getOrderById($this->session->orderId);
            $order->customerId = $newCustomer->customerId;
            $orderService->updateOrder($order);
            $this->session = SessionTool::getSession();
            $this->session->customerId = $newCustomer->customerId;
            $this->data = html_entity_decode($this->destUrl);
        } else {
            $order = $orderService->getOrderById($this->session->orderId);
            $order->customerId = $customer->customerId;
            $orderService->updateOrder($order);
            $this->session = SessionTool::getSession();
            $this->session->customerId = $customer->customerId;
            $this->data = html_entity_decode($this->destUrl);
        }
    }
    
    public function deleteCustomer(){
        $customerService = new CustomerService();
        $customerId = $_POST['customerId'];
        if ($customerService->deleteCustomer($customerId)) {
            $this->data = "true";
        } else {
            $this->data = "false";
        }
    }

    public function getOrderList(){
        $customerId = $this->qryString;
        $orderService = new OrderService();
        $orders = $orderService->getOrdersByCustomerId($customerId);
        $this->data = $orders;
    }
}