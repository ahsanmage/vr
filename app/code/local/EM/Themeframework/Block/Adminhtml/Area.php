<?php
/**
 * EMThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the framework to newer
 * versions in the future. If you wish to customize the framework for your
 * needs please refer to http://www.emthemes.com/ for more information.
 *
 * @category    EM
 * @package     EM_ThemeFramework
 * @copyright   Copyright (c) 2012 CodeSpot JSC. (http://www.emthemes.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Giao L. Trinh (giao.trinh@emthemes.com)
 */

class EM_Themeframework_Block_Adminhtml_Area extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
		$this->_blockGroup = 'themeframework';
        $this->_controller = 'adminhtml_area';
        $this->_headerText = Mage::helper('themeframework')->__('Layout Manager');
        $this->_addButtonLabel = Mage::helper('themeframework')->__('Add New Layout');
		
		$previewBlockUrl = Mage::getModel('core/url')->getDirectUrl('themeframework/area/previewBlock/key/'.Mage::getSingleton('adminhtml/url')->getSecretKey('cms_block', 'edit').'/fblock_key/'.Mage::getSingleton('adminhtml/url')->getSecretKey('fblock', 'edit'));
		$this->_addButton('previewBlock', array(
			'label' => Mage::helper('themeframework')->__("Preview Blocks"),
			'onclick' => 'window.open(\''.$previewBlockUrl.'\', \'_blank\')',
			'class' => 'show-hide'
		));
		$previewAreaUrl = Mage::getModel('core/url')->getDirectUrl('themeframework/area/previewArea/');
		$this->_addButton('previewArea', array(
			'label' => Mage::helper('themeframework')->__("Preview Areas"),
			'onclick' => 'window.open(\''.$previewAreaUrl.'\', \'_blank\')',
			'class' => 'show-hide'
		));
        parent::__construct();        
    }
    
}