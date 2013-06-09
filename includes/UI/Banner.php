<?php
class UI_Banner {
	private $logo;
	private $css;
	private $mainText;
	private $isMobile;
	
	public function __construct($logoUrl, $isMobile) {
		$this->logo = $logoUrl;
		$this->isMobile = $isMobile;
	}
	
	public function draw() {
		$logo = $this->logo;
		print <<<DRAW

<div id="jjd-header">
	
</div>

DRAW
;
	}
}
?>