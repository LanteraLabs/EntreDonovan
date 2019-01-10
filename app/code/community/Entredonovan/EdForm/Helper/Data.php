<?php

class Entredonovan_EdForm_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getModuleBaseUrl() {
		return Mage::getBaseUrl().'edform/';	
	}
	
	public function isCaLoggedIn() {
		$loggedInCa = $this->getLoggedInCa();
		if (isset($loggedInCa['user_id']) && is_numeric($loggedInCa['user_id'])) {
			return true;
		}
		return false;
	}
	
	public function getLoggedInCa() {
		return Mage::getSingleton('core/session')->getLoggedInCa();
	}

	public function isUserApproved() {
		$user = $this->getLoggedInCa();
		if (isset($user['approved'])) {
			return $user['approved'];
		}
		return 0;
	}

	public function getLoggedInCaUserId() {
		$loggedInCa = $this->getLoggedInCa();
		if (isset($loggedInCa['user_id']) && is_numeric($loggedInCa['user_id'])) {
			return $loggedInCa['user_id'];
		}
		return 0;
	}

	public function isUserCaAdmin ($userId = NULL) {
		if (is_integer($userId)) {
			$model 		= Mage::getModel('edform/edformusers');
			$user 		= $model->load($userId);
			$user 		= $user->getData();
		} else {
			$user 		= $this->getLoggedInCa();
		}
		if (empty($user['parent_id']) && $this->isCaLoggedIn()) {
			return true;
		} else {
			return false;
		}
	}

	public function isUserSubCa ($userId = NULL) {
		if (is_integer($userId)) {
			$model 		= Mage::getModel('edform/edformusers');
			$user 		= $model->load($userId);
			$user 		= $user->getData();
		} else {
			$user 		= $this->getLoggedInCa();
		}
		if (!empty($user['parent_id']) && $this->isCaLoggedIn()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function logout() {
		$this->clearEditingBrowserId();
		Mage::getSingleton('core/session')->unsLoggedInCa();
	}
	
	public function getLoginPostUrl() {
		return $this->getModuleBaseUrl().'index/loginPost';
	}
	
	public function getCreateAccountUrl() {
		return $this->getModuleBaseUrl().'index/create';
	}

	public function getCreateSubAccountUrl() {
		return $this->getModuleBaseUrl().'index/subcreate';
	}
	
	public function getCreatePostUrl() {
		return $this->getModuleBaseUrl().'index/createPost';
	}
	
	public function getLogoutUrl() {
		return $this->getModuleBaseUrl().'index/logout';
	}
	
	public function getEdOrderPostUrl() {
		return $this->getModuleBaseUrl().'index/orderPost';
	}

	public function getForgotPasswordUrl() {
		return $this->getModuleBaseUrl().'index/forgotPassword';
	}

	public function getPasswordResetUrl() {
		return $this->getModuleBaseUrl().'index/reset';
	}

	public function getProgressSaveUrl() {
		return $this->getModuleBaseUrl().'index/progressSave';
	}

	public function getListPastOrderUrl() {
		return $this->getModuleBaseUrl().'index/listPastOrders';
	}

	public function getMyAccountUrl() {
		return $this->getModuleBaseUrl().'index/myAccount';
	}

	public function getListDraftsUrl() {
		return $this->getModuleBaseUrl().'index/listDrafts';
	}

	public function getDraftResumeUrl() {
		return $this->getModuleBaseUrl().'index/draftResume';
	}

	public function getDeleteDraftUrl() {
		return $this->getModuleBaseUrl().'index/deleteDraft';
	}

	public function getContactUrl() {
		return $this->getModuleBaseUrl().'index/contact';
	}

	public function getContactPostUrl() {
		return $this->getModuleBaseUrl().'index/contactSave';
	}

	public function getEditProfileUrl() {
		return $this->getModuleBaseUrl().'index/editProfile';
	}

	public function getListSubCaUrl() {
		return $this->getModuleBaseUrl().'index/listSubCa';
	}

	public function getOrderUrl() {
		return $this->getModuleBaseUrl();
	}

	public function getPreviewUrl() {
		return $this->getModuleBaseUrl().'index/preview';
	}

	public function getUserOrderData($orderId, $userId = NULL) {
		if (empty($userId)) {
			$user = $this->getLoggedInCa();
			if ($user['user_id']) {
				$userId = $user['user_id'];
			}
		}
		if (!is_numeric($userId)) {
			return array();
		}
		$progressModel		= Mage::getModel('edform/edformorders');
		$progressCollection = $progressModel->getCollection()->addFieldToFilter('user_id', $userId)->addFieldToFilter("order_id", $orderId);
		foreach ($progressCollection as $progressItem) {
			$progressData = json_decode($progressItem->getData('order_params'), true);
			return $progressData;
		}
		return array();
	}

	public function getProgressData($userId = NULL) {
		$mageAction     = Mage::app()->getRequest()->getActionName();
		$currentUTC		= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
		if (empty($userId)) {
			$user = $this->getLoggedInCa();
			if ($user['user_id']) {
				$userId = $user['user_id'];
			}
		}
		if (!is_numeric($userId)) {
			return array();
		}
		$editingBrowserId 	= $this->getEditingBrowserId();
		if (empty($editingBrowserId)) {
			return array();
		}
		$progressModel		= Mage::getModel('edform/edformprogress');
		$progressCollection = $progressModel
									->getCollection()
									->addFieldToFilter('user_id', $userId)
									->addFieldToFilter('browser_id', $editingBrowserId)
									;
		foreach ($progressCollection as $progressItem) {
			$updatedAt 			= $progressItem->getData('updated_at');
			$timeDifference 	= strtotime($currentUTC) - strtotime($updatedAt);
			$browserId 			= $progressItem->getData("browser_id");
			if ($this->getTmpToken() == $browserId || 'preview' == $mageAction) {
				$this->clearTmpToken();
				$progressData 	= json_decode($progressItem->getData('progress_data'), true);
				return $progressData;
			} else {
				//echo $this->getTmpToken()." != ".$browserId; exit;
				return array();
			}
		}
	}

	public function getLogoUrlByFilename($filename) {
		$mediaPath 		= Mage::getBaseDir('media');
		$edFormPath 	= $mediaPath.'/edform/';
		$edFormTmpPath 	= $edFormPath.'tmp/';
		$url 			= '';
		
		if (file_exists($edFormPath.$filename)) {
			$url = Mage::getBaseUrl('media').'edform/'.$filename;
		} else if (file_exists($edFormTmpPath.$filename)) {
			$url = Mage::getBaseUrl('media').'edform/tmp/'.$filename;
		}
		return $url;
	}
	
	
	public function generateRandomString($length = 30) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function isMagentoAdminLoggedIn() {
		$sesId = isset($_COOKIE['adminhtml']) ? $_COOKIE['adminhtml'] : false ;
		$session = false;
		if($sesId){
			$session = Mage::getSingleton('core/resource_session')->read($sesId);
		}
		$loggedIn = false;
		if($session) {
			if(stristr($session,'Mage_Admin_Model_User'))
			{
				$loggedIn = true;
			}
		} else {
			$sessionFilePath = Mage::getBaseDir('session').DS.'sess_'.$_COOKIE['adminhtml'];
			//write content of file in var
			$sessionFile = file_get_contents($sessionFilePath);

			//save old session
			$oldSession = $_SESSION;
			//decode adminhtml session
			session_decode($sessionFile);
			//save session data from $_SESSION
			$adminSessionData = $_SESSION;
			//set old session back to current session
			$_SESSION = $oldSession;

			if(array_key_exists('user', $adminSessionData['admin'])){
				//save Mage_Admin_Model_User object in var
				$adminUserObj = $adminSessionData['admin']['user'];
				$loggedIn	= true;
			}
		}
		return $loggedIn;
	}


	public function getNewProtectCode() {
		$hasProtectCode = false;
		do {
			$protectCode 		= $this->generateRandomString(60);
			$model 				= Mage::getModel('edform/edformorders');
			$collection 		= $model->getCollection()->addFieldToFilter('protect_code', array('eq' => $protectCode));
			if ($collection->count()) {
				$hasProtectCode	= true;
			}

		} while ($hasProtectCode);
		return $protectCode;
	}
	
	
	public function getCaUserById($userId) {
		$model 		= Mage::getModel('edform/edformusers');
		$user 		= $model->load($userId);
		return $user;		
	}

	public function getUserAddressData($userId, $addressType = 'physical') {
		$user 		= $this->getCaUserById($userId);
		if ('billing' == $addressType) {
			$addressId = $user['billing_address_id'];
		} else if ('shipping' == $addressType) {
			$addressId = $user['shipping_address_id'];
		} else {
			$addressId = $user['physical_address_id'];
		}

		if (is_numeric($addressId)) {
			$address = Mage::getModel("edform/edformaddresses")->load($addressId);
			$addressData = $address->getData();
			return $addressData;
		}
		return array();
	}

	public function mustLogin() {
		$isCaLoggedIn 	= $this->isCaLoggedIn();
		if (!$isCaLoggedIn) {
			Mage::app()->getFrontController()->getResponse()->setRedirect($this->getModuleBaseUrl().'index/login/');
			return;
		}
	}


	public function setEditingBrowserId($browserId) {
		$userId 			= $this->getLoggedInCaUserId();
		$model 				= Mage::getModel('edform/edformediting');
		$currentUTC			= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
		$collection 		= $model
									->getCollection()
									->addFieldToFilter('user_id', array('eq' => $userId))
									->addFieldToFilter('browser_id', array('neq' => $browserId))
									;
		if (count($collection)) {
			foreach ($collection as $editingRow) {
				$editingRow->delete();
			}
		}

		$collection 		= $model
									->getCollection()
									->addFieldToFilter('user_id', array('eq' => $userId))
									->addFieldToFilter('browser_id', array('eq' => $browserId))
									;
		if (count($collection)) {
			foreach ($collection as $editingRow) {
				$editingRow
					->setData('updated_at', $currentUTC)
					->save();
				return $editingRow->getEditId();
			}
		} else {
			$model
				->setData('user_id', $userId)
				->setData('browser_id', $browserId)
				->setData('created_at', $currentUTC)
				->setData('updated_at', $currentUTC)
				->save();
			return $model->getEditId();	
				
		}

		//return Mage::getSingleton('core/session')->setEditingBrowserId($browserId);
	}

	public function getEditingBrowserId() {
		$userId 			= $this->getLoggedInCaUserId();
		$model 				= Mage::getModel('edform/edformediting');
		$currentUTC			= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
		$collection 		= $model
									->getCollection()
									->addFieldToFilter('user_id', array('eq' => $userId))
									;
		if (count($collection)) {
			foreach ($collection as $editingRow) {
				return $editingRow->getBrowserId();
			}
		} 
		return '';
		
		//return Mage::getSingleton('core/session')->getEditingBrowserId();
	}

	public function clearEditingBrowserId() {
		$userId 			= $this->getLoggedInCaUserId();
		$model 				= Mage::getModel('edform/edformediting');
		$currentUTC			= Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
		$collection 		= $model
									->getCollection()
									->addFieldToFilter('user_id', array('eq' => $userId))
									;
		if (count($collection)) {
			foreach ($collection as $editingRow) {
				$editingRow->delete();
			}
		} 
		return true;
		//return Mage::getSingleton('core/session')->unsEditingBrowserId();
	}

	public function isOrderEditing() {
		return false;
		$browserId = $this->getEditingBrowserId();
		if (!empty($browserId)) {
			return true;
		} else {
			return false;
		}
	}

	public function setTmpToken($token) {
		return Mage::getSingleton('core/session')->setTmpToken($token);
	}

	public function getTmpToken() {
		return Mage::getSingleton('core/session')->getTmpToken();
	}

	public function clearTmpToken() {
		return Mage::getSingleton('core/session')->unsTmpToken();
	}
	
	
}


?>