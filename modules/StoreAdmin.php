<?php

/**
 * Description of Module
 *
 * @author arturo
 */
class Mod_StoreAdmin extends AuthAdmin/* | AuthUser | AuthAdmin*/ {

    public function __construct($modName, $qry = "") {
        parent::__construct($modName, $qry);
    }

    public function _default() {
        $customerService = new CustomerService();
        $orderService = new OrderService();
        $kioskJobDao = new KioskJobDao();
        
        $customerList = $customerService->getCustomers();
        $pendingJobs = $kioskJobDao->getPendingJobs();
        $pendingOrders = $orderService->getPendingOrders();
        
        $this->data['customerList'] = $customerList;
        $this->data['pendingJobs'] = $pendingJobs;
        $this->data['pendingOrders'] = $pendingOrders;
    }
}