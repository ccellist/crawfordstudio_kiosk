<?php
/*
 * Created on Dec 19, 2011
 *
 * author: arturo
 * 
 */
?>

<script type="text/javascript">
function toggleAdmin(userid,toggle)
{
	$.post(
		"/index.php?module=UserMgmt&action=togAdmin&view=ajax",
	{
		uid: userid,
		val: toggle
	},
	function (data, textStatus)
	{
		showPopup("Result",data,100,80,50,"window.location.reload");
	})
	.error(
		function()
		{
			showPopup("Error","Error toggling user permissions.",100,80,50);
		}
	);
}

function chgPwd(userid)
{
	$.post(
		"/index.php?module=UserMgmt&action=resetPwd&view=ajax",
	{
		uid: userid
	},
	function (data, textStatus)
	{
		showPopup("Result",data,100,80,50);
	})
	.error(
		function()
		{
			showPopup("Error","Error changing user password.",100,80,50);
		}
	);
	
}
function delUser(userid)
{
	if (confirm("This will permanently delete this user and all associated data.\nProceed?")){
		$.post(
				"/index.php?module=UserMgmt&action=delete&view=ajax",
		{
			uid: userid
		},
		function (data, textStatus) 
		{
			showPopup("Result", data,100,80,50, "window.location.reload");
		})
		.error(
			function() 
			{
				showPopup("Error","Error deleting user.",100,80,50,"window.location.reload");
			}
		);
	}	
}
</script>
<h2>User listing</h2>
<ul>
<?php
foreach ($dispData as $row){
	$uid = $row['uid'];
	$user = $row['username'];
	$star = "";
	if ($row['admin_user']=="1") {
		$star = "*";
		$makeAdmin = "<a href=\"javascript:void(0)\" onclick=\"toggleAdmin($uid,0)\">demote admin</a>";
	} else {
		$makeAdmin = "<a href=\"javascript:void(0)\" onclick=\"toggleAdmin($uid,1)\">promote admin</a>";
	}
	print <<<HTML
	<li>$star$user | <a href="javascript:void(0)" onclick="delUser($uid)">delete</a> | <a href="javascript:void(0)" onclick="chgPwd($uid)">change password</a> | $makeAdmin</li>
	
HTML
;
}
?>
</ul>
