<?php
/*
 * Created on Dec 12, 2011
 *
 * author: arturo
 * 
 */

class Mod_Index extends AuthPublic {
	public function __construct($modName,$qry=""){
		parent::__construct($modName,$qry);
	}
	
	public function _default(){
		$this->data = <<<HTML
		<h2>Welcome to the default home page</h2>
		<p>Insert some other content to see something different here.</p>
HTML
;
	}
}