<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magento.com>
 */
class EM_Em0122settings_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    public function addEMResposiveRtlCss(){
        if(Mage::helper('themeframework/settings')->getGeneral_DisableResponsive()!=1){
            if(Mage::helper('themeframework/settings')->getGeneral_RightToLeft()!=1){
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive.css');
            }else{                
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css'); 
            }            
        }else{
            
            if(Mage::helper('themeframework/settings')->getGeneral_RightToLeft()!=1){                
                $this->addItem('skin_css', 'css/bootstrap/bootstrap.css');                  
            }else{
                $this->addItem('skin_css', 'css/bootstrap/bootstrap-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css'); 
            }
			$this->addItem('skin_css', 'css/responsive.css');
        }
	}
    public function addEMItem($type, $name, $params=null, $if=null, $cond=null){    	
    	if ($type == 'lesscss') {
            $_lessCacheId = 'em0122_less_cache_store_'.Mage::app()->getStore()->getCode();
    		$_newLessConfig = Mage::helper('em0122settings')->getAllCssConfig();
    		if (false !== ($data = Mage::app()->getCache()->load($_lessCacheId))) {
    			$_oldLessConfig = unserialize($data);	
                if (count(array_diff_assoc($_oldLessConfig,$_newLessConfig)) == 0 && count(array_diff_assoc($_newLessConfig,$_oldLessConfig)) == 0) {
    				$_hasChange = false;                    
    			}else{
    				$_hasChange = true;				
    				Mage::app()->getCache()->remove($_lessCacheId);
    				Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0122_LESS_CSS_CACHE'), 86400);		
    			}
    		} else {
    			$_hasChange = true;	
    			Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0122_LESS_CSS_CACHE'), 86400);
    		}			
    		$pathFile = Mage::getBaseDir('media') . DS . 'em0122' . DS . 'css' . DS . 'less' . DS . 'variables_store_'.Mage::app()->getStore()->getCode().'.less';					
    		if ($_hasChange == true || !file_exists($pathFile)) {
    			$stringless = '';
    			if (isset($_newLessConfig['@function_url'])) {
    				foreach (explode(',', $_newLessConfig['@function_url']) as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    			if (isset($_newLessConfig['@variables_url'])) {
    				foreach (explode(',', $_newLessConfig['@variables_url']) as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    			if (isset($_newLessConfig['additional_css_file'])) {
    				foreach ($_newLessConfig['additional_css_file'] as $file) {
    					$stringless .= '@import ' . $file . ';';
    				}
    			}
    
    			foreach ($_newLessConfig as $typo => $value) {
    				if ($typo != 'additional_css_file') {
    					$stringless .= $typo . ":" . $value . ';';
    				}
    
    			}
    			file_put_contents($pathFile, $stringless);
    		}
            $href = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em0122/css/less/variables_store_'.Mage::app()->getStore()->getCode().'.less';
            $this->addItem('link_rel', $href, 'rel="stylesheet/less" type="text/css"');

        }   
    	else
    	{ 
            if($type == 'em_js')
            {                              
                $href = $this->getSkinUrl($name);
                $this->addItem($type, $href, $params, $if, $cond);                             
            }
            else
            {
                if ($if != null) { 
                    if (strcmp('configswatches_general_enabled', $if)==0) {
                        if (Mage::getStoreConfig('configswatches/general/enabled') == $cond) {
                            $this->addItem($type, $name);
                        }
                    } else {
                        if (Mage::helper('themeframework/managetheme')->getConfigTheme($if) == $cond) {
                            $this->addItem($type, $name);
                        }
                    }

                }
				else
                     $this->addItem($type, $name, $params, $if, $cond); 
            }            
    		
		}				
    }

    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe)
    {
        $params = $itemParams ? ' ' . $itemParams : '';
        $href   = $itemName;
        switch ($itemType) {
            case 'rss':
                $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />',
                    $href, $params
                );
                break;
            case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
                break;
            
           	case 'em_js':
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s" %s></script>', $href, $params);
                break;
                                     
        }
    }


}
