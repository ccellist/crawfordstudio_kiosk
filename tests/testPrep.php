<?php
define("BASE_PATH", "/home/arturo/NetBeansProjects/CrawfordPhotoServer/trunk"); // for Linux hosts.
//define("BASE_PATH","C:\\Users\\aa94427\\Documents\\NetBeansProjects\\CrawfordPhotoServer\\trunk"); // for Windows hosts.

function autoload($class) {
    if (preg_match("/^Mod_/", $class)) {
        $file = preg_replace("/^Mod_/", "", $class) . ".php";
        require_once(BASE_PATH . "/modules/" . $file);
    } elseif (preg_match("/^UI_/", $class)) {
        $file = preg_replace("/^UI_/", "", $class) . ".php";
        require_once(BASE_PATH . "/includes/UI/" . $file);
    } elseif (preg_match("/Exception$/", $class)) {
        $file = $class . ".php";
        require_once(BASE_PATH . "/includes/Exceptions/" . $file);
    } else {
        $file = str_replace("_", "/", preg_replace("/s$/", "", $class)) . ".php";
        $file = preg_replace("/^Iface/", "interfaces", $file);
        $fullPath = BASE_PATH . "/includes/" . $file;
        require_once($fullPath);
    }
}
?>
