<?php

class UI_StoreSidebar extends Sidebar implements Iface_Sortable, Iface_CSS, Iface_JavaScript {

    public function __construct($id = "", $class = "") {
        $this->idAttr = $id;
        $this->classAttr = $class;
    }

    public function addItem($objSbItem) {
        $this->items[] = $objSbItem;
    }

    public function printSidebar() {
        $this->buildSidebar();
        $this->printCSS();
        $this->printJS();
        print $this->html;
    }

    public function getSavedSbFrDB($intSbId) {
        $sql = "select * from sidebars where uid = $intSbId";
        $Z = new SQL($sql);
        $res = $Z->getResult();
        if ($Z->num_rows) {
            return $res[0];
        } else {
            return false;
        }
    }

    public static function getAllSbFrDB($asObjOrHTML) {
        
    }

    public static function delItem($intItemId) {
        
    }

    /**
     * (non-PHPdoc)
     * @see CSS::buildCSS()
     */
    public function buildCSS($css, $extUrl = "") {
        /* $extUrl not used here */
        $this->css = <<<CSS

<style>
$css
</style>

CSS
        ;
    }

    public function buildJS($js, $extUrl = "") {
        if (strlen($extUrl) > 0) {
            $this->js = "<script type='text/javascript' src='$extUrl'></script>\n";
        } else {
            $this->js = "<script type='text/javascript language='javascript'>\n$js</script>\n";
        }
    }

    public function saveCSSToDB($name) {
        
    }

    public function getCSSFrDB($dbId) {
        
    }

    public static function getAllCSSFrDB() {
        
    }

    public function printCSS() {
        print $this->css;
    }

    public function saveJSToDB($name) {
        
    }

    public function getJSFrDB($dbId) {
        
    }

    public static function getAllJSFrDB() {
        
    }

    public function printJS() {
        print $this->js;
    }

    public function sortAsc(array $arrayOfObjsToSort) {
        return sort($arrayOfObjsToSort, SORT_STRING);
    }

    public function sortDesc(array $arrayOfObjsToSort) {
        return rsort($arrayOfObjsToSort, SORT_STRING);
    }

    private function buildSidebar() {
        $id = $this->idAttr;
        $class = $this->classAttr;
        $this->html = <<<HTML
<div ID="$id"$class>
<ul id="ul_$id" class="sidebar">
##CONTENT##
</ul>
</div>

HTML
        ;
        $links = "";
        $count = 1;
        foreach ($this->items as $link) {
            $links .= "<li id=\"sbLink_$count\">\n";
            if ($link instanceof UI_Link) {
                $links .= $link->getHtml() . "\n";
            } else {
                //If this value is not an instance of Link then assume straight text/html. Print it verbatim.
                $links .= $link;
            }
            $links .= "</li>\n";
            $count++;
        }

        $this->html = preg_replace("/##CONTENT##/", $links, $this->html);
    }

}