<?php

namespace Icube\Order\Block\User\Edit\Tab;

class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $model = $this->_coreRegistry->registry('permissions_user');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Account Information')]);

        if ($model->getUserId()) {
            $baseFieldset->addField('user_id', 'hidden', ['name' => 'user_id']);
        } else {
            if (!$model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $baseFieldset->addField(
            'username',
            'text',
            [
                'name'     => 'username',
                'label'    => __('User Name'),
                'id'       => 'username',
                'title'    => __('User Name'),
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'firstname',
            'text',
            [
                'name'     => 'firstname',
                'label'    => __('First Name'),
                'id'       => 'firstname',
                'title'    => __('First Name'),
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'lastname',
            'text',
            [
                'name'     => 'lastname',
                'label'    => __('Last Name'),
                'id'       => 'lastname',
                'title'    => __('Last Name'),
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'label'    => __('Email'),
                'id'       => 'customer_email',
                'title'    => __('User Email'),
                'class'    => 'required-entry validate-email',
                'required' => true
            ]
        );

        $isNewObject = $model->isObjectNew();
        if ($isNewObject) {
            $passwordLabel = __('Password');
        } else {
            $passwordLabel = __('New Password');
        }
        $confirmationLabel = __('Password Confirmation');
        $this->_addPasswordFields($baseFieldset, $passwordLabel, $confirmationLabel, $isNewObject);

        $baseFieldset->addField(
            'interface_locale',
            'select',
            [
                'name'   => 'interface_locale',
                'label'  => __('Interface Locale'),
                'title'  => __('Interface Locale'),
                'values' => $this->_LocaleLists->getTranslatedOptionLocales(),
                'class'  => 'select'
            ]
        );

        if ($this->_authSession->getUser()->getId() != $model->getUserId()) {
            $baseFieldset->addField(
                'is_active',
                'select',
                [
                    'name'    => 'is_active',
                    'label'   => __('This account is'),
                    'id'      => 'is_active',
                    'title'   => __('Account Status'),
                    'class'   => 'input-select',
                    'options' => ['1' => __('Active'), '0' => __('Inactive')]
                ]
            );
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $posCol = $om->create('Wyomind\PointOfSale\Model\PointOfSale')->getCollection();
        $storecode[] = array('value' => null, 'label' => 'No Store');
        $storecode[] = array('value' => 'all', 'label' => 'All Store');
        foreach ($posCol as $pos) {
            $storecode[] = array('value' => $pos->getStoreCode(), 'label' => $pos->getStoreCode() . ' - ' . $pos->getName());
        }

        $baseFieldset->addField(
            'store_permission',
            'select',
            [
                'name'   => 'store_permission',
                'label'  => __('Store Permission'),
                'title'  => __('Store Permission'),
                'values' => $storecode,
                'class'  => 'select'
            ]
        );

        $baseFieldset->addField('user_roles', 'hidden', ['name' => 'user_roles', 'id' => '_user_roles']);

        $currentUserVerificationFieldset = $form->addFieldset(
            'current_user_verification_fieldset',
            ['legend' => __('Current User Identity Verification')]
        );
        $currentUserVerificationFieldset->addField(
            self::CURRENT_USER_PASSWORD_FIELD,
            'password',
            [
                'name'     => self::CURRENT_USER_PASSWORD_FIELD,
                'label'    => __('Your Password'),
                'id'       => self::CURRENT_USER_PASSWORD_FIELD,
                'title'    => __('Your Password'),
                'class'    => 'input-text validate-current-password required-entry',
                'required' => true
            ]
        );

        $data = $model->getData();
        unset($data['password']);
        unset($data[self::CURRENT_USER_PASSWORD_FIELD]);
        if (isset($data['user_id'])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $adminpos = $objectManager->create('Icube\Order\Model\AdminPos');
            $getadmin = $adminpos->load($data['user_id'], 'user_id');
            $data['store_permission'] = $getadmin['store_code'];
        }
        $form->setValues($data);

        $this->setForm($form);

        return \Magento\Backend\Block\Widget\Form\Generic::_prepareForm();
    }
}