<?php


class Entredonovan_EdForm_Block_Create extends Mage_Core_Block_Template
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('edform')->__('Commercial Account (CA) Signup'));
        return parent::_prepareLayout();
    }

    
    public function getPostActionUrl()
    {
        return $this->helper('edform')->getCreatePostUrl();
    }
	
	public function getCreateAccountUrl() {
		return $this->helper('edform')->getCreateAccountUrl();
	}
	
	
	
	
}
?>