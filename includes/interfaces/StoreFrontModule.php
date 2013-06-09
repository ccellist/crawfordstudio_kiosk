<?php

interface Iface_StoreFrontModule {
    public function __construct($modName, $qry = "");
    public function _default();
    public function getPhotos();
}

?>
