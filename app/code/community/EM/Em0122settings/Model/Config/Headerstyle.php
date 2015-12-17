<?php
class EM_Em0122settings_Model_Config_Headerstyle
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'style01', 'label'=>Mage::helper('adminhtml')->__('Style 01')),
            array('value' => 'style02', 'label'=>Mage::helper('adminhtml')->__('Style 02')),
			array('value' => 'style03', 'label'=>Mage::helper('adminhtml')->__('Style 03')),
			array('value' => 'style04', 'label'=>Mage::helper('adminhtml')->__('Style 04')),
			array('value' => 'style05', 'label'=>Mage::helper('adminhtml')->__('Style 05')),
			array('value' => 'style06', 'label'=>Mage::helper('adminhtml')->__('Style 06')),
        );
    }
}
?>