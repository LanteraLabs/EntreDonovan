<?php

class Entredonovan_EdForm_Model_EdFormOrders extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init("edform/edformorders");
    }


    public function emailCaOrderSummary($orderId, $to = array()) {
        $storeId 	    = Mage::app()->getStore()->getStoreId();
        $from 		    = Mage::app()->getStore()->getFrontendName()."<br/>".Mage::getStoreConfig('general/store_information/phone')."<br/>".nl2br(Mage::getStoreConfig('general/store_information/address'));
        $order          = Mage::getModel('edform/edformorders')->load($orderId);
        $orderParms     = json_decode($order->getOrderParams(), true);
        $thisDir        = dirname(__FILE__);
        $optionOrders   = explode(",",$orderParms['optionOrders']);
        $orderItems     = '';
        $sno            = 0;
        foreach ($optionOrders as $junk => $itemType) {
            $sno++;
            $tmp            = explode("-", $itemType);
            $itemType       = $tmp[0];
            $itemIndex      = $tmp[2] - 1;
            if ('index' == $itemType || 'preview' == $itemType) {
                $itemType   = $tmp[1];
                $itemIndex  = $tmp[3] - 1;
            }
            
            $gItemIndex     = $sno - 1;
            $itemTemplate   = file_get_contents($thisDir.'/Templates/'.$itemType.'.html');
            $replaces       = array('here' => 'there');

            preg_match_all('/{{([^}]+)}}/', $itemTemplate, $matches);
            $variables      = $matches[1];
            foreach ($variables as $junk => $variable) {
                if ('sectionPrice' == $variable) {
                    $itemIndex  = $gItemIndex;
                }

                $data       = '';
                if (isset($orderParms[$variable][$itemIndex])) {
                    $data   = $orderParms[$variable][$itemIndex];
                }
                $itemTemplate = str_replace("{{".$variable."}}", $data, $itemTemplate);
            }
            $orderItems .= $itemTemplate;            
        }
        $orderParms['orderItems']   = $orderItems;

        $summaryTemplate    = file_get_contents($thisDir.'/Templates/orderSummary.html');
        preg_match_all('/{{([^}]+)}}/', $summaryTemplate, $matches);
        $variables      = $matches[1];
        foreach ($variables as $junk => $variable) {
            
            $data       = '';
            if (isset($orderParms[$variable])) {
                $data   = $orderParms[$variable];
            }
            $summaryTemplate = str_replace("{{".$variable."}}", $data, $summaryTemplate);
        }

        $emailTemplateVariables                 = array();
        $emailTemplateVariables['from']			= $from;
        $emailTemplateVariables['orderDetails'] = $summaryTemplate;
        $emailTemplateVariables['orderData']    = $orderParms;
        $emailTemplateVariables['orderId']      = $orderParms['orderId'];
        if (!isset($to[0]['email']) || !isset($to[0]['name'])) {
            $userId                         = $order->getData('user_id');
            $user                           = Mage::getModel('edform/edformusers')->load($userId);
            $to[0]['email']                 = $user->getData('email');
            $to[0]['name']                  = $user->getData('contact_firstname').' '.$user->getData('contact_lastname');
            $parentId                       = $user->getData('parent_id');
            if (is_numeric($parentId)) {
                $user                       = Mage::getModel('edform/edformusers')->load($parentId);
                $to[1]['email']             = $user->getData('email');
                $to[1]['name']              = $user->getData('contact_firstname').' '.$user->getData('contact_lastname');
            }
        }

        foreach ($to as $index => $receiverInfo) {
            $name                           = $receiverInfo['name'];
            $email                          = $receiverInfo['email'];
            $emailTemplateVariables['name'] = $name;
            
            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($email, $name);
            $mailer->addEmailInfo($emailInfo);
            $templateId = Mage::getStoreConfig('edform/email/template_order_summary', $storeId);
            if (!is_numeric($templateId)) {
                $templateId 	= "edform_order_summary";
            }
            
            //$mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
            $mailer->setSender(array('email'=>(string) 'info@entredonovan.com','name'=> (string)'entreDonovan'));
            $mailer->setStoreId($storeId);
            $mailer->setTemplateId($templateId);
            $mailer->setTemplateParams($emailTemplateVariables);
            $mailer->send();

        }

        
    }
}
?>