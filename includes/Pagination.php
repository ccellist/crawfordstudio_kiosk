<?php

/*
 * Created on Nov 22, 2011
 *
 */

class Pagination implements Iface_Pagination {

    protected $arrData;
    protected $newPage;
    protected $currentPage = 1;
    protected $noPerPage;
    protected $totPages;

    public function __construct(array $arrData, $currentPage, $noPerPage) {
        $this->arrData = $arrData;
        $this->currentPage = $currentPage;
        $this->noPerPage = $noPerPage;
        $this->totPages = (int) ceil(count($arrData) / $noPerPage);
    }

    public function getArrData() {
        return $this->arrData;
    }

    public function setArrData($arrData) {
        $this->arrData = $arrData;
    }

    public function getNewPage() {
        return $this->newPage;
    }

    public function setNewPage($newPage) {
        $this->newPage = $newPage;
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage;
    }

    public function getNoPerPage() {
        return $this->noPerPage;
    }

    public function setNoPerPage($noPerPage) {
        $this->noPerPage = $noPerPage;
    }

    public function nextPage() {
        return $this->getPage($currentPage + 1);
    }

    public function prevPage() {
        return $this->getPage($currentPage - 1);
    }

    public function goToPage($newPage) {
        return $this->getPage($newPage);
    }

    public function getPageCount() {
        return $this->totPages;
    }

    public function getTotPages() {
        return $this->getPageCount();
    }

    private function getPage($newPage) {
        $newArr = array();

        if ($newPage > $this->totPages) {
            $newPage = $this->totPages;
        }
        if ($newPage > 1) {
            $offset = $this->noPerPage * ($newPage - 1);
        } else {
            $offset = 0;
        }
        $limit = $this->noPerPage + $offset;
        if ($limit > count($this->arrData))
            $limit = count($this->arrData);
        reset($this->arrData);
        for ($i = 0; $i < $limit; $i++) {
            if ($offset > 0) {
                if ($i < $offset) {
                    next($this->arrData);
                    continue;
                }
            }
            $newArr[] = current($this->arrData);
            next($this->arrData);
        }
        return $newArr;
    }

}

?>
