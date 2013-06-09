<?php
interface Iface_CSS {
	/**
	 * 
	 * Function to build a stylesheet.
	 * Note the $extUrl variable can only be used
	 * when building a <link ...> tag in the <head>
	 * section of a webpage. Unused anywhere else.
	 * 
	 * @param string $css
	 * @param string $extUrl
	 */
	public function buildCSS($css, $extUrl="");
	public function printCSS();
	public function saveCSSToDB($name);
	public function getCSSFrDB($dbId);
	public static function getAllCSSFrDB();
}