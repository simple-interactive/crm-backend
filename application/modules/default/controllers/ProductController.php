<?php

/**
 * @class ProductController
 */
class ProductController extends App_Controller_Base {

    use App_Trait_MenuService;

    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Exception('Unsupported method', 400);
        }
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
                $this->getParam('ingredients', false),
                $this->getParam('exists', false)
        ));
    }

    public function listAction()
    {
        $this->view->products = App_Map_Product::execute($this->getMenuService()->getProductList($this->user, $this->getParam('offset', 0), $this->getParam('limit', 10)));
    }

    public function listcountAction()
    {
        $this->view->count = $this->getMenuService()->getProductCount($this->user);
    }
} 