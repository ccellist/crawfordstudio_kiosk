<?php
$tableContents = "";
foreach ($dispData as $order) {
    $orderId = $order->orderId;
    $subTotal = money_format('%=5.2n', $order->orderSubtotal);
    $discount = money_format('%=5.2n', $order->orderDiscount);
    $tax = money_format('%=5.2n', $order->orderTaxAmt);
    $total = money_format('%=5.2n', $order->orderTotal);
    $dateCreated = date("Y/m/d", strtotime($order->dateCreated));
    $readyForCheckout = $order->isPending;

    $tableContents .= <<<TABL
   
        <tr>
             <td>$subTotal</td>
             <td>$discount</td>
             <td>$tax</td>
             <td>$total</td>
             <td>$dateCreated</td>
             <td style="text-align:center">$readyForCheckout</td>
             <td><img class="link" src="/includes/images/trash.png" onclick="deleteOrder($orderId)"></td>
        </tr>
   
TABL
    ;
}
?>
<table id="orderListTable" style="background-color:whitesmoke">
    <tbody>
        <tr>
            <th>Subtotal</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Order Total</th>
            <th>Date created</th>
            <th>Ready for checkout</th>
            <th></th>
        </tr>
        <?php print $tableContents; ?>
    </tbody>
</table>
