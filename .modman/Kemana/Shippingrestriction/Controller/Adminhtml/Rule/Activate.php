<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

class Activate extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule\AbstractMassAction
{
    protected function massAction($collection)
    {
        foreach ($collection as $model) {
            $model->setIsActive($this->_srhelper::ACTIVE);
            $model->save();
        }
        $message = __($this->_srhelper::RECORD_UPDATED_MESSAGE);
        $this->messageManager->addSuccess($message);
    }
}
