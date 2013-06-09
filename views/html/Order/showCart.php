<?php

class html_Order_showCart extends presenter implements Iface_Presenter {

    public function __construct($data, $modName, $error) {
        parent::__construct(__CLASS__, $data, $modName, $error);
    }

    public function display() {
        $dispData = $this->data;
        
        $pgErr = $this->error;
        ob_start();
        include(self::$templates . $this->templateFile);
        $mainContents = ob_get_contents();
        ob_clean();
        include(self::$mainTemplPath . "/mainTempl.php");
    }

}