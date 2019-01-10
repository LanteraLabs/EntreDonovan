<?php

class Entredonovan_EdForm_Model_EdFormUsers extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init("edform/edformusers");
    }

    public function emailCaCreateNotification($user, $password, $to = array()) {
        $helper 	    = Mage::helper("edform");
        $storeId 	    = Mage::app()->getStore()->getStoreId();
        $from 		    = Mage::app()->getStore()->getFrontendName()."<br/>".Mage::getStoreConfig('general/store_information/phone')."<br/>".nl2br(Mage::getStoreConfig('general/store_information/address'));
        $thisDir        = dirname(__FILE__);
        
        
        $emailTemplateVariables                 = array();
        $emailTemplateVariables['from']			= $from;
        $emailTemplateVariables['username']     = $user->getUsername();
        $emailTemplateVariables['firstname']    = $user->getContactFirstname();
        $emailTemplateVariables['userId']       = $user->getUserId();
        $emailTemplateVariables['loginUrl']     = $helper->getModuleBaseUrl();
        $emailTemplateVariables['password']     = $password;
        if (!isset($to[0]['email']) || !isset($to[0]['name'])) {
            $userId                         = $user->getData('user_id');
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
            $templateId = Mage::getStoreConfig('edform/email/template_new_ca', $storeId);
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