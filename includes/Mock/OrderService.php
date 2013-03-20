<?php

class Mock_OrderService extends OrderService {
    public function submitPhotoOrder(Order $order){
        $mockOrderDao = new Mock_OrderDao();
        return $mockOrderDao->saveOrderToDb($order);
    }    
    
    public function addPhotoToOrder(Order $order, Photo $photo){
        $output = $order->orderItems;
        $orderItem = new OrderItem($order->orderId, $photo);
        $output[] = $orderItem;
        $order->orderItems = $output;
    }
    
    public function clearOrder(Order $order){
        $order->orderItems = array();
    }
    
    public function removePhotoFromOrder(Order $order, Photo $photo){
        $tmp = $order->unsetValue($order->orderItems, new OrderItem($order->orderId, $photo), false);
        $order->orderItems = $tmp;
    }
}

?>
