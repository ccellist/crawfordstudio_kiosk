<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";

class RotationDaoTest extends PHPUnit_Framework_Testcase {

    private $rotationDao;

    public function setUp() {
        spl_autoload_register('autoload');
        $this->rotationDao = new RotationDao();
    }

    public function testGetRotationById() {
        $rotation = $this->rotationDao->getRotationById(1);
        $this->assertNotNull($rotation);
        $this->assertTrue($rotation instanceof Rotation);
        $this->assertEquals("Morning A", $rotation->getRotationName());
    }

    public function testGetRotationsByName() {
        $rotationList = $this->rotationDao->getRotationsByName("Morning B");
        $this->assertNotNull($rotationList);
        $this->assertEquals(1, count($rotationList));
        $this->assertEquals(2, $rotationList[0]->getRotationId());
    }

}