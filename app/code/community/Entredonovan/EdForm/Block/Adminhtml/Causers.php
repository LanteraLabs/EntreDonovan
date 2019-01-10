<?php
 
class Entredonovan_EdForm_Block_Adminhtml_Causers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
	
        $this->_blockGroup = 'edform';
        $this->_controller = 'adminhtml_causers';
        $this->_headerText = Mage::helper('edform')->__('Commercial Account (CA) Users');
 		
        parent::__construct();
        $this->_removeButton('add');


        $data = array(
            'label'     => 'Add CA Account',
            'class'     => 'some-class',
            'onclick'   => 'window.open(\' '  . Mage::getBaseUrl().'/edform/index/create/' . '\')',
        );
        $this->addButton('create_ca_account', $data);
    }
}
?>