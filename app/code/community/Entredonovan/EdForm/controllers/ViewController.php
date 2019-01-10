<?php
class Entredonovan_Edform_ViewController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	// "Fetch" display
        $this->loadLayout();
		
		//create button
		
		$this->_headerText = Mage::helper('edform')->__('Commercial Account (CA) Users');
		
		if (false) {
			$this->_addContent(
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
				'label'     => Mage::helper('catalog')->__('Add Survey'),
				'onclick'   => "setLocation('".$this->getUrl('edfor/*/edit')."')",
				'class'   => 'add',
				'align' => 'right'
				))
			);
		}
	  
	  //create grid
        $this->_addContent($this->getLayout()->createBlock('edform/adminhtml_grid'));

        $this->renderLayout();
    }
	
}
?>