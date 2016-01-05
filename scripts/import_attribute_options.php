<?php
require_once("lib/Magento.php");
    
    $parseObj = new CSVParser();

    $csv_directory  = 'csv/attributes/';
    $csv_file_name  = $csv_directory.'brands2jan16.csv';
    $attribute_name = 'brands';

    $item_rows = $parseObj->parseCSVFile($csv_file_name);       
    
    $arg_attribute = 'brand';

    foreach($item_rows as $item){
        $brand  =   ucwords(strtolower($item['brands']));
        addAttributeValue($arg_attribute,$brand);
    }
    
    function addAttributeValue($arg_attribute, $arg_value) {
        $attribute_model    = Mage::getModel('eav/entity_attribute');
        $attribute_code     = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute          = $attribute_model->load($attribute_code);

        $value['option'] = array($arg_value, $arg_value);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        $attribute->save();
        echo "Attribute '" . $arg_attribute . "' with 
        option '" . $arg_value . "' saved.<br />";
        
    }
?>