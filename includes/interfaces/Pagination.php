<?php

interface Iface_Pagination {

    public function nextPage();

    public function prevPage();

    public function goToPage($newPage);

    public function getPageCount();
}

?>
