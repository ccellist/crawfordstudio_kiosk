<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";

class OrderDaoTest extends PHPUnit_Framework_Testcase {

    private $orderDao;
    private $customerId;

    public function setUp() {
        spl_autoload_register('autoload');
        $this->orderDao = new Mock_OrderDao();
        $this->customerId = 1;
    }

    public function testSaveOrderToDb() {
        $orderService = new Mock_OrderService();
        $custId = $this->customerId;
        $sql1 = "insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal) " .
                "values ($custId, 3.0000, 0.0000, 0.1500, 3.1500)";
        $testOrders = $orderService->getOrdersByCustomerEmail("DSC000@email.com");
        $testOrder = $testOrders[0];
        $photoDao = new PhotoDao();
        $photo1 = $photoDao->getPhotoById(1);
        $orderService->addPhotoToOrder($testOrder, $photo1);
        $orderService->calculateOrderSubtotal($testOrder);
        $this->assertEquals($sql1, $this->orderDao->saveOrderToDb($testOrder));
    }

    public function testGetPendingOrders() {
        $orders = $this->orderDao->getPendingOrders();
        $this->assertEquals(3, count($orders));
        $order1 = $orders[0];
        $this->assertEquals(1, $order1->orderId);
        $this->assertEquals(3.0000, $order1->orderSubtotal);
        $this->assertEquals(0.0000, $order1->orderDiscount);
        $this->assertEquals(3.1500, $order1->orderTotal);
    }

    public function testGetOrdersByCustomerId() {
        $orderService = new Mock_OrderService();
        $photoDao = new PhotoDao();
        $testOrder = new Order($this->customerId);
        $orderService->addPhotoToOrder($testOrder, $photoDao->getPhotoById(1));
        $orderService->addPhotoToOrder($testOrder, $photoDao->getPhotoById(2));

        $orderList = $this->orderDao->getOrdersByCustomerId($this->customerId);
        $this->assertEquals(2, count($orderList));
        $order = $orderList[0];
        $order1 = $orderList[1];

        $this->assertEquals(1, $order->orderId);
        $this->assertEquals(3.00, $order->orderSubtotal);
        $this->assertEquals(0, $order->orderDiscount);
        $this->assertEquals(0.15, $order->orderTaxAmt);
        $this->assertEquals(3.15, $order->orderTotal);
        $this->assertEquals(Order::IS_PENDING, $order->isPending);

        $this->assertEquals(2, $order1->orderId);
        $this->assertEquals(6.00, $order1->orderSubtotal);
        $this->assertEquals(1.00, $order1->orderDiscount);
        $this->assertEquals(0.25, $order1->orderTaxAmt);
        $this->assertEquals(5.25, $order1->orderTotal);
        $this->assertEquals(Order::NOT_PENDING, $order1->isPending);
    }

    public function testGetOrdersInvalidCustomerId() {
        $orderList = $this->orderDao->getOrdersByCustomerId(99);
        $this->assertEquals(0, count($orderList));
    }

    public function testDeleteOrderById() {
        $sql = "delete from orders where uid = 1";
        $this->assertEquals($sql, $this->orderDao->deleteOrderById(1));
    }

    public function testSaveOrderToArchive() {
        $orderService = new OrderService();
        $order = $orderService->getOrderById(2);
        $sql = "insert into orders_archive (customer_id, customer_fname, " .
                "customer_lname, customer_email_address, customer_primary_phone, " .
                "order_subtotal, order_discount, order_taxamt, order_saletotal, " .
                "order_num_items) values (1, 'Test', 'Customer', 'DSC000@email.com', " .
                "'', 6.0000, 1.0000, 0.2500, 5.2500, 2)";
        $this->assertEquals($sql, $this->orderDao->saveOrderToArchive($order));
    }

    public function testGetArchivedOrders() {
        $this->assertEquals(3, count($this->orderDao->getArchivedOrders()));
    }

    public function testGetArchivedOrdersByCustomerId() {
        $archivedOrders = $this->orderDao->getArchivedOrdersByCustomerId(1);
        $this->assertEquals(2, count($archivedOrders));

        $this->assertEquals("Test", $archivedOrders[0]->customerFName);
        $this->assertEquals("Customer", $archivedOrders[0]->customerLName);
        $this->assertEquals("DSC000@email.com", $archivedOrders[0]->customerEmail);
        $this->assertEquals("", $archivedOrders[0]->customerPrimaryPhone);
        $this->assertEquals(99, $archivedOrders[0]->orderId);
        $this->assertEquals(6.0000, $archivedOrders[0]->orderSubtotal);
        $this->assertEquals(1.0000, $archivedOrders[0]->orderDiscount);
        $this->assertEquals(0.2500, $archivedOrders[0]->orderTaxAmt);
        $this->assertEquals(5.2500, $archivedOrders[0]->orderTotal);
        $this->assertEquals(2, $archivedOrders[0]->orderNoOfItems);
    }

    public function testGetArchivedOrdersByCustomerEmail() {
        $archivedOrders = $this->orderDao->getArchivedOrdersByCustomerEmail("DSC000@email.com");
        $this->assertEquals(2, count($archivedOrders));

        $this->assertEquals("Test", $archivedOrders[0]->customerFName);
        $this->assertEquals("Customer", $archivedOrders[0]->customerLName);
        $this->assertEquals("DSC000@email.com", $archivedOrders[0]->customerEmail);
        $this->assertEquals("", $archivedOrders[0]->customerPrimaryPhone);
        $this->assertEquals(99, $archivedOrders[0]->orderId);
        $this->assertEquals(6.0000, $archivedOrders[0]->orderSubtotal);
        $this->assertEquals(1.0000, $archivedOrders[0]->orderDiscount);
        $this->assertEquals(0.2500, $archivedOrders[0]->orderTaxAmt);
        $this->assertEquals(5.2500, $archivedOrders[0]->orderTotal);
        $this->assertEquals(2, $archivedOrders[0]->orderNoOfItems);
    }

    public function testGetArchivedOrdersByDateRange() {
        
    }

    public function testGetArchivedOrderByOrderId() {
        $archivedOrders = $this->orderDao->getArchivedOrdersByOrderId(99);
        $this->assertEquals(1, count($archivedOrders));

        $this->assertEquals("Test", $archivedOrders[0]->customerFName);
        $this->assertEquals("Customer", $archivedOrders[0]->customerLName);
        $this->assertEquals("DSC000@email.com", $archivedOrders[0]->customerEmail);
        $this->assertEquals("", $archivedOrders[0]->customerPrimaryPhone);
        $this->assertEquals(99, $archivedOrders[0]->orderId);
        $this->assertEquals(6.0000, $archivedOrders[0]->orderSubtotal);
        $this->assertEquals(1.0000, $archivedOrders[0]->orderDiscount);
        $this->assertEquals(0.2500, $archivedOrders[0]->orderTaxAmt);
        $this->assertEquals(5.2500, $archivedOrders[0]->orderTotal);
        $this->assertEquals(2, $archivedOrders[0]->orderNoOfItems);
    }

    public function testGetArchivedOrderByArchivedOrderId() {
        $archivedOrder = $this->orderDao->getArchivedOrderByArchivedOrderId(1);

        $this->assertEquals("Test", $archivedOrder->customerFName);
        $this->assertEquals("Customer", $archivedOrder->customerLName);
        $this->assertEquals("DSC000@email.com", $archivedOrder->customerEmail);
        $this->assertEquals("", $archivedOrder->customerPrimaryPhone);
        $this->assertEquals(99, $archivedOrder->orderId);
        $this->assertEquals(6.0000, $archivedOrder->orderSubtotal);
        $this->assertEquals(1.0000, $archivedOrder->orderDiscount);
        $this->assertEquals(0.2500, $archivedOrder->orderTaxAmt);
        $this->assertEquals(5.2500, $archivedOrder->orderTotal);
        $this->assertEquals(2, $archivedOrder->orderNoOfItems);
    }

}

?>
