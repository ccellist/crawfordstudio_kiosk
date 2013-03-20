<?php

class html_StoreFront_showItem extends presenter {

    public function __construct($data, $modName, $error) {
        parent::__construct(__CLASS__, $data, $modName, $error);
    }

    public function display() {
        $dispData = $this->data;
        $pgErr = $this->error;

        $itm = $dispData;

        $item_id = $itm->itemId;
        $name = $itm->itemName;
        $description = nl2br($itm->itemDescription);
        strlen($description) ? $description : $description = "No description available.";
        $arrImages = $itm->itemImages;

        if (count($arrImages) == 0) {
            $img = new Image("deflt_0.jpg");
            $imgSrc = $img->makeThumbnail(200, 160);
        }

        $arrCategories = $itm->itemCategories;
        $unitcost = money_format('%=5.2n', $itm->unitcost);

        ob_start();
        include(self::$templates . $this->templateFile);
        $mainContents = ob_get_contents();
        ob_clean();
        include(self::$mainTemplPath . "/mainTempl.php");
    }

}
