<?php
class Entredonovan_EdForm_Model_Mysql4_EdFormEditing extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        //parent::_construct();
        $this->_init('edform/edformediting','edit_id');
    }
}
?>