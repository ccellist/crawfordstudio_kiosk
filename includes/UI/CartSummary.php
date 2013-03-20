<?php
/*
 * Created on Nov 14, 2011
 *
 */

class UI_CartSummary implements Iface_HTML {
	private $html;
	private $css;
	private $js;
	
	public function __construct() {
		$this->buildCartSummary();
	}
	
	public function getHtml() {
		return $this->js . "\n\n" . $this->css . "\n\n" . $this->html;
	}
	
	public function setHtml($html){
		$this->html = $html;
	}
	
	public function setJS($js){
		$this->js = $js;
	}
	
	public function setCSS($css){
		$this->css = $css;
	}
	
	private function buildCartSummary() {
		$index_url = INDEX_URL;
		$session = SessionTool::getSession();
		if (!$session->cartid) { $cartid= 0; } else { $cartid= $session->cartid;  }
		$this->js = <<<JS
		<script type="text/javascript" language="javascript">
<!--
$(function()
	{
		getCartDetails();
	});

function viewCart()
{
	window.location="/index.php?module=Cart";	
}

function getCartDetails()
{
	$.post(
			"/index.php?module=Cart&action=getCartInfo&view=ajax",
	{
		cartid: $cartid
	},
	function(data, textStatus)
	{
		$("#cart_item_qty").text(data);
	});
}

-->
</script>

JS
;

		$this->html = <<<HTML
<div id='CartDisplay'>
	<div id='itemsInCart'>
		Items in your cart:&nbsp;<span id='cart_item_qty'></span>
		<input type='button' value='View cart' onclick='javascript:viewCart()' />
	</div>
<!--
	<div id='viewCart'>
		
	</div>-->
</div>

HTML
;
	}
}
?>
