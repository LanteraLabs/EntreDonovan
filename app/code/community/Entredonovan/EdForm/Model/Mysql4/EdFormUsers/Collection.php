<?php

class Entredonovan_EdForm_Model_Mysql4_EdFormUsers_Collection 
      extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('edform/edformusers');
    }
}

?>