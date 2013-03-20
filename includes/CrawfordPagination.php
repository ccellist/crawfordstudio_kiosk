<?php

/**
 * Description of CrawfordPagination
 *
 * @author AA94427
 */
class CrawfordPagination implements Iface_Pagination {

    protected $arrData;
    protected $newPage;
    protected $currentPage = 1;
    protected $noPerPage;
    protected $totPages;
    public static $pagesArray = array();

    public function __construct(array $arrData, $currentPage, $noPerPage) {
        $this->arrData = $arrData;
        $this->currentPage = $currentPage;
        $this->noPerPage = 0;
        $this->totPages = $this->getTotalPages();
        if (ALLOW_DUPLICATE_LANDSCAPE_PHOTOS) {
            //if (count(self::$pagesArray) == 0) {
            $this->prePopulateAllPages();
            //}
        }
    }

    private function prePopulateAllPages() {
        $tmp = array();
        for ($i = 1; $i < $this->totPages; $i++) {
            $tmp[] = $this->getPage($i);
        }
//        $function = function ($val, $key){
//            if (count($val) <= 1){
//                unset($val);
//            }
//        };
//        array_walk(self::$pagesArray, $function);
//        var_dump(self::$pagesArray);
        foreach ($tmp as $page) {
            if (count($page) > 1) {
                self::$pagesArray[] = $page;
            }
        }
    }

    public function nextPage() {
        return $this->getPage($currentPage + 1);
    }

    public function prevPage() {
        return $this->getPage($currentPage - 1);
    }

    public function goToPage($newPage) {
        if (count(self::$pagesArray) > 0) {
            return self::$pagesArray[$newPage - 1];
        } else {
            return $this->getPage($newPage);
        }
    }

    public function getPageCount() {
        if (count(self::$pagesArray) > 0) {
            return count(self::$pagesArray);
        } else {
            return $this->totPages;
        }
    }

    public function getTotPages() {
        return $this->getPageCount();
    }

    private function getPage($newPage) {
        $newArr = array();
        $horizPhotoCount = null;
        $startCounting = null;

        if ($newPage > $this->totPages) {
            $newPage = $this->totPages;
        }
        if ($newPage == 1) {
            $horizPhotoCount = 1;
            $startCounting = true;
        } else {
            $horizPhotoCount = 0;
            $startCounting = false;
        }
        for ($i = 0; $i < count($this->arrData); $i++) {
            $photo = $this->arrData[$i];
            if ((ImageExifProcessor::getPhotoRotateAngle($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName) != 90.0)
                    && (ImageExifProcessor::getPhotoRotateAngle($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName) != 270.0)) {
                $horizPhotoCount++;
                if ($horizPhotoCount >= $newPage) {
                    $startCounting = !$startCounting;
                    break;
                }
            }
            if (($horizPhotoCount == $newPage - 1) && !$startCounting) {
                $startCounting = true;
            }
            if ($startCounting) {
                $newArr[] = $photo;
            }
        }
        return $newArr;
    }

    private function getTotalPages() {
        $count = 0;
        foreach ($this->arrData as $photo) {
            if ((ImageExifProcessor::getPhotoRotateAngle($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName) != 90.0)
                    && (ImageExifProcessor::getPhotoRotateAngle($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName) != 270.0)) {
                $count++;
            }
        }
        return $count;
    }

}

?>
