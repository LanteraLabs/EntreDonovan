<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit_Tab_Shipping extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);

		if (Mage::getSingleton('adminhtml/session')->getCausersData()) {
			$data = Mage::getSingleton('adminhtml/session')->getCausersData();
			Mage::getSingleton('adminhtml/session')->setCausersData(null);
		} elseif (Mage::registry('causers_data')) {
			$data = Mage::registry('causers_data')->getData();
		}
		$fieldset = $form->addFieldset('causers_form', array(
			'legend'=>Mage::helper('edform')->__('Shipping information')
		));

		/*Edit field as text type*/


		$fieldset->addField('shipping_firstname', 'text', array(
			'label' => Mage::helper('edform')->__('Firstname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_firstname',
		));

		$fieldset->addField('shipping_lastname', 'text', array(
			'label' => Mage::helper('edform')->__('Lastname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_lastname',
		));

		$fieldset->addField('shipping_street1', 'text', array(
			'label' => Mage::helper('edform')->__('Street 1'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_street1',
		));

		$fieldset->addField('shipping_street2', 'text', array(
			'label' => Mage::helper('edform')->__('Street 2'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_street2',
		));

		$fieldset->addField('shipping_city', 'text', array(
			'label' => Mage::helper('edform')->__('City'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_city',
		));

		$fieldset->addField('shipping_state', 'text', array(
			'label' => Mage::helper('edform')->__('State'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_state',
		));

		$fieldset->addField('shipping_zip', 'text', array(
			'label' => Mage::helper('edform')->__('Zip'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_zip',
		));

		$fieldset->addField('shipping_phone', 'text', array(
			'label' => Mage::helper('edform')->__('Phone'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_phone',
		));

		$fieldset->addField('shipping_country_id', 'select', array(
			'label' => Mage::helper('edform')->__('Country'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_country_id',
			'values'	=> Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
		));

		$fieldset->addField('shipping_instruction', 'textarea', array(
			'label' => Mage::helper('edform')->__('Instruction'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_instruction',
		));


		 if($this->getRequest()->getParam('id')){

		 }

		$form->setValues($data);
		return parent::_prepareForm();
 	}
}
?>