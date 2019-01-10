<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit_Tab_Basic extends Mage_Adminhtml_Block_Widget_Form
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

		// logo image patch
		if (!empty($data['logo'])) {
			$logoFile 		= $data['logo'];
			if (file_exists(Mage::getBaseDir('media').'/edform/'.$logoFile)) {
				$data['logo'] 	= 'edform/'.$logoFile;
			} else if (file_exists(Mage::getBaseDir('media').'/edform/tmp/'.$logoFile)) {
				$data['logo']	= 'edform/tmp/'.$logoFile;
			} else {
				$data['logo']	= '';
			}
		}

		$fieldset = $form->addFieldset('causers_form', array(
			'legend'=>Mage::helper('edform')->__('Basic information')
		));

		/*Edit field as text type*/

		$fieldset->addField('logo', 'image', array(
			'label'     => Mage::helper('edform')->__('Upload Logo'),
			'required'  => false,
			'name'      => 'logo',
		));


		$fieldset->addField('username', 'text', array(
			'label' => Mage::helper('edform')->__('Username'),
			'class' => '',
			'readonly' => true,
			'required' => true,
			'name' => 'username',
		));

		$fieldset->addField('account_name', 'text', array(
			'label' => Mage::helper('edform')->__('Account Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'account_name',
		));

		$fieldset->addField('contact_firstname', 'text', array(
			'label' => Mage::helper('edform')->__('Firstname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'contact_firstname',
		));

		$fieldset->addField('contact_lastname', 'text', array(
			'label' => Mage::helper('edform')->__('Lastname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'contact_lastname',
		));

		$fieldset->addField('phone', 'text', array(
			'label' => Mage::helper('edform')->__('Phone'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'phone',
		));

		$fieldset->addField('email', 'text', array(
			'label' => Mage::helper('edform')->__('E-mail'),
			'class' => 'required-entry validate-email',
			'required' => true,
			'name' => 'email',
		));


		 if($this->getRequest()->getParam('id')){

		 }

		$form->setValues($data);
		return parent::_prepareForm();
 	}
}
?>