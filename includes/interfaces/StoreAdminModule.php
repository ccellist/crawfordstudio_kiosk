<?php

interface Iface_StoreAdminModule {
    public function __construct($modName, $qry = "");
    public function _default();
    public function editItem();
    public function submitItem();
}

?>
