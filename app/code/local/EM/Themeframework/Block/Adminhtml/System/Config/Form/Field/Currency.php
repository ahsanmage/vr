<?php 
class EM_Themeframework_Block_Adminhtml_System_Config_Form_Field_Currency extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
     * Override field method to add js
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$html = parent::_getElementHtml($element);
        $html .= '<ul class="em-preview-list">';
		foreach (Mage::getModel('themeframework/config_header_currency')->toOptionArray() as $row) {            
			$html .= sprintf('<li class="em-img-preview"><p><a href="%s" class="%s %s" data-lightbox="currency-style" data-title="%s"><img width="100px" height="100px" alt="" src="%s" /></a></p><p><span>%s</span></p></li> ', 
				Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/preview/currency-'.$row['value'].'.jpg',
                $element->getId(),
				$element->getValue() == $row['value'] ? 'selected' : '',
                $row['label'],
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/preview/currency-'.$row['value'].'.jpg',
                $row['label']);            
		}
        $html .= '</ul>';
		
        return $html;
    } 
}
?>