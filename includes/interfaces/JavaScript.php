<?php
interface Iface_JavaScript {
	public function buildJS($js, $extUrl = "");
	public function printJS();
	public function saveJSToDB($name);
	public function getJSFrDB($dbId);
	public static function getAllJSFrDB();
}