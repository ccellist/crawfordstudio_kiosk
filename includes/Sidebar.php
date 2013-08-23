<?php
abstract class Sidebar {
	protected $items = array();
	protected $css = "";
	protected $js = "";
	protected $html = "";
	protected $idAttr = "";
	protected $classAttr = "";
	
	abstract function addItem($objSbItem);
	abstract function printSidebar();
	abstract function getSavedSbFrDB($intSbId);
//	abstract static function getAllSbFrDB($asObjOrHTML);
//	abstract static function delItem($intItemId);
}
?>