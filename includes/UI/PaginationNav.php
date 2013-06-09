<?php

/*
 * Created on Nov 23, 2011
 *
 */

class UI_PaginationNav {

    public static function drawNav(array $arrData, $currPage, $noPerPage, $currUrl, $pagClass) {
        $pg = new $pagClass($arrData, $currPage, 0, $noPerPage);
        ($currPage < $pg->getTotPages()) ? $nextPage = true : $nextPage = false;
        ($currPage > 1) ? $prevPage = true : $prevPage = false;
        if ($nextPage) {
            $newNHref = self::makeUrl($currUrl, $currPage + 1);
            $nextLinkObj = new UI_Link("&gt;&gt;", $newNHref, "next_page");
            $nextLink = $nextLinkObj->getHtml();
            $lastPgHref = self::makeUrl($currUrl, $pg->getPageCount($arrData, $noPerPage));
            $lastPgLinkObj = new UI_Link("&gt;&gt;", $lastPgHref, "last_page");
            $lastPgLink = $lastPgLinkObj->getHtml();
        } else {
            $nextLink = "&gt;&gt;";
            $lastPgLink = "&gt;&gt;";
        }

        if ($prevPage) {
            $newPHref = self::makeUrl($currUrl, $currPage - 1);
            $prevLinkObj = new UI_Link("&lt;&lt;", $newPHref, "prev_page");
            $prevLink = $prevLinkObj->getHtml();
            $firstPgHref = self::makeUrl($currUrl, 1);
            $firstPgLinkObj = new UI_Link("&lt;&lt;", $firstPgHref, "first_page");
            $firstPgLink = $firstPgLinkObj->getHtml();
        } else {
            $prevLink = "&lt;&lt;";
            $firstPgLink = "&lt;&lt;";
        }

        $pgNavNums = array();
        for ($i = 0; $i < $pg->getTotPages(); $i++) {
            $j = $i + 1;
            if ($j != $currPage) {
                $lnk = new UI_Link($j, self::makeUrl($currUrl, $j), "page_" . $j);
                $pgNavNums[] = $lnk->getHtml();
            } else {
                $pgNavNums[] = "<span id=\"currPage\">" . $j . "</span>\n";
            }
        }

        $outputHtml = "<div id='pag_nav'>\n" . $firstPgLink . "&nbsp;" . $prevLink . " " . implode(" ", $pgNavNums) . " " . $nextLink . "&nbsp;" . $lastPgLink . "<br>\n</div>\n";
        return $outputHtml;
    }

    private static function makeUrl($oldUrl, $newPage) {
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
        return $newUrl;
    }

}

?>
