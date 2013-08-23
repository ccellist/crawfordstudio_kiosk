<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class OrderItemDaoTest extends PHPUnit_Framework_Testcase{
    private $orderItemDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->orderItemDao = new OrderItemDao();
    }
    
    public function testGetOrderItemsByOrderId(){
        $orderItems = $this->orderItemDao->getOrderItemsByOrderId(2);
        $this->assertNotNull($orderItems);
        $this->assertEquals(2, count($orderItems));
        $this->assertEquals(2, $orderItems[0]->photo->photoId);
        $this->assertEquals(3, $orderItems[1]->photo->photoId);
    }
    
    public function testGetOrderItemsInvalidOrderId(){
        $orderItems = $this->orderItemDao->getOrderItemsByOrderId(20);
        $this->assertEquals(0, count($orderItems));
    }
    
    public function testGetPhotoFromOrderByPhotoId(){
        $orderService = new OrderService();
        $order = $orderService->getOrderById(1);
        $photo = $this->orderItemDao->getPhotoFromOrderByPhotoId($order, 1);
        $this->assertTrue($photo instanceof Photo);
        $this->assertNotNull($photo);
        $this->assertEquals("c:\\testpics", $photo->photoUri);
        $this->assertEquals("DSC00001.JPG", $photo->photoName);
    }
}