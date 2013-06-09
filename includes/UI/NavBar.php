<?php
class UI_NavBar implements Iface_JavaScript, Iface_HTML {
	private $navId;
	private $navName;
	private $navObjs=array(); /*array of objects, some links, some view cart box, some search box*/
	private $html;
	private $css;
	private $js;
	private $isMobile;
	public $isNavBar=true;
	
	public function __construct($name, $ismobile=false){
		if (($nav = self::getNavByName($name) !== false)) {
			$this->navId = $nav['nav_id'];
			$this->navName = $name;
			$this->navObjs = $this->getnavObjs();
			$this->html = $nav['html'];
			$this->css = $nav['css'];
			$this->js = $nav['js'];
		} else {
			$this->navName = $name;
		}
		$this->isMobile = $ismobile;		
	}
	
	public function populate(array $navObjs) {
		$this->navObjs = $navObjs;
	}
	
	public function saveCss($css, $overwrite=false) {
		$overwrite == true ? $this->css = $css : $this->css .= $css;
	}
		
	public function saveJs($js, $overwrite=false) {
		$overwrite == true ? $this->js = $js : $this->js .= $js;
	}
	
	public function getHtml(){
		$this->buildNav();
		return $this->html;
	}

	public function printNav($strOrientation){
		$this->buildNav($strOrientation);
		print $this->js;
		print $this->css;
		print $this->html;
	}
	
	public static function objIsNav($obj) {
		if (isset($obj->isNavBar)) {
			return $obj->isNavBar;
		} else {
			return false;
		}
	}
	
	public static function getNavByName($name) {
		/*$sql = "select * from navs where nav_name = '$name'";
		$res = $sel->getResult();
		if ($res) {
			return $res[0];
		}
		else {
			return 0;
		}*/
		return false;
	}
	
	private function buildNav($orientation) {
		$this->buildCSS($orientation);
		$html = "<div class='navbar' id='div_{$this->navName}'>\n";
		$html .= "	<ul class='nav' id='{$this->navName}'>\n";
		foreach ($this->navObjs as $link) {
			$html .= "		<li>";
			$html .= $link->getHtml();
			$html .="</li>\n";
		}
		$html .= "	</ul>\n<!-- End .nav#{$this->navName} -->\n";
		$html .= "</div>\n<!-= End .navbar#div_{$this->navName} -->\n";
		$html .= "<div style=\"clear:both\"></div>\n";
		$this->html = $html;
	}
	
	private function getnavObjs() {

	}
	
	private function buildCSS($orientation){
		if ($orientation = "horizontal"){
			$this->css = <<<CSS
<style>
div#div_storePrimaryNav {
	width: 100%;
	background: #222;
	font-weight: 900;
	height: 47px;
}
div#div_storePrimaryNav ul {
	list-style: none;
	margin: 0px;
	width: 100%;
}
div#div_storePrimaryNav ul li {
	float: left;
	width: 14%;
	margin: 12px 10px;
}
div#div_storePrimaryNav ul li a {
	color: white;
	font-size: 20px;
}
div#div_storePrimaryNav ul li a:hover {
	width: 100%;
	background: silver;
}
</style>

CSS
;
		}
	}
	
	/**
	 * Build interface methods
	 */

	
	public function buildJS($js, $extUrl = ""){}
	public function printJS(){}
	public function saveJSToDB($name){}
	public function getJSFrDB($dbId){}
	public static function getAllJSFrDB(){}
}
?>