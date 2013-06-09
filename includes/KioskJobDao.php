<?php

class KioskJobDao extends Dao{
    public function __construct(){
        parent::__construct();
    }
    
    public function addJob($job){
        $this->updsql = "insert into kiosk_jobs (command) values ('$job')";
        $this->commit();
    }
    
    public function getPendingJobs(){
        $output = array();
        $this->updsql = "select * from kiosk_jobs where is_pending = 1";
        $res = $this->retrieve();
        if ($res != null){
            foreach ($res as $record){
                $kioskJob = new KioskJob();
                $kioskJob->createTime = DateTime::createFromFormat("Y-m-d H:i:s", $record['create_time']);
                $kioskJob->isPending = $record['is_pending'];
                $kioskJob->command = $record['command'];
                $output[] = $kioskJob;
            }
        }
        return $output;
    }
}

?>
