<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

class MassDelete extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule\AbstractMassAction
{
    const MESSAGE = 'Record(s) were successfully deleted';

    protected function massAction($collection)
    {
        foreach ($collection as $model) {
            $model->delete();
        }
        $this->messageManager->addSuccess(__(self::MESSAGE));
    }
}
