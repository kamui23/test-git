<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Controller;

/**
 * Inchoo Custom router Controller Router
 *
 * @author      Zoran Salamun <zoran.salamun@inchoo.net>
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    protected $_branchFactory;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Icube\Brands\Model\BrandFactory $brandFactory
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_branchFactory = $brandFactory;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        /*
         * We will search “examplerouter” and “exampletocms” words and make forward depend on word
         * -examplerouter will forward to base router to match inchootest front name, test controller path and test controller class
         * -exampletocms will set front name to cms, controller path to page and action to view
         */
        // $identifier = trim($request->getPathInfo(), '/');

        // if(strpos($identifier, 'exampletocms') !== false) {
        //     return;
        //     /*
        //      * We must set module, controller path and action name + we will set page id 5 witch is about us page on
        //      * default magento 2 installation with sample data.
        //      */
        //     $request->setModuleName('cms')->setControllerName('page')->setActionName('view')->setParam('page_id', 5);
        // } else if(strpos($identifier, 'examplerouter') !== false) {
        //     /*
        //      * We must set module, controller path and action name for our controller class(Controller/Test/Test.php)
        //      */
        //     $request->setModuleName('brands')->setControllerName('view')->setActionName('index');
        // } else {
        //     //There is no match
        //     return;
        // }

        // /*
        //  * We have match and now we will forward action
        //  */
        // return $this->actionFactory->create(
        //     'Magento\Framework\App\Action\Forward',
        //     ['request' => $request]
        // );
        // 

        $identifier = trim($request->getPathInfo(), '/');

        if (strpos($identifier, 'brand/view/index/id') !== false) {
            // called via id 
            return null;
        } else if (strpos($identifier, 'brand/') !== false) {
            $patharr = explode("/", $identifier);
            $urlpath = end($patharr);
            $modelcollection = $this->_branchFactory->create()->getCollection();
            $modelcollection->addFieldToFilter('category_url', $urlpath);
            if ($modelcollection->count() >= 1 && $brand = $modelcollection->getFirstItem()) {
                // var_dump($brand->debug());
                $request->setModuleName('brand')->setControllerName('view')->setActionName('index')->setParam('id', $brand->getId());
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, '/' . $identifier);
                $request->setPathInfo('/' . $identifier);
                return;
            }
            // not found any Category URL
            return null;
        }
        //There is no match
        return null;
    }
}