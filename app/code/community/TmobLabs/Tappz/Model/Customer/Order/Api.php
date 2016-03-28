<?php

class TmobLabs_Tappz_Model_Customer_Order_Api extends Mage_Sales_Model_Order_Api
{
    public function getList($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

        $filter = array('customer_id' => $customer->getId());

        $orderCollection = $this->items($filter);

        $result = array();
        foreach ($orderCollection as $order) {
            $status = $order['status'];
            $state = $order['state'];
            if($status != 'canceled' && $state != 'canceled') {
                $result[] = $this->prepareOrder($order);
            }
        }
        return $result;
    }

    public function info($orderId)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);
        if (!$order->getId()) {
            $this->_fault('not_exists');
        }
        $result = $this->_getAttributes($order, 'order');
        $addressApi = Mage::getSingleton('tappz/Customer_Address_Api');
        $result['items'] = array();
        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }
            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        $result['currency'] = $order->getOrderCurrency()->getCode();
        $result['shipping_address'] = $addressApi->get($order->getShippingAddress()->getCustomerAddressId());
        $result['billing_address'] = $addressApi->get($order->getBillingAddress()->getCustomerAddressId());
        return $this->prepareOrder($result);
    }

    protected function prepareOrder($order)
    {
        $decimalDivider = Mage::getStoreConfig('tappz/general/decimalSeparator');
        $thousandDivider = Mage::getStoreConfig('tappz/general/groupSeparator');
        $lineAverageDeliveryDaysAttributeCode = Mage::getStoreConfig('tappz/basket/averagedeliverydaysattributecode');
        $catalogApi = Mage::getSingleton('tappz/Catalog_Api');
        $result = array();
        $result['id'] = null;
        $result['currency'] = null;
        $result['orderDate'] = null;
        $result['shippingStatus'] = null;
        $result['paymentStatus'] = null;
        $result['ipAddress'] = null;
        $result['itemsPriceTotal'] = null;
        $result['discountTotal'] = null;
        $result['subTotal'] = null;
        $result['shippingTotal'] = null;
        $result['total'] = null;
        $result['delivery'] = array();
        $result['delivery']['shippingAddress'] = $order['shipping_address'] ? $order['shipping_address'] : null;
        $result['delivery']['billingAddress'] = $order['billing_address'] ? $order['billing_address'] : null;
        $result['delivery']['shippingMethod'] = array();
        $result['delivery']['shippingMethod']['id'] = null;
        $result['delivery']['shippingMethod']['displayName'] = null;
        $result['delivery']['shippingMethod']['price'] = null;
        $result['payment'] = array();
        $result['payment']['accountNumber'] = null;
        $result['payment']['bankCode'] = null;
        $result['payment']['type'] = null;
        $result['payment']['displayName'] = null;
        $result['payment']['cashOnDelivery'] = null;
        $result['payment']['creditCard'] = null;
        $result['lines'] = array();
        $result['id'] = $order['increment_id'];
        $result['orderDate'] = date_format(date_create($order['created_at']), 'd/m/Y');
        $result['shippingStatus'] = $order['status'];
        $result['ipAddress'] = $order['remote_ip'];
        $result['itemsPriceTotal'] = number_format($order['base_subtotal'], 2, $decimalDivider, $thousandDivider);
        $result['discountTotal'] = number_format($order['discount_amount'], 2, $decimalDivider, $thousandDivider);
        $result['subTotal'] = number_format($order['subtotal'], 2, $decimalDivider, $thousandDivider);
        $result['shippingTotal'] = number_format($order['shipping_amount'], 2, $decimalDivider, $thousandDivider);
        $result['total'] = number_format($order['grand_total'], 2, $decimalDivider, $thousandDivider);
        $result['delivery']['shippingMethod']['id'] = $order['shipping_method'];
        $result['delivery']['shippingMethod']['displayName'] = $order['shipping_description'];
        $result['delivery']['shippingMethod']['price'] = $order['shipping_amount'];
        $result['payment']['methodType'] = $order['payment']['method']; 
        $result['payment']['accountNumber'] = '**** **** **** ' . $order['payment']['cc_last4'];
        $result['payment']['bankCode'] = $order['payment']['cc_type'];
        if ($result['payment']['methodType'] == 'paypal_express') {
            $result['payment']['methodType'] = 'PayPal';
            $result['payment']['type'] = 'PayPal';
        } elseif ($result['payment']['methodType'] == 'checkmo') {
            $result['payment']['methodType'] = 'MoneyTransfer';
            $result['payment']['type'] = 'Money Transfer';
        } elseif ($result['payment']['methodType'] == 'cashondelivery') {
            $result['payment']['methodType'] = 'CashOnDelivery';
            $result['payment']['type'] = 'Cash on Delivery';
        } else {
            $result['payment']['methodType'] = 'CreditCard';
            $result['payment']['type'] = 'Credit Card';
        }
        foreach ($order['items'] as $item){
            $line = array();
            $line['productId'] = $item['product_id'];
            $line['product'] = $catalogApi->getProduct($item['product_id']);
            $line['quantity'] = $item['qty_ordered'];
            $line['price'] = number_format($item['price_incl_tax'], 2, $decimalDivider, $thousandDivider);
            $line['priceTotal'] = number_format($item['row_total_incl_tax'], 2, $decimalDivider, $thousandDivider);
            $line['averageDeliveryDays'] = $item[$lineAverageDeliveryDaysAttributeCode];
            $line['variants'] = array();

            $result['lines'][] = $line;
        }
        $result['currency'] =  $order['currency'];
        return $result;
    }
}