<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'edform';
		$this->_controller = 'adminhtml_causers';
		$this->_headerText = Mage::helper('edform')->__('Edit CA');
		$this->_updateButton('save', 'label', Mage::helper('edform')->__('Save CA'));
		$this->_updateButton('delete', 'label', Mage::helper('edform')->__('Delete CA'));
		$this->_removeButton('delete');
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('adminhtml')->__('Save And Continue Edit') ,
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		) , -100);
		$this->_formScripts[] = "
 function toggleEditor() {
 if (tinyMCE.getInstanceById('salestaff_content') == null) {
 tinyMCE.execCommand('mceAddControl', false, 'salestaff_content');
 } else {
 tinyMCE.execCommand('mceRemoveControl', false, 'salestaff_content');
 }
 }
 
function saveAndContinueEdit(){
 editForm.submit($('edit_form').action+'back/edit/');
 }
 ";
		$this->_removeButton('saveandcontinue');
	}
}
?>