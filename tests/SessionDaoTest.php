<?php

require_once dirname(__FILE__) . "/testPrep.php";
require_once BASE_PATH . "/includes/config.php";


class ClassTest extends PHPUnit_Framework_Testcase{
    private $sessionDao;
    
    public function setUp() {
        spl_autoload_register('autoload');
        $this->sessionDao = new SessionDao();
    }
    
    public function testGetSessionByName(){
        $sessionName = 'Session 1';
        $sessions = $this->sessionDao->getSessions(null,$sessionName);
        $this->assertNotNull($sessions);
        $this->assertEquals(1, count($sessions));
        $session = $sessions[0];
        $this->assertEquals(1, $session->getSessionId());
    }
    
    public function testGetSessionById(){
        $sessionName = 'Session 1';
        $sessions = $this->sessionDao->getSessions(1);
        $this->assertNotNull($sessions);
        $this->assertEquals(1, count($sessions));
        $session =  $sessions[0];
        $this->assertEquals("Session 1", $session->getSessionName());
    }
}