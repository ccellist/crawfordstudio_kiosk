<?php
$olContents = "";
$buttons = "";
$session = SessionTool::getSession();

if (is_array($dispData)) {
    $orderTotal = $dispData['orderTotal'];
    $orderSubtotal = $dispData['orderSubtotal'];
    $orderDiscount = 0 - $dispData['orderDiscount'];
    if ($orderDiscount * -1 > $orderTotal)
        $orderDiscount = "(Special package price)";
    //$orderTax = $dispData['orderTax'];
    setlocale(LC_MONETARY, "en_US");
    foreach ($dispData['orderItems'] as $orderItem) {
        $olContents .= "<li>" . $orderItem->photo->photoName . "&nbsp;&nbsp;" . money_format("%.2n", $orderItem->photo->photoPrice) . "</li>\n";
    }
    if (is_array($dispData['orderItems'])) {
        //$buttons = "<input type=\"button\" value=\"Edit order\" onclick=\"editOrder()\"/>\n";
        if (count($dispData['orderItems']) > 0) {
            if ($session->prevAction == "showCart"){
                $buttons .= "<input type=\"button\" value=\"Checkout\" onclick=\"$('#checkoutForm').submit()\" />";
            } else {
                $buttons .= "<input type=\"button\" value=\"Proceed to checkout\" onclick=\"window.location='/index.php?module=Order&action=showCart'\" />";
            }
        }
        $buttons .= "<input type=\"button\" value=\"Clear order\" onclick=\"clearOrder()\"/>\n";
    }
    ?>
    <div id="orderDetails">
    <?php print $buttons; ?><br><br>
        Order details:
        <ol>
    <?php print $olContents; ?>
        </ol>
        Subtotal: <?php print money_format("%.2n", $orderSubtotal); ?><br>
        Discount: <?php if (is_numeric($orderDiscount)) {
        print money_format("%.2n", $orderDiscount);
    } else {
        print $orderDiscount;
    } ?><br>
        <hr>
        Order total: <?php print money_format("%.2n", $orderTotal); ?>
        <br>
        <br>
    </div>
            <?php
        } else {
            ?>
    <div id="orderDetails">
        <ol>
    <?php print $dispData; ?>
        </ol>
        Tax: <?php print money_format("%.2n", 0.00); ?>
        <hr>
        Order total: <?php print money_format("%.2n", 0.00); ?>
        <br>
        <br>
    </div>
    <?php
}
