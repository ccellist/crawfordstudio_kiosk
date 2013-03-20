<?php

define("LAST_INSERT", "select LAST_INSERT_ID() FROM ##TABLE##");

class SQL {

    static $db;
    public $sql;
    public $num_rows;
    public $affected_rows;
    protected $results;
    protected $debug;
    protected $dbgOverride;

    public function __construct($dbgOverride = 0) {
        self::$db = DBConnect::getInstance();
        $this->debug = 1;
        $this->dbgOverride = $dbgOverride;
    }

    public function __get($val) {
        return $this->$val;
    }

    public function __set($key, $val) {
        return ($this->$key = $val);
    }

    public function getResult(
    $sql, $asType = "array", $dbgOverride = 0
    ) {
        $count = 0;
        $this->Run($sql, $dbgOverride);
        while ($recs = $this->results->fetch_assoc()) {
            $rec[$count] = $recs;
            $count++;
        }
        if ($count > 0) {
            return $rec;
        } else {
            return 0;
        }
    }

    public function Run($sql, $dbgOverride = 0) {
//echo $sql . "\n\n";
        if (!self::$db instanceof mysqli) {
            self::$db = DBConnect::getInstance();
        }
        if (($this->debug == true) && ($dbgOverride == 0)) {
            $this->LogToDB($sql);
        }
        $this->results = self::$db->obj_mysqli->query($sql);
        @$this->num_rows = $this->results->num_rows;
        @$this->affected_rows = self::$db->obj_mysqli->affected_rows;
        if ((@$this->results->num_rows) || (@self::$db->obj_mysqli->affected_rows > 0)) {
            return true;
        } else {
            return false;
        }
    }

    public static function sanitizeEntry($data) {
        if (!self::$db instanceof mysqli) {
            self::$db = DBConnect::getInstance();
        }
        $sql = self::$db->obj_mysqli->real_escape_string($data);
        return $sql;
    }

    public function RunTransaction($qry) {
        mysqli_report(MYSQLI_REPORT_OFF);
        self::$db->obj_mysqli->autocommit(FALSE);
        if (!is_array($qry)) {
            $tmp = $qry;
            $qry = array($tmp);
        }
        try {
            foreach ($qry as $sql) {
                if (($debug = DEBUG_ON) && ($this->dbgOverride == 0)) {
                    self::LogToDB($sql);
                }
                self::$db->obj_mysqli->query($sql);
                if (self::$db->obj_mysqli->error) {
                    throw new Exception("MySQL error " . self::$db->obj_mysqli->error . " <br> Query:<br> $sql", self::$db->obj_mysqli->errno);
                    break;
                }
            }
            self::$db->commit;
        } catch (Exception $e) {
            self::$db->obj_mysqli->rollback;
            self::$db->obj_mysqli->autocommit(TRUE);
            mysqli_report(MYSQLI_REPORT_ON);
            return false;
        }
        self::$db->obj_mysqli->autocommit(TRUE);
        mysqli_report(MYSQLI_REPORT_ON);
        return true;
    }

    protected function LogErrToDB($file, $msg, $data) {
        $sql = "insert into error_log (file,msg,data) values ('$file','$msg','$data')";
        $this->Run($sql);
    }

    protected function LogToDB($sql) {
//        $qry = "insert into sql_dump (sql_query) values ('" . $sql . "')";
//        $this->Run($qry, 1);
        $timeStamp = date("Y/m/d H:i:s");
        FileWriter::writeLineToFile("[" . $timeStamp . "]: " . $sql, LOG_DIR, QUERY_LOG_FILE);
    }

    public static function RunQuery($sql) {
        $S = new self();
        return $S->Run($sql);
    }

}
