<?php

require_once dirname(__FILE__) . '/testPrep.php';
require_once BASE_PATH . "/includes/config.php";


class PhotoServiceTest extends PHPUnit_Framework_Testcase{
    private $photoService;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->photoService = new PhotoService();
    }
    
    public function testGetPhotoById(){
        $testName = "test1.jpg";
        $testUri = "c:\\testpics";
        $testPrice = 24.99;
        $testOrientation = Photo::LANDSCAPE;
        $testPhoto = new Photo($testName, $testUri, $testPrice, $testOrientation);
        
        $photo = $this->photoService->getPhotoById(1);
        $this->assertTrue($photo instanceof Photo);
        $this->assertEquals($testPhoto->photoName, $photo->photoName);
        $this->assertEquals($testPhoto->photoUri, $photo->photoUri);
        $this->assertEquals($testPhoto->photoPrice, $photo->photoPrice);
        $this->assertEquals($testPhoto->photoOrientation, $photo->photoOrientation);
    }
    
    public function testGetPhotoByInvalidId(){
        $photo = $this->photoService->getPhotoById(133);
        $this->assertNull($photo);
    }
    
    public function testGetPhotoPriceById(){
        $testPrice = 24.99;
        $this->assertEquals($testPrice, $this->photoService->getPhotoPriceById(1));
    }
    
    public function testGetPhotoPriceByInvalidId(){
        $this->assertEquals(0.00, $this->photoService->getPhotoPriceById(331));
    }
    
    public function testGetPhotoNameById(){
        $testName = "test4.jpg";
        $this->assertEquals($testName, $this->photoService->getPhotoNameById(4));
    }
    
    public function testGetPhotoNameByInvalidId(){
        $this->assertEquals("(invalid photo)", $this->photoService->getPhotoNameById(444));
    }
    
    public function testGetPhotoUriById(){
        $testPhotoUri = "c:\\testpics";
        $this->assertEquals($testPhotoUri, $this->photoService->getPhotoUriById(1));
    }
    
    public function testGetPhotoUriByInvalidId(){
        $this->assertEquals("(invalid photo)", $this->photoService->getPhotoUriById(144));
    }
    
    public function testGetPhotoOrientationById(){
        $testOrientation = Photo::PORTRAIT;
        $this->assertEquals($testOrientation, $this->photoService->getPhotoOrientationById(3));
    }
    
    public function testGetPhotoOrientationByInvalidId(){
        $this->assertEquals(Photo::LANDSCAPE, $this->photoService->getPhotoOrientationById(333));
    }
    
    public function testGetPhotoMeetIdByPhotoId(){
        $testMeetId = 1;
        $this->assertEquals($testMeetId, $this->photoService->getPhotoMeetIdByPhotoId(7));
    }
    
    public function testGetPhotoMeetIdByPhotoInvalidId(){
        $this->assertEquals(0, $this->photoService->getPhotoMeetIdByPhotoId(700));
    }
    
    public function testGetPhotoCountByMeetEventId(){
        $testCount = 2;
        $this->assertEquals($testCount, $this->photoService->getPhotoCountByMeetEventId(6));
    }
    
    public function testGetPhotoCountByMeetEventInvalidId(){
        $this->assertEquals(0, $this->photoService->getPhotoCountByMeetEventId(600));
    }
    
    public function testGetPhotosByMeetEventId(){
        $testCount = 2;
        $photoArray  = $this->photoService->getPhotosByMeetEventId(6);
        $this->assertEquals($testCount, count($photoArray));
        
        $testName = "test12.jpg";
        $testUri = "c:\\testpics";
        $testPrice = 24.99;
        $testOrientation = Photo::LANDSCAPE;
        $testPhoto = new Photo($testName, $testUri, $testPrice, $testOrientation);
        $photo = $photoArray[0];
        
        $this->assertTrue($photoArray[0] instanceof Photo);
        $this->assertEquals($testPhoto->photoName, $photo->photoName);
    }
    
    public function testGetPhotosByInvalidMeetEventId(){
        $photoArray  = $this->photoService->getPhotosByMeetEventId(600);
        $this->assertEquals(0, count($photoArray));
    }
}

?>
