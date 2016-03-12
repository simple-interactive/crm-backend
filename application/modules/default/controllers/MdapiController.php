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
        $this->view->ingredients = App_Map_Ingredient::execute(
            $this->getMenuService()->getAllIngredients($this->user)
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

        $this->view->settings = App_Map_Settings::execute(App_Model_Settings::fetchOne([
            'userId' => (string) $this->user->id
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
            $order->data = $item['data'];
            $order->status = $item['status'];
            $order->createdDate = $item['createdDate'];
            $order->payStatus = $item['payStatus'];
            $order->paymentMethod = $item['paymentMethod'];
            $order->tableId = $item['tableId'];
            $order->userId = (string) $this->user->id;
            $order->save();

            $service = new App_Service_Statistics();
            foreach ($order->data as $item) {
                $product = App_Model_Product::fetchOne([
                    'id' => new \MongoId($item ['product']['id'])
                ]);
                $service->putIntoStatistics($product, $order, $item['amount']);
            }
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