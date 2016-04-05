<?php
require_once("lib/Magento.php");

// Deleting attribute options by GUID

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$attribute  = Mage::getSingleton("eav/config")->getAttribute("catalog_product", "brand");

//debug($attribute->getSource()->getAllOptions());die;
//Values have to be deleted one at a time
// Eav Setup.php
$updates = array();
foreach ($attribute->getSource()->getAllOptions() as $option) {
        if ($option['value'] != "") {
            $optionsDelete['delete'][$option['value']] = true;
            $optionsDelete['value'][$option['value']] = true;
        }
}
debug($optionsDelete);
$setup->addAttributeOption($optionsDelete);

?>