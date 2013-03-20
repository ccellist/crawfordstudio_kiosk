<?php

/**
 * Description of MemcacheTool
 *
 * @author AA94427
 */
class MemcacheTool {

    private static $instance;
    private $memcache;

    private function __construct() {
        $this->memcache = new Memcache();
        $this->memcache->connect('localhost', 11211);
    }

    public static function getMemcache() {
        if (self::$instance === null) {
            self::$instance = new MemcacheTool();
        }
        return self::$instance->memcache;
    }

    public function __clone() {
        throw new Exception("Permission denied.");
    }

}

?>
