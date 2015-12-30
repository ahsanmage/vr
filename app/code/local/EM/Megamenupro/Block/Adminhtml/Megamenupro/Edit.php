<?php
class EM_Megamenupro_Block_Adminhtml_Megamenupro_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
		$model	=	Mage::registry('megamenupro_data');

        $this->_objectId = 'id';
        $this->_blockGroup = 'megamenupro';
        $this->_controller = 'adminhtml_megamenupro';
        
        $this->_updateButton('save', 'label', Mage::helper('megamenupro')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('megamenupro')->__('Delete Item'));
		
		if ($model->getId()) {
			$this->_addButton('duplicate', array(
				'label'     => Mage::helper('adminhtml')->__('Duplicate'),
				'onclick'   => "duplicate()",
				'class'     => 'add',
			), 2);
        }

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), 3);
		
		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('megamenupro_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'megamenupro_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'megamenupro_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}

			function duplicate(){
				editForm.submit($('edit_form').action+'duplicate/1/back/edit');
			}
		";

    }

    public function getHeaderText()
    {
        if( Mage::registry('megamenupro_data') && Mage::registry('megamenupro_data')->getId() ) {
            return Mage::helper('megamenupro')->__("Edit Menu '%s'", $this->htmlEscape(Mage::registry('megamenupro_data')->getName()));
        } else {
            return Mage::helper('megamenupro')->__('Add New Menu');
        }
    }
}