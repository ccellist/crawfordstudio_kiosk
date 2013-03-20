<?php

/**
 * Description of PaginationNavAjax
 *
 * @author arturo
 */
class UI_PaginationNavAjax {
    private $paginationObject;
    
    public function __construct(Iface_Pagination $paginationObject){
        $this->paginationObject = $paginationObject;
    }

    public function drawNav($newPage, $noPerPage, $currUrl) {
        ($newPage < $this->paginationObject->getTotPages()) ? $nextPage = true : $nextPage = false;
        ($newPage > 1) ? $prevPage = true : $prevPage = false;
        if ($nextPage) {
            $newNHref = $this->makeUrl($currUrl, $newPage + 1);
            $nextLinkObj = new UI_Link("next&gt;", $newNHref, "next_page");
            $nextLink = $nextLinkObj->getHtml();
            $lastPgHref = $this->makeUrl($currUrl, $this->paginationObject->getPageCount());
            $lastPgLinkObj = new UI_Link("&gt;&gt;", $lastPgHref, "last_page");
            $lastPgLink = $lastPgLinkObj->getHtml();
        } else {
            $nextLink = "next&gt;";
            $lastPgLink = "&gt;&gt;";
        }

        if ($prevPage) {
            $newPHref = $this->makeUrl($currUrl, $newPage - 1);
            $prevLinkObj = new UI_Link("&lt;prev", $newPHref, "prev_page");
            $prevLink = $prevLinkObj->getHtml();
            $firstPgHref = $this->makeUrl($currUrl, 1);
            $firstPgLinkObj = new UI_Link("&lt;&lt;", $firstPgHref, "first_page");
            $firstPgLink = $firstPgLinkObj->getHtml();
        } else {
            $prevLink = "&lt;prev";
            $firstPgLink = "&lt;&lt;";
        }

        $this->paginationObjectNavNums = array();
        for ($i = 0; $i < $this->paginationObject->getTotPages(); $i++) {
            $j = $i + 1;
            if ($j != $newPage) {
                $lnk = new UI_Link($j, $this->makeUrl($currUrl, $j), "page_" . $j);
                $this->paginationObjectNavNums[] = $lnk->getHtml();
            } else {
                $this->paginationObjectNavNums[] = "<span id=\"currPage\">" . $j . "</span>\n";
            }
        }

        $outputHtml = "<div id='pag_nav'>\n" . $firstPgLink . "&nbsp;" . $prevLink . " " . implode(" ", $this->paginationObjectNavNums) . " " . $nextLink . "&nbsp;" . $lastPgLink . "<br>\n</div>\n";
        return $outputHtml;
    }

    private function makeUrl($oldUrl, $newPage) {
        if (strpos($oldUrl, "qry=") === false) {
            $oldUrl .= "&qry=";
        }//echo $oldUrl."<br><br>";
        if (strpos($oldUrl, "|p") !== false) {
            $newUrl = preg_replace("/(|p:)[0-9]+/", "\${1}$newPage", $oldUrl);
        } elseif (strpos($oldUrl, "qry=p") !== false) {
            $newUrl = preg_replace("/qry=p:[0-9]+/", "qry=p:$newPage", $oldUrl);
        } else {
            $newUrl = $oldUrl . "|p:$newPage";
        }//echo $newUrl."<br><br>";
        $newUrl = preg_replace("/qry=\|/", "qry=", $newUrl);
        $newUrl = preg_replace("/index.php&qry/", "index.php?qry", $newUrl);
        $output = sprintf("javascript:nextPage('%s')", urlencode($newUrl));
        return $output;
    }

}

?>
