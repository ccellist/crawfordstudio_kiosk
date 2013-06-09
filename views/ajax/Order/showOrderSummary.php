<?php

class ajax_Order_showOrderSummary extends presenter implements Iface_Presenter {

    public function __construct($data, $modName, $error) {
        parent::__construct(__CLASS__, $data, $modName, $error);
    }

    public function display() {
        $order = $this->data;
        if ($order != "Empty cart") {
            $dispData['orderTotal'] = $order->orderTotal;
            $dispData['orderItems'] = $order->orderItems;
            $dispData['orderSubtotal'] = $order->orderSubtotal;
            $dispData['orderDiscount'] = $order->orderDiscount;
            $dispData['orderTax'] = $order->orderTaxAmt;
        } else {
            $dispData = $this->data;
        }
        $pgErr = $this->error;
        ob_start();
        include(self::$templates . $this->templateFile);
        $mainContents = ob_get_contents();
        ob_clean();
        include(self::$mainTemplPath . "/mainTempl.php");
    }

}