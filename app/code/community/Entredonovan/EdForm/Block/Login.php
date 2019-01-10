<?php


class Entredonovan_EdForm_Block_Login extends Mage_Core_Block_Template
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        //$this->getLayout()->getBlock('head')->setTitle(Mage::helper('edform')->__('Commercial Account (CA) Login'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->helper('edform')->getLoginPostUrl();
    }
	
	public function getCreateAccountUrl() {
		return $this->helper('edform')->getCreateAccountUrl();
	}

	public function getForgotPasswordUrl() {
    	return $this->helper('edform')->getForgotPasswordUrl();
	}

	public function getModuleBaseUrl() {
    	return $this->helper('edform')->getModuleBaseUrl();
	}
	
	
}
?>