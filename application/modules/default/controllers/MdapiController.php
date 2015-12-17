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
    }
} 