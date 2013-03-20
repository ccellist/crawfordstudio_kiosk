<?php

class UI_Button implements Iface_CSS, Iface_JavaScript {

    private $type;
    private $value;
    private $id;
    private $parentFormId;
    private $class;
    private $events; /* array of events */
    private $defImg;
    private $hovImg;
    private $clickImg;
    private $disabledImg;
    private $html;
    private $css;
    private $js;

    public function __construct($type, $value, $id, $parentFormId, $class = "", array $events = array(), array $images = array()) {
        $this->type = $type;
        $this->value = $value;
        $this->id = $id;
        $this->parentFormId = $parentFormId;
        if ($class != '') {
            $this->class = "class='$class'";
        }
        $this->events = $events;
        foreach ($images as $imageType => $url) {
            switch ($imageType) {
                case "default":
                    $this->defImg = new Image($url);
                    $this->hovImg = new Image($url);
                    $this->clickImg = new Image($url);
                    $this->disabledImg = new Image($url);
                    break;
                case "hover":
                    $this->hovImg = new Image($url);
                    break;
                case "click":
                    $this->clickImg = new Image($url);
                    break;
                case "disabled":
                    $this->disabledImg = new Image($url);
                    break;
            }
        }
        $this->buildButton();
    }

    public function printButton() {
        echo $this->css;
        echo $this->html;
    }

    public function getButtonHtml() {
        $output = $this->css;
        $output .= $this->html;
        return $output;
    }
    
    public function disable(){
        $tmp = explode(" ", $this->html);
        $arrLen = count($tmp);
        $tmp[] = $tmp[$arrLen - 1];
        $tmp[$arrLen - 1] = "disabled";
        $this->html = implode(" ", $tmp);
    }

    private function buildButton() {
        try {
            if ($this->defImg != '') {
                $this->buildImgButton();
            } else {
                $this->buildStdButton();
            }
        } catch (Exception $e) {
            $this->buildStdButton();
        }
    }

    private function buildImgButton() {
        $button = "<div id='{$this->id}' {$this->class}";
        $events = "";
        if ($this->type != "submit") {
            if (count($this->events) > 0) {
                foreach ($this->events as $eventType => $action) {
                    $events .= $this->buildJSEvent($eventType, $action);
                }
            }
        } else {
            $events = $this->buildJSEvent("onclick", "$('#" . $this->parentFormId . "').submit()");
        }
        $button .= " $events>" . $this->value . "</div>\n";
        $this->html = $button;
        $this->buildCSS();
    }

    private function buildStdButton() {
        $events = "";
        $button = "<input type=\"" . $this->type . "\" value=\"" . $this->value . "\" id=\"" . $this->id . "\" {$this->class}";
        if ($this->type != "submit") {
            if (count($this->events) > 0) {
                foreach ($this->events as $eventType => $action) {
                    $events .= $this->buildJSEvent($eventType, $action);
                }
            }
        }
        $button .= " $events />";
        $this->html = $button;
    }

    private function buildJSEvent($eventType, $action) {
        $js = "$eventType=\"" . $this->buildJS($action) . "\"";
        return $js;
    }

    /**
     * Implement interface functions
     */

    /**
     * Interface function here only implements javascript
     * associated with button events, not loadable scripts.
     * 
     * @param $js string JS to write
     * @param $extUrl string Not used here
     * 
     */
    public function buildJS($js, $extUrl = "") {
        return $js;
    }

    public function printJS() {
        
    }

    public function saveJSToDB($name) {
        
    }

    public function getJSFrDB($dbId) {
        
    }

    public static function getAllJSFrDB() {
        
    }

    public function buildCSS($css = "", $extUrl = "") {
        $css = "<style>\n";
        if ($this->defImg->getProp("isValid")) {
            $defImgSrc = $this->defImg->getProp("imgSrc");
            $defImgX = $this->defImg->getWidth();
            $defImgY = $this->defImg->getHeight();
            $css .= <<<CSS
		
div#{$this->id} {
	cursor: pointer;
	width: {$defImgX}px;
	height: {$defImgY}px;
	background: url('$defImgSrc');
}

CSS
            ;
        } else {
            throw new Exception("Image does not exist.");
        }

        if ($this->hovImg->getProp("isValid")) {
            $hovImgSrc = $this->hovImg->getProp("imgSrc");
            $hovImgX = $this->hovImg->getWidth();
            $hovImgY = $this->hovImg->getHeight();
            $css .= <<<CSS
		
div#{$this->id} :hover {
	cursor: pointer;
	width: {$hovImgX}px;
	height: {$hovImgY}px;
	background: url('$hovImgSrc');
}

CSS
            ;
        } else {
            throw new Exception("Image does not exist.");
        }

        if ($this->clickImg->getProp("isValid")) {
            $clickImgSrc = $this->clickImg->getProp("imgSrc");
            $clickImgX = $this->clickImg->getWidth();
            $clickImgY = $this->clickImg->getHeight();
            $css .= <<<CSS
		
div#{$this->id} :active {
	cursor: pointer;
	width: {$clickImgX}px;
	height: {$clickImgY}px;
	background: url('$clickImgSrc');
}

CSS
            ;
        } else {
            throw new Exception("Image does not exist.");
        }

        if ($this->disabledImg->getProp("isValid")) {
            $disabledImgSrc = $this->disabledImg->getProp("imgSrc");
            $disabledImgX = $this->disabledImg->getWidth();
            $disabledImgY = $this->disabledImg->getHeight();
            $css .= <<<CSS
		
div#{$this->id}.disabled {
	cursor: pointer;
	width: {$disabledImgX}px;
	height: {$disabledImgY}px;
	background: url('$disabledImgSrc');
}

CSS
            ;
        } else {
            throw new Exception("Image does not exist.");
        }

        $css .= "</style>\n";
        $this->css = $css;
    }

    public function printCSS() {
        
    }

    public function saveCSSToDB($name) {
        
    }

    public function getCSSFrDB($dbId) {
        
    }

    public static function getAllCSSFrDB() {
        
    }

}

?>