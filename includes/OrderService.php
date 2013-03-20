<?php

class OrderService {

    private $orderDao;
    private $orderItemDao;

    public function __construct() {
        $this->orderDao = new OrderDao();
        $this->orderItemDao = new OrderItemDao();
    }

    public function getOrderById($orderId) {
        $order = $this->orderDao->getOrderById($orderId);
        if ($order != null) {
            $orderItems = $this->orderItemDao->getOrderItemsByOrderId($order->orderId);
            $order->orderItems = $orderItems;
            return $order;
        } else {
            return null;
        }
    }

    public function getOrdersByCustomerEmail($email) {
        $customerDao = new CustomerDao();
        $customer = $customerDao->getCustomerByEmail($email);
        $orderList = $this->orderDao->getOrdersByCustomerId($customer->customerId);
        if ($orderList != null) {
            foreach ($orderList as $order) {
                $orderItems = $this->orderItemDao->getOrderItemsByOrderId($order->orderId);
                $order->orderItems = $orderItems;
            }
            return $orderList;
        } else {
            return array();
        }
    }
    
    public function getOrdersByCustomerNameAndPhone($fname, $lname, $phone){
        $customerService = new CustomerService();
        $customer = $customerService->getCustomerByNameAndPhone($fname, $lname, $phone);
        $orderList = $this->orderDao->getOrdersByCustomerId($customer->customerId);
                if ($orderList != null) {
            foreach ($orderList as $order) {
                $orderItems = $this->orderItemDao->getOrderItemsByOrderId($order->orderId);
                $order->orderItems = $orderItems;
            }
            return $orderList;
        } else {
            return array();
        }
    }

    public function getOrdersByCustomerId($customerId) {
        $orderList = $this->orderDao->getOrdersByCustomerId($customerId);
        if ($orderList != null) {
            foreach ($orderList as $order) {
                $orderItems = $this->orderItemDao->getOrderItemsByOrderId($order->orderId);
                $order->orderItems = $orderItems;
            }
            return $orderList;
        } else {
            return array();
        }
    }

    public function getPhotoQuantityByPhotoId(Order $order, $photoId) {
        $orderItems = $order->orderItems;
        $count = 0;
        foreach ($orderItems as $orderItem) {
            if ($orderItem->photo->photoId == $photoId) {
                $count++;
            }
        }
        return $count;
    }

    public function addPhotoToOrder(Order $order, Photo $photo) {
        $this->orderItemDao->addItemToOrder($order, $photo);
        $orderItem = new OrderItem($order->orderId, $photo);
        $tmp = $order->orderItems;
        $tmp[] = $orderItem;
        $order->orderItems = $tmp;
        $order->orderSubtotal = $this->calculateOrderSubtotal($order);
        $order->orderDiscount = $this->calculateDiscount($order);
        $order->orderTaxAmt = $this->calculateTax($order);
        $order->orderTotal = $this->calculateOrderTotal($order);
        $this->orderDao->updateOrder($order);
    }

    public function addPhotosToOrder(Order $order, $photoArray) {
        foreach ($photoArray as $photo) {
            $this->addPhotoToOrder($order, $photo);
        }
    }

    public function calculateOrderSubtotal(Order $order) {
        $orderSubtotal = 0.0;
        $orderItems = $order->orderItems;
        foreach ($orderItems as $orderItem) {
            $orderSubtotal += $orderItem->photo->photoPrice;
        }
        return round($orderSubtotal, 2);
    }

    public function calculateTax(Order $order) {
        $tax = 0.0;
        $taxRate = LOCALITY_TAX_RATE;
        $tax = ($order->orderSubtotal - $order->orderDiscount) * $taxRate;
        return round($tax, 2);
    }

    public function calculateDiscount(Order $order) {
        $orderDiscount = 0.0;
        $perPhotoPrice = $order->orderItems[0]->photo->photoPrice;
        $pricingRules = $this->getPricingRules();
        $photoCount = count($order->orderItems);
        $photoRemainder = $photoCount;
        foreach($pricingRules as $qty => $pricingRule){
            $count = 0;
            while ($photoRemainder - $qty >= 0) {
                $photoRemainder = $photoRemainder - $qty;
                $count++;
            }
            $orderDiscount += $qty * $count * ($perPhotoPrice * (1 - $pricingRule));
        }
        $orderDiscount += $photoRemainder * $order->orderItems[0]->photo->photoPrice;
        return round($order->orderSubtotal - $orderDiscount, 2);
    }

    public function calculateOrderTotal(Order $order) {
        $total = 0.00;
        //No sales tax being charged. Uncomment to include sales tax.
        //$total = ($order->orderSubtotal - $order->orderDiscount) + $order->orderTaxAmt;
        //Comment to charge sales tax above discounted subtotal.
        if (($total = $this->getMaxPackagePrice($order)) == 0) 
            $total = ($order->orderSubtotal - $order->orderDiscount);
        return round($total, 2);
    }
    
    private function getMaxPackagePrice(Order $order){
        $photoCount = count($order->orderItems);
        $pricingRules = $this->getPricingRules();
        $packagePrice = 0;
        foreach ($pricingRules as $qty => $pricingRule){
            if (($pricingRule >= 1) && ($photoCount >= $qty)){
                $packagePrice = $pricingRule;
                break;
            }
        }
        return $packagePrice;
    }

    public function removePhotoFromOrder(Order $order, Photo $photo) {
        $this->orderItemDao->removeItemFromOrder($order, $photo);
        $orderUpdated = $this->getOrderById($order->orderId);
        $order = $orderUpdated;
        $order->orderSubtotal = $this->calculateOrderSubtotal($order);
        $order->orderDiscount = $this->calculateDiscount($order);
        $order->orderTaxAmt = $this->calculateTax($order);
        $order->orderTotal = $this->calculateOrderTotal($order);
        $this->orderDao->updateOrder($order);
    }

    public function clearOrder(Order $order) {
        $this->orderItemDao->deleteOrderItemsByOrderId($order->orderId);
        $order->orderSubtotal = 0;
        $order->orderTaxAmt = 0;
        $order->orderDiscount = 0;
        $order->orderTotal = 0;
        $this->orderDao->updateOrder($order);
        $order->orderItems = array();
    }

    public function submitPhotoOrder(Order $order) {
        $orderDao = new OrderDao();
        $orderDao->saveOrderToDb($order);
    }

    public function prepOrderForBurning(Order $order) {
        $customerService = new CustomerService();
        $customer = $customerService->getCustomerById($order->customerId);
        $photoCopies = array();

        foreach ($order->orderItems as $orderItem) {
            $srcPath = $orderItem->photo->photoUri;
            $photoName = $orderItem->photo->photoName;
            if (($customer->email == null) || strlen($customer->email) == 0) {
                $customerContact = $customer->primaryPhone;
            } else {
                $customerContact = $customer->email;
            }
            $destination = PHOTO_DESTINATION . "\\" . $customerContact . "\\" . date("Ymd") . "\\" . $order->orderId . "_" . count($order->orderItems);
            $photoCopy = new PhotoCopy($photoName, $srcPath, $destination);
            $photoCopies[] = $photoCopy;
        }
        $this->finalizeOrder($order);
        return $photoCopies;
    }

    private function finalizeOrder(Order $order) {
        $order->isPending = Order::IS_PENDING;
        $this->orderDao->updateOrder($order);
    }

    public function archiveOrder(Order $order) {
        $order->isPending = Order::NOT_PENDING;
        $this->orderDao->updateOrder($order);
        $this->orderDao->saveOrderToArchive($order);
        return $this->deleteOrder($order);
    }

    public function saveOrderToDb(Order $newOrder) {
        $orderDao = new OrderDao();
        $orderDao->saveOrderToDb($newOrder);
        $newOrder->orderId = $orderDao->getLastInsertId("orders");
    }

    public function updateOrder(Order $order) {
        $this->orderDao->updateOrder($order);
    }

    public function isPhotoInOrder(Order $order, Photo $photo) {
        $photo = $this->orderItemDao->getPhotoFromOrderByPhotoId($order, $photo->photoId);
        if ($photo == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getPhotoIdListForOrder(Order $order) {
        $output = array();
        foreach ($order->orderItems as $orderItem) {
            $photo = $orderItem->photo;
            $output[] = $photo->photoId;
        }
        return implode(",", $output);
    }

    public function getPendingOrders() {
        $orders = $this->orderDao->getPendingOrders();
        foreach ($orders as $order) {
            $orderItems = $this->orderItemDao->getOrderItemsByOrderId($order->orderId);
            $order->orderItems = $orderItems;
        }
        return $orders;
    }

    public function deleteOrder(Order $order) {
        $orderItemDao = new OrderItemDao();
        $orderItemDao->deleteOrderItemsByOrderId($order->orderId);
        return $this->orderDao->deleteOrderById($order->orderId);
    }

    private function getPricingRules() {
        return $this->orderDao->getPricingRules();
    }

}

?>
