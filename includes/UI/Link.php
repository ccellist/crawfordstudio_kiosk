<?php

class UI_Link extends AppObject implements Iface_HTML {

    private $link_uid;
    private $href;
    private $text;
    private $id;
    private $class;
    private $target;
    private $html;
    private $exists;

    public function __construct($text, $href, $id, $class = "", $target = "") {
        $this->href = $href;
        $this->text = $text;
        $this->id = $id;
        if ($class != "") {
            $this->class = "class='$class'";
        }
        if ($target != "") {
            $this->target = "target='$target'";
        }
        if (strlen($text) > 0 && strlen($href) > 0) {
            $this->html = "<a href=\"$href\" id=\"{$id}\" {$this->class} $target>$text</a>";
        } else {
            $this->html = "";
        }
        $this->exists = self::linkExists($text, $href);
    }

    public function setClass($class) {
        $this->class = "class='$class";
        $this->html = "<a href='{$this->href}' id='{$this->id}' {$this->class} {$this->target}>{$this->text}</a>";
    }

    public function getHtml() {
        return $this->html;
    }

    public function saveToDb() {
        if (!$this->exists) {
            $sql = "insert into links (text,url,target) values ('$text','$href','$target')";
            $Z = new SQL();
            $Z->Run($sql);
            if ($Z->affected_rows) {
                return SUCCESS;
            } else {
                return false;
            }
        } else {
            return LINK_EXISTS;
        }
    }

    private static function linkExists($text, $href) {
        $text = SQL::sanitizeEntry($text);
        $href = SQL::sanitizeEntry($href);
        $sql = "select uid from links where text='$text' and url='$href'";
        $Z = new SQL();
        $res = $Z->getResult($sql);
        if ($Z->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

}

?>