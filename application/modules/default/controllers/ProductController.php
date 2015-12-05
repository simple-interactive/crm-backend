<?php

/**
 * @class ProductController
 */
class ProductController extends App_Controller_Base {

    use App_Trait_MenuService;

    public function indexAction()
    {
        if ($this->getRequest()->isGet()) {
            $this->view->product = App_Map_Product::execute(
                $this->getMenuService()->getProduct($this->user, $this->getParam('id', false))
            );
        }
        elseif ($this->getRequest()->isPost()) {
            $this->view->product = App_Map_Product::execute(
                $this->getMenuService()->saveProduct(
                    $this->user,
                    App_Model_Section::fetchOne(['id' => $this->getParam('sectionId', false)]),
                    App_Model_Product::fetchOne(['id' => $this->getParam('id', false)]),
                    $this->getParam('title', false),
                    $this->getParam('description', false),
                    $this->getParam('price', false),
                    $this->getParam('weight', false),
                    $this->getParam('images', false),
                    $this->getParam('ingredients', null),
                    $this->getParam('options', null),
                    $this->getParam('exists', false)
                ));
        }
        else {
            throw new Exception('unsupported-method', 400);
        }
    }

    public function deleteAction()
    {
        $this->getMenuService()->deleteProduct(
            $this->user,
            App_Model_Product::fetchOne(['id' => $this->getParam('id', false)]));
    }

    public function listAction()
    {
        if (!$this->getRequest()->isGet()) {
            throw new Exception('unsupported-method', 400);
        }
        $this->view->products = App_Map_Product::execute($this->getMenuService()->getProductList(
            $this->user,
            null,
            $this->getParam('offset', 0),
            $this->getParam('limit', 10))
        );
        $this->view->count = $this->getMenuService()->getProductCount(
            $this->user
        );
    }

    public function sectionAction()
    {
        if (!$this->getRequest()->isGet()) {
            throw new Exception('unsupported-method', 400);
        }
        $this->view->products = App_Map_Product::execute($this->getMenuService()->getProductList(
                $this->user,
                App_Model_Section::fetchOne(['id' => $this->getParam('sectionId',  false)]),
                $this->getParam('offset', 0),
                $this->getParam('limit', 10),
                $this->getParam('search', false)
            )
        );
        $this->view->count = $this->getMenuService()->getProductCount(
            $this->user,
            App_Model_Section::fetchOne(['id' => $this->getParam('sectionId',  false)])
        );
    }

    public function searchAction()
    {
        if (!$this->getRequest()->isGet()) {
            throw new Exception('unsupported-method', 400);
        }
        $this->view->products = App_Map_Product::execute($this->getMenuService()->getProductList(
                $this->user,
                null,
                $this->getParam('offset', 0),
                $this->getParam('limit', 10),
                $this->getParam('search', false)
        ));
        $this->view->count = $this->getMenuService()->getProductCount(
            $this->user,
            null,
            $this->getParam('search', false)
        );
    }
} 