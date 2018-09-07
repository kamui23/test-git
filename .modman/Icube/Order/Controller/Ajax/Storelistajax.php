<?php

namespace Icube\Order\Controller\Ajax;

class Storelistajax extends \Magento\Framework\App\Action\Action
{
    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return ($this->getRequest()->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * Storelist AJAX
     *
     * @return boolean
     */
    public function execute()
    {
        /* activate this when it's live */
        /*if (!$this->isAjax()) {
            return;
        }*/

        $response = array();

        // try {
        $params = $this->getRequest()->getParams();
        if ($params['page'] == 'pdp' && $params['product_id'] != NULL) {
            $storelist = $this->_objectManager->create('Icube\Order\Helper\Data')->getStorelistPdp($params['product_id'], $params['product_qty'], $params['store_code']);
        } else {
            $storelist = $this->_objectManager->create('Icube\Order\Helper\Data')->getStorelist($params['store_code']);
        }


        if (count($storelist) > 0) {
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
        }

        // foreach ($storelist as $data) {
        //   $id = $data['state'];
        //   if (isset($result[$id])) {
        //      $result[$id][] = $data;
        //   } else {
        //      $result[$id] = array($data);
        //   }
        // }

        /*$uniques = array();
        foreach ($storelist as $obj) {
            $uniques[$obj['state']] = $obj;
        }*/

        // $fixstorelist = $result;

        // $response['group'] = $fixstorelist;
        $response['ungroup'] = $storelist;

        // } catch (\Exception $e) {
        //     $msg = "";
        //     if ($this->_getSession()->getUseNotice(true)) {
        //         $msg = $e->getMessage();
        //     } else {
        //         $messages = array_unique(explode("\n", $e->getMessage()));
        //         foreach ($messages as $message) {
        //             $msg .= $message.'<br/>';
        //         }
        //     }

        //     $response['status'] = 'error';
        //     $response['ungroup'] = $msg;
        // }


        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
        );
    }
}

?>