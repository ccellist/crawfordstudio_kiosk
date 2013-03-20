<?php

class OrderItemDao extends Dao {

    public function __construct() {
        parent::__construct();
    }

    public function getOrderItemsByOrderId($orderId) {
        $orderItems = array();
        $this->updsql = "select * from order_items where order_id = $orderId";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $photoDao = new PhotoDao();
                $photo = $photoDao->getPhotoById($record['photo_id']);
                $orderItem = new OrderItem($orderId, $photo);
                $orderItems[] = $orderItem;
            }
        }
        return $orderItems;
    }

    public function getPhotoFromOrderByPhotoId(Order $order, $photoId) {
        $orderId = $order->orderId;
        $this->updsql = "select * from order_items where order_id = $orderId and photo_id = $photoId";
        $res = $this->retrieve();
        if ($res != null) {
            $photoDao = new PhotoDao();
            $photo = $photoDao->getPhotoById($res[0]['photo_id']);
            return $photo;
        } else {
            return null;
        }
    }

    public function addItemToOrder(Order $order, Photo $photo) {
        $this->updsql = sprintf("insert into order_items (order_id, photo_id) " .
                "values (%s,%s)", $order->orderId, $photo->photoId);
        $this->commit();
    }

    public function removeItemFromOrder(Order $order, Photo $photo) {
        $this->updsql = sprintf("delete from order_items where order_id = %s " .
                "and photo_id = %s", $order->orderId, $photo->photoId);
        $this->commit();
    }

    public function deleteOrderItemsByOrderId($orderId) {
        $this->updsql = "delete from order_items where order_id = $orderId";
        $this->commit();
    }

}

?>
