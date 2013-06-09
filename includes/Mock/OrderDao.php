<?php

class Mock_OrderDao extends OrderDao {
    public function __construct(){
        parent::__construct();
    }
    
    public function deleteOrderById($orderId){
        return "delete from orders where uid = $orderId";
    }
    
    public function saveOrderToDb(Order $order){
        $sql = sprintf("insert into orders (customer_id, order_subtotal, order_discount, " .
                "order_taxamt, order_saletotal) values (%s, %s, %s, %s, %s)",
                $order->customerId, $order->orderSubtotal, $order->orderDiscount,
                $order->orderTaxAmt, $order->orderTotal);
        return $sql;
    }
    
    public function saveOrderToArchive(Order $order){
        $customerService = new CustomerService();
        $customer = $customerService->getCustomerById($order->customerId);
        $output = sprintf("insert into orders_archive (customer_id, customer_fname, " .
                "customer_lname, customer_email_address, customer_primary_phone, " .
                "order_subtotal, order_discount, order_taxamt, order_saletotal, " .
                "order_num_items) values (%s, '%s', '%s', '%s', " . 
                "'%s', %s, %s, %s, %s, %s)", 
                $order->customerId, $customer->firstName, $customer->lastName, $customer->email, $customer->primaryPhone,
                $order->orderSubtotal, $order->orderDiscount, $order->orderTaxAmt, 
                $order->orderTotal, count($order->orderItems));
        return $output;
    }
}

?>
