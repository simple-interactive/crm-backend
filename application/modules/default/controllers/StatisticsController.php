<?php

class StatisticsController extends App_Controller_Base
{
    public function filtersAction()
    {
        $this->view->ingredients = App_Map_STIngredient::execute(App_Model_STIngredient::fetchAll());
        $this->view->products = App_Map_STProduct::execute(App_Model_STProduct::fetchAll());
        $this->view->sections = App_Map_STSection::execute(App_Model_STSection::fetchAll());
    }
}