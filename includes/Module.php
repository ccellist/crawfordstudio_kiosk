<?php

/*
 * Created on Dec 15, 2011
 *
 * Main Module class. Provides common properties and methods
 * for all Modules used in the framework, all of which should 
 * be subclassed from this class.
 *
 * author: Arturo Araya
 * 
 */

class Module extends SQL {

    protected $moduleName;
    protected $actionName;
    protected $data;
    protected $error;
    protected $qryString;
    protected $session;

    public function __construct($modName, $qry = "") {
        parent::__construct();
        $this->moduleName = $modName;
        if (strpos($qry, "|") === false) {
            $this->qryString = $qry;
        } else {
            $tmp = explode("|", $qry);
            foreach ($tmp as $itm) {
                $tmp2 = split(":", $itm);
                $this->qryString[$tmp2[0]] = $tmp2[1];
            }
        }
        $this->session = SessionTool::getSession();
        $this->session->moduleName = $this->moduleName;
    }

    public function _default() {
        throw new Exception("This is the default behavior for all modules.<br>Instantiate a specific _default() method to actually do something useful.");
    }

    public function getData() {
        return $this->data;
    }

    public function dumpToDbErrLog($file, $data, $msg = "") {
        ob_start();
        var_dump($data);
        $tmp = ob_get_contents();
        ob_clean();
        $this->logErrToDB($file, $msg, $tmp);
    }

    public function __set($key, $val) {
        if ($key == "actionName") {
            $this->session->prevAction = $this->session->actionName;
            $this->session->actionName = $val;
        }
        return ($this->$key = $val);
    }

}