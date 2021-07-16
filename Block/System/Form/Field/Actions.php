<?php

namespace MageSuite\Pagination\Block\System\Form\Field;

class Actions extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('path', [
            'label' => __('Expression'),
            'class' => 'required-entry'
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
