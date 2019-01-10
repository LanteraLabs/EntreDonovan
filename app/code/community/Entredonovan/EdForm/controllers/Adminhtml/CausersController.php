<?php
 
class Entredonovan_EdForm_Adminhtml_CausersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
		
		$this->_title($this->__('Customer'))->_title($this->__('Commercial Account (CA) Users'));
		
		/*
		$this->loadLayout()
            ->_setActiveMenu('customer/customer')
            ->_addBreadcrumb(
                Mage::helper('edform')->__('Customer'),
                Mage::helper('edform')->__('Commercial Account (CA) Users')
            )->renderLayout();
		return;
		*/
        
        $this->loadLayout();
        $this->_setActiveMenu('customer/customer');
        $this->_addContent($this->getLayout()->createBlock('edform/adminhtml_causers'));
        $this->renderLayout();
    }

    public function editAction() {

		$id  = $this->getRequest()->getParam('id');
		$model = Mage::getModel('edform/edformusers')->load($id);

		if ($model->getId() || $salestaffId == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('causers_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('customers/causers');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit CA'), Mage::helper('adminhtml')->__('Edit CA'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('edform/adminhtml_causers_edit'))->_addLeft($this->getLayout()->createBlock('edform/adminhtml_causers_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('edform')->__('CA does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function saveAction() {
		$params = $this->getRequest()->getParams();
		if (is_numeric($params['id'])) {
			$id = $params['id'];
			unset($params['username']);
			$model = Mage::getModel('edform/edformusers')->load($id);
			$model->setData($params);
			$model->setId($id)->save();
			$userId = $model->getUserId();
			$p = $params;

			if (true) {
				// physical address

				$pa = Mage::getModel('edform/edformaddresses');
				$allAddresses = $pa->getCollection()->addFieldToFilter('user_id', $model->getUserId());
				foreach ($allAddresses as $address) {
					$address->delete();
				}
				#print_r($p); exit;

				$physicalAddress = array();
				foreach ($p as $key => $value) {
					if (stristr($key, 'physical_')) {
						$pkey = str_replace('physical_', '', $key);
						$physicalAddress[$pkey] = $value;
					}
				}
				$physicalAddress['user_id'] 	= $userId;
				$pa = Mage::getModel('edform/edformaddresses');
				$pa->setData($physicalAddress);
				$pa->save();
				$physicalAddressId = $pa->getAddressId();

				// billing address
				if (isset($p['billing_same_as_physical'])) {
					$model->setData('billing_address_id', $physicalAddressId);
				} else {
					$billingAddress = array();
					foreach ($p as $key => $value) {
						if (stristr($key, 'billing_')) {
							$pkey = str_replace('billing_', '', $key);
							$billingAddress[$pkey] = $value;
						}
					}
					$billingAddress['user_id'] 	= $userId;
					$pa = Mage::getModel('edform/edformaddresses');
					$pa->setData($billingAddress);
					$pa->save();
					$billingAddressId = $pa->getAddressId();
					$model->setData('billing_address_id', $billingAddressId);
				}

				// shipping address
				if (isset($p['shipping_same_as_physical'])) {
					$model->setData('shipping_address_id', $physicalAddressId);
				} else {
					$shippingAddress = array();
					foreach ($p as $key => $value) {
						if (stristr($key, 'shipping_')) {
							$pkey = str_replace('shipping_', '', $key);
							$shippingAddress[$pkey] = $value;
						}
					}
					$shippingAddress['user_id'] 	= $userId;
					$pa = Mage::getModel('edform/edformaddresses');
					$pa->setData($shippingAddress);
					$pa->save();
					$shippingAddressId = $pa->getAddressId();
					$model->setData('shipping_address_id', $shippingAddressId);
				}


				$model->setData('physical_address_id', $physicalAddressId);
				$model->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('edform')->__('User Updated'));
		$this->_redirect('*/*/');

	}
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('edform/adminhtml_causers_grid')->toHtml()
        );
    }
 
    public function exportEdformCsvAction()
    {
        $fileName = 'ca_users.csv';
        $grid = $this->getLayout()->createBlock('entredonovan_edform/adminhtml_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportEdformExcelAction()
    {
        $fileName = 'ca_users.xml';
        $grid = $this->getLayout()->createBlock('entredonovan_edform/adminhtml_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	
	public function massApproveAction($newstatus = 1) {
		$userIds = $this->getRequest()->getParam('user_id');
		if(!is_array($userIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('edform')->__('Please select users(s).'));
		} else {
			try {
				$model 		= Mage::getModel('edform/edformusers');
				foreach ($userIds as $userId) {
					//$model->load($userId)->delete();
					$model->load($userId)->setApproved($newstatus)->save();
					if ($newstatus) { // approved
						$email = $model->getEmail();
						$fName = $model->getContactFirstname();
						$lName = $model->getContactLastname();
						$accountName = $model->getAccountName();
						$username		= $model->getUsername();
						$name = $fName . ' ' . $lName;
						$htmlP = 'Hello, ' . $accountName . ' ('.$username.'), 
						
	Your recent profile edits have been processed and are now effective. 
	If you have any questions, concerns, or suggestions, simply reply to this message. Someone will assist you promptly.
	 
	Your friends at entreDonovan';
						ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
						ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));
						$html = nl2br($htmlP);
						$mail = new Zend_Mail('utf-8');
						$mail->setSubject('=?utf-8?B?' . base64_encode('Profile Activated') . '?=');
						$mail->setBodyHTML($html);
						$mail->setFrom('info@entredonovan.com', 'entreDonovan');
						$mail->addTo($email, '=?utf-8?B?' . base64_encode($name) . '?=');
						try {
							$returnPathEmail = 'info@entredonovan.com';
							$mailTransport = new Zend_Mail_Transport_Sendmail("-f".$returnPathEmail);
							Zend_Mail::setDefaultTransport($mailTransport);
							$mail->send();
						} catch (Exception $e) {
							Mage::getSingleton('core/session')->addError('Unable to send e-mail to %s', $email);
						}
					}
				}
				if ($newstatus) {
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('edform')->__(
						'Total of %d record(s) were approved.', count($userIds)
						)
					);
				} else {
					Mage::helper('edform')->__(
					'Total of %d record(s) were unapproved.', count($userIds)
					);
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/index');
	}
	
	
	public function massUnapproveAction() {
		$this->massApproveAction("0");
	}
	
}
?>