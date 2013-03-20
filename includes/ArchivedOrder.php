<?php
/**
 * Description of ArchivedOrder
 *
 * @author arturo
 */
class ArchivedOrder extends AppObject {
    private $customerId;
    private $customerFName;
    private $customerLName;
    private $customerEmail;
    private $customerPrimaryPhone;
    private $orderId;
    private $orderSubtotal;
    private $orderDiscount;
    private $orderTaxAmt;
    private $orderTotal;
    private $orderNoOfItems;
    
    function __construct($customerId, $customerFName, $customerLName, $customerEmail, $customerPrimaryPhone, $orderId, $orderSubtotal, $orderDiscount, $orderTaxAmt, $orderTotal, $orderNoOfItems) {
        $this->customerId = $customerId;
        $this->customerFName = $customerFName;
        $this->customerLName = $customerLName;
        $this->customerEmail = $customerEmail;
        $this->customerPrimaryPhone = $customerPrimaryPhone;
        $this->orderId = $orderId;
        $this->orderSubtotal = $orderSubtotal;
        $this->orderDiscount = $orderDiscount;
        $this->orderTaxAmt = $orderTaxAmt;
        $this->orderTotal = $orderTotal;
        $this->orderNoOfItems = $orderNoOfItems;
    }

    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

?>
