<?php
/*
 * Created on Dec 23, 2011
 *
 * Module used for viewing data logged to sql_dump table.
 *
 * author: arturo
 * 
 */

class Mod_SQLView extends AuthPublic {
	public function __construct($modName,$qry="",$error=0) {
		parent::__construct($modName,$qry);
	}
	
	public function _default(){	
		if ($this->qryString == "error_log"){
			$sql = "select file,msg,data,trace,log_time from error_log order by uid desc limit 100";
		} else {
			$sql = "select sql_query,created_stamp from sql_dump order by uid desc limit 100";
		}
		
		$res = $this->getResult($sql,"array",1);
		$this->data = $res;
	}
}