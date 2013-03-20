<?php
/**
 * Description of Order
 *
 * @author arturo
 */
class Order extends AppObject {
    private $orderId;
    private $customerId;
    private $orderItems;
    private $orderSubtotal;
    private $orderTaxAmt;
    private $orderDiscount;
    private $orderTotal;
    private $isPending;
    private $dateCreated;
    
    const IS_PENDING = 1;
    const NOT_PENDING = 0;
    
    public function __construct($customerId){
        $this->customerId = $customerId;
        $this->orderSubtotal = 0.00;
        $this->orderTaxAmt = 0.00;
        $this->orderTotal = 0.00;
        $this->orderDiscount = 0.00;
        $this->orderItems = array();
        $this->isPending = self::IS_PENDING;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

