<?php

class StatisticsController extends App_Controller_Base
{
    public function filtersAction()
    {
        $this->view->ingredients = App_Map_STIngredient::execute(App_Model_STIngredient::fetchAll([
            'userId' => (string) $this->user->id
        ]));

        $distinctIds = App_Model_STProduct::getMapper()->getCollection()->distinct('productId', [
            'userId' => (string) $this->user->id
        ]);

        $this->view->products = App_Map_STProduct::execute(App_Model_STProduct::fetchAll([
            'productId' => ['$in' => $distinctIds],
            'userId' => (string) $this->user->id
        ]));

        $this->view->sections = App_Map_STSection::execute(App_Model_STSection::fetchAll([
            'userId' => (string) $this->user->id
        ]));
    }

    public function dataAction()
    {
        $cond = [
            'createdDate' => [
                '$gte' => $this->getParam('startTime', 0),
                '$lte' => $this->getParam('endTime', 0)
            ],
            'userId' => (string) $this->user->id
        ];

        if ($paymentMethod = $this->getParam('paymentMethod', null)) {
            $cond ['paymentMethod'] = $paymentMethod;
        }

        if ($status = $this->getParam('status', null)) {
            $cond ['status'] = $status;
        }

        $orders = App_Model_Order::fetchAll($cond);
        $ids = array_map(function($order){
            return (string) $order->id;
        }, $orders->asArray());

        $cond = [];
        $cond ['orderId'] = ['$in' => $ids];

        if ($productId = $this->getParam('productId', null)) {
            $product = App_Model_STProduct::fetchOne([
                'id' => new \MongoId($productId)
            ]);
            $cond ['productId'] = $product->productId;
        }

        if ($sectionId = $this->getParam('sectionId', null)) {
            $section = App_Model_STSection::fetchOne([
                'id' => new \MongoId($sectionId)
            ]);
            $cond ['sectionId'] = (string) $section->sectionId;
        }

        $products = App_Model_STProduct::fetchAll($cond, [], $this->getParam('limit', 10), $this->getParam('offset', 0));
        $this->view->products = App_Map_STProduct::execute($products);
    }
}