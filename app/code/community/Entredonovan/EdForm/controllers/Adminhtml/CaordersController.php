<?php
 
class Entredonovan_EdForm_Adminhtml_CaordersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
		
		$this->_title($this->__('Customer'))->_title($this->__('Commercial Account (CA) Orders'));
		
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
        $this->_addContent($this->getLayout()->createBlock('edform/adminhtml_caorders'));
        $this->renderLayout();
    }

    public function viewAction() {

		$appEmulation 			= Mage::getSingleton('core/app_emulation');
		$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation('default','frontend');


		$this->loadLayout();
		$this->renderLayout();

		$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
		//$this->getLayout()->getUpdate()->getHandles()->setData('area', 'frontend');
		//Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
	}

	public function editAction() {
		$params 	= $this->getRequest()->getParams();
		$id 		= $params['id'];
		$url 		= Mage::getUrl('adminhtml').'caorders/view/order_id/'.$id.'/key/'.$key;
		$url 		= Mage::helper("adminhtml")->getUrl("adminhtml/caorders/view/order_id/".$id);
		Mage::app()->getFrontController()->getResponse()->setRedirect($url);
	}
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('edform/adminhtml_caorders_grid')->toHtml()
        );
    }
 
    public function exportEdformCsvAction()
    {
        $fileName = 'ca_orders.csv';
        $grid = $this->getLayout()->createBlock('entredonovan_edform/adminhtml_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportEdformExcelAction()
    {
        $fileName = 'ca_orders.xml';
        $grid = $this->getLayout()->createBlock('entredonovan_edform/adminhtml_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
	
	public function massApproveAction($newstatus = 1) {
		$userIds = $this->getRequest()->getParam('order_id');
		if(!is_array($userIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('edform')->__('Please select users(s).'));
		} else {
			try {
				$model 		= Mage::getModel('edform/edformorders');
				foreach ($userIds as $userId) {
					//$model->load($userId)->delete();
					$model->load($userId)->setApproved($newstatus)->save();
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