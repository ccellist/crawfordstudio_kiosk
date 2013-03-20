<?php

class OrderDao extends Dao {

    public function saveOrderToDb(Order $order) {
        $this->updsql = sprintf("insert into orders (customer_id, order_subtotal, order_discount," .
                "order_taxamt, order_saletotal) values (%s, %s, %s, %s, %s)", $order->customerId, $order->orderSubtotal, $order->orderDiscount, $order->orderTaxAmt, $order->orderTotal);
        $this->commit();
    }

    public function updateOrder(Order $order) {
        $this->updsql = sprintf("update orders set customer_id = %s, order_subtotal = '%s', order_discount = '%s', "
                . "order_taxamt = '%s', order_saletotal = '%s', is_pending = %s where uid = %s", $order->customerId, $order->orderSubtotal, $order->orderDiscount, $order->orderTaxAmt, $order->orderTotal, $order->isPending, $order->orderId
        );
        $this->commit();
    }

    public function getOrderById($orderId) {
        $this->updsql = "select * from orders where uid = $orderId";
        $res = $this->retrieve();
        if ($res != null) {
            $order = new Order($res[0]['customer_id']);
            $order->orderId = $res[0]['uid'];
            $order->orderSubtotal = $res[0]['order_subtotal'];
            $order->orderDiscount = $res[0]['order_discount'];
            $order->orderTaxAmt = $res[0]['order_taxamt'];
            $order->orderTotal = $res[0]['order_saletotal'];
            $order->isPending = $res[0]['is_pending'];
            $order->dateCreated = $res[0]['date_created'];
            return $order;
        } else {
            return null;
        }
    }

    public function getOrdersByCustomerId($customerId) {
        $this->updsql = "select * from orders where customer_id = $customerId";
        $res = $this->retrieve();
        if ($res != null) {
            $ordersList = array();
            foreach ($res as $record) {
                $order = new Order($record['customer_id']);
                $order->orderId = $record['uid'];
                $order->orderSubtotal = $record['order_subtotal'];
                $order->orderDiscount = $record['order_discount'];
                $order->orderTaxAmt = $record['order_taxamt'];
                $order->orderTotal = $record['order_saletotal'];
                $order->isPending = $record['is_pending'];
                $order->dateCreated = $record['date_created'];
                $ordersList[] = $order;
            }
            return $ordersList;
        } else {
            return null;
        }
    }

    public function getPendingOrders() {
        $orders = array();
        $this->updsql = "select * from orders_by_customer_view where is_pending = " . Order::IS_PENDING . " order by customer_uid, order_uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $order = new Order($record['customer_uid']);
                $order->orderId = $record['order_uid'];
                $order->orderTotal = $record['order_saletotal'];
                $order->orderSubtotal = $record['order_subtotal'];
                $order->orderTaxAmt = $record['order_taxamt'];
                $order->isPending = $record['is_pending'];
                $order->orderDiscount = $record['order_discount'];
                $order->dateCreated = $record['date_created'];
                $orders[] = $order;
            }
        }
        return $orders;
    }

    public function deleteOrderById($orderId) {
        $this->updsql = "delete from orders where uid = $orderId";
        return $this->commit();
    }

    public function getPricingRules() {
        $output = array();
        $this->updsql = "select * from pricing_rules order by uid desc";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $rule) {
                $output[$rule['qty_threshold']] = $rule['discount_rate'];
            }
            return $output;
        } else {
            return array();
        }
    }

    public function saveOrderToArchive(Order $order) {
        $customerService = new CustomerService();
        $customer = $customerService->getCustomerById($order->customerId);
        $this->updsql = sprintf("insert into orders_archive (customer_id, customer_fname, " .
                "customer_lname, customer_email_address, customer_primary_phone, " .
                "order_subtotal, order_discount, order_taxamt, order_saletotal, " .
                "order_num_items) values (%s, '%s', '%s', '%s', " .
                "'%s', %s, %s, %s, %s, %s)", $order->customerId, $customer->firstName, $customer->lastName, $customer->email, $customer->primaryPhone, $order->orderSubtotal, $order->orderDiscount, $order->orderTaxAmt, $order->orderTotal, count($order->orderItems));
        $this->commit();
    }

    public function getArchivedOrders() {

        $archivedOrders = array();
        $this->updsql = "select * from orders_archive order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $archivedOrder = new ArchivedOrder($record['customer_id'],
                                $record['customer_fname'],
                                $record['customer_lname'],
                                $record['customer_email_address'],
                                $record['customer_primary_phone'],
                                $record['order_id'],
                                $record['order_subtotal'],
                                $record['order_discount'],
                                $record['order_taxamt'],
                                $record['order_saletotal'],
                                $record['order_num_items']);
                $archivedOrders[] = $archivedOrder;
            }
            return $archivedOrders;
        } else {
            return null;
        }
    }

    public function getArchivedOrdersByCustomerId($customerId) {

        $archivedOrders = array();
        $this->updsql = "select * from orders_archive where customer_id = $customerId order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $archivedOrder = new ArchivedOrder($record['customer_id'],
                                $record['customer_fname'],
                                $record['customer_lname'],
                                $record['customer_email_address'],
                                $record['customer_primary_phone'],
                                $record['order_id'],
                                $record['order_subtotal'],
                                $record['order_discount'],
                                $record['order_taxamt'],
                                $record['order_saletotal'],
                                $record['order_num_items']);
                $archivedOrders[] = $archivedOrder;
            }
            return $archivedOrders;
        } else {
            return null;
        }
    }

    public function getArchivedOrdersByCustomerEmail($customerEmail) {

        $archivedOrders = array();
        $this->updsql = "select * from orders_archive where customer_email_address = '$customerEmail' order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $archivedOrder = new ArchivedOrder($record['customer_id'],
                                $record['customer_fname'],
                                $record['customer_lname'],
                                $record['customer_email_address'],
                                $record['customer_primary_phone'],
                                $record['order_id'],
                                $record['order_subtotal'],
                                $record['order_discount'],
                                $record['order_taxamt'],
                                $record['order_saletotal'],
                                $record['order_num_items']);
                $archivedOrders[] = $archivedOrder;
            }
            return $archivedOrders;
        } else {
            return null;
        }
    }

    public function testGetArchivedOrdersByDateRange(DateTime $start, DateTime $end) {
        $strStart = $start->format('Y-m-d H:i:s');
        $strEnd = $end->format('Y-m-d H:i:s');
        $archivedOrders = array();
        $this->updsql = "select * from orders_archive where date_archived between '$strStart' and '$strEnd' order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $archivedOrder = new ArchivedOrder($record['customer_id'],
                                $record['customer_fname'],
                                $record['customer_lname'],
                                $record['customer_email_address'],
                                $record['customer_primary_phone'],
                                $record['order_id'],
                                $record['order_subtotal'],
                                $record['order_discount'],
                                $record['order_taxamt'],
                                $record['order_saletotal'],
                                $record['order_num_items']);
                $archivedOrders[] = $archivedOrder;
            }
            return $archivedOrders;
        } else {
            return null;
        }
    }

    public function getArchivedOrdersByOrderId($orderId) {

        $archivedOrders = array();
        $this->updsql = "select * from orders_archive where order_id = $orderId order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $record) {
                $archivedOrder = new ArchivedOrder($record['customer_id'],
                                $record['customer_fname'],
                                $record['customer_lname'],
                                $record['customer_email_address'],
                                $record['customer_primary_phone'],
                                $record['order_id'],
                                $record['order_subtotal'],
                                $record['order_discount'],
                                $record['order_taxamt'],
                                $record['order_saletotal'],
                                $record['order_num_items']);
                $archivedOrders[] = $archivedOrder;
            }
            return $archivedOrders;
        } else {
            return null;
        }
    }

    public function getArchivedOrderByArchivedOrderId($archivedOrderId) {

        $this->updsql = "select * from orders_archive where uid = $archivedOrderId order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            $record = $res[0];
            $archivedOrder = new ArchivedOrder($record['customer_id'],
                            $record['customer_fname'],
                            $record['customer_lname'],
                            $record['customer_email_address'],
                            $record['customer_primary_phone'],
                            $record['order_id'],
                            $record['order_subtotal'],
                            $record['order_discount'],
                            $record['order_taxamt'],
                            $record['order_saletotal'],
                            $record['order_num_items']);
            return $archivedOrder;
        } else {
            return null;
        }
    }

}

?>
