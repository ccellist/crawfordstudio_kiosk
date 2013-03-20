<?php
$order = $dispData;
if ($order instanceof Order) {
    $orderItems = $order->orderItems;
    $orderService = new OrderService();
    $tableContents = "";

    foreach ($orderItems as $orderItem) {
        $tableContents .= "<tr id=\"orderRow_" . $orderItem->photo->photoId . "\">\n";
        $tableContents .= " <td><input id=\"" . $orderItem->photo->photoId . "\" type=\"checkbox\" /></td>\n";
        $tableContents .= sprintf(" <td>%s</td>\n", $orderItem->photo->photoName);
        $tableContents .= sprintf(" <td>%s</td>\n", $orderService->getPhotoQuantityByPhotoId($order, $orderItem->photo->photoId));
        $tableContents .= sprintf(" <td>%s</td>\n", money_format("%.2n", $orderItem->photo->photoPrice));
        $tableContents .= "</tr>\n";
    }
} else {
    $tableContents = $order;
}
?>
<script type="text/javascript">
    function removeItems(){
        var items = "";
        $("input:checked").each(function(){
            items += this.id + " ";
        });
        var tmp = items.toString().trim().split(" ");
        items = Array(tmp).join(",");
        $.ajax({
            type: 'POST',
            data: { items: items, orderId: "<?php print $order->orderId; ?>" },
            url: "/index.php?module=Order&action=removeSelectedItems&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data                        
                reloadOrderDetails();
                $("input:checked").each(function(){
                    var id = this.id;
                    $("#orderRow_" + id).hide();
                    $("#orderRow_" + id).empty();
                });
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }
    /*function clearOrder(){
        $.ajax({
            type: 'GET',
            url: "/index.php?module=Order&action=clearAllItems&qry=" + orderId + "&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data                        
                reloadOrderDetails();
                $('#cartSummary').empty();
                $('#cartSummary').append(data);
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }*/
    function showThumbnailView(){
        $.ajax({
            type: 'GET',
            url: "/index.php?module=Order&action=cartThumbnailView&qry=" + orderId + "&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data                        
                reloadOrderDetails();
                $('#cartSummary').empty();
                $('#cartSummary').append(data);
                $('#btn_thumbnailView').attr("value", "Summary view");
                $('#btn_thumbnailView').attr("onclick", "showSummaryView()");
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }
</script>
<div id="mainContents">
    <form id="checkoutForm" action="/index.php?module=Order&action=checkOut&view=popup" method="post">
        <div class="buttonBar">
            <input type="button" value="Remove items" onclick="removeItems()"/>
            <!--<input type="submit" value="Checkout"/>-->
            <input type="button" value="Empty cart" onclick="clearOrder('mainContents')"/>
            <input id="btn_thumbnailView"type="button" value="Thumbnail view" onclick="showThumbnailView()"/>
        </div>
        <table id="cartSummary">
            <tbody>
                <tr>
                    <th></th>
                    <th>Photo Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                <?php print $tableContents; ?>
            </tbody>
        </table>
    </form>
</div>