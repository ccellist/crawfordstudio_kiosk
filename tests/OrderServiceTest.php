<?php

require_once dirname(__FILE__) . '/testPrep.php';
require_once BASE_PATH . "/includes/config.php";


class OrderServiceTest extends PHPUnit_Framework_Testcase{
    private $orderService;
    private $customerId;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->orderService = new Mock_OrderService();
        $this->customerId = 1;
    }
        
    public function testGetOrderById(){
        $order = $this->orderService->getOrderById(1);
        $this->assertEquals(1, count($order->orderItems));
        $this->assertEquals("DSC00001.JPG", $order->orderItems[0]->photo->photoName);
        $this->assertEquals("c:\\testpics", $order->orderItems[0]->photo->photoUri);
    }
    
    public function testGetOrderByIdNull(){
        $order = $this->orderService->getOrderById(12);
        $this->assertNull($order);
    }
    
    public function testGetOrdersByCustomerEmail(){
        $photoDao = new PhotoDao();
        $testOrder = new Order($this->customerId);
        $testOrder->orderId = 1;
        $testOrder2 = new Order($this->customerId);
        $testOrder->orderId = 2;
        $this->orderService->addPhotoToOrder($testOrder, $photoDao->getPhotoById(1));
        $this->orderService->addPhotoToOrder($testOrder2, $photoDao->getPhotoById(2));
        $this->orderService->addPhotoToOrder($testOrder2, $photoDao->getPhotoById(3));
        $customerOrders = $this->orderService->getOrdersByCustomerEmail("DSC000@email.com");
        $this->assertEquals(count(array($testOrder, $testOrder2)), count($customerOrders));   
        $this->assertEquals("DSC00001.JPG", $customerOrders[0]->orderItems[0]->photo->photoName);   
        $this->assertEquals("DSC00002.JPG", $customerOrders[1]->orderItems[0]->photo->photoName);   
        $this->assertEquals("DSC00017.JPG", $customerOrders[1]->orderItems[1]->photo->photoName);
    }
    
    public function testGetPhotoQuantityByPhotoId(){
        $order1 = $this->orderService->getOrderById(1);
        $this->assertEquals(1, $this->orderService->getPhotoQuantityByPhotoId($order1, 1));
        $this->assertEquals(0, $this->orderService->getPhotoQuantityByPhotoId($order1, 3));
        $order2 = $this->orderService->getOrderById(5);
        $this->assertEquals(0, $this->orderService->getPhotoQuantityByPhotoId($order2, 3));
        $this->assertEquals(1, $this->orderService->getPhotoQuantityByPhotoId($order2, 5));

    }
    
    public function testGetOrdersFromCustomerWithNoOrders(){
        $customerOrders = $this->orderService->getOrdersByCustomerEmail("DSC0004@email.com");
        $this->assertEquals(0, count($customerOrders));
    }
    
    public function testCalculateOrderSubtotal(){
        $photoDao = new PhotoDao();
        $testOrder = new Order($this->customerId);
        $testOrder->orderId = 1;
        $photo1 = $photoDao->getPhotoById(1);
        $photo2 = $photoDao->getPhotoById(2);
        $photo3 = $photoDao->getPhotoById(3);
        $this->orderService->addPhotosToOrder($testOrder, array($photo1,$photo2,$photo3));
        $testAmount = 3.00 * 3;
        $this->assertEquals($testAmount, $this->orderService->calculateOrderSubtotal($testOrder));
    }
    
    public function testCalculateOrderDiscount1Photo(){
        $order = $this->orderService->getOrderById(1);
        $this->assertEquals($order->orderSubtotal - 3.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testCalculateOrderDiscount2Photos(){
        $order = $this->orderService->getOrderById(2);
        $this->assertEquals($order->orderSubtotal - 5.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testCalculateOrderDiscount3Photos(){
        $order = $this->orderService->getOrderById(5);
        $this->assertEquals($order->orderSubtotal - 8.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testCalculateOrderDiscount4Photos(){
        $order = $this->orderService->getOrderById(6);
        $this->assertEquals($order->orderSubtotal - 10.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testCalculateOrderDiscount5Photos(){
        $order = $this->orderService->getOrderById(7);
        $this->assertEquals($order->orderSubtotal - 10.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testCalculateOrderDiscount6Photos(){
        $order = $this->orderService->getOrderById(8);
        $this->assertEquals($order->orderSubtotal - 13.00, $this->orderService->calculateDiscount($order));
    }
    
    public function testAddPhotoToOrder(){
        $photoDao = new PhotoDao();
        $orderDao = new OrderDao();
        $testOrder = new Order($this->customerId);
        $testOrder->orderId = 1;
        $photo1 = $photoDao->getPhotoById(1);
        $this->orderService->addPhotoToOrder($testOrder, $photo1);
        $this->assertEquals(1, count($testOrder->orderItems));
    }
    
    public function testAddPhotosToOrder(){
        $photoDao = new PhotoDao();
        $testOrder = new Order($this->customerId);
        $testOrder->orderId = 1;
        $photo1 = $photoDao->getPhotoById(1);
        $photo2 = $photoDao->getPhotoById(2);
        $photo3 = $photoDao->getPhotoById(3);
        $this->orderService->addPhotosToOrder($testOrder, array($photo1,$photo2,$photo3));
        $this->assertEquals(count(array($photo1,$photo2,$photo3)), count($testOrder->orderItems));
    }
    
//    public function testRemovePhotoFromOrder(){
//        $photoDao = new PhotoDao();
//        $testOrder = new Order($this->customerId);
//        $testOrder->orderId = 1;
//        $photo1 = $photoDao->getPhotoById(1);
//        $photo2 = $photoDao->getPhotoById(2);
//        $photo3 = $photoDao->getPhotoById(3);
//        $this->orderService->addPhotosToOrder($testOrder, array($photo1,$photo2,$photo3));
//        $this->assertEquals(3, count($testOrder->orderItems));
//        $this->orderService->removePhotoFromOrder($testOrder, $photo2);
//        $this->assertEquals(count(array($photo1,$photo3)), count($testOrder->orderItems));
//        $this->assertEquals("DSC00001.JPG", $testOrder->orderItems[0]->photo->photoName);
//        $this->assertEquals("DSC00003.JPG", $testOrder->orderItems[2]->photo->photoName);
//    }
    
    public function testClearOrder(){
        $photoDao = new PhotoDao();
        $testOrder = new Order($this->customerId);
        $testOrder->orderId = 1;
        $photo1 = $photoDao->getPhotoById(1);
        $photo2 = $photoDao->getPhotoById(2);
        $photo3 = $photoDao->getPhotoById(3);
        $this->orderService->addPhotosToOrder($testOrder, array($photo1,$photo2,$photo3));
        $this->assertEquals(count(array($photo1,$photo2,$photo3)), count($testOrder->orderItems));
        $this->orderService->clearOrder($testOrder);
        $this->assertEquals(0, count($testOrder->orderItems));
    }
    
    public function testSubmitPhotoOrder(){
        $photoDao = new PhotoDao();
        $orderDao = new OrderDao();
        $sql1 = "insert into orders (customer_id, order_subtotal, order_discount, order_taxamt, order_saletotal) " .
                "values (1, 3.0000, 0.0000, 0.1500, 3.1500)";
        $testOrder = $orderDao->getOrderById(1);
        $photo1 = $photoDao->getPhotoById(1);
        $this->orderService->addPhotoToOrder($testOrder, $photo1);
        $this->orderService->calculateOrderSubtotal($testOrder);
        $mockService = new Mock_OrderService();
        $this->assertEquals($sql1, $mockService->submitPhotoOrder($testOrder));
    }
    
    public function testPrepOrderForBurning(){
        $today = date("Ymd", time());
        $order = $this->orderService->getOrderById(1);
        $photosToCopy = $this->orderService->prepOrderForBurning($order);
        $this->assertEquals(1, count($photosToCopy));
        $this->assertEquals(PHOTO_DESTINATION . "\\DSC000@email.com\\$today\\1_1", $photosToCopy[0]->destPath);
        $this->assertEquals("DSC00001.JPG", $photosToCopy[0]->photoName);
        $this->assertEquals("c:\\testpics", $photosToCopy[0]->srcPath);
    }
    
    public function testGetPhotoIdListForOrder(){
        $order = $this->orderService->getOrderById(2);
        $this->assertEquals("2,3", $this->orderService->getPhotoIdListForOrder($order));
    }
}

?>
