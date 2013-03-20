<?php
/**
 * Description of PaginationFactory
 *
 * @author AA94427
 */
class PaginationFactory {
    
    public static function getPagination($arrData, $currPage, $numPhotos){
        if (PAGINATION_STYLE == ORIENTATION){
            return new CrawfordPagination($arrData, $currPage, $numPhotos);
        } else {
            return new Pagination($arrData, $currPage, $numPhotos);
        }
    }
    
    public static function drawNav(Iface_Pagination $paginationObj, $newPage, $numPhotos, $url, $view = "html"){
        if ($view == "ajax"){
            $pagNav = new UI_PaginationNavAjax($paginationObj);
            return $pagNav->drawNav($newPage, $numPhotos, $url);
        } else {
            $pagNav = new UI_PaginationNav($paginationObj);
            return $pagNav->drawNav($newPage, $numPhotos, $url);
        }
    }
}

?>
