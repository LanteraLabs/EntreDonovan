<?php
class Entredonovan_EdForm_Block_Adminhtml_Renderer_Account extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		# $this->getColumn()->getIndex()
		$parentId =  $row->getData('parent_id');
		if ($parentId) {
			$parentUser = Mage::getModel('edform/edformusers')->load($parentId);
			$parentUser = $parentUser->getData();
			$parentUsername = $parentUser['username'];
			return '<span style="">'.$this->__('Sub-Account of %s', $parentUsername).'</span>';
		} else {
			return '<span>'.$this->__('CA').'</span>';
		}

	}
}

?>