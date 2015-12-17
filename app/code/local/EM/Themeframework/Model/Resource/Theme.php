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

class EM_Themeframework_Model_Resource_Theme extends Mage_Core_Model_Resource_Db_Abstract {
    const CACHE_TAG     = 'themeframework_theme';
    protected $_cacheTag= 'themeframework_theme';

    protected function _construct()
    {
        $this->_init('themeframework/theme', 'theme_id');
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'theme_id = ?'     => (int) $object->getId(),
        );
        return parent::_beforeDelete($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
	
	public function getActiveConfigData($scope, $scopeId){
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('core/config_data'),array('value'))
            ->where('path = :path AND scope = :scope AND scope_id = :scope_id');
        $binds = array(
            ':path'     =>  EM_Themeframework_Model_Theme::CONFIG_PATH_ACTIVE,
            ':scope'     =>  $scope,
            ':scope_id'  =>  $scopeId
        );
        return $adapter->fetchOne($select, $binds);
    }

    public function isUseOtherTheme($scope, $scopeId){
        $pathArray = array('design/package/name','design/theme/template','design/theme/layout','design/theme/skin','design/theme/default');
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('core/config_data'),'*')
            ->where('path in (?) AND scope = :scope AND scope_id = :scope_id',$pathArray);
        $binds = array(
            ':scope'     =>  $scope,
            ':scope_id'  =>  $scopeId
        );
        return $adapter->fetchAll($select, $binds);
    }
}