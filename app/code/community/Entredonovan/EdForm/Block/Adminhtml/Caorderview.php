<?php
 
class Entredonovan_EdForm_Block_Adminhtml_Caorderview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
	
        $this->_blockGroup = 'edform';
        $this->_controller = 'adminhtml_caorders';
        $this->_headerText = Mage::helper('edform')->__('Commercial Account (CA) Orders');
 		
        parent::__construct();
        $this->_removeButton('add');
    }
}
?>