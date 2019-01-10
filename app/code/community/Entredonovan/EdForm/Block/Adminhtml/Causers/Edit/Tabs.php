<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('causers_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('edform')->__('Sections'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('basic_info', array(
			'label' => Mage::helper('edform')->__('Basic Information'),
			'title' => Mage::helper('edform')->__('Basic Information'),
			'content' => $this->getLayout()
				->createBlock('edform/adminhtml_causers_edit_tab_basic')
				->toHtml(),
		));

		$this->addTab('shipping_info', array(
			'label' => Mage::helper('edform')->__('Addresses'),
			'title' => Mage::helper('edform')->__('Addresses'),
			'content' => $this->getLayout()
				->createBlock('edform/adminhtml_causers_edit_tab_addresses')
				->toHtml(),
		));

		$this->addTab('ed_tier', array(
			'label' => Mage::helper('edform')->__('Standard Order Prep'),
			'title' => Mage::helper('edform')->__('Standard Order Prep'),
			'content' => $this->getLayout()
				->createBlock('edform/adminhtml_causers_edit_tab_markup')
				->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}
?>