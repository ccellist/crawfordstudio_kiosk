<?php
/*
 * Created on Dec 23, 2011
 *
 * author: arturo
 * 
 */

class html_SQLView_default extends presenter {
	public function __construct($data,$modName,$error) {
		parent::__construct(__CLASS__, $data, $modName, $error);
	}
	
	public function display(){
		print <<<DISPLAY
<html>
<head>
<title>
SQL dump
</title>
<style>
td {
font-family: sans;
font-size: 1em;
padding: 1px 5px;
border: solid black 1px;
}
</style>
<script type="text/javascript" language="javascript">
function refreshMe()
{
	window.location.reload();
}
</script>
</head>
<body onload="setTimeout('refreshMe()',5000)">
<table>

DISPLAY
;

foreach ($this->data as $row) {
	$count = 0;
	print "<tr>\n	";
	foreach ($row as $col) {
		if (($count == 1) && (strpos($col,"select") !== false) && (strpos($col,"delete") === false)){
			$output = "<a href='index.php?module=SQLView&action=viewQry&qry=$col' target='_blank'>" . urldecode($col) . "</a>";
		}
		else {
			$output = urldecode($col);
		}
		print "<td>\n		$output\n	</td>";
		$count++;
	}
	print "</tr>\n";
}
	print <<<DISPLAY
</table>
</body>
</html>

DISPLAY
;
	}
}