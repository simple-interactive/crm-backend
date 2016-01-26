<?php

class MdapiController extends App_Controller_MDAuth
{
    use App_Trait_MenuService;

    public function indexAction()
    {
        $this->view->products = App_Map_Product::execute(
            $this->getMenuService()->getAllProduct($this->user)
        , 'MD');
        $this->view->sections = App_Map_Section::execute(
            $this->getMenuService()->getAllSection($this->user)
        );
        $this->view->style = App_Map_Style::execute(
            $this->getMenuService()->getStyle($this->user)
        );
        $products = App_Model_Product::fetchAll([
            'userId' => (string) $this->user->id
        ]);
        $ids = [];
        foreach ($products as $product) {
            $ids [] = (string) $product->id;
        }
        $this->view->search = App_Map_Search::execute(App_Model_Search::fetchAll([
            'productId' => ['$in' => $ids]
        ]));
    }

    public function orderAction()
    {
        $orders = $this->getParam('orders', null);
        if (empty($orders)) {
            throw new \Exception('orders-invalid');
        }
        foreach ($orders as $item) {
            $order = new App_Model_Order();
            $order->id = new \MongoId($item['id']['$id']);
            $order->data = $item['data'][0];
            $order->status = $item['status'];
            $order->createdDate = $item['createdDate'];
            $order->payStatus = $item['payStatus'];
            $order->tableId = $item['tableId'];
            $order->userId = (string) $this->user->id;
            $order->save();
        }
    }

    public function productExistsAction()
    {
        $product = App_Model_Product::fetchOne([
            'id' => $this->getParam('id', null)
        ]);
        if (!$product) {
            throw new \Exception('Product not found', 400);
        }
        $product->exists = $this->getParam('exists', null);
        $product->save();
    }
} 