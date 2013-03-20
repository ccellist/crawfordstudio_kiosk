<?php

abstract class presenter {
	protected $data;
	protected $error;
	protected static $templates;
	protected $templateFile;
	protected static $mainTemplPath;
	protected $moduleName;
	
	public function __construct($className,$data,$moduleName,$error) {
		$this->moduleName = $moduleName;
		$presenterType = substr($className,0,strpos($className,"_"));
		self::$mainTemplPath = BASE_PATH . "/views/".$presenterType;
		self::$templates = BASE_PATH . "/views/$presenterType/".$this->moduleName."/templates/";
		$this->templateFile = preg_replace("/^".$presenterType."_".$this->moduleName."_/","",$className)."Templ.php";
		$this->data = $data;
		$this->error = $error;
	}
	
	public function display(){
		$dispData = $this->data;
		$pgErr = $this->error;
		ob_start();
		include(self::$templates . $this->templateFile);
		$mainContents = ob_get_contents();
		ob_clean();
		include(self::$mainTemplPath . "/mainTempl.php");		
	}
	
	public function __set($key,$val){
		$this->$key = $val;
	}
	
	public function __get($val){
		return $this->$val;
	}
}
