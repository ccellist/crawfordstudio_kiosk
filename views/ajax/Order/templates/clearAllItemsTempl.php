<?php
$orderService = new OrderService();
$order = $orderService->getOrderById($dispData);
$orderItems = $order->orderItems;

foreach ($orderItems as $orderItem) {
    $tableContents .= "<tr>\n";
    $tableContents .= " <td><input type=\"checkbox\" /></td>\n";
    $tableContents .= sprintf(" <td>%s</td>\n", $orderItem->photo->photoName);
    $tableContents .= sprintf(" <td>%s</td>\n", $orderService->getPhotoQuantityByPhotoId($order, $orderItem->photo->photoId));
    $tableContents .= sprintf(" <td>%s</td>\n", money_format("%.2n", $orderItem->photo->photoPrice));
    $tableContents .= "</tr>\n";
}
?>
<tbody>
    <tr>
        <th></th>
        <th>Photo Name</th>
        <th>Qty</th>
        <th>Price</th>
    </tr>
    <?php print $tableContents; ?>
</tbody>