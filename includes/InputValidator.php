<?php

/*
 * Created on Dec 9, 2011
 *
 * InputValidator class serves as the repository for
 * data validation rules that attempt to thwart XSS
 * and SQL injection attacks.
 *
 * author: arturo
 * 
 */

class InputValidator extends SQL {

    private $rules = array();
    private $input;
    private $cleanOutput;

    public function __construct(array $input) {
        parent::__construct();
        $this->input = $input;
    }

    private function runRules() {
        $tmpArr = $this->input; //var_dump($tmpArr);
        $tmpArr = $this->cleanSlashes($tmpArr); //var_dump($tmpArr);
        $tmpArr = $this->cleanSQL($tmpArr);
        $tmpArr = $this->cleanHTML($tmpArr); //var_dump($tmpArr);
        $this->cleanOutput = $tmpArr;
    }

    private function cleanSlashes($arritems) {
        $newarr = array();
        if (get_magic_quotes_gpc()) {
            foreach ($arritems as $key => $item) {
                if (is_array($item)) {
                    $newarr[$key] =  $this->cleanSlashes($item);
                } else {
                    $newarr[$key] = stripslashes($item);
                }
            }
        } else {
            $newarr = $arritems;
        }
        return $newarr;
    }

    private function cleanSQL($arritems) {
        $newarr = array();
        if (is_array($arritems)) {
            foreach ($arritems as $key => $item) {
                if (is_array($item)) {
                    $newarr[$key] = $this->cleanSQL($item);
                } else {
                    if (!preg_match("/(.+)\\\\(.+)/", $item)) {
                        $item = self::$db->obj_mysqli->real_escape_string($item);
                    }
                    $newarr[$key] = $item;
                }
            }
        } else {
            if (!preg_match("/(.+)\\\\(.+)/", $arritems)) {
                $newarr = self::$db->obj_mysqli->real_escape_string($arritems);
            }
        }
        return $newarr;
    }

    private function cleanHTML($arritems) {
        $newarr = array();
        if (is_array($arritems)) {
            foreach ($arritems as $key => $item) {
                if (is_array($item)) {
                    $newarr[$key] = $this->cleanHTML($item);
                } else {
                    $newarr[$key] = htmlspecialchars($item, ENT_QUOTES);
                }
            }
        } else {
            $newarr[] = htmlspecialchars($arritems, ENT_QUOTES);
        }
        return $newarr;
    }

    public static function validate(array $input) {
        $val = new self($input);
        $val->runRules();
        return $val->cleanOutput;
    }

}