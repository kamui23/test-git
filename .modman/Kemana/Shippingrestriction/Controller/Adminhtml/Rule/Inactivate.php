<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

class Inactivate extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule\AbstractMassAction
{
    protected function massAction($collection)
    {
        foreach ($collection as $model) {
            $model->setIsActive($this->_srhelper::INACTIVE);
            $model->save();
        }
        $message = __($this->_srhelper::RECORD_UPDATED_MESSAGE);
        $this->messageManager->addSuccess($message);
    }
}
