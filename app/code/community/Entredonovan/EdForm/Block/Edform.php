<?php


class Entredonovan_EdForm_Block_Edform extends Mage_Core_Block_Template
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('edform')->__('Commercial Account (CA) Login'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->helper('edform')->getEdOrderPostUrl();
    }
	
	public function getCreateAccountUrl() {
		return $this->helper('edform')->getCreateAccountUrl();
	}
	
	public function getLogoutUrl() {
		return $this->helper('edform')->getLogoutUrl();
    }
    
    public function getAllUserOrders() {
        $helper = $this->helper('edform');
        if ($helper->isCaLoggedIn() && $user = $helper->getLoggedInCa()) {
            $userId     = $user['user_id'];
            $collection = Mage::getModel('edform/edformorders')->getCollection()->addFieldToFilter('user_id', $userId)->setOrder('order_id', 'DESC');
            return $collection;
        } else {
            return array();
        }

    }

    public function getAllUserDrafts() {
        $helper = $this->helper('edform');
        if ($helper->isCaLoggedIn() && $user = $helper->getLoggedInCa()) {
            $userId     = $user['user_id'];
            $collection = Mage::getModel('edform/edformprogress')->getCollection()->addFieldToFilter('user_id', $userId)->setOrder('progress_id', 'DESC');
            return $collection;
        } else {
            return array();
        }
    }

    public function getAllSubCa() {
        $helper = $this->helper('edform');
        if ($helper->isCaLoggedIn() && $user = $helper->getLoggedInCa()) {
            $userId     = $user['user_id'];
            $collection = Mage::getModel('edform/edformusers')->getCollection()->addFieldToFilter('parent_id', $userId)->setOrder('created_at', 'DESC');
            $query      = "SELECT user_id, COUNT(`user_id`) AS `total_orders` FROM `edform_orders` GROUP BY `edform_orders`.`user_id`";
            $collection->getSelect()->joinLeft(
                new Zend_Db_Expr('('.$query.')'),
                'main_table.user_id = t.user_id',
                array('total_orders')
            );
            return $collection;
        } else {
            return array();
        }
    }
	
	
}
?>