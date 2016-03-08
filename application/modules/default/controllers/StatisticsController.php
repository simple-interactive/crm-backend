<?php

class StatisticsController extends App_Controller_Base
{
    public function filtersAction()
    {
        $this->view->ingredients = App_Map_STIngredient::execute(App_Model_STIngredient::fetchAll());
        $this->view->products = App_Map_STProduct::execute(App_Model_STProduct::fetchAll());
        $this->view->sections = App_Map_STSection::execute(App_Model_STSection::fetchAll());
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

//        if ($productId = $this->getParam('productId', null)) {
//            $cond ['productId'] = $productId;
//        }
//
//        if ($sectionId = $this->getParam('sectionId', null)) {
//            $cond ['sectionId'] = $sectionId;
//        }

        $products = App_Model_STProduct::fetchAll($cond);
        $this->view->products = App_Map_STProduct::execute($products);
    }
}