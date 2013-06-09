<?php
/*
 * Created on Dec 19, 2011
 *
 * author: arturo
 * 
 */
?>

<script type="text/javascript">
function delUser(userid)
{
	$.post(
			"/index.php?module=UserMgmt&action=delete&view=ajax",
	{
		uid: userid
	},
	function (data, textStatus) 
	{
		showPopup(data);
	})
	.error(
		function() 
		{
			showPopup("Error deleting user.");
		}
	);
}
</script>

<ul>
<?php
foreach ($dispData as $row){
	$uid = $row['uid'];
	$user = $row['username'];
	print <<<HTML
	<li><a href="javascript:void(0)" onclick="delUser($uid)">$user</a></li>
	
HTML
;
}
?>
</ul>
