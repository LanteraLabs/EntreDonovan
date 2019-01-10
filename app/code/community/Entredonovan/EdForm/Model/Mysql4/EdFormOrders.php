<?php
class Entredonovan_EdForm_Model_Mysql4_EdFormOrders extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        //parent::_construct();
        $this->_init('edform/edformorders','order_id');
    }
}
?>