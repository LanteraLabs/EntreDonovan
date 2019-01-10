<?php

class Entredonovan_EdForm_Block_Adminhtml_Caorders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('causersGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
	  $this->setUseAjax(true);
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('edform/edformorders')->getCollection();
	  $collection->getSelect()->join( array('causers'=> 'edform_users'), 'causers.user_id = main_table.user_id', array('causers.username'));
	  $collection->setOrder('main_table.order_id', 'DESC');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

	protected function _prepareColumns()
	{
		$helper 		= Mage::helper('edform');

		$this->addColumn('order_id', array(
			'header'    => Mage::helper('edform')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'order_id',
		));

		$this->addColumn('username', array(
			'header'    => Mage::helper('edform')->__('Username'),
			'align'     =>'left',
			'index'     => 'username',
		));

		$this->addColumn('client_name', array(
			'header'    => Mage::helper('edform')->__('Client Name'),
			'align'     =>'left',
			'index'     => 'client_name',
		));


		$this->addColumn('invoicing_email', array(
			'header'    => Mage::helper('edform')->__('Invoice E-mail'),
			'align'     =>'left',
			'index'     => 'invoicing_email',
		));

		$this->addColumn('number_of_pieces', array(
			'header'    => Mage::helper('edform')->__('Pieces'),
			'align'     =>'left',
			'index'     => 'number_of_pieces',
		));


		$this->addColumn('estimated_order_total', array(
			'header'    => Mage::helper('edform')->__('Estimated Total'),
			'align'     =>'left',
			'index'     => 'estimated_order_total',
		));


		$this->addColumn('created_at', array(
			'header'    => Mage::helper('edform')->__('Created At'),
			'align'     =>'left',
			'type'		=> 'date',
			'index'     => 'created_at',
		));


		$this->addColumn('action',
			array(
				'header'    => Mage::helper('edform')->__('Action'),
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' 	=> Mage::helper('edform')->__('View'),
						'url'     	=> array('base' => '*/caorders/view'),
						'field'   	=> 'order_id',
						'target'	=> '_blank',
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
				)
		);

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