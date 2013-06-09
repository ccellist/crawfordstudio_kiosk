<?php
/*
 * Created on Nov 11, 2011
 *
 */
 
 class UI_SidebarLink extends UI_Link {
 	private $sidebarId;
 	private $sidebarName;
 	private $sidebarType;
 	
 	public function __construct($sidebarId,$text,$href,$id,$class="",$target=""){
            parent::__construct($text, $href, $id, $class, $target);
            $this->sidebarId = $sidebarId;
        }
 }
?>
