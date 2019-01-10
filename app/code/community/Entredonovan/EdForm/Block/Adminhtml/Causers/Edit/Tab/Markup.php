<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit_Tab_Markup extends Mage_Adminhtml_Block_Widget_Form
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
			'legend'=>Mage::helper('edform')->__('Standard Order Prep')
		));

		/*Edit field as text type*/

		$fieldset->addField('logo_type', 'radios', array(
			'label' => Mage::helper('edform')->__('Show Logo'),
			'class' => 'validate-one-required-by-name',
			//'required' => true,
			'name' => 'logo_type',
			'values'	=> array(
				array('value'=>'White Label', 'label' => 'White Label'),
				array('value'=>'Show eD Logo', 'label' => 'Show eD Logo'),
				array('value'=>'Show CA Logo', 'label' => 'Show CA Logo'),
				array('value'=>'Show Both Logo', 'label' => 'Show Both Logo'),
			),
			'style' => 'margin: 0px 5px 0px 10px;',
		));


		$fieldset->addField('ship_type', 'radios', array(
			'label' => Mage::helper('edform')->__('Ship Type'),
			'class' => 'validate-one-required-by-name',
			//'required' => true,
			'name' => 'ship_type',
			'values'	=> array(
				array('value'=>'flat', 'label' => 'Flat'),
				array('value'=>'pressed_hanging', 'label' => 'Pressed Hanging'),
			),
			'style' => 'margin: 0px 5px 0px 10px;',
		));


		if (false) {
			$fieldset->addField('ship_flat', 'text', array(
				'label' => Mage::helper('edform')->__('Ship Flat'),
				'class' => 'required-entry validate-number',
				#'required' => true,
				'name' => 'ship_flat',
			));

			$fieldset->addField('ship_pressed_hanging', 'text', array(
				'label' => Mage::helper('edform')->__('Pressed & Hanging'),
				'class' => 'required-entry',
				#'required' => true,
				'name' => 'ship_pressed_hanging',
			));
		}

		$fieldset->addField('retail_markup', 'text', array(
			'label' => Mage::helper('edform')->__('Retail Markup'),
			'class' => 'validate-number',
			#'required' => true,
			'name' => 'retail_markup',
		));


		$fieldset->addField('ship_wood_hanger', 'text', array(
			'label' => Mage::helper('edform')->__('Wood Hanger'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'ship_wood_hanger',
		));

		$fieldset->addField('ship_garment_bag', 'text', array(
			'label' => Mage::helper('edform')->__('Garment Bag'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'ship_garment_bag',
		));

		$fieldset->addField('ship_sew_label', 'text', array(
			'label' => Mage::helper('edform')->__('Sew Label'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'ship_sew_label',
		));

		$fieldset->addField('ship_sew_client_monogram_label', 'text', array(
			'label' => Mage::helper('edform')->__('Sew client monogram'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'ship_sew_client_monogram_label',
		));

		$fieldset->addField('volume_tier_with_ed', 'text', array(
			'label' => Mage::helper('edform')->__('Volume tier with eD'),
			//'class' => 'required-entry',
			//'required' => true,
			'name' => 'volume_tier_with_ed',
		));

		$fieldset->addField('account_type', 'radios', array(
			'label' => Mage::helper('edform')->__('Account Type'),
			'class' => 'validate-one-required-by-name',
			//'required' => true,
			'name' => 'account_type',
			'values'	=> array(
								array('value'=>'WC', 'label' => 'WC'),
								array('value'=>'IC', 'label' => 'IC'),
								array('value'=>'CA', 'label' => 'CA'),
			),
			'style' => 'margin: 0px 5px 0px 10px;',
		));

		$fieldset->addField('weight_type', 'radios', array(
			'label' => Mage::helper('edform')->__('Show Weight in'),
			'class' => 'validate-one-required-by-name',
			//'required' => true,
			'name' => 'weight_type',
			'values'	=> array(
				array('value'=>'kg', 'label' => 'kg'),
				array('value'=>'lbs', 'label' => 'lbs'),
			),
			'style' => 'margin: 0px 5px 0px 10px;',
		));


		if (false) {
			$fieldset->addField('tax_rate', 'text', array(
				'label' => Mage::helper('edform')->__('Tax Rate'),
				'class' => 'required-entry validate-number',
				'required' => true,
				'name' => 'tax_rate',
			));

			$fieldset->addField('tax_state', 'text', array(
				'label' => Mage::helper('edform')->__('Tax State'),
				'class' => 'required-entry',
				'required' => true,
				'name' => 'tax_state',
			));
		}

		$fieldset->addField('payment_type', 'radios', array(
			'label' => Mage::helper('edform')->__('Payment Type'),
			'class' => 'validate-one-required-by-name',
			//'required' => true,
			'name' => 'payment_type',
			'values'	=> array(
				array('value'=>'Veem', 'label' => 'Veem'),
				array('value'=>'CC on file', 'label' => 'CC on file'),
				array('value'=>'Other', 'label' => 'Other'),
			),
			'style' => 'margin: 0px 5px 0px 10px;',
		));

		$fieldset->addField('payment_method', 'text', array(
			'label' => Mage::helper('edform')->__('Payment Method'),
			//'class' => 'required-entry',
			//'required' => true,
			'name' => 'payment_method',
		));

		$fieldset->addField('shipping_instruction', 'textarea', array(
			'label' => Mage::helper('edform')->__('Instruction'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_instruction',
		));

		$field = $fieldset->addField('privacy_policy', 'textarea', array(
			'label' => Mage::helper('edform')->__('Sales Policy'),
			//'class' => 'required-entry',
			//'required' => true,
			'name' => 'Sales Policy',
		));


		 if($this->getRequest()->getParam('id')){

		 }

		 $field->setAfterElementHtml('
		 	<script>
		 	//< ![CDATA
		 		function togglePaymentType() {
		 		    jQuery("#payment_method").removeClass("required-entry").parents("tr").hide();
					paymentType = jQuery("input[name=payment_type]:checked").val();
					if ("Other" == paymentType) {
						jQuery("#payment_method").addClass("required-entry").parents("tr").show();    
					}
		 		}
		 		jQuery( document ).ready(function() {
		 		    togglePaymentType();
		 		    
		 		    jQuery("input[name=payment_type]").change(function() {
		 		    	togglePaymentType(); 
		 		    });
		 		    
				});
				 
				
			 
			//]]>
		 	</script>
		 ');

		$form->setValues($data);
		return parent::_prepareForm();
 	}
}
?>