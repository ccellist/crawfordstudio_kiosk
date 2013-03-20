<script type="text/javascript">
    function toggleRow(rowId){
        if ($('#' + rowId).css("visibility") == "hidden"){
            $('#' + rowId).show(500).css({visibility: '', display: 'table-row'});
        } else {
            $('#' + rowId).hide(500).css({visibility: 'hidden', display: ''});
        }
    }
    function getOrderDetail(orderId){
        $.ajax({
            type: 'GET',
            url: "/index.php?module=Order&action=showOrderSummary&qry=" + orderId + "&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data
                var contents = "<div style=\"padding: 10px 10px 0px 10px\">\n" + data + "</div>\n";
                showPopup("Order number " + orderId, contents, 200, 300, 90);
                $('#popupContents').css("background-color", "white");
            },
            error:function(){
                // failed request; give feedback to user
                showPopup("Order number " + orderId, "Error connecting to server.", 200, 300, 90);
            }
        });
    }
    function cancelOrder(orderId, rowId){
        if (confirm("Are you sure you want to delete this order? This cannot be undone.")){            
            $.ajax({
                type: 'POST',
                data: { orderId: orderId },
                url: "/index.php?module=Order&action=deleteOrder&view=ajax",
                beforeSend:function(){
                    // this is where we append a loading image
                    $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                },
                success:function(data){
                    // successful request; do something with the data
                    if (data == "true") {
                        $("#" + rowId).html("");
                    }
                },
                error:function(){
                    // failed request; give feedback to user
                    showPopup("Order number " + orderId, "Error connecting to server.", 200, 300, 90);
                }
            });
        }
    }
    function finalizeOrder(orderId, elemId){
        if (confirm("Are you sure you want to finalize the order and move it to the archive? This cannot be undone.")){            
            $.ajax({
                type: 'POST',
                data: { orderId: orderId },
                url: "/index.php?module=Order&action=finalizeOrder&view=ajax",
                beforeSend:function(){
                    // this is where we append a loading image
                    $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                },
                success:function(data){
                    // successful request; do something with the data
                    if (data == "true") {
                        $("#" + elemId).prop("disabled", "true");
                        $("#btn_dtl_" + orderId).prop("disabled", "true");
                        $("#btn_cancel_" + orderId).prop("disabled", "true");
                        $("#orderDetail_" + orderId).css("background-color","silver");
                    }
                },
                error:function(){
                    // failed request; give feedback to user
                    showPopup("Order number " + orderId, "Error connecting to server.", 200, 300, 90);
                }
            });
        } else {
            $("#" + elemId).attr("checked", false);
        }
    }
    function deleteCustomer(customerId){
        if (confirm("Are you sure you want to delete this customer?")){
            $.ajax({
                type: 'POST',
                data: { customerId: customerId },
                url: "/index.php?module=Customer&action=deleteCustomer&view=ajax",
                beforeSend:function(){
                    // this is where we append a loading image
                    $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                },
                success:function(data){
                    // successful request; do something with the data
                    if (data == "true") {
                        $("#customerRec_" + customerId).html("");
                        $("#customerRec_" + customerId).hide(500,"");
                    } else {
                        alert("Could not delete customer. Customer may have have open orders. Delete those first.");
                    }
                },
                error:function(){
                    // failed request; give feedback to user
                    showPopup("Delete order '" + orderId + "' failed.", "Error connecting to server.", 200, 300, 90);
                }
            });
        }
    }
    function showOrderList(customerId){
        $.ajax({
            type: 'GET',
            url: "/index.php?module=Customer&action=getOrderList&qry=" + customerId + "&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data
                showPopup("Orders for customer id '" + customerId + "'", data, 533, 137, 90);
                var tableHeight = $("#orderListTable").css("height");
                $("#popup").css("height", parseInt(tableHeight) + 65 + "px");
            },
            error:function(){
                // failed request; give feedback to user
                showPopup("Orders for customer id '" + customerId + "'", "Error connecting to server.", 200, 300, 90);
            }
        });
    }
    function deleteOrder(orderId){
        $.ajax({
            type: 'POST',
            data: { orderId: orderId },
            url: "/index.php?module=Order&action=deleteOrder&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data
                if (data == "true") {
                    $('#popupContents').html("<div style='margin: 0 auto; width: 22%'>Order deleted!<br><input type='button' value='Close' onclick='hidePopup(\"location.reload\")'></div>");
                }
            },
            error:function(){
                // failed request; give feedback to user
                showPopup("Order number " + orderId, "Error connecting to server.", 200, 300, 90);
            }
        });
    }
</script>
<h1>Admin Console</h1>
<?php
print "<h2>Orders Awaiting Checkout</h2><br>\n";
print $dispData['pendingOrders'];
print "<br>";
print "<h2>Pending Copy Jobs</h2><br>\n";
print $dispData['pendingJobs'];
print "<br>";
print "<h2>Customer List</h2><br>\n";
print "Customers with <img src=\"/includes/images/yellowstar.png\"> have orders in the database and cannot be deleted from this list.<br>\n";
print "Click on the star to view them.<br>\n";
print $dispData['customers'];
?>
