<?php

class html_StoreAdmin_default extends presenter implements Iface_Presenter {

    public function __construct($data, $modName, $error) {
        parent::__construct(__CLASS__, $data, $modName, $error);
    }

    public function display() {
        $customerService = new CustomerService();
        $customerList = $this->data['customerList'];
        $pendingJobs = $this->data['pendingJobs'];
        $pendingOrders = $this->data['pendingOrders'];
        $alt = "";
        $hasOrders = "";

        $customerLayout = "<table>\n<tbody>\n";
        $customerLayout .= "<tr>\n<th>First Name</th>\n<th>Last Name</th>\n<th>Email</th>\n<th>Primary Phone</th>\n<th></th>\n</tr>";
        $count = 0;
        foreach ($customerList as $customer) {
            if ($count % 2 == 0) {
                $alt = " class=\"alt\" ";
            } else {
                $alt = "";
            }
            if ($customerService->customerHasOrders($customer->customerId)){
                $hasOrders = sprintf("<img class=\"link\" src=\"/includes/images/yellowstar.png\" onclick=\"showOrderList(%s)\">", $customer->customerId);
            } else {
                $hasOrders = "";
            }
            $customerLayout .= sprintf("<tr$alt id=\"customerRec_%s\">\n<td>%s</td>\n<td>%s</td>\n<td>%s</td><td>%s</td>\n<td><img class=\"link\" src=\"includes/images/trash.png\" onclick=\"deleteCustomer(%s)\">$hasOrders</td>\n</tr>\n", 
                    $customer->customerId, $customer->firstName, $customer->lastName, $customer->email, $customer->primaryPhone, $customer->customerId);
            $count++;
        }
        $customerLayout .= "</tbody>\n</table>\n";

        $pendingJobLayout = "<table>\n<tbody>\n";
        $pendingJobLayout .= "<tr>\n<th>Create Time</th>\n<th>Command</th>\n</tr>\n";
        $count = 0;
        foreach ($pendingJobs as $pendingJob) {
            if ($count % 2 == 0) {
                $alt = " class=\"alt\" ";
            } else {
                $alt = "";
            }
            $pendingJobLayout .= sprintf("<tr$alt>\n<td>%s</td>\n<td>%s</td>\n</tr>\n", $pendingJob->createTime->format('Y-m-d H:i:s'), $pendingJob->command);
            $count++;
        }
        $pendingJobLayout .= "</tbody>\n</table>\n";

        $customerId = 0;
        $count = 0;
        $customer = null;
        $orderTable = false;
        $pendingOrdersLayout = "<table>\n<tbody>\n";
        $pendingOrdersLayout .= "<tr>\n<th></th>\n<th>First Name</th>\n<th>Last Name</th>\n<th>Email</th>\n<th>Primary Phone</th>\n</tr>\n";
        foreach ($pendingOrders as $pendingOrder) {
            if ($count % 2 == 0) {
                $alt = " class=\"alt\" ";
            } else {
                $alt = "";
            }
            if ($customerId != $pendingOrder->customerId) {
                $count++;
                $customerId = $pendingOrder->customerId;
                $customer = $customerService->getCustomerById($customerId);
                if ($orderTable) {
                    $pendingOrdersLayout .= "</tbody>\n</table>\n</td>\n</tr>\n";
                    $orderTable = false;
                }
                $pendingOrdersLayout .= sprintf("<tr class=\"alt\">\n<td><a href=\"javascript:void(0)\" onclick=\"toggleRow('orderDetail_$customerId')\">X</a></td>\n<td>%s</td>\n<td>%s</td>\n<td>%s</td>\n<td>%s</td>\n</tr>", $customer->firstName, $customer->lastName, $customer->email, $customer->primaryPhone);
                $pendingOrdersLayout .= "<tr id=\"orderDetail_$customerId\">\n<td></td>\n<td colspan=3>\n<table>\n<tbody>\n";
                $pendingOrdersLayout .= "<tr style=\"background:gainsboro\">\n<th>Order Id</th>\n<th>Picture Qty</th>\n<th>Sale Total</th>\n<th></th\n</tr>\n";
                $orderTable = true;
            }
            $orderId = $pendingOrder->orderId;
            $events["onclick"] = "getOrderDetail(" . $pendingOrder->orderId . ")";
            $detailButton = new UI_Button("button", "Order details", "btn_dtl_" . $pendingOrder->orderId, "", "dtl_btn", $events);
            $showDetailButtonHtml = $detailButton->getButtonHtml();
            $events2["onclick"] = "cancelOrder(" . $pendingOrder->orderId . ", 'orderDetail_$orderId')";
            $cancelOrderButton = new UI_Button("button", "Cancel order", "btn_cancel_" . $pendingOrder->orderId, "", "cncl_btn", $events2);
            $cancelOrderButtonHtml = $cancelOrderButton->getButtonHtml();
            $finalizeOrder = "Finalize order <input type=\"checkbox\" id=\"chk_finalize_$orderId\" onclick=\"finalizeOrder($orderId, this.id)\">";
            $pendingOrdersLayout .= sprintf("<tr id=\"orderDetail_$orderId\">\n<td>%s</td>\n<td>%s</td>\n<td>%s</td>\n<td>$showDetailButtonHtml$cancelOrderButtonHtml$finalizeOrder</td>\n</tr>\n", $pendingOrder->orderId, count($pendingOrder->orderItems), $pendingOrder->orderTotal);
        }
        if ($orderTable) {
            $pendingOrdersLayout .= "</tbody>\n</table>\n</td>\n</tr>\n";
            $orderTable = false;
        }
        $pendingOrdersLayout .= "</tbody>\n</table>\n";

        $dispData['pendingOrders'] = $pendingOrdersLayout;
        $dispData['pendingJobs'] = $pendingJobLayout;
        $dispData['customers'] = $customerLayout;

        $pgErr = $this->error;
        ob_start();
        include(self::$templates . $this->templateFile);
        $mainContents = ob_get_contents();
        ob_clean();
        include(self::$mainTemplPath . "/mainNoSidebarsTempl.php");
    }

}