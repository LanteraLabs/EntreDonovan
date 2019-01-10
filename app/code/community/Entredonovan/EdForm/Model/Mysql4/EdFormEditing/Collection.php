<?php

class Entredonovan_EdForm_Model_Mysql4_EdFormEditing_Collection
      extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('edform/edformediting');
    }
}

?>