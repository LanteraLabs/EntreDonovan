<?php

class Entredonovan_EdForm_Block_Adminhtml_Causers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('causersGrid');
      $this->setDefaultSort('user_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('edform/edformusers')->getCollection();	  
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

	protected function _prepareColumns()
	{
		$helper 		= Mage::helper('edform');

		$this->addColumn('user_id', array(
			'header'    => Mage::helper('edform')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'user_id',
		));

		$this->addColumn('username', array(
			'header'    => Mage::helper('edform')->__('Username'),
			'align'     =>'left',
			'index'     => 'username',
		));

		$this->addColumn('account_name', array(
			'header'    => Mage::helper('edform')->__('Account Name'),
			'align'     =>'left',
			'index'     => 'account_name',
		));


		$this->addColumn('contact_firstname', array(
			'header'    => Mage::helper('edform')->__('Contact Firstname'),
			'align'     =>'left',
			'index'     => 'contact_firstname',
		));

		$this->addColumn('contact_lastname', array(
			'header'    => Mage::helper('edform')->__('Contact Lastname'),
			'align'     =>'left',
			'index'     => 'contact_lastname',
		));


		$this->addColumn('phone', array(
			'header'    => Mage::helper('edform')->__('Phone'),
			'align'     =>'left',
			'index'     => 'phone',
		));


		$this->addColumn('email', array(
			'header'    => Mage::helper('edform')->__('E-mail'),
			'align'     =>'left',
			'index'     => 'email',
		));
		
		$this->addColumn('parent_id', array(
			'header'    => Mage::helper('edform')->__('Account Type'),
			'align'     =>'left',
			'index'     => 'parent_id',
			'filter' 	=> false,
			'sortable'  => false,
			'renderer'  => 'Entredonovan_EdForm_Block_Adminhtml_Renderer_Account',
		));
		
		
		$this->addColumn('approved', array(
			'header'    => Mage::helper('edform')->__('Status'),
			'align'     =>'left',
			'index'     => 'approved',
			'type'		=> 'options',
			'options' 	=> array('1' => 'Approved', '0' => 'Pending')
		));

		$this->addExportType('*/*/exportEdformCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportEdformExcel', $helper->__('Excel XML'));


		return parent::_prepareColumns();
	}
	
	
	protected function _prepareMassaction() {
		$this->setMassactionIdField('user_id');
		$this->getMassactionBlock()->setFormFieldName('user_id');

		$this->getMassactionBlock()->addItem('Approve', array(
			'label'=> Mage::helper('edform')->__('Approve'),
			'url'  => $this->getUrl('*/*/massApprove', array('' => '')),
			'confirm' => Mage::helper('edform')->__('Are you sure?')
		));
		
		$this->getMassactionBlock()->addItem('Pending', array(
			'label'=> Mage::helper('edform')->__('Pending'),
			'url'  => $this->getUrl('*/*/massUnApprove', array('' => '')),
			'confirm' => Mage::helper('edform')->__('Are you sure?')
		));

		return $this;
	}	

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}