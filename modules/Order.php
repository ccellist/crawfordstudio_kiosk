<?php

/**
 * Description of Order
 *
 * @author arturo
 */
class Mod_Order extends AuthPublic {

    public function __construct($modName, $qry = "") {
        parent::__construct($modName, $qry);
    }

    public function _default() {
        
    }

    public function modifyCart() {
        $this->data = $this->addItemsToCart();
        $this->data = $this->removeItemFromCart();
    }

    public function cartThumbnailView() {
        $this->showCart();
    }

    private function addItemsToCart() {
        $this->session = SessionTool::getSession();
        $orderService = new OrderService();
        $order = null;
        $customerId = 0;
        $photoIds = explode(",", $_POST['photoIds']);
        if ($this->session->orderId != null) {
            $order = $orderService->getOrderById($this->session->orderId);
            if ($order === null) {
                $customerId = $this->getCustomerId();
                $order = $this->makeNewOrder($customerId);
                $this->session->orderId = $order->orderId;
            }
        } else {
            $customerId = $this->getCustomerId();
            $order = $this->makeNewOrder($customerId);
        }
        $photoService = new PhotoService();
        foreach ($photoIds as $photoId) {
            if (($photoId != null) && (strlen($photoId) > 0)) {
                $photo = $photoService->getPhotoById($photoId);
                if (!$orderService->isPhotoInOrder($order, $photo))
                    $orderService->addPhotoToOrder($order, $photo);
            }
        }
        //$this->data = $_SERVER["HTTP_REFERER"];
        return $order->orderId;
    }

    private function getCustomerId() {
        $customerDao = new CustomerDao();
        if ($this->session->customerId != null) {
            return $this->session->customerId;
        } else {
            $customer = new Customer("guest", $this->session->session_id, "");
            $customerDao->saveNewCustomerToDb($customer);
            $customer->customerId = $customerDao->getLastInsertId("customers");
            $customerId = $customer->customerId;
        }
        return $customerId;
    }

    private function makeNewOrder($customerId) {
        $orderService = new OrderService();
        $newOrder = new Order($customerId);
        $orderService->saveOrderToDb($newOrder);
        $this->session->orderId = $newOrder->orderId;
        $order = $orderService->getOrderById($this->session->orderId);
        return $order;
    }

    private function removeItemFromCart() {
        $this->session = SessionTool::getSession();
        $orderService = new OrderService();
        $photoDao = new PhotoDao();
        $order = null;
        $postIds = explode(",", $_POST['photoIds']);
        if ($this->session->orderId != null) {
            $order = $orderService->getOrderById($this->session->orderId);
            $photoIds = explode(",", $orderService->getPhotoIdListForOrder($order));
            foreach ($order->orderItems as $orderItem) {
                foreach ($postIds as $photoId) {
                    if (($photoId != null) && strlen($photoId) > 0) {
                        if ($photoId == $orderItem->photo->photoId) {
                            $photoIds = AppObject::filterValue($photoIds, $photoId);
                            break;
                        }
                    }
                }
            }
            foreach ($photoIds as $photoId) {
                $photo = $photoDao->getPhotoById($photoId);
                $orderService->removePhotoFromOrder($order, $photo);
            }
            return $order->orderId;
        } else {
            return $_SERVER["HTTP_REFERER"] . "&e=" . NO_SUCH_CUSTOMER;
        }
    }

    public function clearAllItems() {
        $orderId = $this->qryString;
        $orderService = new OrderService();
        $order = $orderService->getOrderById($orderId);
        $orderService->clearOrder($order);
        $this->data = $orderId;
    }

    public function removeSelectedItems() {
        $photoIds = split(",", $_POST["items"]);
        $orderId = $_POST['orderId'];
        $orderService = new OrderService();
        $photoService = new PhotoService();
        $order = $orderService->getOrderById($orderId);
        foreach ($photoIds as $photoId) {
            $photo = $photoService->getPhotoById($photoId);
            $orderService->removePhotoFromOrder($order, $photo);
        }
        $this->data = "true";
    }

    public function showOrderSummary() {
        $orderId = $this->qryString;
        if (is_numeric($orderId)) {
            if ($orderId != null) {
                $orderService = new OrderService();
                $order = $orderService->getOrderById($orderId);
                if ($order != null) {
                    $this->data = $order;
                } else {
                    $this->data = "Empty cart";
                }
            } else {
                $this->data = "Empty cart";
            }
        } else {
            $this->data = "An error has occurred.";
        }
    }

    public function showCart() {
        $this->session = SessionTool::getSession();
        $orderService = new OrderService();
        $order = null;
        $customerId = 0;

        if ($this->session->orderId != null) {
            $order = $orderService->getOrderById($this->session->orderId);
            if ($order === null) {
                $this->data = "No items in your order.";
            } else {
                $this->data = $order;
            }
        } else {
            $this->data = "No items in your order.";
        }
    }

    public function checkOut() {
        $this->session = SessionTool::getSession();
        $orderService = new OrderService();
        $order = $orderService->getOrderById($this->session->orderId);
        $customerService = new CustomerService();
        $customer = $customerService->getCustomerById($order->customerId);
        if (($customer->firstName == "guest") && ($customer->lastName == $this->session->session_id)) {
            $url = urlencode($_SERVER['REQUEST_URI']);
            $this->data['resultData'] = "/index.php?module=Customer&action=registerNew&qry=$url&view=redirect";
            $this->data['foundCustomer'] = false;
        } else {
            $this->data['foundCustomer'] = true;
            $copies = $orderService->prepOrderForBurning($order);
            $photoCopierService = new PhotoCopierService();
            foreach ($copies as $photoCopy) {
                if (!$photoCopierService->doPhotoCopy($photoCopy)) {
                    $this->data['resultData'] = "Error copying photos. Please contact the administrator.";
                    break;
                }
            }
            if (!isset($this->data['resultData']) || ($this->data['resultData'] == "")) {
                $this->session->orderId = 0;
                unset($this->session->orderId);
                $this->data['resultData'] = "Your order has been submitted. Please visit the checkout counter to pay for and retrieve your order.";
            }
        }
    }

    public function deleteOrder() {
        $orderId = $_POST['orderId'];
        $orderService = new OrderService();
        $order = $orderService->getOrderById($orderId);
        $this->data = $orderService->deleteOrder($order);
    }

    public function finalizeOrder() {
        $orderId = $_POST['orderId'];
        $orderService = new OrderService();
        $order = $orderService->getOrderById($orderId);
        $this->data = $orderService->archiveOrder($order);
    }

}

?>
