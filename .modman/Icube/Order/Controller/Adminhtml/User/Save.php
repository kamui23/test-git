<?php

namespace Icube\Order\Controller\Adminhtml\User;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Security\Model\SecurityCookie;

class Save extends \Magento\User\Controller\Adminhtml\User\Save
{
    /**
     * @var SecurityCookie
     */
    private $securityCookie;

    /**
     * Get security cookie
     *
     * @return SecurityCookie
     * @deprecated
     */
    private function getSecurityCookie()
    {
        if (!($this->securityCookie instanceof SecurityCookie)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(SecurityCookie::class);
        } else {
            return $this->securityCookie;
        }
    }

    /**
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $userId = (int)$this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        /** @var $model \Magento\User\Model\User */
        $model = $this->_userFactory->create()->load($userId);
        if ($userId && $model->isObjectNew()) {
            $this->messageManager->addError(__('This user no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', []);
        if (count($uRoles)) {
            $model->setRoleId($uRoles[0]);
        }

        /** @var $currentUser \Magento\User\Model\User */
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if ($userId == $currentUser->getId() && $this->_objectManager->get(
                'Magento\Framework\Validator\Locale'
            )->isValid(
                $data['interface_locale']
            )
        ) {
            $this->_objectManager->get(
                'Magento\Backend\Model\Locale\Manager'
            )->switchBackendInterfaceLocale(
                $data['interface_locale']
            );
        }

        /** Before updating admin user data, ensure that password of current admin user is entered and is correct */
        $currentUserPasswordField = \Magento\User\Block\User\Edit\Tab\Main::CURRENT_USER_PASSWORD_FIELD;
        $isCurrentUserPasswordValid = isset($data[$currentUserPasswordField])
            && !empty($data[$currentUserPasswordField]) && is_string($data[$currentUserPasswordField]);
        try {
            if (!($isCurrentUserPasswordValid)) {
                throw new AuthenticationException(__('You have entered an invalid password for current user.'));
            }
            $currentUser->performIdentityCheck($data[$currentUserPasswordField]);
            $saveduser = $model->save();

            $adminpos = $this->_objectManager->create('Icube\Order\Model\AdminPos');
            if (isset($data['user_id']) && $adminpos->load($data['user_id'], 'user_id')->getStoreCode()) {
                $adminpos->setStoreCode($data['store_permission']);
            } else {
                $adminpos->setUserId($saveduser['user_id']);
                $adminpos->setStoreCode($data['store_permission']);
            }
            $adminpos->save();


            $model->sendNotificationEmailsIfRequired();

            $this->messageManager->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('adminhtml/*/');
        } catch (UserLockedException $e) {
            $this->_auth->logout();
            $this->getSecurityCookie()->setLogoutReasonCookie(
                \Magento\Security\Model\AdminSessionsManager::LOGOUT_REASON_USER_LOCKED
            );
            $this->_redirect('adminhtml/*/');
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $this->messageManager->addError(__('You have entered an invalid password for current user.'));
            $this->redirectToEdit($model, $data);
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($model, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($model, $data);
        }
    }
}