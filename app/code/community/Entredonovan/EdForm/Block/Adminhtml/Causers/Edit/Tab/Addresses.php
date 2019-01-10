<?php
class Entredonovan_EdForm_Block_Adminhtml_Causers_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Form
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
		$physicalAddressId = $data['physical_address_id'];
		$physicalAddress = Mage::getModel('edform/edformaddresses')->load($physicalAddressId);
		$physicalAddressData = $physicalAddress->getData();
		foreach ($physicalAddressData as $key => $value) {
			$data['physical_'.$key] = $value;
		}

		$billingAddressId = $data['billing_address_id'];
		$billingAddress = Mage::getModel('edform/edformaddresses')->load($billingAddressId);
		$billingAddressData = $billingAddress->getData();
		foreach ($billingAddressData as $key => $value) {
			$data['billing_'.$key] = $value;
		}

		$shippingAddressId = $data['shipping_address_id'];
		$shippingAddress = Mage::getModel('edform/edformaddresses')->load($shippingAddressId);
		$shippingAddressData = $shippingAddress->getData();
		foreach ($shippingAddressData as $key => $value) {
			$data['shipping_'.$key] = $value;
		}
		$data['physical_region_id_init']	= $data['physical_region_id'];
		$data['billing_region_id_init']		= $data['billing_region_id'];
		$data['shipping_region_id_init']	= $data['shipping_region_id'];



		//physical address
		$fieldset = $form->addFieldset('causers_form_physical', array(
			'legend'=>Mage::helper('edform')->__('Physical Address')
		));

		$fieldset->addField('physical_firstname', 'text', array(
			'label' => Mage::helper('edform')->__('Firstname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_firstname',
		));

		$fieldset->addField('physical_lastname', 'text', array(
			'label' => Mage::helper('edform')->__('Lastname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_lastname',
		));

		$fieldset->addField('physical_street1', 'text', array(
			'label' => Mage::helper('edform')->__('Street 1'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_street1',
		));

		$fieldset->addField('physical_street2', 'text', array(
			'label' => Mage::helper('edform')->__('Street 2'),
			'class' => '',
			//'required' => true,
			'name' => 'physical_street2',
		));

		$fieldset->addField('physical_city', 'text', array(
			'label' => Mage::helper('edform')->__('City'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_city',
		));

		$fieldset->addField('physical_state', 'text', array(
			'label' => Mage::helper('edform')->__('State'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_state',
		));

		$fieldset->addField('physical_region_id', 'select', array(
			'label'     => Mage::helper('edform')->__('State'),
			'class'     => 'required-entry',
			'values'    => array(),
			'name'      => 'physical_region_id',
		));

		$fieldset->addField('physical_region_id_init', 'hidden',
			array(
				'name'      => 'physical_region_id_init',
				'label'     => 'physical state',
				'readonly'  => true,
			)
		);


		$fieldset->addField('physical_zip', 'text', array(
			'label' => Mage::helper('edform')->__('Zip'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_zip',
		));

		$fieldset->addField('physical_phone', 'text', array(
			'label' => Mage::helper('edform')->__('Phone'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_phone',
		));

		$fieldset->addField('physical_country_id', 'select', array(
			'label' => Mage::helper('edform')->__('Country'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'physical_country_id',
			'values'	=> Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
			'onchange' => 'loadStates(this);',
		));




		//billing address
		$fieldset = $form->addFieldset('causers_form_billing', array(
			'legend'=>Mage::helper('edform')->__('Billing Address')
		));

		$fieldset->addField('billing_firstname', 'text', array(
			'label' => Mage::helper('edform')->__('Firstname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_firstname',
		));

		$fieldset->addField('billing_lastname', 'text', array(
			'label' => Mage::helper('edform')->__('Lastname'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_lastname',
		));

		$fieldset->addField('billing_street1', 'text', array(
			'label' => Mage::helper('edform')->__('Street 1'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_street1',
		));

		$fieldset->addField('billing_street2', 'text', array(
			'label' => Mage::helper('edform')->__('Street 2'),
			'class' => '',
			//'required' => true,
			'name' => 'billing_street2',
		));

		$fieldset->addField('billing_city', 'text', array(
			'label' => Mage::helper('edform')->__('City'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_city',
		));

		$fieldset->addField('billing_state', 'text', array(
			'label' => Mage::helper('edform')->__('State'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_state',
		));

		$fieldset->addField('billing_region_id', 'select', array(
			'label'     => Mage::helper('edform')->__('State'),
			'class'     => 'required-entry',
			'values'    => array(),
			'name'      => 'billing_region_id',
		));

		$fieldset->addField('billing_region_id_init', 'hidden',
			array(
				'name'      => 'billing_region_id_init',
				'label'     => 'billing state',
				'readonly'  => true,
			)
		);

		$fieldset->addField('billing_zip', 'text', array(
			'label' => Mage::helper('edform')->__('Zip'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_zip',
		));

		$fieldset->addField('billing_phone', 'text', array(
			'label' => Mage::helper('edform')->__('Phone'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_phone',
		));

		$fieldset->addField('billing_country_id', 'select', array(
			'label' => Mage::helper('edform')->__('Country'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'billing_country_id',
			'values'	=> Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
			'onchange' => 'loadStates(this);',
		));





		//shipping address
		$fieldset = $form->addFieldset('causers_form_shipping', array(
			'legend'=>Mage::helper('edform')->__('Shipping Address')
		));

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
			'class' => '',
			//'required' => true,
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

		$fieldset->addField('shipping_region_id_init', 'hidden',
			array(
				'name'      => 'shipping_region_id_init',
				'label'     => 'shipping state',
				'readonly'  => true,
			)
		);

		$fieldset->addField('shipping_region_id', 'select', array(
			'label'     => Mage::helper('edform')->__('State'),
			'class'     => 'required-entry',
			'values'    => array(),
			'name'      => 'shipping_region_id',
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

		$field = $fieldset->addField('shipping_country_id', 'select', array(
			'label' => Mage::helper('edform')->__('Country'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'shipping_country_id',
			'values'	=> Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
			'onchange' => 'loadStates(this);',
		));

		$jQueryUrl		= Mage::getDesign()->getSkinUrl("edform/js/jquery-1.12.4.js", array('_area' => 'frontend'));
		$field->setAfterElementHtml('
			<script language="javascript" src="'.$jQueryUrl.'"></script>
			<script>
			//<![CDATA[
				jQuery.noConflict();
				jq = jQuery;
				
				function loadStates(ele) {
					elementName = jq(ele).prop("id");
					addressType = elementName.replace("_country_id", "");
					countryCode = jq(ele).val();
					found       = false;
			
					if (found) {
						console.log("found for: " + addressType);
						jq("#" + addressType + "_region_id").addClass("validate-select").parent().parent().show();
						jq("#" + addressType + "_state").removeClass("required-entry").parent().parent().hide();
					} else {
						console.log("NOT found");
						jq("#" + addressType + "_region_id").removeClass("validate-select").removeClass("required-entry").parent().parent().hide();
						jq("#" + addressType + "_state").addClass("required-entry").parent().parent().show();
					}
			
					if (countryCode) {
					    
						jQuery("#loading-mask").hide();
					    jQuery.ajax({
							async: false,
							type: "GET",
							dataType: "json",
							url: "'.Mage::getBaseUrl().'edform/index/getRegions?countryCode=" + countryCode,
							success: function(data) {
								jQuery("#" + addressType + "_region_id").find(\'option\').remove();
								console.log("Length: " + Object.keys(data).length);
								if (Object.keys(data).length > 0) {
									found = true;
								}
								
								if (found) {
									console.log("found for: " + addressType);
									jQuery("#" + addressType + "_region_id").addClass("validate-select").parent().parent().show();
									jQuery("#" + addressType + "_state").removeClass("required-entry").parent().parent().hide();
									
								} else {
									console.log("NOT found");
									jQuery("#" + addressType + "_region_id").removeClass("validate-select").removeClass("required-entry").parent().parent().hide();
									jQuery("#" + addressType + "_state").addClass("required-entry").parent().parent().show();
								}
								
								Object.keys(data).forEach(function(key) {
									//console.log(\'Key : \' + key + \', Value : \' + data[key])
									jQuery("#" + addressType + "_region_id").append(\'<option value="\'+ key +\'">\' + data[key] + \'</option>\');
								});
									
								if (found) {
									regionIdInit 	= jQuery("#"+ addressType + "_region_id_init").val();
									if (regionIdInit) {
										jQuery("#" + addressType + "_region_id").val(regionIdInit);
									}
								}
								
								jQuery("#loading-mask").hide();
							}
						});
					}
				}
				
				jQuery( document ).ready(function() {
					physicalCountryIdEle 	= jQuery("#physical_country_id");
					billingCountryIdEle 	= jQuery("#billing_country_id");
					shippingCountryIdEle 	= jQuery("#shipping_country_id");
					loadStates(physicalCountryIdEle);
					loadStates(billingCountryIdEle);
					loadStates(shippingCountryIdEle);
				});

			//]]>
			</script>
		');


		if($this->getRequest()->getParam('id')){

		}


		$form->setValues($data);


		return parent::_prepareForm();
 	}
}
?>