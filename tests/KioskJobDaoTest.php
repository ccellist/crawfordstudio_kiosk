<?php

require_once dirname(__FILE__) . '/testPrep.php';
require_once BASE_PATH . "/includes/config.php";


class KioskJobDaoTest extends PHPUnit_Framework_Testcase{
    private $kioskJobDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->kioskJobDao = new KioskJobDao();
    }
    
    public function testGetPendingJobs(){
        $dateTime = DateTime::createFromFormat("Y-m-d H:i:s", strtotime('2012-10-03 21:57:12'));
        $command = "cp /tmp/test.jpg /home/user/test.jpg";
        $kioskJobs = $this->kioskJobDao->getPendingJobs();
        
        $this->assertEquals(1, count($kioskJobs));
        $this->assertEquals($dateTime, $kioskJobs[0]->createTime);
        $this->assertEquals($command, $kioskJobs[0]->command);
    }
}