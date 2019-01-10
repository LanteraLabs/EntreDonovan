<?php

class Entredonovan_EdForm_IndexController extends Mage_Core_Controller_Front_Action {

	public function mustLogin() {
		$helper 		= Mage::helper('edform');
		$helper->mustLogin();
	}
	
	public function indexAction() {
		$helper 		= Mage::helper('edform');
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		$isCaLoggedIn 	= $helper->isCaLoggedIn();
		if (!$isCaLoggedIn) {
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/login/');
			return;
		}
		$this->restrictApprove();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function restrictApprove() {
		$helper 		= Mage::helper('edform');
		if (!$helper->isUserApproved()) {
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getEditProfileUrl());
			return;
		}
	}


	public function previewAction() {
		$this->indexAction();
	}

	public function listPastOrdersAction() {
		$this->mustLogin();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('List Orders - Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function myAccountAction() {
		$this->mustLogin();
		$this->restrictApprove();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Account - Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function listDraftsAction() {
		$this->mustLogin();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('List Drafts - Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function draftResumeAction() {
		$this->mustLogin();
		$helper 		= Mage::helper('edform');
		$params			= Mage::app()->getRequest()->getParams();
		$progressId 	= $params['draft_id'];
		$userId 		= 0;
		if ($helper->isOrderEditing()) {
			Mage::getSingleton('core/session')->addError($this->__('Another order is already being edited.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getListDraftsUrl());
			return;
		}
		$loggedInCa = $helper->getLoggedInCa();
		if (isset($loggedInCa['user_id']) && is_numeric($loggedInCa['user_id'])) {
			$userId = $loggedInCa['user_id'];
		}
		$progressModel		= Mage::getModel('edform/edformprogress');
		$progressCollection = $progressModel
									->getCollection()
									->addFieldToFilter('user_id', $userId)
									->addFieldToFilter('progress_id', $progressId)
									;
		if (count($progressCollection)) {
			foreach ($progressCollection as $progressData) {
				$browserId 	= $progressData->getBrowserId();
				$helper->setEditingBrowserId($browserId);
				$helper->setTmpToken($browserId);
				Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getOrderUrl());
				return;
			}
		} else {
			Mage::getSingleton('core/session')->addError($this->__('Could not find the draft!'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getListDraftsUrl());
			return;
		}

	}

	public function deleteDraftAction() {
		$this->mustLogin();
		$helper 		= Mage::helper('edform');
		$params			= Mage::app()->getRequest()->getParams();
		$progressId 	= $params['draft_id'];
		$userId 		= $helper->getLoggedInCaUserId();

		$progressModel		= Mage::getModel('edform/edformprogress');
		$progressCollection = $progressModel
									->getCollection()
									->addFieldToFilter('user_id', $userId)
									->addFieldToFilter('progress_id', $progressId)
									;
		if (count($progressCollection)) {
			foreach ($progressCollection as $progressItem) {
				$progressItem->delete();
			}
			Mage::getSingleton('core/session')->addSuccess($this->__('A draft deleted!'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getListDraftsUrl());
			return;
		} else {
			Mage::getSingleton('core/session')->addError($this->__('No matching draft found.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getListDraftsUrl());
			return;
		}
	}

	public function listSubCaAction() {
		$this->mustLogin();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('List Sub-CA - Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function contactAction() {
		$this->mustLogin();
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Contact Us - Ed Order Form'));
		$this->getLayout()->getBlock('root')->setTemplate('page/empty.phtml');
		$this->renderLayout();
	}

	public function contactSaveAction() {
		$this->mustLogin();
		$helper 		= Mage::helper('edform');
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		$params			= Mage::app()->getRequest()->getParams();
		$storeId 		= Mage::app()->getStore()->getStoreId();
		//print_r($params); exit;
		$reqiredFields 	= array('name', 'email', 'phone', 'message');
		$allOk 			= true;
		foreach ($reqiredFields as $junk => $field) {
			if (!isset($params[$field])) {
				$allOk 	= false;
				break;
			}
		}

		if (!$allOk) {
			Mage::getSingleton('core/session')->addError("Required field missing!"); 
			Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getContactUrl());
			return;
		}

		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] 		= $params['name'];
		$emailTemplateVariables['email'] 		= $params['email'];
		$emailTemplateVariables['phone'] 		= $params['phone'];
		$emailTemplateVariables['message']		= nl2br($params['message']);

		$mailer = Mage::getModel('core/email_template_mailer');
		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo('info@entredonovan.com', 'entreDonovan');
		$mailer->addEmailInfo($emailInfo);
		$templateId = Mage::getStoreConfig('edform/email/template_contact', $storeId);
		if (!is_numeric($templateId)) {
			$templateId 	= "edform_contact";
		}
		
		
		$mailer->setSender(array('email'=>(string) 'info@entredonovan.com','name'=> (string)'entreDonovan'));
		$mailer->setStoreId($storeId);
		$mailer->setTemplateId($templateId);
		$mailer->setTemplateParams($emailTemplateVariables);
		$mailer->send();

		Mage::getSingleton('core/session')->addSuccess("Thank you for your message. We shall get back to you."); 
		Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getContactUrl());
		return;
	}
	
	public function logoutAction() {
		$helper 		= Mage::helper('edform');
		$helper->logout();
		Mage::getSingleton('core/session')->addSuccess("You have logged out successfully."); 
		Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/login');
		return;
	}
	
	public function loginAction () {
		$this->loadLayout();
		$this->renderLayout();
		//echo 'login page'; exit;
	}
	
	public function loginPostAction() {
		$helper 		= Mage::helper('edform');
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		$params			= Mage::app()->getRequest()->getParams();
		if (isset($params['login']['username']) && isset($params['login']['password'])) {
			$username 	= $params['login']['username'];
			$password 	= md5($params['login']['password']);
			
			$model 		= Mage::getModel('edform/edformusers');
			$users 		= $model->getCollection()
							->addFieldToFilter('username', $username)
							->addFieldToFilter('password', $password);
			
			
			foreach ($users as $user) {
				$userData 	= $user->getData();
				$approved 	= $userData['approved'];
				if (!$approved && false) {
					Mage::getSingleton('core/session')->addError($this->__('Your account is not yet approved!')); 
					Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/login');
					return;
				}
				$physicalAddress 	= $helper->getUserAddressData($user->getId(), 'physical');
				$billingAddress 	= $helper->getUserAddressData($user->getId(), 'billing');
				$shippingAddress 	= $helper->getUserAddressData($user->getId(), 'shipping');
				$userData['physical_address'] 	= $physicalAddress;
				$userData['billing_address']	= $billingAddress;
				$userData['shipping_address'] 	= $shippingAddress;
				Mage::getSingleton('core/session')->setLoggedInCa($userData);
				Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getMyAccountUrl());
				return;
			}
		}
		
		Mage::getSingleton('core/session')->addError($this->__('Invalid username or password')); 
		Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/login');
	}
	
	
	public function createAction () {
		$helper 	= Mage::helper("edform");
		$loggedIn 	= $helper->isMagentoAdminLoggedIn();
		if($loggedIn) {
			$this->loadLayout();
			$this->renderLayout();
		} else {
			$moduleBaseUrl	= $helper->getModuleBaseUrl();
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
		}
		//echo 'login page'; exit;
	}

	public function editProfileAction() {
		$this->mustLogin();
		$helper 		= Mage::helper('edform');
		$params			= Mage::app()->getRequest()->getParams();
		Mage::getSingleton('core/session')->unsEdformCreatePost();
		$preservePost 	= Mage::getSingleton('core/session')->getEdformCreatePost();
		if (empty($preservePost)) {
			$userId 			= $helper->getLoggedInCaUserId();
			$user 				= Mage::getModel('edform/edformusers')->load($userId);
			$p 					= $user->getData();
			$physicalAddressId	= $p['physical_address_id'];
			$billingAddressId	= $p['billing_address_id'];
			$shippingAddressId	= $p['shipping_address_id'];
			if (is_numeric($physicalAddressId)) {
				$pa = Mage::getModel('edform/edformaddresses')->load($physicalAddressId);
				$pa = $pa->getData();
				foreach ($pa as $key => $value) {
					$p['physical_address_'.$key] = $value;
				}
			}
			if (is_numeric($billingAddressId)) {
				$pa = Mage::getModel('edform/edformaddresses')->load($billingAddressId);
				$pa = $pa->getData();
				foreach ($pa as $key => $value) {
					$p['billing_address_'.$key] = $value;
					$p['has_billing_address']	= "1";
				}
			}
			if (is_numeric($shippingAddressId)) {
				$pa = Mage::getModel('edform/edformaddresses')->load($shippingAddressId);
				$pa = $pa->getData();
				foreach ($pa as $key => $value) {
					$p['shipping_address_'.$key] = $value;
					$p['has_shipping_address']	= "1";
				}
			}
			//echo "<pre>"; print_r($p); echo "</pre>";

			$rowObj = new Varien_Object();
			$rowObj->setData($p);
			Mage::getSingleton('core/session')->setEdformCreatePost($rowObj);
		}
		

		$this->loadLayout();
		$this->renderLayout();
	}

	public function createPostAction() {
		$helper 	= Mage::helper("edform");
		$params		= Mage::app()->getRequest()->getParams();
		$userId 	= $helper->getLoggedInCaUserId();
		$loggedIn 	= $helper->isMagentoAdminLoggedIn();
		$postCreate = false;
		$createSub	= false;
		if ($userId) {
			$userData 	= $helper->getCaUserById($userId);
			$isApproved = 0;
			if (isset($userData['approved'])) {
				$isApproved	= $userData['approved'];
			}
			if ($isApproved < 1) {
				$postCreate	= true;
			}
		}
		$editPro 	= false;
		if (isset($params['user_id']) && $userId == $params['user_id']) {
			$loggedIn 	= true;
			$editPro 	= true;
		}
		if (isset($params['parent_id']) && $userId == $params['parent_id']) {
			$loggedIn	= true;
			$createSub 	= true;
		}
		if(!$loggedIn) {
			$moduleBaseUrl	= $helper->getModuleBaseUrl();
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
		}
		Mage::getSingleton('core/session')->unsEdformCreatePost();
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		$params			= Mage::app()->getRequest()->getParams();
		$p				= @$params['login'];
		//print_r($p); exit;
		$errorMsgs		= array();
		if ($p['password'] != $p['confirm_password']) {
			$errorMsgs['confirm_password'] 	= $this->__('Confirmation Password did not match');
		}
		
		$username 		= $p['username'];
		$model 			= Mage::getModel('edform/edformusers');
		$users 			= $model->getCollection()
							->addFieldToFilter('username', $username);
		if ($editPro) {
			$users->addFieldToFilter("user_id", array("neq" => $userId));
		}
		if (count($users)) {
			$errorMsgs['username'] 			= $this->__('Username %s already exists, Please try a new one', $username);
		}
		
		@mkdir(Mage::getBaseDir('media') . DS . 'edform');
		@mkdir(Mage::getBaseDir('media') . DS . 'edform'.DS.'tmp');
		if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
			try {
				$fileName       = $_FILES['logo']['name'];
				$fileExt        = strtolower(substr(strrchr($fileName, "."), 1));
				$fileNamewoe    = rtrim($fileName, $fileExt);
				$fileName       = str_replace(' ', '', $fileNamewoe) . '.' . $fileExt;
				$fileName 		= $helper->generateRandomString()."_".$fileName;
				$fileName 		= str_replace("..", ".", $fileName);

				$uploader       = new Varien_File_Uploader('logo');
				$uploader->setAllowedExtensions(array('png', 'jpg', 'gif')); //allowed extensions
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS . 'edform'. DS . 'tmp';
				if(!is_dir($path)){
					mkdir($path, 0777, true);
				}
				$uploader->save($path . DS, $fileName );
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		
		
		if (count($errorMsgs)) {
			foreach ($errorMsgs as $key => $message) {
				Mage::getSingleton('core/session')->addError($message); 
			}
			$rowObj = new Varien_Object();
			$rowObj->setData($p);
			Mage::getSingleton('core/session')->setEdformCreatePost($rowObj);
			if ($editPro) {
				Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getEditProfileUrl());
				return;
			} else {
				Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/create');
				return;
			}
		}
		
		try {
			$plainPwd 		= $p['password'];
			if ($editPro && strlen($p['password']) < 1) {
				unset($p['password']);
			} else {
				$p['password'] 	= md5($p['password']);
			}
			if ($editPro && strlen($fileName) < 1) {
				unset($p['logo']);
			} else {
				$p['logo'] 		= $fileName;
			}
			//echo "<pre>"; print_r($p); exit;
			$isCaAdmin 		= $helper->isUserCaAdmin();
			if ($isCaAdmin) {
				$caAdminUser 		= $helper->getLoggedInCa();
				$p['parent_id'] 	= $caAdminUser['user_id'];
				if ($caAdminUser['approved']) {
					$p['approved']		= '1';
				}
			}
			if ($editPro && is_numeric($userId) && $params['user_id'] == $userId) {
				$p['user_id'] = $userId;
				$model = Mage::getModel('edform/edformusers')->load($userId);
				if ($model->getUsername() == $p['username']) {
					unset($p['username']);
				}
				unset($p['parent_id']);
			}
			$model->setData($p);
			$model->save();
			$userId 		= $model->getUserId();
			$newUser		= $model;
			$newUserId 		= $userId;
			// physical address
			$physicalAddress = array();
			foreach ($p as $key => $value) {
				if (stristr($key, 'physical_address_')) {
					$pkey = str_replace('physical_address_', '', $key);
					$physicalAddress[$pkey] = $value;
				}
			}
			$pa 		= Mage::getModel('edform/edformaddresses');
			$collection = $pa->getCollection()->addFieldToFilter("user_id", $userId);
			foreach ($collection as $addressItem) {
				$addressItem->delete();
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
					if (stristr($key, 'billing_address_')) {
						$pkey = str_replace('billing_address_', '', $key);
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
					if (stristr($key, 'shipping_address_')) {
						$pkey = str_replace('shipping_address_', '', $key);
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


			Mage::getSingleton('core/session')->unsEdformCreatePost();
			if (is_numeric($p['parent_id'])) {
				Mage::getSingleton('core/session')->addSuccess("Sub CA user created!");
			} else if ($editPro || $postCreate) {
				$successMessage = 'Profile updated!';
				if ($postCreate) {
					$successMessage = 'Your changes have been submitted.  You will receive an email when the changes have been reviewed by our staff.';
				}
				Mage::getSingleton('core/session')->addSuccess($successMessage);
				Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getEditProfileUrl());
				return;
			} else {
				if (is_numeric($newUserId)) {
					Mage::getModel("edform/edformusers")->emailCaCreateNotification($newUser, $plainPwd);
				}
				Mage::getSingleton('core/session')->addSuccess("CA user created!");
				Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
				return;
			}
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/myAccount');
			return;
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($this->__("There has been an error: %s", $e->getMessage())); 
			$rowObj = new Varien_Object();
			$rowObj->setData($p);
			Mage::getSingleton('core/session')->setEdformCreatePost($rowObj);
			if ($editPro) {
				Mage::app()->getFrontController()->getResponse()->setRedirect($helper->getEditProfileUrl());
			} else if ($isCaAdmin) {
				Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl . 'index/subcreate');
			} else {
				Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl . 'index/create');
			}
			return;
		}
		
	}


	public function subcreateAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function progressSaveAction() {
		$this->mustLogin();
		$helper 			= Mage::helper('edform');
		$user 				= $helper->getLoggedInCa();
		$result 			= array();
		$params 			= Mage::app()->getRequest()->getParams();
		$editingBrowserId 	= $helper->getEditingBrowserId();
		if (!isset($params['browser_id']) || strlen($params['browser_id']) < 16) {
			$result['error']	= $this->__('Browser Id missing.');
		} else if (!empty($editingBrowserId) && $editingBrowserId != $params['browser_id'] && false) {
			$result['error']	= $this->__('Another order is being actively edited from your account. Please close this window!');
			$result['reload']	= 'yes';
		} else if ($user['user_id']) {
			$userId 			= $user['user_id'];
			$currentUTC			= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
			$browserId 			= $params['browser_id'];
			
			$progressData 		= json_encode($params);
			$progressModel		= Mage::getModel('edform/edformprogress');
			$progressCollection = $progressModel
										->getCollection()
										->addFieldToFilter('user_id', $userId)
										->addFieldToFilter('browser_id', $browserId)
										;
			if (count($progressCollection) > 0) {
				foreach ($progressCollection as $progressItem) {
					//$progressItem->delete();
					$progressId = $progressItem->getProgressId();
					$progressModel	= Mage::getModel('edform/edformprogress')->load($progressId);
				}
			} else {
				$helper->setEditingBrowserId($browserId);
			}
			$progressModel->setData('user_id', $userId);
			$progressModel->setData('progress_data', $progressData);
			$progressModel->setData('client_name', $params['clientName']);
			$progressModel->setData('browser_id', $params['browser_id']);
			$progressModel->setData('updated_at', $currentUTC);
			$progressModel->save();
			$result['savedId'] 	= $progressModel->getProgressId();
		} else {
			$result['error']	= $this->__('No User Id');
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function orderPostAction() {
		$this->mustLogin();
		Mage::getSingleton('core/session')->unsEdformOrderPost();
		Mage::getSingleton('customer/session')->unsLastEdformOrderId();
		$helper 			= Mage::helper('edform');
		$moduleBaseUrl		= $helper->getModuleBaseUrl();
		$params				= Mage::app()->getRequest()->getParams();
		$p 					= $params;
		$browserId 			= $p['browser_id'];
		$editingBrowserId 	= $helper->getEditingBrowserId(); 
		$rowObj 			= new Varien_Object();
		$rowObj->setData($p);
		Mage::getSingleton('core/session')->setEdformOrderPost($rowObj);

		// save into db
		$model 			= Mage::getModel('edform/edformorders');
		$collection 	= $model->getCollection()->addFieldToFilter('protect_code', array('eq' => $p['protectCode']));
		if ($collection->count()) {
			$alreadyPosted = true;
			$error 			= array('message' => 'This form was already saved. Hence, it is not saved again.');
			Mage::getSingleton('core/session')->setEdformOrderPostError($error);
		} else if ($browserId != $editingBrowserId) {
			$error 			= array('message' => 'Multiple order edit detected!');
			Mage::getSingleton('core/session')->setEdformOrderPostError($error);			
		} else {
			$userId = 0;
			$loggedInCa = $helper->getLoggedInCa();
			if (isset($loggedInCa['user_id']) && is_numeric($loggedInCa['user_id'])) {
				$userId = $loggedInCa['user_id'];
			}
			$optionOrders 			= explode(",", rtrim($p['optionOrders'], ","));
			$numberOfPieces			= count($optionOrders);
			$p['numberOfPieces']	= $numberOfPieces;
			$p['orderId'] 			= $p['orderId'].date('mdy');

			$orderParams = json_encode($p);


			$dataMatch = array('client_name' 				=> 'clientName',
								'monogram_name' 			=> 'monogramName',
								'date_of_body_scan' 		=> 'bodyScanDate',
								'client_weight' 			=> 'clientWeight',
								'client_weight_date' 		=> 'scanDate',
								'order_number' 				=> 'orderId',
								'ca_po' 					=> 'orderCAId',
								'sales_rep' 				=> 'salesrep',
								'sales_rep_name' 			=> 'repName',
								'commercial_account_name' 	=> 'commName',
								'sales_rep_email' 			=> 'repEmail',
								'sales_rep_phone' 			=> 'repPhone',
								'order_comments' 			=> 'designNotes',
								'invoicing_email' 			=> 'clientEmail',
								'protect_code' 				=> 'protectCode',
								'number_of_pieces' 			=> 'numberOfPieces',
								'estimated_order_total' 	=> 'estimatedOrderTotal',
								'tax_title' 				=> 'taxTitle',
								'tax_percent' 				=> 'taxPercent',
			);

			if ($userId > 0) {
				$model->setData('user_id', $userId);
				$model->setData('order_params', $orderParams);
				foreach ($dataMatch as $dbKey => $formKey) {
					if (isset($p[$formKey])) {
						$model->setData($dbKey, $p[$formKey]);
					}
				}
				$model->save();
				$newOrderId = $model->getOrderId();

				Mage::getSingleton('customer/session')->setLastEdformOrderId($newOrderId);
				
				
				$progressModel		= Mage::getModel('edform/edformprogress');
				$progressCollection = $progressModel
											->getCollection()
											->addFieldToFilter('user_id', $userId)
											->addFieldToFilter('browser_id', $browserId)
											;
				foreach ($progressCollection as $progressItem) {
					$progressItem->delete();
				}
				$helper->clearEditingBrowserId();

				// send notification e-mail
				Mage::getModel('edform/edformorders')->emailCaOrderSummary($newOrderId);

			}
		}
		
		$this->loadLayout();
		$this->renderLayout();
		
		//echo "<pre>"; print_r($params); exit;
		
		
		//$p				= @$params['login'];
	}


	public function forgotPasswordAction () {
		$helper 		= Mage::helper('edform');
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		if ($username = $this->getRequest()->getPost('username')) {
			$collection = Mage::getModel('edform/edformusers')
							->getCollection()
							->addFieldToFilter('username', $username)
							->addFieldToFilter('approved', '1');
			$model 		= $collection->getFirstItem();
			if ($model->getUserId()) {
				$storeId 	= Mage::app()->getStore()->getStoreId();
				$userId 	= $model->getUserId();
				$name 		= $model->getContactFirstname().' '.$model->getContactLastname();
				$token 		= $helper->generateRandomString();
				$email 		= $model->getEmail();
				$link 		= $helper->getPasswordResetUrl().'?token='.$token;
				$link 		= '<a href="'.$link.'">'.$link.'</a>';
				$from 		= Mage::app()->getStore()->getFrontendName()."<br/>".Mage::getStoreConfig('general/store_information/phone')."<br/>".nl2br(Mage::getStoreConfig('general/store_information/address'));
				$gmtTime 	= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');

				$model 		= Mage::getModel('edform/edformusers')->load($userId);
				$model->setData('reset_token', $token);
				$model->setData('reset_at', $gmtTime);
				$model->save();



				$emailTemplateVariables = array();
				$emailTemplateVariables['token'] 		= $token;
				$emailTemplateVariables['name'] 		= $name;
				$emailTemplateVariables['accountName'] 	= $model->getAccountName();
				$emailTemplateVariables['link'] 		= $link;
				$emailTemplateVariables['from']			= $from;
				$emailTemplateVariables['requestedAt']	= Mage::app()->getLocale()->storeDate(
																Mage::app()->getStore(),
																Varien_Date::toTimestamp($gmtTime),
																true
															);
				
				/*
				Mage::getModel('core/email_template')
					->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
					->sendTransactional(
						'edform_forgot_password',
						Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
						$email,
						null,
						$emailTemplateVariables
					);
				*/


				$emailTemplate = Mage::getModel('core/email_template')->loadDefault('edform_forgot_password');
				$emailTemplate->setType('html');
				$emailTemplate->setTemplateSubject('Reset your password');
				$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables); //echo $processedTemplate; exit;

				if (false) {
					//$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables); //echo $processedTemplate; exit;
					$emailTemplate->send($email, $name, $emailTemplateVariables);


					$mailer = Mage::getModel('core/email_template_mailer');
					$emailInfo = Mage::getModel('core/email_info');
					$emailInfo->addTo((string)$email, (string)$name);
					$mailer->addEmailInfo($emailInfo);
					// Set all required params and send emails
					$mailer->setSender(array('email' => (string)'lf@entredonovan.com', 'name' => (string)'Linda'));
					$mailer->setStoreId($storeId);
					$mailer->setTemplateId('edform_forgot_password');
					$mailer->setTemplateParams($emailTemplateVariables);
					$mailer->send();
				}




				$mailer = Mage::getModel('core/email_template_mailer');
				$emailInfo = Mage::getModel('core/email_info');
				$emailInfo->addTo($email, $name);
				$mailer->addEmailInfo($emailInfo);
				$templateId = Mage::getStoreConfig('edform/email/template_forgot_password', $storeId);
				if (!is_numeric($templateId)) {
					$templateId 	= "edform_forgot_password";
				}
				//echo $templateId; exit;
				//$templateId = 5;

				// Set all required params and send emails
				//$mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				$mailer->setSender(array('email'=>(string) 'info@entredonovan.com','name'=> (string)'entreDonovan'));
				$mailer->setStoreId($storeId);
				$mailer->setTemplateId($templateId);
				$mailer->setTemplateParams($emailTemplateVariables);
				$mailer->send();


				if (false) {

					$mail = Mage::getModel('core/email');
					$mail->setToName('Aashish');
					$mail->setToEmail('aashish.tuladhar@gmail.com');
					$mail->setBody('Mail Text / Mail Content');
					$mail->setSubject('Mail Subject');
					$mail->setFromEmail('info@entredonovan.com');
					$mail->setReplyTo('info@entredonovan.com');
					$mail->setFromName("Linda");
					$mail->setType('html');// YOu can use Html or text as Mail format
					$mail->setBodyHTML($processedTemplate);  // your content or message
					//echo $processedTemplate; exit;
					try {
						$mail->send();
					} catch (Exception $e) {
						echo "Error: " . $e->getMessage();
					}
				}
				
				
				//echo $processedTemplate;
				//echo 'sent'; exit;
			}
			Mage::getSingleton('core/session')->addSuccess($this->__('You will receive an e-mail with link to reset your password if the matching account exists.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl.'index/login');
			return;
		}
		$this->loadLayout();
		$this->renderLayout();
		//echo 'login page'; exit;
	}


	public function resetAction() {
		$helper 		= Mage::helper('edform');
		$moduleBaseUrl	= $helper->getModuleBaseUrl();
		if ($this->getRequest()->getPost('token') && $this->getRequest()->getPost('password')) {
			$token 		= $this->getRequest()->getPost('token');
			$password 	= $this->getRequest()->getPost('password');
			$model 		= $this->getUserByResetToken($token);
			if (!$model->getUserId()) {
				Mage::getSingleton('core/session')->addError($this->__('Invalid reset token.'));
				Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
				return;
			}
			$model 		= Mage::getModel('edform/edformusers')->load($model->getUserId());
			$model->setData('password', md5($password))
					->setData('reset_token', NULL)
					->setData('reset_at', NULL)
					->save();
			Mage::getSingleton('core/session')->addSuccess($this->__('Your password has been reset.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
			return;
		}
		$token 	= trim(Mage::app()->getRequest()->getParam('token'));
		if (empty($token)) {
			Mage::getSingleton('core/session')->addError($this->__('Invalid reset token.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
			return;
		}
		$model 			= $this->getUserByResetToken($token);
		if (!$model->getUserId()) {
			Mage::getSingleton('core/session')->addError($this->__('Invalid reset token.'));
			Mage::app()->getFrontController()->getResponse()->setRedirect($moduleBaseUrl);
			return;
		}

		$this->loadLayout();
		$this->getLayout()->getBlock('edformPasswordReset')->assign('token', $token);
		$this->renderLayout();
	}


	public function validateResetToken($token) {
		$validDays	= 4;
		$collection	= Mage::getModel('edform/edformusers')
						->getCollection()
						->addFieldToFilter('reset_token', $token);
		$model 		= $collection->getFirstItem();
		if (!$model->getUserId()) {
			return false;
		}
		$timeNow 		= strtotime(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
		$gmt 			= strtotime($model->getResetAt());
		$minutesDiff	= round(abs($timeNow - $gmt) / 60,2);
		$validMinutes 	= 60 * 24 * $validDays;
		if ($minutesDiff > $validMinutes) {
			return false;
		}
		return true;
 	}

 	public function getUserByResetToken ($token) {
		$emptyObj = new Varien_Object();
		if (!$this->validateResetToken($token)) {
			return $emptyObj;
		}
		$collection	= Mage::getModel('edform/edformusers')
			->getCollection()
			->addFieldToFilter('reset_token', $token);
		$model 		= $collection->getFirstItem();
		if ($model->getUserId()) {
			return $model;
		}
		return $emptyObj;
	}

	public function getRegionsAction() {
		$params				= Mage::app()->getRequest()->getParams();
		$regionData			= array();
		if (!empty($params['countryCode'])) {
			$regionCollection = Mage::getModel('directory/region_api')->items($params['countryCode']);
			if (count($regionCollection)) {
				foreach ($regionCollection as $index => $region) {
					$regionData[$region['region_id']] = $region['name'];
				}
			}
		}
		$this->getResponse()->setBody(Zend_Json::encode($regionData));
	}

	public function getRegionCodeById ($regionId) {
		$region = Mage::getModel('directory/region')->load($regionId);
		return $region->getCode();
	}

	public function getRegionIdByCode($regionCode, $countryCode) {
		$regionModel 	= Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
		$regionId 		= $regionModel->getId();
		return $regionId;
	}

	public function getTaxRateAction() {
		$params			= Mage::app()->getRequest()->getParams();

		if (is_numeric($params['to_state'])) {
			$params['to_state'] = $this->getRegionCodeById($params['to_state']);
		}
		if (is_numeric($params['from_state'])) {
			$params['from_state'] = $this->getRegionCodeById($params['from_state']);
		}

		$stateInfo 		= $this->getStateFromZip($params['to_zip']);
		$state 			= key($stateInfo);
		$stateName 		= array_shift(array_values($stateInfo));
		$stateOverode	= false;
		if ($state != $params['to_state'] && !is_null($state)) {
			$params['to_state']	= $state;
			$stateOverode		= true;
		}




		$token      = '03c238b1fd5d7e9cc5c34aa49f23c3a9';
		$token 		= 'a38ba98c14d5599ffe4923fb3e20f219';
		$token 		= 'e8a09d73bebae9f833e903c821389244';


		$items 		= $params['items'];
		$params     = array('shipping' => 0, 'to_zip'  => $params['to_zip'], 'to_state' => $params['to_state'], 'to_country' => $params['to_country'], 'from_zip' => $params['from_zip'], 'from_state' => $params['from_state'], 'from_country' => $params['from_country']);
		$items 		= explode("|", $items);
		$lineItems 	= array();
		foreach ($items as $junk => $item) {
			if ("" != $item) {
				$tmp 	= explode("-", $item);
				$itemId		= $tmp[0];
				$itemPrice	= $tmp[1];
				$lineItem 	= array(
									'id' => $itemId,
									'quantity' => 1,
									'product_tax_code' => '20010',
									'unit_price' => $itemPrice,
									'discount' => 0
								);
				$lineItems[]	= $lineItem;
			}
		}
		/*
		$lineItems	= array(
							array(
								'id' => '2018-07-10-a',
								'quantity' => 1,
								'product_tax_code' => '20010',
								'unit_price' => 15,
								'discount' => 0
							),
							array(
								'id' => '2',
								'quantity' => 1,
								'product_tax_code' => '20010',
								'unit_price' => 150,
								'discount' => 0
							),
						);
		*/
		$params['line_items']	= $lineItems;


		if (false) {
			try {
				$libPath 		= Mage::getBaseDir().'/lib/taxjar/vendor/autoload.php';
				include($libPath);
				$client 	= TaxJar\Client::withApiKey($token);
				$order_taxes = $client->taxForOrder($params);
				$this->getResponse()->setBody(Zend_Json::encode($order_taxes));

			} catch (Exception $e) {
				$result = array("error" => $e->getMessage());
				$this->getResponse()->setBody(Zend_Json::encode($result));

			}
		}

		if (true) {

			//$payload 	= json_encode($params);
			$gParams = '';
			foreach ($params as $k => $v) {
				$gParams .= $k . '=' . $v . '&';
			}
			//$params = json_encode($params);
			$gParams = trim($gParams, '&');
			$gParams = http_build_query($params);
			$params 	= json_encode($params);
			$url = "https://api.taxjar.com/v2/taxes";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

			$headers = array(
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json',
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$output = curl_exec($ch);
			curl_close($ch);

			if ($stateOverode) {
				$paramsD 				= json_decode($params, true);
				$regionId 				= $this->getRegionIdByCode($state, $paramsD['to_country']);
				$output 				= json_decode($output, true);
				$output['regionId'] 	= $regionId;
				$output['stateName']	= $stateName;
				$output 				= json_encode($output);
			}

			echo $output;
			exit;

			$this->getResponse()->setBody(Zend_Json::encode($params));
		}
	}



	public function getStateFromZip($zipCode) {
		$params 	= json_encode(array());
		$url 		= "http://api.zippopotam.us/us/".$zipCode;
		$ch 		= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, FALSE);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output 	= curl_exec($ch);
		curl_close($ch);
		$output 	= json_decode($output, true);
		if (isset($output['places'][0]['state abbreviation']) && isset($output['places'][0]['state'])) {
			return array($output['places'][0]['state abbreviation'] => $output['places'][0]['state']);
		} else {
			return NULL;
		}
	}
}
