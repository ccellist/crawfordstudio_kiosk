<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";

class PhotoDaoTest extends PHPUnit_Framework_Testcase {

    private $photoDao;

    public function setUp() {
        spl_autoload_register('autoload');
        $this->photoDao = new PhotoDao();
    }

    public function testGetPhotosByMeetEventId() {
        $testName = "test1.jpg";
        $testThumbnail = "test1_tn.jpg";
        $testUri = "c:\\testpics";
        $testPrice = 3.00;
        $testOrientation = Photo::LANDSCAPE;
        $testPhoto = new Photo($testName, $testUri, $testPrice, $testOrientation);
        $testPhoto->photoThumbnail = $testThumbnail;
        $testPhoto->eventId = 1;

        $photoArray = $this->photoDao->getPhotosByMeetEventId(1);
        $photo = $photoArray[0];
        $this->assertEquals(2, count($photoArray));

        $this->assertTrue($photo instanceof Photo);
        $this->assertEquals($testPhoto->photoName, $photo->photoName);
        $this->assertEquals($testPhoto->photoUri, $photo->photoUri);
        $this->assertEquals($testPhoto->photoPrice, $photo->photoPrice);
        $this->assertEquals($testPhoto->photoOrientation, $photo->photoOrientation);
        $this->assertEquals($testPhoto->photoThumbnail, $photo->photoThumbnail);
        $this->assertEquals($testPhoto->eventId, $photo->eventId);
    }

    public function testGetPhotosByInvalidMeetEventId() {
        $photoArray = $this->photoDao->getPhotosByMeetEventId(31);
        $this->assertNull($photoArray);
    }

    public function testGetPhotoById() {
        $testName = "test1.jpg";
        $testThumbnail = "test1_tn.jpg";
        $testUri = "c:\\testpics";
        $testPrice = 3.00;
        $testOrientation = Photo::LANDSCAPE;
        $testPhoto = new Photo($testName, $testUri, $testPrice, $testOrientation);
        $testPhoto->photoThumbnail = $testThumbnail;
        $testPhoto->eventId = 1;

        $photo = $this->photoDao->getPhotoById(1);

        $this->assertTrue($photo instanceof Photo);
        $this->assertEquals($testPhoto->photoName, $photo->photoName);
        $this->assertEquals($testPhoto->photoUri, $photo->photoUri);
        $this->assertEquals($testPhoto->photoPrice, $photo->photoPrice);
        $this->assertEquals($testPhoto->photoOrientation, $photo->photoOrientation);
        $this->assertEquals($testPhoto->photoThumbnail, $photo->photoThumbnail);
        $this->assertEquals($testPhoto->eventId, $photo->eventId);
    }

    public function testGetPhotoInvalidId() {
        $photo = $this->photoDao->getPhotoById(133);
        $this->assertNull($photo);
    }

}

?>
