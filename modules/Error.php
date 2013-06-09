<?php

/**
 * Description of Error
 *
 * @author arturo
 */
class Mod_Error extends AuthPublic {
    public function __construct($modName, $qry = ""){
            parent::__construct($modName, $qry);
    }
        
    public function _default(){
        $this->data = $this->qryString;
    }
}

?>
